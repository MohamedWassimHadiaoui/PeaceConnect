<?php
/**
 * Email Configuration Test Script
 * 
 * This script tests the Resend API configuration and email sending functionality.
 * Access this file via your browser: http://localhost/ff2/final/test_email.php
 * 
 * IMPORTANT: Delete this file after testing for security reasons.
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/controller/EmailService.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Configuration Test - Peace</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
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
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .test-section {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        .test-section h2 {
            color: #333;
            margin-bottom: 15px;
            font-size: 18px;
        }
        .status {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 10px;
        }
        .status.success {
            background: #d4edda;
            color: #155724;
        }
        .status.error {
            background: #f8d7da;
            color: #721c24;
        }
        .status.warning {
            background: #fff3cd;
            color: #856404;
        }
        .info-box {
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            border-radius: 5px;
            padding: 15px;
            margin: 15px 0;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            word-break: break-all;
        }
        .error-box {
            background: #ffe7e7;
            border: 1px solid #ffb3b3;
            border-radius: 5px;
            padding: 15px;
            margin: 15px 0;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            color: #721c24;
        }
        .success-box {
            background: #e7ffe7;
            border: 1px solid #b3ffb3;
            border-radius: 5px;
            padding: 15px;
            margin: 15px 0;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            color: #155724;
        }
        .form-group {
            margin: 20px 0;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        input[type="email"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        input[type="email"]:focus {
            outline: none;
            border-color: #667eea;
        }
        button {
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #5568d3;
        }
        button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .warning strong {
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🕊️ Email Configuration Test</h1>
        <p class="subtitle">Testing Resend API integration for Peace application</p>

        <?php
        // Test 1: Check PHP cURL extension
        echo '<div class="test-section">';
        echo '<h2>Test 1: PHP cURL Extension';
        if (function_exists('curl_init')) {
            echo '<span class="status success">✓ PASSED</span>';
        } else {
            echo '<span class="status error">✗ FAILED</span>';
        }
        echo '</h2>';
        if (function_exists('curl_init')) {
            echo '<div class="success-box">cURL extension is enabled and ready to use.</div>';
        } else {
            echo '<div class="error-box">ERROR: cURL extension is not enabled. Please enable it in your php.ini file.</div>';
        }
        echo '</div>';

        // Test 2: Check configuration
        echo '<div class="test-section">';
        echo '<h2>Test 2: Email Configuration';
        $config = config::getEmailConfig();
        $configValid = !empty($config['api_key']) && 
                      $config['api_key'] !== 'YOUR_RESEND_API_KEY_HERE' &&
                      !empty($config['from_email']);
        
        if ($configValid) {
            echo '<span class="status success">✓ CONFIGURED</span>';
        } else {
            echo '<span class="status error">✗ NOT CONFIGURED</span>';
        }
        echo '</h2>';
        echo '<div class="info-box">';
        echo '<strong>API Key:</strong> ' . (empty($config['api_key']) ? 'NOT SET' : substr($config['api_key'], 0, 10) . '...' . substr($config['api_key'], -5)) . '<br>';
        echo '<strong>From Email:</strong> ' . htmlspecialchars($config['from_email']) . '<br>';
        echo '<strong>From Name:</strong> ' . htmlspecialchars($config['from_name']) . '<br>';
        echo '<strong>API URL:</strong> ' . htmlspecialchars($config['api_url']) . '<br>';
        echo '</div>';
        if (!$configValid) {
            echo '<div class="error-box">ERROR: Please configure your Resend API key in config.php</div>';
        }
        echo '</div>';

        // Test 3: Test API connection (if form submitted)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_email'])) {
            $testEmail = $_POST['test_email'] ?? '';
            
            if (empty($testEmail) || !filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
                echo '<div class="test-section">';
                echo '<h2>Test 3: Send Test Email <span class="status error">✗ FAILED</span></h2>';
                echo '<div class="error-box">Please enter a valid email address.</div>';
                echo '</div>';
            } else {
                echo '<div class="test-section">';
                echo '<h2>Test 3: Send Test Email';
                
                // Generate a test code
                $testCode = EmailService::generateVerificationCode();
                
                // Attempt to send email
                $startTime = microtime(true);
                $emailSent = EmailService::sendPasswordResetCode($testEmail, $testCode, 'Test User');
                $endTime = microtime(true);
                $duration = round(($endTime - $startTime) * 1000, 2);
                
                if ($emailSent) {
                    echo '<span class="status success">✓ SUCCESS</span>';
                } else {
                    echo '<span class="status error">✗ FAILED</span>';
                }
                echo '</h2>';
                
                if ($emailSent) {
                    echo '<div class="success-box">';
                    echo '✓ Email sent successfully!<br>';
                    echo '✓ Response time: ' . $duration . 'ms<br>';
                    echo '✓ Test verification code: <strong>' . htmlspecialchars($testCode) . '</strong><br>';
                    echo '✓ Please check your inbox at: <strong>' . htmlspecialchars($testEmail) . '</strong><br>';
                    echo '</div>';
                } else {
                    echo '<div class="error-box">';
                    echo '✗ Failed to send email. Possible reasons:<br><br>';
                    echo '1. Invalid or expired API key<br>';
                    echo '2. API key doesn\'t have proper permissions<br>';
                    echo '3. Network/connection issues<br>';
                    echo '4. Sender email domain not verified<br><br>';
                    echo '<strong>Check your PHP error logs for detailed error messages.</strong><br>';
                    echo 'Error logs location: ' . ini_get('error_log') . '<br>';
                    echo '</div>';
                }
                echo '</div>';
            }
        } else {
            // Show test form
            echo '<div class="test-section">';
            echo '<h2>Test 3: Send Test Email <span class="status warning">READY</span></h2>';
            echo '<p>Enter your email address below to receive a test verification code:</p>';
            echo '<form method="POST" action="">';
            echo '<div class="form-group">';
            echo '<label for="test_email">Your Email Address:</label>';
            echo '<input type="email" id="test_email" name="test_email" placeholder="your.email@example.com" required>';
            echo '</div>';
            echo '<button type="submit" ' . (!function_exists('curl_init') || !$configValid ? 'disabled' : '') . '>';
            echo 'Send Test Email';
            echo '</button>';
            echo '</form>';
            echo '</div>';
        }

        // Test 4: Check error logs location
        echo '<div class="test-section">';
        echo '<h2>Test 4: Error Logging Configuration</h2>';
        $errorLog = ini_get('error_log');
        echo '<div class="info-box">';
        echo '<strong>Error Log Location:</strong> ' . ($errorLog ? htmlspecialchars($errorLog) : 'Using default PHP error log') . '<br>';
        echo '<strong>Error Reporting:</strong> ' . (error_reporting() ? 'Enabled' : 'Disabled') . '<br>';
        echo '<strong>Display Errors:</strong> ' . (ini_get('display_errors') ? 'Enabled' : 'Disabled') . '<br>';
        echo '</div>';
        if ($errorLog) {
            echo '<div class="success-box">Error logging is configured. Check this file for detailed error messages.</div>';
        } else {
            echo '<div class="warning">Using default error log. Check your PHP configuration for the exact location.</div>';
        }
        echo '</div>';

        // Summary
        echo '<div class="test-section">';
        echo '<h2>Summary & Next Steps</h2>';
        $allTestsPassed = function_exists('curl_init') && $configValid;
        
        if ($allTestsPassed) {
            echo '<div class="success-box">';
            echo '<strong>✓ All basic tests passed!</strong><br><br>';
            echo 'Your email configuration appears to be set up correctly. If you\'re still experiencing issues:<br>';
            echo '1. Verify your API key is active in your Resend dashboard<br>';
            echo '2. Check that your API key has "Sending Access" or "Full Access" permissions<br>';
            echo '3. Try sending a test email using the form above<br>';
            echo '4. Check PHP error logs for detailed error messages<br>';
            echo '</div>';
        } else {
            echo '<div class="error-box">';
            echo '<strong>✗ Configuration issues detected</strong><br><br>';
            if (!function_exists('curl_init')) {
                echo '• Enable cURL extension in php.ini<br>';
            }
            if (!$configValid) {
                echo '• Configure your Resend API key in config.php<br>';
            }
            echo '</div>';
        }
        echo '</div>';

        // Security warning
        echo '<div class="warning">';
        echo '<strong>⚠️ Security Notice:</strong> Delete this test file (test_email.php) after testing to prevent unauthorized access to your configuration details.';
        echo '</div>';
        ?>
    </div>
</body>
</html>

