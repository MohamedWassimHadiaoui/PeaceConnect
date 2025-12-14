<?php
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../Model/Organisation.php";

class OrganisationController {
    private $db;

    public function __construct() {
        $this->db = getConnection();
    }

    public function listOrganisations($onlyActive = false) {
        $sql = "SELECT * FROM organisations";
        if ($onlyActive) $sql .= " WHERE status = 'active'";
        $sql .= " ORDER BY created_at DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function searchOrganisations($q = '', $category = '', $city = '', $onlyActive = false) {
        $where = [];
        $params = [];
        if ($q !== '') { $where[] = "(name LIKE :q OR acronym LIKE :q)"; $params[':q'] = '%' . $q . '%'; }
        if ($category !== '') { $where[] = "category = :category"; $params[':category'] = $category; }
        if ($city !== '') { $where[] = "city LIKE :city"; $params[':city'] = '%' . $city . '%'; }
        if ($onlyActive) { $where[] = "status = 'active'"; }

        $sql = "SELECT * FROM organisations";
        if (!empty($where)) $sql .= " WHERE " . implode(" AND ", $where);
        $sql .= " ORDER BY created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getOrganisationById($id) {
        $stmt = $this->db->prepare("SELECT * FROM organisations WHERE id = :id");
        $stmt->execute([':id' => (int)$id]);
        return $stmt->fetch();
    }

    public function addOrganisation($org) {
        $sql = "INSERT INTO organisations (name, acronym, description, category, email, phone, website, address, city, country, logo_path, status)
                VALUES (:name, :acronym, :description, :category, :email, :phone, :website, :address, :city, :country, :logo_path, :status)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':name' => $org->getName(),
            ':acronym' => $org->getAcronym(),
            ':description' => $org->getDescription(),
            ':category' => $org->getCategory(),
            ':email' => $org->getEmail(),
            ':phone' => $org->getPhone(),
            ':website' => $org->getWebsite(),
            ':address' => $org->getAddress(),
            ':city' => $org->getCity(),
            ':country' => $org->getCountry(),
            ':logo_path' => $org->getLogoPath(),
            ':status' => $org->getStatus()
        ]);
        return $this->db->lastInsertId();
    }

    public function updateOrganisation($id, $org) {
        $sql = "UPDATE organisations SET
                    name = :name,
                    acronym = :acronym,
                    description = :description,
                    category = :category,
                    email = :email,
                    phone = :phone,
                    website = :website,
                    address = :address,
                    city = :city,
                    country = :country,
                    logo_path = :logo_path,
                    status = :status
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id' => (int)$id,
            ':name' => $org->getName(),
            ':acronym' => $org->getAcronym(),
            ':description' => $org->getDescription(),
            ':category' => $org->getCategory(),
            ':email' => $org->getEmail(),
            ':phone' => $org->getPhone(),
            ':website' => $org->getWebsite(),
            ':address' => $org->getAddress(),
            ':city' => $org->getCity(),
            ':country' => $org->getCountry(),
            ':logo_path' => $org->getLogoPath(),
            ':status' => $org->getStatus()
        ]);
        return true;
    }

    public function deleteOrganisation($id) {
        $stmt = $this->db->prepare("DELETE FROM organisations WHERE id = :id");
        return $stmt->execute([':id' => (int)$id]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    $controller = new OrganisationController();
    $action = $_POST['action'] ?? '';
    $source = $_POST['source'] ?? '';
    if ($source === '') {
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if (strpos($referer, '/View/backoffice/') !== false) $source = 'backoffice';
        elseif (strpos($referer, '/View/frontoffice/') !== false) $source = 'frontoffice';
    }

    $listPath = ($source === 'backoffice') ? "../View/backoffice/organisations.php" : "../View/frontoffice/organisations.php";
    $formPath = ($source === 'backoffice') ? "../View/backoffice/organisation_form.php" : "../View/frontoffice/organisations.php";

    if ($action === 'add' || $action === 'update') {
        $errors = [];
        $name = trim($_POST['name'] ?? '');
        if ($name === '' || mb_strlen($name) < 2) $errors[] = "Name is required (min 2 chars)";

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            $redir = $formPath;
            if ($action === 'update' && !empty($_POST['id']) && $source === 'backoffice') $redir .= "?id=" . urlencode($_POST['id']);
            header("Location: " . $redir);
            exit;
        }

        $logoPath = $_POST['existing_logo'] ?? null;
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $max = 5 * 1024 * 1024;
            if ($_FILES['logo']['size'] <= $max) {
                $tmp = $_FILES['logo']['tmp_name'];
                $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg','jpeg','png','gif'])) {
                    $info = @getimagesize($tmp);
                    if ($info !== false) {
                        $dir = __DIR__ . '/../uploads/org_logos';
                        if (!is_dir($dir)) @mkdir($dir, 0755, true);
                        $newName = 'org_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
                        $dest = $dir . '/' . $newName;
                        if (move_uploaded_file($tmp, $dest)) {
                            $logoPath = 'uploads/org_logos/' . $newName;
                        }
                    }
                }
            }
        }

        $org = new Organisation(
            null,
            htmlspecialchars($name),
            htmlspecialchars(trim($_POST['acronym'] ?? '')),
            htmlspecialchars(trim($_POST['description'] ?? '')),
            htmlspecialchars(trim($_POST['category'] ?? '')),
            htmlspecialchars(trim($_POST['email'] ?? '')),
            htmlspecialchars(trim($_POST['phone'] ?? '')),
            htmlspecialchars(trim($_POST['website'] ?? '')),
            htmlspecialchars(trim($_POST['address'] ?? '')),
            htmlspecialchars(trim($_POST['city'] ?? '')),
            htmlspecialchars(trim($_POST['country'] ?? '')),
            $logoPath,
            ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : ($_POST['status'] ?? 'active')
        );

        if ($action === 'add') {
            $controller->addOrganisation($org);
            $_SESSION['success'] = "Organisation added.";
            header("Location: " . $listPath);
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $controller->updateOrganisation($id, $org);
            $_SESSION['success'] = "Organisation updated.";
        }
        header("Location: " . $listPath);
        exit;
    }

    if ($action === 'delete') {
        if (!empty($_POST['id'])) $controller->deleteOrganisation((int)$_POST['id']);
        $_SESSION['success'] = "Organisation deleted.";
        header("Location: " . $listPath);
        exit;
    }
}


