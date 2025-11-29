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
            NULL, :name, :lastname, :email, :password, :cin, :tel, :gender, :role
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
                'role'      => $user->getRole()
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
                    role = :role
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
                'role'      => $user->getRole()
            ]);
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
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
}
?>
