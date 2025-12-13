<?php
include __DIR__ . '/partials/header.php';
require_once __DIR__ . "/../../Controller/reportController.php";
require_once __DIR__ . "/../../Controller/mediatorController.php";
require_once __DIR__ . "/../../Controller/aiController.php";

$rc = new ReportController();
$mc = new MediatorController();
$ai = new AIController();
$mediators = $mc->listMediators();

$report = null;
$isEdit = false;
$aiFlags = [];
if (isset($_GET['id'])) {
    $report = $rc->getReportById($_GET['id']);
    $isEdit = true;
    $aiFlags = $ai->getReportFlags($_GET['id']);
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
    <title><?= $isEdit ? 'Edit' : 'Add' ?> Report - PeaceConnect Admin</title>
    <link rel="icon" type="image/svg+xml" href="<?= $assets ?>favicon.svg">
    <link rel="stylesheet" href="<?= $assets ?>style.css?v=<?php echo filemtime(__DIR__ . "/../assets/style.css"); ?>">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <style>
        #map { height: 200px; border-radius: 8px; margin-top: 0.5rem; }
        .calendar-input { position: relative; }
        .calendar-wrapper { position: absolute; top: 100%; left: 0; z-index: 1000; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; padding: 1rem; box-shadow: 0 10px 30px var(--shadow); display: none; min-width: 280px; }
        .calendar-wrapper.show { display: block; }
        .cal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
        .cal-header button { background: none; border: none; font-size: 1.25rem; cursor: pointer; color: var(--text-primary); padding: 0.25rem 0.5rem; }
        .cal-header span { font-weight: 600; }
        .cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 0.25rem; text-align: center; }
        .cal-day-name { font-size: 0.7rem; font-weight: 600; color: var(--text-muted); padding: 0.25rem; }
        .cal-day { padding: 0.5rem; border-radius: 8px; cursor: pointer; font-size: 0.85rem; transition: all 0.2s; }
        .cal-day:hover { background: rgba(99,102,241,0.2); }
        .cal-day.today { border: 2px solid var(--primary); }
        .cal-day.selected { background: var(--primary); color: white; }
        .cal-day.other-month { opacity: 0.3; }
        .ai-card { margin-top: 1.5rem; border: 2px solid var(--warning); }
        .ai-card .card-header { background: rgba(245, 158, 11, 0.1); }
        .ai-flag { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; border-radius: 8px; margin: 0.25rem; font-size: 0.85rem; }
        .ai-flag.critical { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
        .ai-flag.high { background: rgba(249, 115, 22, 0.2); color: #f97316; }
        .ai-flag.medium { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
        .ai-flag.low { background: rgba(34, 197, 94, 0.2); color: #22c55e; }
    </style>
</head>
<body>
    <div class="bg-animation"><span></span><span></span><span></span><span></span><span></span><span></span></div>
    <?php include 'partials/navbar.php'; ?>

    <main class="main">
        <div class="container-sm">
            <div class="hero">
                <h1><?= $isEdit ? 'Edit Report' : 'Add Report' ?></h1>
            </div>

            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger"><div><?php foreach($errors as $e) echo htmlspecialchars($e).'<br>'; ?></div></div>
            <?php endif; ?>

            <?php if ($isEdit && count($aiFlags) > 0): ?>
            <div class="card ai-card">
                <div class="card-header">
                    <h3 class="card-title">Automated Analysis</h3>
                </div>
                <div class="card-body">
                    <div style="margin-bottom:1rem">
                        <?php foreach ($aiFlags as $flag): ?>
                        <span class="ai-flag <?= $flag['severity'] ?>">
                            <?= strtoupper(substr((string)($flag['severity'] ?? ''), 0, 1)) ?>
                            <?= htmlspecialchars(ucfirst($flag['flag_type'])) ?>
                            (<?= round(($flag['confidence_score'] ?? 0) * 100) ?>%)
                        </span>
                        <?php endforeach; ?>
                    </div>
                    <?php if (!empty($aiFlags[0]['ai_summary'])): ?>
                    <p style="color:var(--text-muted);font-size:0.9rem;margin:0"><strong>Summary:</strong> <?= htmlspecialchars($aiFlags[0]['ai_summary']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body" style="padding:2rem">
                    <form id="reportForm" action="../../Controller/reportController.php" method="POST" enctype="multipart/form-data" novalidate>
                        <input type="hidden" name="action" value="<?= $isEdit ? 'update' : 'add' ?>">
                        <input type="hidden" name="source" value="backoffice">
                        <?php if ($isEdit): ?>
                        <input type="hidden" name="id" value="<?= $report['id'] ?>">
                        <input type="hidden" name="existing_attachment" value="<?= htmlspecialchars($report['attachment_path'] ?? '') ?>">
                        <?php endif; ?>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Type</label>
                                <select name="type" class="form-control">
                                    <option value="">-- Select --</option>
                                    <option value="conflict" <?= (($report['type'] ?? $old['type'] ?? '') === 'conflict') ? 'selected' : '' ?>>Conflict</option>
                                    <option value="harassment" <?= (($report['type'] ?? $old['type'] ?? '') === 'harassment') ? 'selected' : '' ?>>Harassment</option>
                                    <option value="discrimination" <?= (($report['type'] ?? $old['type'] ?? '') === 'discrimination') ? 'selected' : '' ?>>Discrimination</option>
                                    <option value="violence" <?= (($report['type'] ?? $old['type'] ?? '') === 'violence') ? 'selected' : '' ?>>Violence</option>
                                    <option value="other" <?= (($report['type'] ?? $old['type'] ?? '') === 'other') ? 'selected' : '' ?>>Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Priority</label>
                                <select name="priority" class="form-control">
                                    <option value="low" <?= (($report['priority'] ?? $old['priority'] ?? '') === 'low') ? 'selected' : '' ?>>Low</option>
                                    <option value="medium" <?= (($report['priority'] ?? $old['priority'] ?? 'medium') === 'medium') ? 'selected' : '' ?>>Medium</option>
                                    <option value="high" <?= (($report['priority'] ?? $old['priority'] ?? '') === 'high') ? 'selected' : '' ?>>High</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="title" class="form-control" placeholder="Report title" value="<?= htmlspecialchars($report['title'] ?? $old['title'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Describe the incident..."><?= htmlspecialchars($report['description'] ?? $old['description'] ?? '') ?></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group calendar-input">
                                <label>Incident Date</label>
                                <input type="text" id="dateDisplay" class="form-control" placeholder="Click to select date" readonly>
                                <input type="hidden" name="incident_date" id="incidentDate" value="<?= htmlspecialchars($report['incident_date'] ?? $old['incident_date'] ?? date('Y-m-d')) ?>">
                                <div class="calendar-wrapper" id="calendarWrapper">
                                    <div class="cal-header">
                                        <button type="button" id="prevMonth">&lt;</button>
                                        <span id="calMonthYear"></span>
                                        <button type="button" id="nextMonth">&gt;</button>
                                    </div>
                                    <div class="cal-grid" id="calendarGrid"></div>
                                    <button type="button" id="confirmDate" class="btn btn-primary btn-sm" style="width:100%;margin-top:1rem">Select</button>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="pending" <?= (($report['status'] ?? $old['status'] ?? '') === 'pending') ? 'selected' : '' ?>>Pending</option>
                                    <option value="assigned" <?= (($report['status'] ?? $old['status'] ?? '') === 'assigned') ? 'selected' : '' ?>>Assigned</option>
                                    <option value="in_mediation" <?= (($report['status'] ?? $old['status'] ?? '') === 'in_mediation') ? 'selected' : '' ?>>In Mediation</option>
                                    <option value="resolved" <?= (($report['status'] ?? $old['status'] ?? '') === 'resolved') ? 'selected' : '' ?>>Resolved</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Location</label>
                            <input type="text" name="location" id="location" class="form-control" placeholder="Click map or enter address" value="<?= htmlspecialchars($report['location'] ?? $old['location'] ?? '') ?>">
                            <div id="map"></div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Assigned Mediator</label>
                                <select name="mediator_id" class="form-control">
                                    <option value="">-- None --</option>
                                    <?php foreach ($mediators as $m): ?>
                                    <option value="<?= $m['id'] ?>" <?= (($report['mediator_id'] ?? '') == $m['id']) ? 'selected' : '' ?>><?= htmlspecialchars($m['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Attachment</label>
                                <input type="file" name="attachment" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                <?php if (!empty($report['attachment_path'])): ?>
                                <div class="form-text" style="color:var(--text-muted);font-size:0.85rem;margin-top:0.25rem">Current: <?= htmlspecialchars(basename($report['attachment_path'])) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div style="display:flex;gap:1rem;margin-top:1.5rem">
                            <button type="submit" class="btn btn-success" style="flex:1">Save</button>
                            <a href="reports.php" class="btn btn-secondary">Cancel</a>
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
        // Validation
        Validator.init('reportForm', {
            'type': [{ type: 'required', message: 'Please select a type' }],
            'title': [{ type: 'required', message: 'Title is required' }, { type: 'minLength', min: 5, message: 'Min 5 characters' }],
            'description': [{ type: 'required', message: 'Description is required' }, { type: 'minLength', min: 10, message: 'Min 10 characters' }]
        });

        // Map
        const map = L.map('map').setView([36.8, 10.18], 10);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '(c) OpenStreetMap' }).addTo(map);
        let marker;
        
        // Load existing location
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

        // Custom Calendar
        let currentDate = new Date();
        let selectedDate = null;
        const calendarWrapper = document.getElementById('calendarWrapper');
        const dateDisplay = document.getElementById('dateDisplay');
        const incidentDate = document.getElementById('incidentDate');
        const calMonthYear = document.getElementById('calMonthYear');
        const calendarGrid = document.getElementById('calendarGrid');

        if (incidentDate.value) {
            selectedDate = new Date(incidentDate.value);
            currentDate = new Date(selectedDate);
            updateDateDisplay();
        }

        dateDisplay.addEventListener('click', () => {
            calendarWrapper.classList.toggle('show');
            renderCalendar();
        });

        document.getElementById('prevMonth').addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar();
        });

        document.getElementById('nextMonth').addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar();
        });

        document.getElementById('confirmDate').addEventListener('click', () => {
            if (selectedDate) {
                updateDateDisplay();
                calendarWrapper.classList.remove('show');
            }
        });

        function updateDateDisplay() {
            if (selectedDate) {
                dateDisplay.value = selectedDate.toLocaleDateString('en-US', { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' });
                incidentDate.value = toLocalDateValue(selectedDate);
            }
        }

        function pad2(n) { return String(n).padStart(2, '0'); }
        function toLocalDateValue(d) {
            return `${d.getFullYear()}-${pad2(d.getMonth() + 1)}-${pad2(d.getDate())}`;
        }

        function renderCalendar() {
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            calMonthYear.textContent = currentDate.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });

            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const today = new Date();

            let html = '<div class="cal-day-name">Su</div><div class="cal-day-name">Mo</div><div class="cal-day-name">Tu</div><div class="cal-day-name">We</div><div class="cal-day-name">Th</div><div class="cal-day-name">Fr</div><div class="cal-day-name">Sa</div>';

            for (let i = 0; i < firstDay; i++) html += '<div class="cal-day other-month"></div>';

            for (let day = 1; day <= daysInMonth; day++) {
                const isToday = today.getDate() === day && today.getMonth() === month && today.getFullYear() === year;
                const isSelected = selectedDate && selectedDate.getDate() === day && selectedDate.getMonth() === month && selectedDate.getFullYear() === year;
                html += `<div class="cal-day ${isToday ? 'today' : ''} ${isSelected ? 'selected' : ''}" data-day="${day}">${day}</div>`;
            }

            calendarGrid.innerHTML = html;

            calendarGrid.querySelectorAll('.cal-day[data-day]').forEach(el => {
                el.addEventListener('click', () => {
                    selectedDate = new Date(year, month, parseInt(el.dataset.day));
                    renderCalendar();
                });
            });
        }

        document.addEventListener('click', (e) => {
            if (!e.target.closest('.calendar-input')) calendarWrapper.classList.remove('show');
        });
    </script>
</body>
</html>

