<?php
include __DIR__ . '/partials/header.php';
require_once __DIR__ . "/../../Controller/authMiddleware.php";
requireLogin();
require_once __DIR__ . "/../../Controller/helpRequestController.php";

$hc = new HelpRequestController();
$myRequests = $hc->listUserRequests($_SESSION['user_id']);

$errors = $_SESSION['errors'] ?? [];
$success = $_SESSION['success'] ?? null;
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['success'], $_SESSION['old']);

$types = ['sociale' => 'Social', 'juridique' => 'Legal', 'psychologique' => 'Psychological'];
$statuses = ['pending' => 'Pending', 'in_progress' => 'In Progress', 'resolved' => 'Resolved', 'closed' => 'Closed'];
$urgencies = ['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Request - PeaceConnect</title>
    <link rel="icon" type="image/svg+xml" href="<?= $assets ?>favicon.svg">
    <link rel="stylesheet" href="<?= $assets ?>style.css?v=<?php echo filemtime(__DIR__ . "/../assets/style.css"); ?>">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        .page-header { text-align: center; padding: 2rem 0; }
        .page-header h1 { font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem; }
        .page-header p { color: var(--text-muted); }
        .form-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 20px; padding: 2rem; margin-bottom: 2rem; }
        .type-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
        .type-card { padding: 1.5rem 1rem; border: 2px solid var(--border-color); border-radius: 16px; text-align: center; cursor: pointer; transition: all 0.3s; }
        .type-card:hover { border-color: var(--primary); transform: translateY(-3px); }
        .type-card.selected { border-color: var(--primary); background: rgba(99,102,241,0.1); }
        .type-card .icon { font-size: 2.5rem; margin-bottom: 0.75rem; }
        .type-card h4 { font-size: 1rem; font-weight: 600; margin-bottom: 0.25rem; }
        .type-card p { font-size: 0.8rem; color: var(--text-muted); margin: 0; }
        @media (max-width: 600px) { .type-cards { grid-template-columns: 1fr; } }
        .request-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 1.25rem; margin-bottom: 0.75rem; }
        .request-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; }
        .request-id { font-weight: 600; }
        .request-situation { color: var(--text-muted); font-size: 0.9rem; }
        #map { height: 200px; border-radius: 12px; margin-top: 0.5rem; border: 1px solid var(--border-color); }
        .calendar-container { position: relative; }
        .calendar-popup { position: absolute; top: 100%; left: 0; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 1rem; z-index: 1000; box-shadow: 0 10px 30px var(--shadow); display: none; }
        .calendar-popup.show { display: block; }
        .calendar-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
        .calendar-header button { background: none; border: none; font-size: 1.25rem; cursor: pointer; color: var(--text-primary); }
        .calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 0.25rem; text-align: center; }
        .calendar-day { padding: 0.5rem; border-radius: 8px; cursor: pointer; font-size: 0.85rem; }
        .calendar-day:hover { background: rgba(99,102,241,0.1); }
        .calendar-day.today { background: var(--primary); color: white; }
        .calendar-day.selected { background: var(--secondary); color: white; }
        .calendar-day-header { font-weight: 600; color: var(--text-muted); font-size: 0.75rem; }
    </style>
</head>
<body>
    <div class="bg-animation"><span></span><span></span><span></span><span></span><span></span><span></span></div>
    <?php include 'partials/navbar.php'; ?>

    <main class="main">
        <div class="container-sm">
            <div class="page-header">
                <h1>Request Help</h1>
                <p>Get professional assistance</p>
            </div>

            <?php if ($success): ?>
            <div class="alert alert-success"><div><?= htmlspecialchars($success) ?></div></div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger"><div><?php foreach($errors as $e) echo htmlspecialchars($e).'<br>'; ?></div></div>
            <?php endif; ?>

            <div class="form-card">
                <form id="helpForm" action="../../Controller/helpRequestController.php" method="POST" novalidate>
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="help_type" id="selectedType" value="<?= $old['help_type'] ?? '' ?>">

                    <h3 style="margin-bottom:0.5rem">Type of Help</h3>
                    <p style="color:var(--text-muted);margin-bottom:1rem;font-size:0.9rem">Select the type of assistance you need</p>
                    
                    <div class="type-cards">
                        <div class="type-card <?= ($old['help_type']??'')==='sociale'?'selected':'' ?>" data-type="sociale">
                            <div class="icon">🏠</div>
                            <h4>Social</h4>
                            <p>Housing, food, daily support</p>
                        </div>
                        <div class="type-card <?= ($old['help_type']??'')==='juridique'?'selected':'' ?>" data-type="juridique">
                            <div class="icon">⚖️</div>
                            <h4>Legal</h4>
                            <p>Rights, procedures, advice</p>
                        </div>
                        <div class="type-card <?= ($old['help_type']??'')==='psychologique'?'selected':'' ?>" data-type="psychologique">
                            <div class="icon">💚</div>
                            <h4>Psychological</h4>
                            <p>Support, listening, mental health</p>
                        </div>
                    </div>
                    <div id="type-error" class="form-error" style="color:#ef4444;font-size:0.85rem"></div>

                    <div class="form-group">
                        <label>Describe Your Situation</label>
                        <textarea name="situation" id="situation" class="form-control" rows="5" placeholder="Explain your situation in detail (min 20 characters)..."><?= htmlspecialchars($old['situation'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Location</label>
                        <input type="text" name="location" id="location" class="form-control" placeholder="Click on map or enter address" value="<?= htmlspecialchars($old['location'] ?? '') ?>">
                        <div id="map"></div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Urgency</label>
                            <select name="urgency_level" class="form-control">
                                <option value="low" <?= ($old['urgency_level']??'')==='low'?'selected':'' ?>>Low</option>
                                <option value="medium" <?= ($old['urgency_level']??'medium')==='medium'?'selected':'' ?>>Medium</option>
                                <option value="high" <?= ($old['urgency_level']??'')==='high'?'selected':'' ?>>High</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Preferred Contact</label>
                            <select name="contact_method" class="form-control">
                                <option value="">-- Select --</option>
                                <option value="email" <?= ($old['contact_method']??'')==='email'?'selected':'' ?>>Email</option>
                                <option value="telephone" <?= ($old['contact_method']??'')==='telephone'?'selected':'' ?>>Phone</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success" style="width:100%;padding:1rem">Submit request</button>
                </form>
            </div>

            <?php if (count($myRequests) > 0): ?>
            <h2 style="margin-bottom:1rem">My Requests</h2>
            <?php foreach ($myRequests as $r): ?>
            <div class="request-card">
                <div class="request-header">
                    <span class="request-id">#<?= $r['id'] ?> &middot; <?= $types[$r['help_type']] ?? $r['help_type'] ?></span>
                    <span class="badge badge-<?= str_replace('_','-',$r['status']) ?>"><?= $statuses[$r['status']] ?? $r['status'] ?></span>
                </div>
                <div class="request-situation"><?= htmlspecialchars(substr($r['situation'] ?? '', 0, 100)) ?>...</div>
                <div style="margin-top:0.5rem;font-size:0.85rem;color:var(--text-muted)">
                    Location: <?= htmlspecialchars($r['location'] ?? 'Not specified') ?> &middot; 
                    <?= $urgencies[$r['urgency_level']] ?? $r['urgency_level'] ?>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <?php include '../partials/chatbot_widget.php'; ?>
    <script src="<?= $assets ?>theme.js"></script>
    <script src="../assets/validation.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
    // Type selection
    document.querySelectorAll('.type-card').forEach(card => {
        card.addEventListener('click', () => {
            document.querySelectorAll('.type-card').forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');
            document.getElementById('selectedType').value = card.dataset.type;
            document.getElementById('type-error').textContent = '';
        });
    });

    // Map integration
    const map = L.map('map').setView([36.8, 10.18], 10);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '(c) OpenStreetMap' }).addTo(map);
    let marker;
    map.on('click', e => {
        if (marker) map.removeLayer(marker);
        marker = L.marker(e.latlng).addTo(map);
        document.getElementById('location').value = e.latlng.lat.toFixed(5) + ', ' + e.latlng.lng.toFixed(5);
    });

    // Form validation
    document.getElementById('helpForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let isValid = true;
        
        if (!document.getElementById('selectedType').value) {
            document.getElementById('type-error').textContent = 'Please select a type of help';
            isValid = false;
        }
        
        const situation = document.getElementById('situation');
        if (situation.value.trim().length < 20) {
            Validator.showError(situation, 'Description must be at least 20 characters');
            isValid = false;
        } else {
            Validator.showSuccess(situation);
        }
        
        if (isValid) this.submit();
    });
    </script>
</body>
</html>

