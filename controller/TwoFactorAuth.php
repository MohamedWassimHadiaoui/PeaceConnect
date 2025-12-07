<?php
/**
 * Google Authenticator (TOTP) 2FA Service
 * 
 * This class handles Time-based One-Time Password (TOTP) generation and verification
 * compatible with Google Authenticator and other TOTP apps.
 */

class TwoFactorAuth {
    
    /**
     * Generate a random secret key for 2FA
     * 
     * @return string Base32 encoded secret (16 characters)
     */
    public static function generateSecret() {
        // Generate 10 random bytes (80 bits)
        $randomBytes = random_bytes(10);
        // Convert to base32
        return self::base32Encode($randomBytes);
    }
    
    /**
     * Generate QR code URL for Google Authenticator
     * 
     * @param string $secret The secret key
     * @param string $email User's email
     * @param string $issuer Application name
     * @return string QR code URL
     */
    public static function getQRCodeUrl($secret, $email, $issuer = 'Peace') {
        $label = urlencode($email);
        $issuerEncoded = urlencode($issuer);
        $secretEncoded = urlencode($secret);
        
        // Google Charts API for QR code (free, no API key needed)
        return "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . 
               urlencode("otpauth://totp/{$issuer}:{$label}?secret={$secretEncoded}&issuer={$issuerEncoded}");
    }
    
    /**
     * Verify a TOTP code
     * 
     * @param string $secret The secret key
     * @param string $code The 6-digit code from authenticator app
     * @param int $timeStep Time step window (default 30 seconds)
     * @param int $discrepancy Allowed time steps discrepancy (default 2 = ±60 seconds)
     * @return bool True if code is valid
     */
    public static function verifyCode($secret, $code, $timeStep = 30, $discrepancy = 2) {
        // Clean the secret - remove spaces and convert to uppercase
        $secret = strtoupper(preg_replace('/\s+/', '', $secret));
        
        // Remove any spaces from code
        $code = preg_replace('/\s+/', '', $code);
        
        // Code must be 6 digits
        if (!preg_match('/^\d{6}$/', $code)) {
            return false;
        }
        
        // Ensure code is a string for comparison
        $code = str_pad($code, 6, '0', STR_PAD_LEFT);
        $currentTimeStep = floor(time() / $timeStep);
        
        // Check current time step and adjacent time steps (to account for clock skew)
        // Increased discrepancy to 2 (60 seconds) for better compatibility
        for ($i = -$discrepancy; $i <= $discrepancy; $i++) {
            $timeStepToCheck = $currentTimeStep + $i;
            $expectedCode = self::generateTOTP($secret, $timeStepToCheck);
            
            // Compare as strings to ensure proper padding
            if ($expectedCode === $code) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Generate TOTP code for a specific time step
     * 
     * @param string $secret The secret key
     * @param int $timeStep The time step
     * @return string 6-digit code
     */
    private static function generateTOTP($secret, $timeStep) {
        // Clean the secret - remove spaces and convert to uppercase
        $secret = strtoupper(preg_replace('/\s+/', '', $secret));
        
        // Decode base32 secret
        $key = self::base32Decode($secret);
        
        if (empty($key)) {
            error_log("2FA Error: Empty key after base32 decode. Secret: " . substr($secret, 0, 10) . "...");
            return '000000';
        }
        
        // Pack time step as 64-bit big-endian integer (8 bytes)
        // High 32 bits (always 0 for reasonable time values)
        // Low 32 bits (the time step)
        $time = pack('N', 0) . pack('N', $timeStep);
        
        // Generate HMAC-SHA1
        $hash = hash_hmac('sha1', $time, $key, true);
        
        if (strlen($hash) < 20) {
            error_log("2FA Error: HMAC hash too short. Length: " . strlen($hash));
            return '000000';
        }
        
        // Dynamic truncation (RFC 4226)
        $offset = ord($hash[19]) & 0x0f;
        
        // Ensure we don't go out of bounds
        if ($offset + 3 >= strlen($hash)) {
            error_log("2FA Error: Offset out of bounds");
            return '000000';
        }
        
        // Extract 31-bit value
        $code = (
            ((ord($hash[$offset + 0]) & 0x7f) << 24) |
            ((ord($hash[$offset + 1]) & 0xff) << 16) |
            ((ord($hash[$offset + 2]) & 0xff) << 8) |
            (ord($hash[$offset + 3]) & 0xff)
        ) % 1000000;
        
        // Pad to 6 digits
        return str_pad($code, 6, '0', STR_PAD_LEFT);
    }
    
    /**
     * Encode binary data to Base32
     * 
     * @param string $data Binary data
     * @return string Base32 encoded string
     */
    private static function base32Encode($data) {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $result = '';
        $buffer = 0;
        $bitsLeft = 0;
        
        for ($i = 0; $i < strlen($data); $i++) {
            $buffer = ($buffer << 8) | ord($data[$i]);
            $bitsLeft += 8;
            
            while ($bitsLeft >= 5) {
                $result .= $chars[($buffer >> ($bitsLeft - 5)) & 31];
                $bitsLeft -= 5;
            }
        }
        
        if ($bitsLeft > 0) {
            $result .= $chars[($buffer << (5 - $bitsLeft)) & 31];
        }
        
        return $result;
    }
    
    /**
     * Decode Base32 string to binary
     * 
     * @param string $data Base32 encoded string
     * @return string Binary data
     */
    private static function base32Decode($data) {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        // Remove spaces and convert to uppercase
        $data = strtoupper(preg_replace('/\s+/', '', $data));
        $result = '';
        $buffer = 0;
        $bitsLeft = 0;
        
        for ($i = 0; $i < strlen($data); $i++) {
            $char = $data[$i];
            $value = strpos($chars, $char);
            
            if ($value === false) {
                // Skip invalid characters but log for debugging
                continue;
            }
            
            $buffer = ($buffer << 5) | $value;
            $bitsLeft += 5;
            
            if ($bitsLeft >= 8) {
                $result .= chr(($buffer >> ($bitsLeft - 8)) & 255);
                $bitsLeft -= 8;
            }
        }
        
        return $result;
    }
    
    /**
     * Format secret key for display (with spaces every 4 characters)
     * 
     * @param string $secret The secret key
     * @return string Formatted secret
     */
    public static function formatSecret($secret) {
        return chunk_split($secret, 4, ' ');
    }
}
?>

