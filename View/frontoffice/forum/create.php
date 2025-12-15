<?php
include __DIR__ . '/../partials/header.php';
require_once __DIR__ . "/../../../Controller/authMiddleware.php";
requireLogin();

$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);

$categories = [
    'discussion' => 'General Discussion',
    'help' => 'Help & Support',
    'experience' => 'Share Experience',
    'legal' => 'Legal Questions',
    'events' => 'Events',
    'resources' => 'Resources'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post - PeaceConnect Forum</title>
    <link rel="icon" type="image/svg+xml" href="<?= $assets ?>favicon.svg">
    <link rel="stylesheet" href="<?= $assets ?>style.css?v=<?php echo filemtime(__DIR__ . "/../../assets/style.css"); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        .page-header { text-align: center; padding: 2rem 0; }
        .page-header h1 { font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem; }
        .page-header p { color: var(--text-muted); }
        .form-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 20px; padding: 2rem; }
        .category-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; margin-bottom: 1.5rem; }
        .category-option { padding: 1rem; border: 2px solid var(--border-color); border-radius: 12px; text-align: center; cursor: pointer; transition: all 0.3s; }
        .category-option:hover { border-color: var(--primary); }
        .category-option.selected { border-color: var(--primary); background: rgba(99,102,241,0.1); }
        @media (max-width: 600px) { .category-grid { grid-template-columns: repeat(2, 1fr); } }
    </style>
</head>
<body>
    <div class="bg-animation"><span></span><span></span><span></span><span></span><span></span><span></span></div>
    <?php include '../partials/navbar.php'; ?>

    <main class="main">
        <div class="container-sm">
            <div class="page-header">
                <h1>Create Post</h1>
                <p>Share with the community</p>
            </div>

            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger"><div><?php foreach($errors as $e) echo htmlspecialchars($e).'<br>'; ?></div></div>
            <?php endif; ?>

            <div class="form-card">
                <form id="forumForm" action="../../../Controller/publicationController.php" method="POST" novalidate>
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="source" value="frontoffice">
                    <input type="hidden" name="categorie" id="selectedCategory" value="<?= $old['categorie'] ?? '' ?>">

                    <h3 style="margin-bottom:0.75rem">Category</h3>
                    <div class="category-grid">
                        <?php foreach ($categories as $key => $label): ?>
                        <div class="category-option <?= ($old['categorie']??'')===$key?'selected':'' ?>" data-category="<?= $key ?>">
                            <?= $label ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div id="category-error" class="form-error" style="color:#ef4444;font-size:0.85rem"></div>

                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="titre" class="form-control" placeholder="Your post title" value="<?= htmlspecialchars($old['titre'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label>Content</label>
                        <textarea name="contenu" class="form-control" rows="6" placeholder="Write your post content here..."><?= htmlspecialchars($old['contenu'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Tags (optional)</label>
                        <input type="text" name="tags" class="form-control" placeholder="#peace, #mediation, #community" value="<?= htmlspecialchars($old['tags'] ?? '') ?>">
                    </div>

                    <div style="display:flex;gap:1rem;margin-top:1.5rem">
                        <button type="submit" class="btn btn-success" style="flex:1">Publish</button>
                        <a href="../forum.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <?php include '../../partials/chatbot_widget.php'; ?>
    <script src="<?= $assets ?>theme.js"></script>
    <script src="<?= $assets ?>validation.js"></script>
    <script>
    // Category selection
    document.querySelectorAll('.category-option').forEach(opt => {
        opt.addEventListener('click', () => {
            document.querySelectorAll('.category-option').forEach(o => o.classList.remove('selected'));
            opt.classList.add('selected');
            document.getElementById('selectedCategory').value = opt.dataset.category;
            document.getElementById('category-error').textContent = '';
        });
    });

    // Form validation
    document.getElementById('forumForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let isValid = true;
        
        // Validate category
        if (!document.getElementById('selectedCategory').value) {
            document.getElementById('category-error').textContent = 'Please select a category';
            isValid = false;
        }
        
        // Validate title
        const title = document.querySelector('[name="titre"]');
        if (title.value.trim().length < 5) {
            Validator.showError(title, 'Title must be at least 5 characters');
            isValid = false;
        } else {
            Validator.showSuccess(title);
        }
        
        // Validate content
        const content = document.querySelector('[name="contenu"]');
        if (content.value.trim().length < 20) {
            Validator.showError(content, 'Content must be at least 20 characters');
            isValid = false;
        } else {
            Validator.showSuccess(content);
        }
        
        if (isValid) {
            this.submit();
        }
    });
    </script>
</body>
</html>


