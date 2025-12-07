<?php
require_once __DIR__ . '/../config.php';
include(__DIR__ . '/../model/User.php');
class UserController {

    public function listUsers() {
        $sql = "SELECT * FROM user ORDER BY id_user DESC";
        $db = config::getConnexion();
        try {
            $list = $db->query($sql);
            return $list;
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function deleteUser($id) {
        $sql = "DELETE FROM user WHERE id_user = :id";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->bindValue(':id', $id);

        try {
            $req->execute();
        } catch (Exception $e) {
            die('Error:' . $e->getMessage());
        }
    }

    public function addUser(User $user) {
        $sql = "INSERT INTO user VALUES (
            NULL, :name, :lastname, :email, :password, :cin, :tel, :gender, :role, :avatar
        )";

        $db = config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute([
                'name'      => $user->getName(),
                'lastname'  => $user->getLastname(),
                'email'     => $user->getEmail(),
                'password'  => $user->getPassword(),
                'cin'       => $user->getCin(),
                'tel'       => $user->getTel(),
                'gender'    => $user->getGender(),
                'role'      => $user->getRole(),
                'avatar'    => $user->getAvatar()
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function updateUser(User $user, $id) {
        $sql = "UPDATE user SET
                    name = :name,
                    lastname = :lastname,
                    email = :email,
                    password = :password,
                    cin = :cin,
                    tel = :tel,
                    gender = :gender,
                    role = :role,
                    avatar = :avatar
                WHERE id_user = :id";

        $db = config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id'        => $id,
                'name'      => $user->getName(),
                'lastname'  => $user->getLastname(),
                'email'     => $user->getEmail(),
                'password'  => $user->getPassword(),
                'cin'       => $user->getCin(),
                'tel'       => $user->getTel(),
                'gender'    => $user->getGender(),
                'role'      => $user->getRole(),
                'avatar'    => $user->getAvatar()
            ]);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    
    /**
     * Update user avatar
     * 
     * @param int $userId User ID
     * @param string $avatarFilename Avatar filename
     * @return bool True if successful
     */
    public function updateAvatar($userId, $avatarFilename) {
        $sql = "UPDATE user SET avatar = :avatar WHERE id_user = :id";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        
        try {
            $query->execute([
                ':avatar' => $avatarFilename,
                ':id' => $userId
            ]);
            return true;
        } catch (Exception $e) {
            error_log('Error updating avatar: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete user avatar
     * 
     * @param int $userId User ID
     * @return bool True if successful
     */
    public function deleteAvatar($userId) {
        $sql = "UPDATE user SET avatar = NULL WHERE id_user = :id";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        
        try {
            $query->execute([':id' => $userId]);
            return true;
        } catch (Exception $e) {
            error_log('Error deleting avatar: ' . $e->getMessage());
            return false;
        }
    }
    public function showUser($id) {
        $sql = "SELECT * FROM user WHERE id_user = $id";
        $db = config::getConnexion();
        $query = $db->prepare($sql);

        try {
            $query->execute();
            $user = $query->fetch();
            return $user;
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

   
    public function getUserById($id) {
        $sql = "SELECT * FROM user WHERE id_user = :id";
        $db = config::getConnexion();
        $query = $db->prepare($sql);

        try {
            $query->execute([":id" => $id]);
            $user = $query->fetch();
            return $user;
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }
    public function getUserByEmail($email) {
        $sql = "SELECT * FROM user WHERE email = :email";
        $db = config::getConnexion();
        $query = $db->prepare($sql);

        try {
            $query->execute([":email" => $email]);
            $user = $query->fetch();
            return $user;
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Enable 2FA for a user
     * 
     * @param int $userId User ID
     * @param string $secret 2FA secret key
     * @return bool True if successful
     */
    public function enable2FA($userId, $secret) {
        // Clean the secret - remove spaces and convert to uppercase
        $secret = strtoupper(preg_replace('/\s+/', '', $secret));
        
        $sql = "UPDATE user SET 
                    two_factor_secret = :secret,
                    two_factor_enabled = 1
                WHERE id_user = :id";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        
        try {
            $query->execute([
                ':secret' => $secret,
                ':id' => $userId
            ]);
            return true;
        } catch (Exception $e) {
            error_log('Error enabling 2FA: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Disable 2FA for a user
     * 
     * @param int $userId User ID
     * @return bool True if successful
     */
    public function disable2FA($userId) {
        $sql = "UPDATE user SET 
                    two_factor_secret = NULL,
                    two_factor_enabled = 0
                WHERE id_user = :id";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        
        try {
            $query->execute([':id' => $userId]);
            return true;
        } catch (Exception $e) {
            error_log('Error disabling 2FA: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if user has 2FA enabled
     * 
     * @param int $userId User ID
     * @return bool True if 2FA is enabled
     */
    public function is2FAEnabled($userId) {
        $sql = "SELECT two_factor_enabled FROM user WHERE id_user = :id";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        
        try {
            $query->execute([':id' => $userId]);
            $result = $query->fetch();
            return isset($result['two_factor_enabled']) && $result['two_factor_enabled'] == 1;
        } catch (Exception $e) {
            error_log('Error checking 2FA status: ' . $e->getMessage());
            return false;
        }
    }
}
?>
