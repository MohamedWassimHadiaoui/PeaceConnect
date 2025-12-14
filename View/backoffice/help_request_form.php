<?php
include __DIR__ . '/partials/header.php';
require_once __DIR__ . "/../../Controller/helpRequestController.php";
$hc = new HelpRequestController();

$request = null;
$isEdit = false;
if (isset($_GET['id'])) {
    $request = $hc->getHelpRequestById($_GET['id']);
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
    <title><?= $isEdit ? 'Edit' : 'Add' ?> Help Request - PeaceConnect Admin</title>
    <link rel="icon" type="image/svg+xml" href="<?= $assets ?>favicon.svg">
    <link rel="stylesheet" href="<?= $assets ?>style.css?v=<?php echo filemtime(__DIR__ . "/../assets/style.css"); ?>">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <style>
        #map { height: 200px; border-radius: 8px; margin-top: 0.5rem; }
    </style>
</head>
<body>
    <div class="bg-animation"><span></span><span></span><span></span><span></span><span></span><span></span></div>
    <?php include 'partials/navbar.php'; ?>

    <main class="main">
        <div class="container-sm">
            <div class="hero">
                <h1><?= $isEdit ? 'Edit Help Request' : 'Add Help Request' ?></h1>
            </div>

            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger"><div><?php foreach($errors as $e) echo htmlspecialchars($e).'<br>'; ?></div></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body" style="padding:2rem">
                    <form id="helpForm" action="../../Controller/helpRequestController.php" method="POST" novalidate>
                        <input type="hidden" name="action" value="<?= $isEdit ? 'update' : 'add' ?>">
                        <input type="hidden" name="source" value="backoffice">
                        <?php if ($isEdit): ?><input type="hidden" name="id" value="<?= $request['id'] ?>"><?php endif; ?>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Type</label>
                                <select name="help_type" class="form-control">
                                    <option value="">-- Select --</option>
                                    <option value="sociale" <?= (($request['help_type'] ?? $old['help_type'] ?? '') === 'sociale') ? 'selected' : '' ?>>Social</option>
                                    <option value="juridique" <?= (($request['help_type'] ?? $old['help_type'] ?? '') === 'juridique') ? 'selected' : '' ?>>Legal</option>
                                    <option value="psychologique" <?= (($request['help_type'] ?? $old['help_type'] ?? '') === 'psychologique') ? 'selected' : '' ?>>Psychological</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Urgency</label>
                                <select name="urgency_level" class="form-control">
                                    <option value="low" <?= (($request['urgency_level'] ?? $old['urgency_level'] ?? '') === 'low') ? 'selected' : '' ?>>Low</option>
                                    <option value="medium" <?= (($request['urgency_level'] ?? $old['urgency_level'] ?? 'medium') === 'medium') ? 'selected' : '' ?>>Medium</option>
                                    <option value="high" <?= (($request['urgency_level'] ?? $old['urgency_level'] ?? '') === 'high') ? 'selected' : '' ?>>High</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Situation</label>
                            <textarea name="situation" class="form-control" rows="5" placeholder="Describe the situation..."><?= htmlspecialchars($request['situation'] ?? $old['situation'] ?? '') ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>Location</label>
                            <input type="text" name="location" id="location" class="form-control" placeholder="Click on map or enter location" value="<?= htmlspecialchars($request['location'] ?? $old['location'] ?? '') ?>">
                            <div id="map"></div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Contact Method</label>
                                <select name="contact_method" class="form-control">
                                    <option value="">-- Select --</option>
                                    <option value="email" <?= (($request['contact_method'] ?? $old['contact_method'] ?? '') === 'email') ? 'selected' : '' ?>>Email</option>
                                    <option value="telephone" <?= (($request['contact_method'] ?? $old['contact_method'] ?? '') === 'telephone') ? 'selected' : '' ?>>Phone</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="pending" <?= (($request['status'] ?? $old['status'] ?? '') === 'pending') ? 'selected' : '' ?>>Pending</option>
                                    <option value="in_progress" <?= (($request['status'] ?? $old['status'] ?? '') === 'in_progress') ? 'selected' : '' ?>>In Progress</option>
                                    <option value="resolved" <?= (($request['status'] ?? $old['status'] ?? '') === 'resolved') ? 'selected' : '' ?>>Resolved</option>
                                    <option value="closed" <?= (($request['status'] ?? $old['status'] ?? '') === 'closed') ? 'selected' : '' ?>>Closed</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Assigned To</label>
                            <input type="text" name="responsable" class="form-control" placeholder="Responsible person" value="<?= htmlspecialchars($request['responsable'] ?? $old['responsable'] ?? '') ?>">
                        </div>

                        <div style="display:flex;gap:1rem;margin-top:1.5rem">
                            <button type="submit" class="btn btn-success" style="flex:1">Save</button>
                            <a href="help_requests.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script src="<?= $assets ?>theme.js"></script>
    <script src="../assets/validation.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Map
        const map = L.map('map').setView([36.8, 10.18], 10);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '(c) OpenStreetMap' }).addTo(map);
        let marker;
        
        // If there's an existing location, try to place marker
        const existingLocation = document.getElementById('location').value;
        if (existingLocation && existingLocation.includes(',')) {
            const parts = existingLocation.split(',');
            if (parts.length === 2) {
                const lat = parseFloat(parts[0].trim());
                const lng = parseFloat(parts[1].trim());
                if (!isNaN(lat) && !isNaN(lng)) {
                    marker = L.marker([lat, lng]).addTo(map);
                    map.setView([lat, lng], 12);
                }
            }
        }
        
        map.on('click', e => {
            if (marker) map.removeLayer(marker);
            marker = L.marker(e.latlng).addTo(map);
            document.getElementById('location').value = e.latlng.lat.toFixed(5) + ', ' + e.latlng.lng.toFixed(5);
        });

        // Validation
        Validator.init('helpForm', {
            'help_type': [
                { type: 'required', message: 'Please select a type' }
            ],
            'situation': [
                { type: 'required', message: 'Situation is required' },
                { type: 'minLength', min: 20, message: 'Situation must be at least 20 characters' }
            ]
        });
    </script>
</body>
</html>

