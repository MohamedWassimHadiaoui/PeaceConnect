<?php
include __DIR__ . '/partials/header.php';
require_once __DIR__ . "/../../Controller/organisationController.php";

$oc = new OrganisationController();
$q = trim($_GET['q'] ?? '');
$category = trim($_GET['category'] ?? '');
$city = trim($_GET['city'] ?? '');

$organisations = $oc->searchOrganisations($q, $category, $city, true);
$success = $_SESSION['success'] ?? null;
unset($_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organisations - PeaceConnect</title>
    <link rel="icon" type="image/svg+xml" href="<?= $assets ?>favicon.svg">
    <link rel="stylesheet" href="<?= $assets ?>style.css?v=<?php echo filemtime(__DIR__ . "/../assets/style.css"); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        .grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:1.25rem; }
        .org-card { background:var(--bg-card); border:1px solid var(--border-color); border-radius:18px; overflow:hidden; }
        .org-top { padding:1.25rem; display:flex; gap:1rem; align-items:center; }
        .org-logo { width:56px; height:56px; border-radius:50%; overflow:hidden; background:linear-gradient(135deg,var(--primary),var(--secondary)); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:700; }
        .org-logo img { width:100%; height:100%; object-fit:cover; }
        .org-name { font-weight:700; margin:0; }
        .org-meta { color:var(--text-muted); font-size:0.9rem; }
        .org-body { padding: 0 1.25rem 1.25rem 1.25rem; color:var(--text-muted); font-size:0.92rem; line-height:1.6; }
        .org-actions { padding: 1rem 1.25rem; border-top:1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center; gap:0.75rem; }
        .filters { background:var(--bg-card); border:1px solid var(--border-color); border-radius:18px; padding:1rem; margin: 1.5rem 0; display:flex; gap:0.75rem; flex-wrap:wrap; }
        .filters .form-control { min-width: 220px; }
    </style>
</head>
<body>
    <div class="bg-animation"><span></span><span></span><span></span><span></span><span></span><span></span></div>
    <?php include 'partials/navbar.php'; ?>

    <main class="main">
        <div class="container">
            <div class="hero">
                <h1>Partner Organisations</h1>
                <p>Discover organisations that support mediation, inclusion, and community help.</p>
            </div>

            <?php if ($success): ?>
            <div class="alert alert-success"><div><?= htmlspecialchars($success) ?></div></div>
            <?php endif; ?>

            <form class="filters" method="GET" action="organisations.php" novalidate>
                <input class="form-control" type="text" name="q" placeholder="Search by name..." value="<?= htmlspecialchars($q) ?>">
                <input class="form-control" type="text" name="category" placeholder="Category (optional)" value="<?= htmlspecialchars($category) ?>">
                <input class="form-control" type="text" name="city" placeholder="City (optional)" value="<?= htmlspecialchars($city) ?>">
                <button class="btn btn-primary" type="submit">Search</button>
                <a class="btn btn-secondary" href="organisations.php">Reset</a>
            </form>

            <?php if (count($organisations) > 0): ?>
            <div class="grid">
                <?php foreach ($organisations as $o): ?>
                <div class="org-card">
                    <div class="org-top">
                        <div class="org-logo">
                            <?php if (!empty($o['logo_path'])): ?>
                            <img src="../../<?= htmlspecialchars($o['logo_path']) ?>" alt="Logo">
                            <?php else: ?>
                            <?= htmlspecialchars(mb_strtoupper(mb_substr($o['name'] ?? 'ORG', 0, 2))) ?>
                            <?php endif; ?>
                        </div>
                        <div style="min-width:0">
                            <p class="org-name"><?= htmlspecialchars($o['name'] ?? '') ?></p>
                            <div class="org-meta">
                                <?= htmlspecialchars($o['category'] ?? 'General') ?>
                                <?php if (!empty($o['city'])): ?> &middot; <?= htmlspecialchars($o['city']) ?><?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="org-body">
                        <?= htmlspecialchars(mb_substr($o['description'] ?? '', 0, 140)) ?><?= mb_strlen($o['description'] ?? '') > 140 ? '...' : '' ?>
                    </div>
                    <div class="org-actions">
                        <span class="badge <?= ($o['status'] ?? 'active') === 'active' ? 'badge-resolved' : 'badge-pending' ?>">
                            <?= ($o['status'] ?? 'active') === 'active' ? 'ACTIVE' : 'INACTIVE' ?>
                        </span>
                        <a class="btn btn-sm btn-primary" href="organisation_show.php?id=<?= (int)$o['id'] ?>">View</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="card">
                <div class="empty-state">
                    <h3>No organisations found</h3>
                    <p style="color:var(--text-muted)">Try adjusting your search.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include '../partials/chatbot_widget.php'; ?>
    <script src="<?= $assets ?>theme.js"></script>
</body>
</html>



