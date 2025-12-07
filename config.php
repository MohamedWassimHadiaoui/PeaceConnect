<?php
class config
{   private static $pdo = null;
    public static function getConnexion()
    {
        if (!isset(self::$pdo)) {
            $servername="localhost";
            $username="root";
            $password ="";
            $dbname="peace";
            try {
                self::$pdo = new PDO("mysql:host=$servername;dbname=$dbname",
                        $username,
                        $password
                   
                );
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
               
               
            } catch (Exception $e) {
                die('Erreur: ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }
    
    // Email API Configuration (Resend - Free tier available)
    public static function getEmailConfig()
    {
        return [
            'api_key' => getenv('RESEND_API_KEY') ?: 're_FVwpvyFX_PmjtLw1TsgsVAjJDvwPykNwZ',
            // Use Resend's test domain for development (works without verification)
            // For production, verify your domain in Resend dashboard and use your own email
            'from_email' => getenv('FROM_EMAIL') ?: 'onboarding@resend.dev',
            'from_name' => 'Peace',
            'api_url' => 'https://api.resend.com/emails'
        ];
    }
}
config::getConnexion();
?>









