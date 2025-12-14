<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../../config.php';

// Use absolute paths from BASE_URL
$assets = BASE_URL . 'View/assets/';
$frontoffice = BASE_URL . 'View/frontoffice/';
$backoffice = BASE_URL . 'View/backoffice/';
$controller = BASE_URL . 'Controller/';

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    $_SESSION['errors'] = ['Admin access required'];
    header("Location: " . $frontoffice . "login.php");
    exit;
}

// Logo detection
$logoCandidates = [
    __DIR__ . '/../../assets/img/logo.png' => $assets . 'img/logo.png',
    __DIR__ . '/../../assets/img/logo.jpg' => $assets . 'img/logo.jpg',
    __DIR__ . '/../../assets/img/logo.jpeg' => $assets . 'img/logo.jpeg',
];
$logoUrl = null;
foreach ($logoCandidates as $file => $url) {
    if (is_file($file)) { $logoUrl = $url . '?v=' . @filemtime($file); break; }
}
?>
<nav class="navbar">
    <div class="navbar-container">
        <a href="<?= $backoffice ?>index.php" class="navbar-brand" id="brandLink" aria-label="PeaceConnect admin home">
            <div class="logo <?= $logoUrl ? 'has-img' : '' ?>">
                <?php if ($logoUrl): ?>
                    <img class="brand-logo-img" src="<?= htmlspecialchars($logoUrl) ?>" alt="PeaceConnect logo">
                <?php else: ?>
                    PC
                <?php endif; ?>
            </div>
            <span class="brand-text">PeaceConnect</span> 
            <span class="brand-tag">ADMIN</span>
        </a>
        <div class="navbar-links">
            <a href="<?= $backoffice ?>index.php">Dashboard</a>
            <a href="<?= $backoffice ?>reports.php">Reports</a>
            <a href="<?= $backoffice ?>help_requests.php">Help</a>
            <a href="<?= $backoffice ?>events.php">Events</a>
            <a href="<?= $backoffice ?>resources.php">Resources</a>
            <a href="<?= $backoffice ?>forum.php">Forum</a>
            <a href="<?= $backoffice ?>mediators.php">Mediators</a>
            <a href="<?= $backoffice ?>sessions.php">Sessions</a>
            <a href="<?= $backoffice ?>organisations.php">Organisations</a>
            <a href="<?= $backoffice ?>users.php">Users</a>
            <a href="<?= $frontoffice ?>index.php" style="color:var(--success)">Site</a>
            <a href="<?= $controller ?>userController.php?action=logout" style="color:#ef4444">Logout</a>
            <button class="theme-toggle" id="themeToggle" onclick="toggleTheme()">Theme</button>
        </div>
    </div>
</nav>
<script>
(() => {
  const brand = document.getElementById('brandLink');
  if (!brand) return;
  brand.addEventListener('click', (e) => {
    const path = (window.location.pathname || '').toLowerCase();
    const isDash = path.endsWith('/index.php') && path.includes('/backoffice/');
    if (isDash) {
      e.preventDefault();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  });
})();
</script>
