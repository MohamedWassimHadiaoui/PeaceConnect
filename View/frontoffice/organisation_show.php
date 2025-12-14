<?php
include __DIR__ . '/partials/header.php';
require_once __DIR__ . "/../../Controller/organisationController.php";

$oc = new OrganisationController();
$id = (int)($_GET['id'] ?? 0);
$org = $id ? $oc->getOrganisationById($id) : null;
if (!$org || ($org['status'] ?? 'active') !== 'active') {
    header("Location: " . $frontoffice . "organisations.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($org['name'] ?? 'Organisation') ?> - PeaceConnect</title>
    <link rel="icon" type="image/svg+xml" href="<?= $assets ?>favicon.svg">
    <link rel="stylesheet" href="<?= $assets ?>style.css?v=<?php echo filemtime(__DIR__ . "/../assets/style.css"); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        .header { display:flex; gap:1.25rem; align-items:center; margin: 2rem 0; }
        .logo { width:84px; height:84px; border-radius:50%; overflow:hidden; background:linear-gradient(135deg,var(--primary),var(--secondary)); display:flex; align-items:center; justify-content:center; color:#fff; font-size:1.5rem; font-weight:800; }
        .logo img { width:100%; height:100%; object-fit:cover; }
        .meta { color:var(--text-muted); margin-top:0.25rem; }
        .info-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(240px,1fr)); gap:1rem; margin-top:1.5rem; }
        .info { background:var(--bg-card); border:1px solid var(--border-color); border-radius:16px; padding:1.25rem; }
        .info .k { color:var(--text-muted); font-size:0.85rem; }
        .info .v { margin-top:0.25rem; font-weight:600; word-break:break-word; }
    </style>
</head>
<body>
    <div class="bg-animation"><span></span><span></span><span></span><span></span><span></span><span></span></div>
    <?php include 'partials/navbar.php'; ?>

    <main class="main">
        <div class="container">
            <a class="btn btn-secondary" href="organisations.php">Back</a>

            <div class="header">
                <div class="logo">
                    <?php if (!empty($org['logo_path'])): ?>
                    <img src="../../<?= htmlspecialchars($org['logo_path']) ?>" alt="Logo">
                    <?php else: ?>
                    <?= htmlspecialchars(mb_strtoupper(mb_substr($org['name'] ?? 'ORG', 0, 2))) ?>
                    <?php endif; ?>
                </div>
                <div>
                    <h1 style="margin:0"><?= htmlspecialchars($org['name'] ?? '') ?></h1>
                    <div class="meta">
                        <?= htmlspecialchars($org['category'] ?? 'General') ?>
                        <?php if (!empty($org['acronym'])): ?> &middot; <?= htmlspecialchars($org['acronym']) ?><?php endif; ?>
                        <?php if (!empty($org['city'])): ?> &middot; <?= htmlspecialchars($org['city']) ?><?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">About</h2>
                    <p style="color:var(--text-muted);line-height:1.7"><?= nl2br(htmlspecialchars($org['description'] ?? '')) ?></p>
                </div>
            </div>

            <div class="info-grid">
                <?php if (!empty($org['email'])): ?><div class="info"><div class="k">Email</div><div class="v"><?= htmlspecialchars($org['email']) ?></div></div><?php endif; ?>
                <?php if (!empty($org['phone'])): ?><div class="info"><div class="k">Phone</div><div class="v"><?= htmlspecialchars($org['phone']) ?></div></div><?php endif; ?>
                <?php if (!empty($org['website'])): ?><div class="info"><div class="k">Website</div><div class="v"><a href="<?= htmlspecialchars($org['website']) ?>" target="_blank" style="color:var(--primary)"><?= htmlspecialchars($org['website']) ?></a></div></div><?php endif; ?>
                <?php if (!empty($org['address']) || !empty($org['country'])): ?>
                <div class="info"><div class="k">Address</div><div class="v"><?= htmlspecialchars(trim(($org['address'] ?? '') . ' ' . ($org['country'] ?? ''))) ?></div></div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include '../partials/chatbot_widget.php'; ?>
    <script src="<?= $assets ?>theme.js"></script>
</body>
</html>



