<?php
include __DIR__ . '/partials/header.php';
require_once __DIR__ . "/../../Controller/organisationController.php";

$oc = new OrganisationController();
$isEdit = isset($_GET['id']);
$org = $isEdit ? $oc->getOrganisationById((int)$_GET['id']) : null;

$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);

$data = $org ?: $old;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isEdit ? 'Edit' : 'New' ?> Organisation - Admin</title>
    <link rel="icon" type="image/svg+xml" href="<?= $assets ?>favicon.svg">
    <link rel="stylesheet" href="<?= $assets ?>style.css?v=<?php echo filemtime(__DIR__ . "/../assets/style.css"); ?>">
</head>
<body>
    <div class="bg-animation"><span></span><span></span><span></span><span></span><span></span><span></span></div>
    <?php include 'partials/navbar.php'; ?>

    <main class="main">
        <div class="container-sm">
            <div class="hero">
                <h1><?= $isEdit ? 'Edit' : 'New' ?> Organisation</h1>
                <p>Partner directory entry</p>
            </div>

            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger"><div><?php foreach ($errors as $e) echo htmlspecialchars($e) . '<br>'; ?></div></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body" style="padding:2rem">
                    <form action="../../Controller/organisationController.php" method="POST" enctype="multipart/form-data" novalidate>
                        <input type="hidden" name="action" value="<?= $isEdit ? 'update' : 'add' ?>">
                        <input type="hidden" name="source" value="backoffice">
                        <?php if ($isEdit): ?>
                            <input type="hidden" name="id" value="<?= (int)$org['id'] ?>">
                            <input type="hidden" name="existing_logo" value="<?= htmlspecialchars($org['logo_path'] ?? '') ?>">
                        <?php endif; ?>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Name *</label>
                                <input class="form-control" type="text" name="name" value="<?= htmlspecialchars($data['name'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label>Acronym</label>
                                <input class="form-control" type="text" name="acronym" value="<?= htmlspecialchars($data['acronym'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Category</label>
                                <input class="form-control" type="text" name="category" value="<?= htmlspecialchars($data['category'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select class="form-control" name="status">
                                    <?php $st = $data['status'] ?? 'active'; ?>
                                    <option value="active" <?= $st === 'active' ? 'selected' : '' ?>>Active</option>
                                    <option value="inactive" <?= $st === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="description" rows="4"><?= htmlspecialchars($data['description'] ?? '') ?></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Email</label>
                                <input class="form-control" type="text" name="email" value="<?= htmlspecialchars($data['email'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label>Phone</label>
                                <input class="form-control" type="text" name="phone" value="<?= htmlspecialchars($data['phone'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Website</label>
                                <input class="form-control" type="text" name="website" value="<?= htmlspecialchars($data['website'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label>City</label>
                                <input class="form-control" type="text" name="city" value="<?= htmlspecialchars($data['city'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Address</label>
                                <input class="form-control" type="text" name="address" value="<?= htmlspecialchars($data['address'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label>Country</label>
                                <input class="form-control" type="text" name="country" value="<?= htmlspecialchars($data['country'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Logo (image)</label>
                            <input class="form-control" type="file" name="logo" accept="image/*">
                            <?php if ($isEdit && !empty($org['logo_path'])): ?>
                                <div style="margin-top:0.75rem">
                                    <img src="../../<?= htmlspecialchars($org['logo_path']) ?>" alt="Logo" style="max-height:80px;border-radius:12px;border:1px solid var(--border-color)">
                                </div>
                            <?php endif; ?>
                        </div>

                        <div style="display:flex;gap:1rem;margin-top:1.25rem">
                            <button class="btn btn-primary" type="submit" style="flex:1"><?= $isEdit ? 'Save' : 'Create' ?></button>
                            <a class="btn btn-secondary" href="organisations.php">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script src="<?= $assets ?>theme.js"></script>
</body>
</html>



