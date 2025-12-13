<?php
include __DIR__ . '/partials/header.php';
require_once __DIR__ . "/../../Controller/mediatorController.php";
$mc = new MediatorController();

$mediator = null;
$isEdit = false;
if (isset($_GET['id'])) {
    $mediator = $mc->getMediatorById($_GET['id']);
    $isEdit = true;
}

$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isEdit ? 'Edit' : 'Add' ?> Mediator - PeaceConnect Admin</title>
    <link rel="icon" type="image/svg+xml" href="<?= $assets ?>favicon.svg">
    <link rel="stylesheet" href="<?= $assets ?>style.css?v=<?php echo filemtime(__DIR__ . "/../assets/style.css"); ?>">
</head>
<body>
    <div class="bg-animation"><span></span><span></span><span></span><span></span><span></span><span></span></div>
    <?php include 'partials/navbar.php'; ?>

    <main class="main">
        <div class="container-sm">
            <div class="hero">
                <h1><?= $isEdit ? 'Edit Mediator' : 'Add Mediator' ?></h1>
            </div>

            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger"><div><?php foreach($errors as $e) echo htmlspecialchars($e).'<br>'; ?></div></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body" style="padding:2rem">
                    <form id="mediatorForm" action="../../Controller/mediatorController.php" method="POST" novalidate>
                        <input type="hidden" name="action" value="<?= $isEdit ? 'update' : 'add' ?>">
                        <input type="hidden" name="source" value="backoffice">
                        <?php if ($isEdit): ?><input type="hidden" name="id" value="<?= $mediator['id'] ?>"><?php endif; ?>

                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Mediator name" value="<?= htmlspecialchars($mediator['name'] ?? $old['name'] ?? '') ?>">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="text" name="email" class="form-control" placeholder="email@example.com" value="<?= htmlspecialchars($mediator['email'] ?? $old['email'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text" name="phone" class="form-control" placeholder="+216 XX XXX XXX" value="<?= htmlspecialchars($mediator['phone'] ?? $old['phone'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Expertise</label>
                                <input type="text" name="expertise" class="form-control" placeholder="e.g., Family conflicts, Workplace" value="<?= htmlspecialchars($mediator['expertise'] ?? $old['expertise'] ?? '') ?>">
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select name="availability" class="form-control">
                                    <option value="available" <?= (($mediator['availability'] ?? $old['availability'] ?? '') === 'available') ? 'selected' : '' ?>>Available</option>
                                    <option value="busy" <?= (($mediator['availability'] ?? $old['availability'] ?? '') === 'busy') ? 'selected' : '' ?>>Busy</option>
                                    <option value="unavailable" <?= (($mediator['availability'] ?? $old['availability'] ?? '') === 'unavailable') ? 'selected' : '' ?>>Unavailable</option>
                                </select>
                            </div>
                        </div>

                        <div style="display:flex;gap:1rem;margin-top:1.5rem">
                            <button type="submit" class="btn btn-success" style="flex:1">Save</button>
                            <a href="mediators.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script src="<?= $assets ?>theme.js"></script>
    <script src="../assets/validation.js"></script>
    <script>
        Validator.init('mediatorForm', {
            'name': [
                { type: 'required', message: 'Name is required' },
                { type: 'minLength', min: 3, message: 'Name must be at least 3 characters' }
            ],
            'email': [
                { type: 'required', message: 'Email is required' },
                { type: 'email', message: 'Please enter a valid email' }
            ],
            'expertise': [
                { type: 'required', message: 'Expertise is required' }
            ]
        });
    </script>
</body>
</html>


