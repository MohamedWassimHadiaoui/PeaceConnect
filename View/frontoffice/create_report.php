<?php
include __DIR__ . '/partials/header.php';
require_once __DIR__ . "/../../Controller/authMiddleware.php";
requireLogin();

$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Report - PeaceConnect</title>
    <link rel="icon" type="image/svg+xml" href="<?= $assets ?>favicon.svg">
    <link rel="stylesheet" href="<?= $assets ?>style.css?v=<?php echo filemtime(__DIR__ . "/../assets/style.css"); ?>">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }
        .page-header { text-align: center; padding: 2rem 0; }
        .page-header h1 { font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem; }
        .page-header p { color: var(--text-muted); }
        .form-card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 20px; padding: 2rem; }
        .form-section { margin-bottom: 2rem; padding-bottom: 2rem; border-bottom: 1px solid var(--border-color); }
        .form-section:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
        .form-section-title { font-size: 1.1rem; font-weight: 600; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem; }
        #map { height: 250px; border-radius: 12px; margin-top: 0.5rem; border: 1px solid var(--border-color); }
        .type-options { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 0.75rem; }
        .type-option { padding: 1rem; border: 2px solid var(--border-color); border-radius: 12px; text-align: center; cursor: pointer; transition: all 0.3s; }
        .type-option:hover { border-color: var(--primary); }
        .type-option.selected { border-color: var(--primary); background: rgba(99,102,241,0.1); }
        .type-option .icon { font-size: 1.5rem; margin-bottom: 0.5rem; }
        .type-option .label { font-size: 0.85rem; font-weight: 500; }
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
    </style>
</head>
<body>
    <div class="bg-animation"><span></span><span></span><span></span><span></span><span></span><span></span></div>
    <?php include 'partials/navbar.php'; ?>

    <main class="main">
        <div class="container-sm">
            <div class="page-header">
                <h1>Create Report</h1>
                <p>Report an incident for mediation</p>
            </div>

            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger"><div><?php foreach($errors as $e) echo htmlspecialchars($e).'<br>'; ?></div></div>
            <?php endif; ?>

            <div class="form-card">
                <form id="reportForm" action="../../Controller/reportController.php" method="POST" enctype="multipart/form-data" novalidate>
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="source" value="frontoffice">
                    <input type="hidden" name="type" id="selectedType" value="<?= $old['type'] ?? '' ?>">

                    <div class="form-section">
                        <h3 class="form-section-title">Incident Type</h3>
                        <div class="type-options">
                            <div class="type-option <?= ($old['type']??'')==='conflict'?'selected':'' ?>" data-type="conflict">
                                <div class="icon">⚔️</div>
                                <div class="label">Conflict</div>
                            </div>
                            <div class="type-option <?= ($old['type']??'')==='harassment'?'selected':'' ?>" data-type="harassment">
                                <div class="icon">🚫</div>
                                <div class="label">Harassment</div>
                            </div>
                            <div class="type-option <?= ($old['type']??'')==='discrimination'?'selected':'' ?>" data-type="discrimination">
                                <div class="icon">⛔</div>
                                <div class="label">Discrimination</div>
                            </div>
                            <div class="type-option <?= ($old['type']??'')==='violence'?'selected':'' ?>" data-type="violence">
                                <div class="icon">🔴</div>
                                <div class="label">Violence</div>
                            </div>
                            <div class="type-option <?= ($old['type']??'')==='other'?'selected':'' ?>" data-type="other">
                                <div class="icon">❓</div>
                                <div class="label">Other</div>
                            </div>
                        </div>
                        <div id="type-error" class="form-error" style="color:#ef4444;font-size:0.85rem;margin-top:0.5rem"></div>
                    </div>

                    <div class="form-section">
                        <h3 class="form-section-title">Details</h3>
                        
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="title" class="form-control" placeholder="Brief title for the incident" value="<?= htmlspecialchars($old['title'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="5" placeholder="Describe what happened in detail..."><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                        </div>

                        <div class="form-row">
                            <div class="form-group calendar-input">
                                <label>Incident Date</label>
                                <input type="text" id="dateDisplay" class="form-control" placeholder="Click to select date" readonly>
                                <input type="hidden" name="incident_date" id="incidentDate" value="<?= htmlspecialchars($old['incident_date'] ?? date('Y-m-d')) ?>">
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
                                <label>Priority</label>
                                <select name="priority" class="form-control">
                                    <option value="low" <?= ($old['priority']??'')==='low'?'selected':'' ?>>Low</option>
                                    <option value="medium" <?= ($old['priority']??'medium')==='medium'?'selected':'' ?>>Medium</option>
                                    <option value="high" <?= ($old['priority']??'')==='high'?'selected':'' ?>>High</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3 class="form-section-title">Location</h3>
                        <div class="form-group">
                            <label>Address or Coordinates</label>
                            <input type="text" name="location" id="location" class="form-control" placeholder="Click on map or enter address" value="<?= htmlspecialchars($old['location'] ?? '') ?>">
                            <div id="map"></div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h3 class="form-section-title">Attachment</h3>
                        <div class="form-group">
                            <input type="file" name="attachment" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text" style="color:var(--text-muted);font-size:0.85rem;margin-top:0.25rem">PDF, JPG or PNG (optional)</div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success" style="width:100%;padding:1rem;font-size:1rem">
                        Submit report
                    </button>
                </form>
            </div>
        </div>
    </main>

    <?php include '../partials/chatbot_widget.php'; ?>
    <script src="<?= $assets ?>theme.js"></script>
    <script src="../assets/validation.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
    // Type selection
    document.querySelectorAll('.type-option').forEach(opt => {
        opt.addEventListener('click', () => {
            document.querySelectorAll('.type-option').forEach(o => o.classList.remove('selected'));
            opt.classList.add('selected');
            document.getElementById('selectedType').value = opt.dataset.type;
            document.getElementById('type-error').textContent = '';
        });
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

    // Form validation
    document.getElementById('reportForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let isValid = true;
        
        if (!document.getElementById('selectedType').value) {
            document.getElementById('type-error').textContent = 'Please select an incident type';
            isValid = false;
        }
        
        const title = document.querySelector('[name="title"]');
        if (title.value.trim().length < 5) {
            Validator.showError(title, 'Title must be at least 5 characters');
            isValid = false;
        } else {
            Validator.showSuccess(title);
        }
        
        const desc = document.querySelector('[name="description"]');
        if (desc.value.trim().length < 20) {
            Validator.showError(desc, 'Description must be at least 20 characters');
            isValid = false;
        } else {
            Validator.showSuccess(desc);
        }
        
        if (isValid) this.submit();
    });

    // Map
    const map = L.map('map').setView([36.8, 10.18], 10);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '(c) OpenStreetMap' }).addTo(map);
    let marker;
    map.on('click', e => {
        if (marker) map.removeLayer(marker);
        marker = L.marker(e.latlng).addTo(map);
        document.getElementById('location').value = e.latlng.lat.toFixed(5) + ', ' + e.latlng.lng.toFixed(5);
    });
    </script>
</body>
</html>

