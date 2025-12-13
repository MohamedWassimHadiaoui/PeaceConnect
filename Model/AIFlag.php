<?php
class AIFlag {
    private $id;
    private $report_id;
    private $flag_type;
    private $severity;
    private $confidence_score;
    private $keywords_detected;
    private $ai_summary;
    private $suggested_priority;
    private $created_at;

    public function __construct(
        $id = null,
        $report_id = null,
        $flag_type = null,
        $severity = null,
        $confidence_score = null,
        $keywords_detected = null,
        $ai_summary = null,
        $suggested_priority = null,
        $created_at = null
    ) {
        $this->id = $id;
        $this->report_id = $report_id;
        $this->flag_type = $flag_type;
        $this->severity = $severity;
        $this->confidence_score = $confidence_score;
        $this->keywords_detected = $keywords_detected;
        $this->ai_summary = $ai_summary;
        $this->suggested_priority = $suggested_priority;
        $this->created_at = $created_at;
    }

    public function getId() { return $this->id; }
    public function getReportId() { return $this->report_id; }
    public function getFlagType() { return $this->flag_type; }
    public function getSeverity() { return $this->severity; }
    public function getConfidenceScore() { return $this->confidence_score; }
    public function getKeywordsDetected() { return $this->keywords_detected; }
    public function getAiSummary() { return $this->ai_summary; }
    public function getSuggestedPriority() { return $this->suggested_priority; }
    public function getCreatedAt() { return $this->created_at; }

    public function setId($id) { $this->id = $id; }
    public function setReportId($report_id) { $this->report_id = $report_id; }
    public function setFlagType($flag_type) { $this->flag_type = $flag_type; }
    public function setSeverity($severity) { $this->severity = $severity; }
    public function setConfidenceScore($confidence_score) { $this->confidence_score = $confidence_score; }
    public function setKeywordsDetected($keywords_detected) { $this->keywords_detected = $keywords_detected; }
    public function setAiSummary($ai_summary) { $this->ai_summary = $ai_summary; }
    public function setSuggestedPriority($suggested_priority) { $this->suggested_priority = $suggested_priority; }
    public function setCreatedAt($created_at) { $this->created_at = $created_at; }

    public function getKeywordsArray() {
        if (is_string($this->keywords_detected)) {
            return json_decode($this->keywords_detected, true) ?: [];
        }
        return $this->keywords_detected ?: [];
    }

    public function getSeverityBadgeClass() {
        switch ($this->severity) {
            case 'critical': return 'badge-critical';
            case 'high': return 'badge-high';
            case 'medium': return 'badge-medium';
            case 'low': return 'badge-low';
            default: return 'badge-low';
        }
    }

    public function getFlagTypeIcon() {
        switch ($this->flag_type) {
            case 'violence': return '🔴';
            case 'urgency': return '⚠️';
            case 'harassment': return '🟠';
            case 'discrimination': return '🟡';
            default: return '🔵';
        }
    }

    public function getFlagTypeLabel() {
        switch ($this->flag_type) {
            case 'violence': return 'Violence Detected';
            case 'urgency': return 'Urgency Detected';
            case 'harassment': return 'Harassment Detected';
            case 'discrimination': return 'Discrimination Detected';
            default: return 'Alert';
        }
    }
}
?>
