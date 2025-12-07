<?php
/**
 * 2FA Test Script
 * 
 * This script helps debug 2FA issues by testing the TOTP implementation
 * Access: http://localhost/ff2/final/test_2fa.php?secret=YOUR_SECRET
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/controller/TwoFactorAuth.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2FA Test - Peace</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 30px;
        }
        h1 { color: #333; margin-bottom: 20px; }
        .form-group { margin: 20px 0; }
        label { display: block; margin-bottom: 8px; color: #333; font-weight: 500; }
        input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        button {
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover { background: #5568d3; }
        .result {
            margin: 20px 0;
            padding: 15px;
            border-radius: 5px;
            font-family: monospace;
        }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .info { background: #e7f3ff; color: #004085; }
        .code-display {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 5px;
            margin: 10px 0;
            letter-spacing: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 2FA Test Tool</h1>
        
        <?php
        $secret = $_GET['secret'] ?? '';
        $testCode = $_POST['test_code'] ?? '';
        
        if (empty($secret) && $_SERVER['REQUEST_METHOD'] === 'GET') {
            // Generate a test secret
            $secret = TwoFactorAuth::generateSecret();
            echo '<div class="result info">';
            echo '<strong>Generated Test Secret:</strong><br>';
            echo '<div class="code-display">' . htmlspecialchars($secret) . '</div>';
            echo '<p>Use this secret in your authenticator app to test.</p>';
            echo '</div>';
        }
        
        if ($secret) {
            echo '<div class="result info">';
            echo '<strong>Testing Secret:</strong> ' . htmlspecialchars($secret) . '<br>';
            echo '<strong>Current Time:</strong> ' . date('Y-m-d H:i:s') . '<br>';
            echo '<strong>Time Step:</strong> ' . floor(time() / 30) . '<br>';
            echo '</div>';
            
            // Generate current code
            $currentTimeStep = floor(time() / 30);
            $reflection = new ReflectionClass('TwoFactorAuth');
            $method = $reflection->getMethod('generateTOTP');
            $method->setAccessible(true);
            $currentCode = $method->invoke(null, $secret, $currentTimeStep);
            
            echo '<div class="result success">';
            echo '<strong>Expected Code (Current Time Step):</strong><br>';
            echo '<div class="code-display">' . htmlspecialchars($currentCode) . '</div>';
            echo '</div>';
            
            // Show codes for adjacent time steps
            echo '<div class="result info">';
            echo '<strong>Codes for Adjacent Time Steps (for debugging):</strong><br>';
            for ($i = -2; $i <= 2; $i++) {
                $ts = $currentTimeStep + $i;
                $code = $method->invoke(null, $secret, $ts);
                $label = $i == 0 ? ' (current)' : ($i < 0 ? ' (' . ($i * 30) . 's ago)' : ' (+' . ($i * 30) . 's)');
                echo 'Time Step ' . $ts . $label . ': <strong>' . $code . '</strong><br>';
            }
            echo '</div>';
            
            // Test verification
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($testCode)) {
                $isValid = TwoFactorAuth::verifyCode($secret, $testCode);
                
                if ($isValid) {
                    echo '<div class="result success">';
                    echo '<strong>✓ Code Verified Successfully!</strong><br>';
                    echo 'The code "' . htmlspecialchars($testCode) . '" is valid.';
                    echo '</div>';
                } else {
                    echo '<div class="result error">';
                    echo '<strong>✗ Code Verification Failed</strong><br>';
                    echo 'The code "' . htmlspecialchars($testCode) . '" is not valid.<br>';
                    echo 'Make sure you\'re using the correct secret and the code hasn\'t expired.';
                    echo '</div>';
                }
            }
            
            // Test form
            echo '<form method="POST" action="?secret=' . urlencode($secret) . '">';
            echo '<div class="form-group">';
            echo '<label for="test_code">Enter Code from Authenticator App:</label>';
            echo '<input type="text" id="test_code" name="test_code" placeholder="000000" maxlength="6" pattern="[0-9]{6}" required>';
            echo '</div>';
            echo '<button type="submit">Test Code</button>';
            echo '</form>';
            
            // QR code
            $qrUrl = TwoFactorAuth::getQRCodeUrl($secret, 'test@example.com', 'Peace Test');
            echo '<div style="text-align: center; margin-top: 20px;">';
            echo '<p><strong>QR Code for Testing:</strong></p>';
            echo '<img src="' . htmlspecialchars($qrUrl) . '" alt="QR Code" style="max-width: 300px; border: 4px solid #ddd; border-radius: 8px;">';
            echo '</div>';
        }
        ?>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #ddd;">
            <h3>How to Use:</h3>
            <ol style="margin: 10px 0; padding-left: 20px;">
                <li>This page will generate a test secret</li>
                <li>Scan the QR code with your authenticator app (Authy, Google Authenticator, etc.)</li>
                <li>Enter the 6-digit code from your app</li>
                <li>Click "Test Code" to verify it works</li>
                <li>If it works here, it should work in your app!</li>
            </ol>
        </div>
    </div>
</body>
</html>

