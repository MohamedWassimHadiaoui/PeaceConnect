<?php
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../ai_config.php";
require_once __DIR__ . "/../Model/AIFlag.php";

class AIController {
    private $db;

    public function __construct() {
        $this->db = getConnection();
    }

    public function analyzeReport($reportId, $title, $description, $type) {
        $textToAnalyze = "Title: $title\nType: $type\nDescription: $description";
        $aiResponse = $this->callGrokAPI($textToAnalyze);
        
        if ($aiResponse && isset($aiResponse['analysis'])) {
            $this->saveAIFlags($reportId, $aiResponse['analysis']);
            
            if (isset($aiResponse['analysis']['suggested_priority'])) {
                $this->updateReportPriority($reportId, $aiResponse['analysis']['suggested_priority']);
            }
            
            return $aiResponse;
        }
        
        return ['success' => false, 'error' => 'AI analysis failed'];
    }

    private function callGrokAPI($text) {
        $prompt = $this->buildPrompt($text);
        
        $data = [
            'model' => XAI_MODEL,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a report analysis assistant for a conflict mediation platform. You respond ONLY with valid JSON, no markdown, no extra text.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.1,
            'max_tokens' => 1024
        ];

        $ch = curl_init(XAI_API_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . XAI_API_KEY
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            error_log("xAI Grok API Error: $error");
            return $this->fallbackAnalysis($text);
        }

        if ($httpCode !== 200) {
            error_log("xAI Grok API HTTP Error: $httpCode - $response");
            return $this->fallbackAnalysis($text);
        }

        $result = json_decode($response, true);
        
        if (isset($result['choices'][0]['message']['content'])) {
            $aiText = $result['choices'][0]['message']['content'];
            return $this->parseGrokResponse($aiText);
        }

        return $this->fallbackAnalysis($text);
    }

    private function buildPrompt($text) {
        return "Analyze this conflict report and return ONLY a valid JSON object.

TEXT TO ANALYZE:
$text

RESPONSE FORMAT (JSON only, no markdown):
{\"violence_detected\":false,\"urgency_level\":\"low\",\"harassment_detected\":false,\"discrimination_detected\":false,\"keywords_found\":[],\"suggested_priority\":\"medium\",\"confidence_score\":0.5,\"summary\":\"Brief summary in English\"}

RULES:
- violence_detected: true if physical threats or assault mentioned
- urgency_level: critical/high/medium/low based on severity
- harassment_detected: true if harassment or intimidation present
- discrimination_detected: true if discrimination mentioned
- suggested_priority: high if violence/urgency, medium if harassment, low otherwise
- confidence_score: between 0.0 and 1.0
- summary: brief English summary (max 100 characters)

IMPORTANT: Respond ONLY with the JSON, no text before or after.";
    }

    private function parseGrokResponse($text) {
        $text = trim($text);
        $text = preg_replace('/^```json\s*/i', '', $text);
        $text = preg_replace('/^```\s*/i', '', $text);
        $text = preg_replace('/\s*```$/i', '', $text);
        $text = trim($text);

        $analysis = json_decode($text, true);
        
        if ($analysis === null) {
            error_log("Failed to parse xAI Grok response: $text");
            return null;
        }

        return [
            'success' => true,
            'analysis' => [
                'violence_detected' => $analysis['violence_detected'] ?? false,
                'urgency_level' => $analysis['urgency_level'] ?? 'low',
                'harassment_detected' => $analysis['harassment_detected'] ?? false,
                'discrimination_detected' => $analysis['discrimination_detected'] ?? false,
                'keywords_found' => $analysis['keywords_found'] ?? [],
                'suggested_priority' => $analysis['suggested_priority'] ?? 'medium',
                'confidence_score' => $analysis['confidence_score'] ?? 0.5,
                'summary' => $analysis['summary'] ?? ''
            ]
        ];
    }

    private function fallbackAnalysis($text) {
        $textLower = mb_strtolower($text);
        
        $violenceDetected = false;
        $harassmentDetected = false;
        $discriminationDetected = false;
        $urgencyLevel = 'low';
        $keywordsFound = [];

        foreach (VIOLENCE_KEYWORDS as $keyword) {
            if (strpos($textLower, $keyword) !== false) {
                $violenceDetected = true;
                $keywordsFound[] = $keyword;
            }
        }

        foreach (URGENCY_KEYWORDS as $keyword) {
            if (strpos($textLower, $keyword) !== false) {
                $urgencyLevel = 'high';
                $keywordsFound[] = $keyword;
            }
        }

        foreach (HARASSMENT_KEYWORDS as $keyword) {
            if (strpos($textLower, $keyword) !== false) {
                $harassmentDetected = true;
                $keywordsFound[] = $keyword;
            }
        }

        foreach (DISCRIMINATION_KEYWORDS as $keyword) {
            if (strpos($textLower, $keyword) !== false) {
                $discriminationDetected = true;
                $keywordsFound[] = $keyword;
            }
        }

        $suggestedPriority = 'medium';
        if ($violenceDetected || $urgencyLevel === 'high' || $urgencyLevel === 'critical') {
            $suggestedPriority = 'high';
            $urgencyLevel = 'high';
        } elseif (!$harassmentDetected && !$discriminationDetected) {
            $suggestedPriority = 'low';
        }

        return [
            'success' => true,
            'analysis' => [
                'violence_detected' => $violenceDetected,
                'urgency_level' => $urgencyLevel,
                'harassment_detected' => $harassmentDetected,
                'discrimination_detected' => $discriminationDetected,
                'keywords_found' => array_unique($keywordsFound),
                'suggested_priority' => $suggestedPriority,
                'confidence_score' => 0.7,
                'summary' => 'Keyword analysis (API unavailable)'
            ],
            'fallback' => true
        ];
    }

    private function saveAIFlags($reportId, $analysis) {
        $this->deleteFlags($reportId);

        if ($analysis['violence_detected']) {
            $this->createFlag($reportId, 'violence', 
                $analysis['urgency_level'] === 'critical' ? 'critical' : 'high',
                $analysis['confidence_score'],
                $analysis['keywords_found'],
                $analysis['summary'],
                $analysis['suggested_priority']
            );
        }

        if ($analysis['urgency_level'] === 'high' || $analysis['urgency_level'] === 'critical') {
            $this->createFlag($reportId, 'urgency',
                $analysis['urgency_level'],
                $analysis['confidence_score'],
                $analysis['keywords_found'],
                $analysis['summary'],
                $analysis['suggested_priority']
            );
        }

        if ($analysis['harassment_detected']) {
            $this->createFlag($reportId, 'harassment', 'medium',
                $analysis['confidence_score'],
                $analysis['keywords_found'],
                $analysis['summary'],
                $analysis['suggested_priority']
            );
        }

        if ($analysis['discrimination_detected']) {
            $this->createFlag($reportId, 'discrimination', 'medium',
                $analysis['confidence_score'],
                $analysis['keywords_found'],
                $analysis['summary'],
                $analysis['suggested_priority']
            );
        }

        if (!$analysis['violence_detected'] && 
            !$analysis['harassment_detected'] && 
            !$analysis['discrimination_detected'] &&
            $analysis['urgency_level'] === 'low') {
            $this->createFlag($reportId, 'general', 'low',
                $analysis['confidence_score'],
                $analysis['keywords_found'],
                $analysis['summary'],
                $analysis['suggested_priority']
            );
        }
    }

    private function createFlag($reportId, $flagType, $severity, $confidence, $keywords, $summary, $priority) {
        if (!$this->tableExists()) {
            return false;
        }
        try {
            $sql = "INSERT INTO ai_flags (report_id, flag_type, severity, confidence_score, keywords_detected, ai_summary, suggested_priority) 
                    VALUES (:report_id, :flag_type, :severity, :confidence_score, :keywords_detected, :ai_summary, :suggested_priority)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':report_id' => $reportId,
                ':flag_type' => $flagType,
                ':severity' => $severity,
                ':confidence_score' => $confidence,
                ':keywords_detected' => json_encode($keywords),
                ':ai_summary' => $summary,
                ':suggested_priority' => $priority
            ]);
            return true;
        } catch (Exception $e) {
            error_log("AI flag creation error: " . $e->getMessage());
            return false;
        }
    }

    public function deleteFlags($reportId) {
        if (!$this->tableExists()) {
            return false;
        }
        try {
            $sql = "DELETE FROM ai_flags WHERE report_id = :report_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':report_id' => $reportId]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    private function updateReportPriority($reportId, $priority) {
        $sql = "UPDATE reports SET priority = :priority WHERE id = :id AND priority != 'high'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':priority' => $priority, ':id' => $reportId]);
    }

    public function getFlagsByReportId($reportId) {
        if (!$this->tableExists()) {
            return [];
        }
        try {
            $sql = "SELECT * FROM ai_flags WHERE report_id = :report_id ORDER BY 
                    CASE severity 
                        WHEN 'critical' THEN 1 
                        WHEN 'high' THEN 2 
                        WHEN 'medium' THEN 3 
                        ELSE 4 
                    END";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':report_id' => $reportId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    public function getAllFlagsWithReports() {
        if (!$this->tableExists()) {
            return [];
        }
        try {
            $sql = "SELECT f.*, r.title as report_title, r.type as report_type, r.status as report_status
                    FROM ai_flags f
                    JOIN reports r ON f.report_id = r.id
                    ORDER BY 
                        CASE f.severity 
                            WHEN 'critical' THEN 1 
                            WHEN 'high' THEN 2 
                            WHEN 'medium' THEN 3 
                            ELSE 4 
                        END,
                        f.created_at DESC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    private function tableExists() {
        try {
            $sql = "SHOW TABLES LIKE 'ai_flags'";
            $stmt = $this->db->query($sql);
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    public function countCriticalFlags() {
        if (!$this->tableExists()) {
            return 0;
        }
        try {
            $sql = "SELECT COUNT(*) as count FROM ai_flags WHERE severity IN ('critical', 'high')";
            $stmt = $this->db->query($sql);
            return $stmt->fetch()['count'];
        } catch (Exception $e) {
            return 0;
        }
    }

    public function getReportsWithFlags() {
        if (!$this->tableExists()) {
            try {
                $sql = "SELECT r.*, NULL as flag_types, 0 as max_severity FROM reports r ORDER BY r.created_at DESC";
                $stmt = $this->db->query($sql);
                return $stmt->fetchAll();
            } catch (Exception $e) {
                return [];
            }
        }
        try {
            $sql = "SELECT r.*, 
                           GROUP_CONCAT(DISTINCT f.flag_type) as flag_types,
                           MAX(CASE f.severity 
                               WHEN 'critical' THEN 4 
                               WHEN 'high' THEN 3 
                               WHEN 'medium' THEN 2 
                               ELSE 1 
                           END) as max_severity
                    FROM reports r
                    LEFT JOIN ai_flags f ON r.id = f.report_id
                    GROUP BY r.id
                    ORDER BY max_severity DESC, r.created_at DESC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }

    public function getReportFlags($reportId) {
        return $this->getFlagsByReportId($reportId);
    }

    public function analyzeHelpRequest($type, $situation) {
        $textToAnalyze = "Help type: $type\nSituation: $situation";
        $aiResponse = $this->callGrokAPI($textToAnalyze);
        
        if ($aiResponse && isset($aiResponse['analysis'])) {
            return $aiResponse['analysis'];
        }
        
        return $this->fallbackAnalysis($textToAnalyze)['analysis'] ?? null;
    }

    public function analyzeForumPost($title, $content) {
        $textToAnalyze = "Title: $title\nContent: $content";
        $textLower = strtolower($title . ' ' . $content);
        
        $blockedPatterns = [
            '/\b(kill|murder|attack)\s+(you|him|her|them)\b/i',
            '/\b(i\s+will|gonna|going\s+to)\s+(hurt|harm|kill)\b/i',
            '/\bdeath\s+threat/i',
            '/\bbuy\s+now\b.*\bclick\b/i',
            '/\bfree\s+money\b/i',
            '/\bvisit\s+my\s+(site|website|link)\b/i'
        ];
        
        foreach ($blockedPatterns as $pattern) {
            if (preg_match($pattern, $textLower)) {
                return ['is_safe' => false, 'flags' => ['blocked_pattern' => true], 'auto_approve' => false];
            }
        }
        
        return ['is_safe' => true, 'flags' => null, 'auto_approve' => true];
    }

    public function moderateContent($content) {
        $result = $this->analyzeForumPost('', $content);
        return $result['is_safe'];
    }
}
?>
