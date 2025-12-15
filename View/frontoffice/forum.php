<?php
include __DIR__ . '/partials/header.php';
require_once __DIR__ . "/../../Controller/publicationController.php";
$pc = new PublicationController();
$posts = $pc->listPublications();
$categories = [
    'discussion' => 'Discussion',
    'help' => 'Help & Support',
    'experience' => 'Experience',
    'legal' => 'Legal',
    'events' => 'Events',
    'resources' => 'Resources',
    // legacy/extra values
    'support' => 'Support',
    'question' => 'Question'
];
$success = $_SESSION['success'] ?? null;
unset($_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum - PeaceConnect</title>
    <link rel="icon" type="image/svg+xml" href="<?= $assets ?>favicon.svg">
    <link rel="stylesheet" href="<?= $assets ?>style.css?v=<?php echo filemtime(__DIR__ . "/../assets/style.css"); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        .page-header {
            text-align: center;
            padding: 3rem 0;
        }
        .page-header h1 { font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem; }
        .page-header p { color: var(--text-muted); font-size: 1.1rem; }
        .forum-post {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s;
        }
        .forum-post:hover {
            border-color: var(--primary);
            box-shadow: 0 5px 20px var(--shadow);
        }
        .forum-post-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        .forum-post h3 {
            font-size: 1.2rem;
            font-weight: 600;
            margin: 0;
            color: var(--text-primary);
        }
        .forum-post-content {
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 1rem;
        }
        .forum-post-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
            font-size: 0.85rem;
        }
        .forum-author {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-muted);
        }
        .forum-stats {
            display: flex;
            gap: 1rem;
        }
        .forum-stat {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            color: var(--text-muted);
        }
    </style>
</head>
<body>
    <div class="bg-animation"><span></span><span></span><span></span><span></span><span></span><span></span></div>
    <?php include 'partials/navbar.php'; ?>

    <main class="main">
        <div class="container">
            <div class="page-header">
                <h1>Community Forum</h1>
                <p>Share experiences and connect with the community</p>
                <?php if (isset($_SESSION['user_id'])): ?>
                <div style="margin-top:1.25rem">
                    <a href="forum/create.php" class="btn btn-success">Create post</a>
                </div>
                <?php else: ?>
                <div style="margin-top:1.25rem">
                    <a href="login.php" class="btn btn-primary">Login to post</a>
                </div>
                <?php endif; ?>
            </div>

            <?php if ($success): ?>
            <div class="alert alert-success"><div><?= htmlspecialchars($success) ?></div></div>
            <?php endif; ?>

            <?php if (count($posts) > 0): ?>
            <div class="forum-list">
                <?php foreach ($posts as $p): ?>
                <?php
                    $postId = (int)($p['id'] ?? 0);
                    $fullTitle = (string)($p['titre'] ?? '');
                    $fullContent = (string)($p['contenu'] ?? '');
                    $snippet = mb_substr($fullContent, 0, 200);
                    $hasMore = mb_strlen($fullContent) > 200;
                ?>
                <div class="forum-post">
                    <div class="forum-post-header">
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;width:100%">
                            <div style="flex:1;min-width:0">
                                <h3 id="post-title-<?= $postId ?>"
                                    data-original="<?= htmlspecialchars($fullTitle, ENT_QUOTES, 'UTF-8') ?>"
                                    style="word-break:break-word">
                                    <?= htmlspecialchars($fullTitle) ?>
                                </h3>
                            </div>
                            <div style="display:flex;gap:0.5rem;align-items:center;flex-shrink:0">
                                <div class="translate-dropdown" data-post-id="<?= $postId ?>">
                                    <button type="button" class="translate-btn" title="Translate">Translate</button>
                                    <div class="translate-menu">
                                        <button type="button" data-lang="en">🇬🇧 English</button>
                                        <button type="button" data-lang="fr">🇫🇷 Français</button>
                                        <button type="button" data-lang="ar">🇸🇦 العربية</button>
                                        <button type="button" data-lang="original">📄 Original</button>
                                    </div>
                                </div>
                                <button type="button"
                                        class="speech-btn"
                                        title="Read"
                                        data-titre="<?= htmlspecialchars($fullTitle, ENT_QUOTES, 'UTF-8') ?>"
                                        data-contenu="<?= htmlspecialchars($fullContent, ENT_QUOTES, 'UTF-8') ?>">
                                    Read
                                </button>
                                <span class="badge badge-assigned"><?= $categories[$p['categorie']] ?? $p['categorie'] ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="forum-post-content"
                         id="post-content-<?= $postId ?>"
                         data-original="<?= htmlspecialchars($snippet . ($hasMore ? '...' : ''), ENT_QUOTES, 'UTF-8') ?>"
                         data-original-full="<?= htmlspecialchars($fullContent, ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($snippet) ?><?= $hasMore ? '...' : '' ?>
                    </div>
                    <div class="forum-post-footer">
                        <div class="forum-author">
                            <span style="width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--secondary));display:flex;align-items:center;justify-content:center;font-size:0.75rem;color:#fff">
                                <?= htmlspecialchars(strtoupper(substr((string)($p['auteur'] ?? 'U'), 0, 1))) ?>
                            </span>
                            <span><?= htmlspecialchars($p['auteur']) ?></span>
                            <span>&middot;</span>
                            <span><?= date('M j, Y', strtotime($p['created_at'])) ?></span>
                        </div>
                        <div class="forum-stats">
                            <div class="forum-stat">Likes: <?= $p['nombre_likes'] ?? 0 ?></div>
                            <div class="forum-stat">Comments: <?= $p['nombre_commentaires'] ?? 0 ?></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="card">
                <div class="empty-state">
                    <h3>No Posts Yet</h3>
                    <p style="color:var(--text-muted)">Be the first to start a discussion!</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include '../partials/chatbot_widget.php'; ?>
    <script src="<?= $assets ?>theme.js"></script>
    <script src="<?= $assets ?>forum_features.js"></script>
</body>
</html>

