<?php
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../Model/User.php";
require_once __DIR__ . "/../lib/TwoFactorAuth.php";
require_once __DIR__ . "/../lib/PHPMailer/src/Exception.php";
require_once __DIR__ . "/../lib/PHPMailer/src/PHPMailer.php";
require_once __DIR__ . "/../lib/PHPMailer/src/SMTP.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailerException;

class UserController {
    private $db;

    public function __construct() {
        $this->db = getConnection();
    }

    public function addUser($user) {
        $sql = "INSERT INTO users (name, lastname, email, password, cin, tel, gender, role, avatar) 
                VALUES (:name, :lastname, :email, :password, :cin, :tel, :gender, :role, :avatar)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':name' => $user->getName(),
            ':lastname' => $user->getLastname(),
            ':email' => $user->getEmail(),
            ':password' => password_hash($user->getPassword(), PASSWORD_DEFAULT),
            ':cin' => $user->getCin(),
            ':tel' => $user->getTel(),
            ':gender' => $user->getGender(),
            ':role' => $user->getRole(),
            ':avatar' => $user->getAvatar()
        ]);
        return $this->db->lastInsertId();
    }

    public function listUsers() {
        $sql = "SELECT * FROM users ORDER BY id DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function searchUsers($search = '', $role = '') {
        $sql = "SELECT * FROM users WHERE 1=1";
        $params = [];
        
        if (!empty($search)) {
            $sql .= " AND (name LIKE :search OR lastname LIKE :search2 OR email LIKE :search3)";
            $params[':search'] = "%$search%";
            $params[':search2'] = "%$search%";
            $params[':search3'] = "%$search%";
        }
        if (!empty($role)) {
            $sql .= " AND role = :role";
            $params[':role'] = $role;
        }
        $sql .= " ORDER BY id DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getUserById($id) {
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function getUserByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    public function updateUser($user) {
        $sql = "UPDATE users SET name = :name, lastname = :lastname, 
                cin = :cin, tel = :tel, gender = :gender, avatar = :avatar 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id' => $user->getId(),
            ':name' => $user->getName(),
            ':lastname' => $user->getLastname(),
            ':cin' => $user->getCin(),
            ':tel' => $user->getTel(),
            ':gender' => $user->getGender(),
            ':avatar' => $user->getAvatar()
        ]);
    }

    public function updateUserAdmin($id, $data) {
        $sql = "UPDATE users SET name = :name, lastname = :lastname, email = :email,
                cin = :cin, tel = :tel, gender = :gender, role = :role, avatar = :avatar 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':name' => $data['name'],
            ':lastname' => $data['lastname'],
            ':email' => $data['email'],
            ':cin' => $data['cin'],
            ':tel' => $data['tel'],
            ':gender' => $data['gender'],
            ':role' => $data['role'],
            ':avatar' => $data['avatar']
        ]);
    }

    public function updatePassword($id, $password) {
        $sql = "UPDATE users SET password = :password WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':password' => password_hash($password, PASSWORD_DEFAULT)
        ]);
    }

    public function deleteUser($id) {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
    }

    public function login($email, $password) {
        $user = $this->getUserByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    public function countAll() {
        $sql = "SELECT COUNT(*) as total FROM users";
        $stmt = $this->db->query($sql);
        return $stmt->fetch()['total'];
    }

    public function countByRole($role) {
        $sql = "SELECT COUNT(*) as total FROM users WHERE role = :role";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':role' => $role]);
        return $stmt->fetch()['total'];
    }

    // Password Reset Functions
    public function generateResetCode($email) {
        $user = $this->getUserByEmail($email);
        if (!$user) return false;
        
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $expires = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        
        $sql = "UPDATE users SET reset_code = :code, reset_code_expires = :expires WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':code' => $code, ':expires' => $expires, ':id' => $user['id']]);
        
        return $code;
    }

    public function sendResetCodeEmail($toEmail, $code) {
        $cfgPath = __DIR__ . "/../mail_config.php";
        if (!file_exists($cfgPath)) {
            throw new \Exception("Missing mail_config.php");
        }

        $cfg = require $cfgPath;
        $host = trim($cfg['host'] ?? '');
        $port = intval($cfg['port'] ?? 0);
        $username = trim($cfg['username'] ?? '');
        $password = strval($cfg['password'] ?? '');
        $secure = strtolower(trim($cfg['secure'] ?? 'tls'));
        $fromEmail = trim($cfg['from_email'] ?? '');
        $fromName = strval($cfg['from_name'] ?? 'PeaceConnect');

        if ($host === '' || $port <= 0 || $username === '' || $password === '' || $fromEmail === '') {
            throw new \Exception("SMTP not configured in mail_config.php");
        }

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $host;
        $mail->SMTPAuth = true;
        $mail->Username = $username;
        $mail->Password = $password;
        $mail->Port = $port;
        $mail->CharSet = 'UTF-8';
        $mail->Timeout = 30;
        
        // SSL options for shared hosting compatibility
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        if ($secure === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } else {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }

        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($toEmail);
        $mail->Subject = 'PeaceConnect - Password Reset Code';
        $mail->isHTML(true);

        $safeCode = htmlspecialchars($code, ENT_QUOTES, 'UTF-8');
        $mail->Body = "
        <div style='font-family:Arial,sans-serif;max-width:500px;margin:0 auto;padding:20px'>
            <div style='text-align:center;margin-bottom:20px'>
                <h1 style='color:#6366f1;margin:0'>🕊️ PeaceConnect</h1>
            </div>
            <div style='background:#f8fafc;border-radius:12px;padding:24px;text-align:center'>
                <h2 style='margin:0 0 16px;color:#1e293b'>Password Reset Code</h2>
                <p style='color:#64748b;margin:0 0 20px'>Use this code to reset your password:</p>
                <div style='background:#6366f1;color:white;font-size:32px;font-weight:bold;letter-spacing:8px;padding:16px 24px;border-radius:8px;display:inline-block'>{$safeCode}</div>
                <p style='color:#64748b;margin:20px 0 0;font-size:14px'>This code expires in <strong>15 minutes</strong>.</p>
            </div>
            <p style='color:#94a3b8;font-size:12px;text-align:center;margin-top:20px'>If you didn't request this, you can safely ignore this email.</p>
        </div>";
        $mail->AltBody = "Your PeaceConnect password reset code is: {$code}\nThis code expires in 15 minutes.\nIf you did not request this, ignore this email.";

        $mail->send();
        return true;
    }

    public function verifyResetCode($email, $code) {
        $sql = "SELECT * FROM users WHERE email = :email AND reset_code = :code AND reset_code_expires > NOW()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email, ':code' => $code]);
        return $stmt->fetch();
    }

    public function resetPassword($email, $code, $newPassword) {
        $user = $this->verifyResetCode($email, $code);
        if (!$user) return false;
        
        $sql = "UPDATE users SET password = :password, reset_code = NULL, reset_code_expires = NULL WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':password' => password_hash($newPassword, PASSWORD_DEFAULT),
            ':id' => $user['id']
        ]);
        return true;
    }

    // 2FA Functions (TOTP)
    public function generate2FASecret($userId) {
        $secret = TwoFactorAuth::generateSecret();
        $sql = "UPDATE users SET two_factor_secret = :secret WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':secret' => $secret, ':id' => $userId]);
        return $secret;
    }

    public function enable2FA($userId, $secret = null) {
        if ($secret) {
            $sql = "UPDATE users SET two_factor_enabled = 1, two_factor_secret = :secret WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':secret' => $secret, ':id' => $userId]);
            return true;
        }
        $sql = "UPDATE users SET two_factor_enabled = 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $userId]);
        return true;
    }

    public function disable2FA($userId) {
        $sql = "UPDATE users SET two_factor_enabled = 0, two_factor_secret = NULL WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $userId]);
    }

    public function verify2FACode($userId, $code) {
        $user = $this->getUserById($userId);
        if (!$user || !$user['two_factor_secret']) return false;
        return TwoFactorAuth::verifyCode($user['two_factor_secret'], $code);
    }

    public function get2FACode($userId) {
        $user = $this->getUserById($userId);
        if (!$user || !$user['two_factor_secret']) return null;
        return TwoFactorAuth::currentCode($user['two_factor_secret']);
    }

    // Password Complexity Check
    public static function isPasswordComplex($password) {
        if (strlen($password) < 8) return false;
        if (!preg_match('/[A-Z]/', $password)) return false;
        if (!preg_match('/[a-z]/', $password)) return false;
        if (!preg_match('/[0-9]/', $password)) return false;
        return true;
    }

    // Simple Math Captcha
    public static function generateCaptcha() {
        $num1 = rand(1, 10);
        $num2 = rand(1, 10);
        $operators = ['+', '-'];
        $op = $operators[array_rand($operators)];
        
        if ($op === '+') {
            $answer = $num1 + $num2;
        } else {
            if ($num1 < $num2) { $tmp = $num1; $num1 = $num2; $num2 = $tmp; }
            $answer = $num1 - $num2;
        }
        
        return ['question' => "$num1 $op $num2 = ?", 'answer' => $answer];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['action'])) {
    session_start();
    $controller = new UserController();
    $action = $_POST['action'] ?? $_GET['action'] ?? '';

    if ($action === 'register') {
        $errors = [];
        
        // Captcha verification
        if (!isset($_POST['captcha']) || $_POST['captcha'] != ($_SESSION['captcha_answer'] ?? '')) {
            $errors[] = "Captcha answer is incorrect";
        }
        
        if (empty($_POST['name']) || strlen($_POST['name']) < 2) $errors[] = "Name must be at least 2 characters";
        if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email";
        
        // Password complexity check
        $password = $_POST['password'] ?? '';
        if (!UserController::isPasswordComplex($password)) {
            $errors[] = "Password must be at least 8 characters with uppercase, lowercase, and number";
        }
        if ($password !== ($_POST['password_confirm'] ?? '')) $errors[] = "Passwords do not match";
        if ($controller->getUserByEmail($_POST['email'])) $errors[] = "Email already in use";

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header("Location: ../View/frontoffice/register.php");
            exit;
        }

        $user = new User(null, $_POST['name'], $_POST['lastname'] ?? '', $_POST['email'], $_POST['password'], 
                         null, null, null, 'client');
        $controller->addUser($user);
        unset($_SESSION['captcha_answer']);
        $_SESSION['success'] = "Registration successful! Please login.";
        header("Location: ../View/frontoffice/login.php");
        exit;
    }

    if ($action === 'login') {
        $errors = [];
        if (empty($_POST['email'])) $errors[] = "Email is required";
        if (empty($_POST['password'])) $errors[] = "Password is required";

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = ['email' => $_POST['email'] ?? ''];
            header("Location: ../View/frontoffice/login.php");
            exit;
        }

        $user = $controller->login($_POST['email'], $_POST['password']);
        if ($user) {
            // Check if 2FA is enabled
            if ($user['two_factor_enabled']) {
                $_SESSION['2fa_user_id'] = $user['id'];
                $_SESSION['2fa_pending'] = true;
                header("Location: ../View/frontoffice/verify_2fa.php");
                exit;
            }
            
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_regenerate_id(true);
            }
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'] === 'admin' ? 'admin' : 'client';
            $_SESSION['user_avatar'] = $user['avatar'];
            
            if ($user['role'] === 'admin') {
                header("Location: ../View/backoffice/index.php");
            } else {
                header("Location: ../View/frontoffice/index.php");
            }
        } else {
            $_SESSION['errors'] = ["Incorrect email or password"];
            $_SESSION['old'] = ['email' => $_POST['email'] ?? ''];
            header("Location: ../View/frontoffice/login.php");
        }
        exit;
    }

    if ($action === 'verify_2fa') {
        $userId = $_SESSION['2fa_user_id'] ?? null;
        $code = $_POST['code'] ?? '';
        
        if (!$userId) {
            header("Location: ../View/frontoffice/login.php");
            exit;
        }
        
        if ($controller->verify2FACode($userId, $code)) {
            $user = $controller->getUserById($userId);
            unset($_SESSION['2fa_user_id'], $_SESSION['2fa_pending']);
            
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'] === 'admin' ? 'admin' : 'client';
            $_SESSION['user_avatar'] = $user['avatar'];
            
            if ($user['role'] === 'admin') {
                header("Location: ../View/backoffice/index.php");
            } else {
                header("Location: ../View/frontoffice/index.php");
            }
        } else {
            $_SESSION['errors'] = ["Invalid 2FA code"];
            header("Location: ../View/frontoffice/verify_2fa.php");
        }
        exit;
    }

    if ($action === 'enable_2fa') {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) { header("Location: ../View/frontoffice/login.php"); exit; }
        
        $controller->enable2FA($userId);
        $_SESSION['success'] = "2FA enabled successfully!";
        header("Location: ../View/frontoffice/profile.php");
        exit;
    }

    if ($action === 'disable_2fa') {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) { header("Location: ../View/frontoffice/login.php"); exit; }
        
        $controller->disable2FA($userId);
        $_SESSION['success'] = "2FA disabled successfully!";
        header("Location: ../View/frontoffice/profile.php");
        exit;
    }

    if ($action === 'forgot_password') {
        $email = trim($_POST['email'] ?? '');
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['errors'] = ["Please enter a valid email"];
            header("Location: ../View/frontoffice/forgot_password.php");
            exit;
        }
        
        $code = $controller->generateResetCode($email);
        $_SESSION['reset_email'] = $email;

        if ($code) {
            try {
                $controller->sendResetCodeEmail($email, $code);
                $_SESSION['success'] = "A reset code has been sent to your email. Check your inbox/spam. The code expires in 15 minutes.";
            } catch (\Throwable $e) {
                error_log("Reset email send failed: " . $e->getMessage());
                $msg = $e->getMessage();
                if (stripos($msg, 'mail_config.php') !== false || stripos($msg, 'SMTP not configured') !== false) {
                    $_SESSION['errors'] = ["Email sending is not configured. Please fill SMTP settings in mail_config.php."];
                } else {
                    $_SESSION['errors'] = ["Unable to send reset email right now. Please try again later."];
                }
                header("Location: ../View/frontoffice/forgot_password.php");
                exit;
            }
        } else {
            $_SESSION['success'] = "If this email exists, a reset code has been sent.";
        }

        header("Location: ../View/frontoffice/reset_password.php");
        exit;
    }

    if ($action === 'reset_password') {
        $email = $_SESSION['reset_email'] ?? $_POST['email'] ?? '';
        $code = $_POST['code'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $errors = [];
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email is missing. Please restart the reset process.";
        if (empty($code)) $errors[] = "Reset code is required";
        if (!UserController::isPasswordComplex($password)) {
            $errors[] = "Password must be at least 8 characters with uppercase, lowercase, and number";
        }
        if ($password !== ($_POST['password_confirm'] ?? '')) $errors[] = "Passwords do not match";
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header("Location: ../View/frontoffice/reset_password.php");
            exit;
        }
        
        if ($controller->resetPassword($email, $code, $password)) {
            unset($_SESSION['reset_email']);
            $_SESSION['success'] = "Password reset successfully! Please login.";
            header("Location: ../View/frontoffice/login.php");
        } else {
            $_SESSION['errors'] = ["Invalid or expired reset code"];
            header("Location: ../View/frontoffice/reset_password.php");
        }
        exit;
    }

    if ($action === 'logout') {
        session_destroy();
        header("Location: ../index.php");
        exit;
    }

    if ($action === 'update_profile') {
        $id = $_POST['id'] ?? $_SESSION['user_id'] ?? null;
        if (!$id) { header("Location: ../View/frontoffice/profile.php"); exit; }

        $errors = [];
        if (empty($_POST['name']) || strlen($_POST['name']) < 2) $errors[] = "Name must be at least 2 characters";

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header("Location: ../View/frontoffice/profile.php");
            exit;
        }

        $existing = $controller->getUserById($id);
        $avatarPath = $existing['avatar'] ?? null;
        
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $maxFileSize = 5 * 1024 * 1024;
            if ($_FILES['avatar']['size'] > $maxFileSize) {
                $_SESSION['errors'] = ["Avatar file too large. Maximum 5MB allowed."];
                header("Location: ../View/frontoffice/profile.php");
                exit;
            }
            
            $imageInfo = getimagesize($_FILES['avatar']['tmp_name']);
            if ($imageInfo === false) {
                $_SESSION['errors'] = ["File is not a valid image."];
                header("Location: ../View/frontoffice/profile.php");
                exit;
            }
            
            $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg','jpeg','png','gif'])) {
                if (!is_dir(__DIR__ . '/../uploads/avatars')) mkdir(__DIR__ . '/../uploads/avatars', 0755, true);
                $newName = 'avatar_' . $id . '_' . time() . '.' . $ext;
                $oldAvatar = $existing['avatar'] ?? null;
                
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], __DIR__ . '/../uploads/avatars/' . $newName)) {
                    $avatarPath = 'uploads/avatars/' . $newName;
                    if ($oldAvatar && file_exists(__DIR__ . '/../' . $oldAvatar)) {
                        @unlink(__DIR__ . '/../' . $oldAvatar);
                    }
                }
            }
        }

        $user = new User($id, $_POST['name'], $_POST['lastname'] ?? '', $existing['email'], null,
                         $_POST['cin'] ?? null, $_POST['tel'] ?? null, $_POST['gender'] ?? null, 
                         $existing['role'], $avatarPath);
        $controller->updateUser($user);
        $_SESSION['user_name'] = $_POST['name'];
        $_SESSION['user_avatar'] = $avatarPath;
        $_SESSION['success'] = "Profile updated successfully!";
        header("Location: ../View/frontoffice/profile.php");
        exit;
    }

    if ($action === 'update') {
        $id = $_POST['id'] ?? null;
        if (!$id) { header("Location: ../View/backoffice/users.php"); exit; }

        $errors = [];
        if (empty($_POST['name']) || strlen($_POST['name']) < 2) $errors[] = "Name must be at least 2 characters";
        if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email";

        $existingEmail = $controller->getUserByEmail($_POST['email']);
        if ($existingEmail && $existingEmail['id'] != $id) {
            $errors[] = "Email already in use by another user";
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header("Location: ../View/backoffice/user_form.php?id=" . $id);
            exit;
        }

        $existing = $controller->getUserById($id);
        $avatarPath = $_POST['existing_avatar'] ?? $existing['avatar'] ?? null;
        
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $maxFileSize = 5 * 1024 * 1024;
            if ($_FILES['avatar']['size'] > $maxFileSize) {
                $_SESSION['errors'] = ["Avatar file too large. Maximum 5MB allowed."];
                $_SESSION['old'] = $_POST;
                header("Location: ../View/backoffice/user_form.php?id=" . $id);
                exit;
            }
            
            $imageInfo = getimagesize($_FILES['avatar']['tmp_name']);
            if ($imageInfo === false) {
                $_SESSION['errors'] = ["File is not a valid image."];
                $_SESSION['old'] = $_POST;
                header("Location: ../View/backoffice/user_form.php?id=" . $id);
                exit;
            }
            
            $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg','jpeg','png','gif'])) {
                if (!is_dir(__DIR__ . '/../uploads/avatars')) mkdir(__DIR__ . '/../uploads/avatars', 0755, true);
                $newName = 'avatar_' . $id . '_' . time() . '.' . $ext;
                $oldAvatar = $existing['avatar'] ?? null;
                
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], __DIR__ . '/../uploads/avatars/' . $newName)) {
                    $avatarPath = 'uploads/avatars/' . $newName;
                    if ($oldAvatar && file_exists(__DIR__ . '/../' . $oldAvatar)) {
                        @unlink(__DIR__ . '/../' . $oldAvatar);
                    }
                }
            }
        }

        $controller->updateUserAdmin($id, [
            'name' => $_POST['name'],
            'lastname' => $_POST['lastname'] ?? '',
            'email' => $_POST['email'],
            'cin' => $_POST['cin'] ?? null,
            'tel' => $_POST['tel'] ?? null,
            'gender' => $_POST['gender'] ?? null,
            'role' => $_POST['role'] ?? 'client',
            'avatar' => $avatarPath
        ]);

        $_SESSION['success'] = "User updated successfully!";
        header("Location: ../View/backoffice/users.php");
        exit;
    }

    if ($action === 'add') {
        $errors = [];
        if (empty($_POST['name']) || strlen($_POST['name']) < 2) $errors[] = "Name must be at least 2 characters";
        if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email";
        
        $password = $_POST['password'] ?? '';
        if (!UserController::isPasswordComplex($password)) {
            $errors[] = "Password must be at least 8 characters with uppercase, lowercase, and number";
        }
        if ($password !== ($_POST['password_confirm'] ?? '')) $errors[] = "Passwords do not match";
        if ($controller->getUserByEmail($_POST['email'])) $errors[] = "Email already in use";

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header("Location: ../View/backoffice/user_form.php");
            exit;
        }

        $avatarPath = null;
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $maxFileSize = 5 * 1024 * 1024;
            if ($_FILES['avatar']['size'] > $maxFileSize) {
                $_SESSION['errors'] = ["Avatar file too large. Maximum 5MB allowed."];
                $_SESSION['old'] = $_POST;
                header("Location: ../View/backoffice/user_form.php");
                exit;
            }
            
            $imageInfo = getimagesize($_FILES['avatar']['tmp_name']);
            if ($imageInfo === false) {
                $_SESSION['errors'] = ["File is not a valid image."];
                $_SESSION['old'] = $_POST;
                header("Location: ../View/backoffice/user_form.php");
                exit;
            }
            
            $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg','jpeg','png','gif'])) {
                if (!is_dir(__DIR__ . '/../uploads/avatars')) mkdir(__DIR__ . '/../uploads/avatars', 0755, true);
                $newName = 'avatar_' . time() . '.' . $ext;
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], __DIR__ . '/../uploads/avatars/' . $newName)) {
                    $avatarPath = 'uploads/avatars/' . $newName;
                }
            }
        }

        $user = new User(null, $_POST['name'], $_POST['lastname'] ?? '', $_POST['email'], $_POST['password'], 
                         $_POST['cin'] ?? null, $_POST['tel'] ?? null, $_POST['gender'] ?? null, 
                         $_POST['role'] ?? 'client', $avatarPath);
        $controller->addUser($user);
        $_SESSION['success'] = "User created successfully!";
        header("Location: ../View/backoffice/users.php");
        exit;
    }

    if ($action === 'change_password') {
        $id = $_POST['id'] ?? $_SESSION['user_id'] ?? null;
        if (!$id) { header("Location: ../View/frontoffice/profile.php"); exit; }

        $errors = [];
        $user = $controller->getUserById($id);
        
        if (!password_verify($_POST['current_password'] ?? '', $user['password'])) {
            $errors[] = "Current password is incorrect";
        }
        
        $newPassword = $_POST['new_password'] ?? '';
        if (!UserController::isPasswordComplex($newPassword)) {
            $errors[] = "Password must be at least 8 characters with uppercase, lowercase, and number";
        }
        if ($newPassword !== ($_POST['confirm_password'] ?? '')) {
            $errors[] = "New passwords do not match";
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header("Location: ../View/frontoffice/profile.php");
            exit;
        }

        $controller->updatePassword($id, $newPassword);
        $_SESSION['success'] = "Password changed successfully!";
        header("Location: ../View/frontoffice/profile.php");
        exit;
    }

    if ($action === 'delete') {
        if (!empty($_POST['id'])) $controller->deleteUser($_POST['id']);
        $_SESSION['success'] = "User deleted successfully!";
        header("Location: ../View/backoffice/users.php");
        exit;
    }
}
?>
