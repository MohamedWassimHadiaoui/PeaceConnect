<?php
require_once __DIR__ . '/../config.php';

class EmailService {
    
    /**
     * Send email using Resend API (free tier available)
     * 
     * @param string $to Recipient email address
     * @param string $subject Email subject
     * @param string $htmlContent HTML email content
     * @param string $textContent Plain text email content
     * @return bool True if email was sent successfully, false otherwise
     */
    private static function sendEmail($to, $subject, $htmlContent, $textContent = '') {
        $config = config::getEmailConfig();
        $apiKey = $config['api_key'];
        
        // Check if API key is configured
        if (empty($apiKey) || $apiKey === 'YOUR_RESEND_API_KEY_HERE') {
            error_log('Resend API key not configured');
            return false;
        }
        
        // Use Resend's test domain for development/testing
        // This works without domain verification
        // For production, verify your domain in Resend dashboard and use your own email
        $fromEmail = $config['from_email'];
        if (strpos($fromEmail, '@resend.dev') === false && strpos($fromEmail, '@peace.com') !== false) {
            // If using a custom domain that might not be verified, use test domain
            $fromEmail = 'onboarding@resend.dev';
        }
        
        // Prepare Resend API request
        $data = [
            'from' => $config['from_name'] . ' <' . $fromEmail . '>',
            'to' => [$to],
            'subject' => $subject,
            'html' => $htmlContent
        ];
        
        if (!empty($textContent)) {
            $data['text'] = $textContent;
        }
        
        // Send via cURL
        $ch = curl_init($config['api_url']);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            error_log('Resend API cURL Error: ' . $error);
            return false;
        }
        
        // Parse response for better error messages
        $responseData = json_decode($response, true);
        
        // Resend returns 200 OK on success
        if ($httpCode === 200) {
            return true;
        } else {
            // Log detailed error information
            $errorMessage = 'Resend API Error: HTTP ' . $httpCode;
            if ($responseData && isset($responseData['message'])) {
                $errorMessage .= ' - ' . $responseData['message'];
            } elseif ($responseData && isset($responseData['error'])) {
                $errorMessage .= ' - ' . $responseData['error'];
            } else {
                $errorMessage .= ' - ' . substr($response, 0, 200);
            }
            error_log($errorMessage);
            
            // Log full response for debugging (truncated to avoid huge logs)
            if ($responseData) {
                error_log('Resend API Response: ' . json_encode($responseData));
            }
            
            return false;
        }
    }
    
    /**
     * Send a password reset verification code via email
     * 
     * @param string $to Recipient email address
     * @param string $code 6-digit verification code
     * @param string $userName User's name for personalization
     * @return bool True if email was sent successfully, false otherwise
     */
    public static function sendPasswordResetCode($to, $code, $userName = 'User') {
        $subject = "Password Reset Verification Code - Peace";
        
        $htmlContent = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #4CAF50; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                .content { padding: 20px; background-color: #f9f9f9; }
                .code-box { background-color: #fff; border: 2px solid #4CAF50; padding: 20px; text-align: center; margin: 20px 0; border-radius: 5px; }
                .code { font-size: 32px; font-weight: bold; color: #4CAF50; letter-spacing: 5px; font-family: monospace; }
                .footer { padding: 20px; text-align: center; color: #666; font-size: 12px; }
                .warning { background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 10px; margin: 15px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🕊️ Peace</h1>
                </div>
                <div class='content'>
                    <h2>Password Reset Verification</h2>
                    <p>Hello " . htmlspecialchars($userName) . ",</p>
                    <p>You have requested to reset your password. Please use the following verification code to proceed:</p>
                    
                    <div class='code-box'>
                        <div class='code'>" . htmlspecialchars($code) . "</div>
                    </div>
                    
                    <div class='warning'>
                        <strong>⚠️ Security Notice:</strong> This code will expire in 15 minutes. If you did not request a password reset, please ignore this email.
                    </div>
                    
                    <p>Enter this code on the password reset page to create a new password.</p>
                </div>
                <div class='footer'>
                    <p>&copy; 2025 Peace. All Rights Reserved.</p>
                    <p>This is an automated message, please do not reply.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $textContent = "Hello " . $userName . ",\n\n" .
                      "You have requested to reset your password.\n\n" .
                      "Your verification code is: " . $code . "\n\n" .
                      "This code will expire in 15 minutes.\n\n" .
                      "If you did not request a password reset, please ignore this email.\n\n" .
                      "© 2025 Peace. All Rights Reserved.";
        
        return self::sendEmail($to, $subject, $htmlContent, $textContent);
    }
    
    /**
     * Send password change confirmation email
     * 
     * @param string $to Recipient email address
     * @param string $userName User's name for personalization
     * @return bool True if email was sent successfully, false otherwise
     */
    public static function sendPasswordChangeConfirmation($to, $userName = 'User') {
        $subject = "Password Changed Successfully - Peace";
        
        $htmlContent = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #4CAF50; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                .content { padding: 20px; background-color: #f9f9f9; }
                .success-box { background-color: #d4edda; border-left: 4px solid #28a745; padding: 15px; margin: 15px 0; }
                .footer { padding: 20px; text-align: center; color: #666; font-size: 12px; }
                .warning { background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 10px; margin: 15px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🕊️ Peace</h1>
                </div>
                <div class='content'>
                    <h2>Password Changed Successfully</h2>
                    <p>Hello " . htmlspecialchars($userName) . ",</p>
                    
                    <div class='success-box'>
                        <strong>✓ Your password has been successfully changed.</strong>
                    </div>
                    
                    <p>Your account password was changed on " . date('F j, Y \a\t g:i A') . ".</p>
                    
                    <div class='warning'>
                        <strong>⚠️ Security Notice:</strong> If you did not make this change, please contact our support team immediately and secure your account.
                    </div>
                    
                    <p>For your security, if you did not make this change, we recommend:</p>
                    <ul>
                        <li>Changing your password again immediately</li>
                        <li>Reviewing your account activity</li>
                        <li>Contacting support if you notice any suspicious activity</li>
                    </ul>
                </div>
                <div class='footer'>
                    <p>&copy; 2025 Peace. All Rights Reserved.</p>
                    <p>This is an automated message, please do not reply.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $textContent = "Hello " . $userName . ",\n\n" .
                      "Your password has been successfully changed on " . date('F j, Y \a\t g:i A') . ".\n\n" .
                      "If you did not make this change, please contact our support team immediately.\n\n" .
                      "© 2025 Peace. All Rights Reserved.";
        
        return self::sendEmail($to, $subject, $htmlContent, $textContent);
    }
    
    /**
     * Generate a random 6-digit verification code
     * 
     * @return string 6-digit code
     */
    public static function generateVerificationCode() {
        return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }
}
?>

