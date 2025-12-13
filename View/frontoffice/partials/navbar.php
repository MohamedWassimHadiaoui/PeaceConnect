<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<?php
// Include config for BASE_URL
require_once __DIR__ . '/../../../config.php';

// Use absolute paths from BASE_URL
$base = BASE_URL . 'View/frontoffice/';
$backoffice = BASE_URL . 'View/backoffice/';
$assets = BASE_URL . 'View/assets/';
$uploads = BASE_URL . 'uploads/';
$controller = BASE_URL . 'Controller/';

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
        <a href="<?= $base ?>index.php" class="navbar-brand" id="brandLink" aria-label="PeaceConnect home">
            <div class="logo <?= $logoUrl ? 'has-img' : '' ?>">
                <?php if ($logoUrl): ?>
                    <img class="brand-logo-img" src="<?= htmlspecialchars($logoUrl) ?>" alt="PeaceConnect logo">
                <?php else: ?>
                    PC
                <?php endif; ?>
            </div>
            <span class="brand-text">PeaceConnect</span>
        </a>
        <div class="navbar-links">
            <a href="<?= $base ?>index.php">Home</a>
            <a href="<?= $base ?>events.php">Events</a>
            <a href="<?= $base ?>forum.php">Forum</a>
            <a href="<?= $base ?>resources.php">Resources</a>
            <a href="<?= $base ?>organisations.php">Organisations</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?= $base ?>create_report.php">Report</a>
                <a href="<?= $base ?>my_reports.php">My Reports</a>
                <a href="<?= $base ?>help_request.php">Help</a>
                <?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?>
                <a href="<?= $backoffice ?>index.php" style="color:var(--primary)">Admin</a>
                <?php endif; ?>
                <a href="<?= $base ?>profile.php" style="display:flex;align-items:center;gap:0.5rem">
                    <span style="width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--secondary));display:flex;align-items:center;justify-content:center;font-size:0.8rem;color:#fff;overflow:hidden">
                        <?php if (!empty($_SESSION['user_avatar'])): ?>
                        <img src="<?= $uploads ?><?= htmlspecialchars(basename($_SESSION['user_avatar'])) ?>" style="width:100%;height:100%;object-fit:cover">
                        <?php else: ?><?= htmlspecialchars(strtoupper(substr((string)($_SESSION['user_name'] ?? 'U'), 0, 1))) ?><?php endif; ?>
                    </span>
                    <?= htmlspecialchars($_SESSION['user_name'] ?? 'Profile') ?>
                </a>
                <a href="<?= $controller ?>userController.php?action=logout" style="color:#ef4444">Logout</a>
            <?php else: ?>
                <a href="<?= $base ?>login.php">Login</a>
                <a href="<?= $base ?>register.php" class="btn btn-primary btn-sm">Get Started</a>
            <?php endif; ?>
            <button class="theme-toggle" id="themeToggle" onclick="toggleTheme()">Theme</button>
        </div>
    </div>
</nav>
<script>
// Make the logo feel interactive: if already on home, clicking it scrolls to top smoothly
(() => {
  const brand = document.getElementById('brandLink');
  if (!brand) return;
  brand.addEventListener('click', (e) => {
    const path = (window.location.pathname || '').toLowerCase();
    const isHome = path.endsWith('/index.php') || path.endsWith('/frontoffice/');
    if (isHome) {
      e.preventDefault();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  });
})();
</script>
