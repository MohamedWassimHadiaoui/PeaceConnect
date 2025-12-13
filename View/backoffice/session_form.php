<?php
include __DIR__ . '/partials/header.php';
require_once __DIR__ . "/../../Controller/sessionController.php";
require_once __DIR__ . "/../../Controller/reportController.php";
require_once __DIR__ . "/../../Controller/mediatorController.php";

$sc = new SessionController();
$rc = new ReportController();
$mc = new MediatorController();

$reports = $rc->listReports();
$mediators = $mc->listMediators();

$isEdit = isset($_GET['id']);
$session = $isEdit ? $sc->getSessionById($_GET['id']) : null;

$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? ($session ?? []);
unset($_SESSION['errors'], $_SESSION['old']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isEdit ? 'Edit' : 'Schedule' ?> Session - PeaceConnect</title>
    <link rel="icon" type="image/svg+xml" href="<?= $assets ?>favicon.svg">
    <link rel="stylesheet" href="<?= $assets ?>style.css?v=<?php echo filemtime(__DIR__ . "/../assets/style.css"); ?>">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <style>
        .popup { display:none; position:absolute; top:calc(100% + 8px); left:0; background:var(--bg-card); backdrop-filter:blur(20px); border:1px solid var(--border-color); border-radius:14px; padding:1rem; z-index:100; min-width:280px; }
        .popup.show { display:block; animation:popIn 0.2s ease; }
        @keyframes popIn { from{opacity:0;transform:translateY(-8px)} to{opacity:1;transform:translateY(0)} }
        .picker-container { position:relative; }
        .picker-btn { position:absolute; right:10px; top:50%; transform:translateY(-50%); background:linear-gradient(135deg,var(--primary),var(--secondary)); color:#fff; border:none; width:38px; height:38px; border-radius:10px; cursor:pointer; font-size:1rem; }
        .picker-btn:hover { transform:translateY(-50%) scale(1.05); }
        .calendar-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; }
        .calendar-header button { background:rgba(255,255,255,0.1); border:none; color:var(--text-primary); width:32px; height:32px; border-radius:8px; cursor:pointer; }
        .calendar-header button:hover { background:var(--primary); }
        .calendar-days { display:grid; grid-template-columns:repeat(7,1fr); gap:4px; text-align:center; }
        .calendar-days span { padding:8px; cursor:pointer; border-radius:8px; font-size:0.85rem; color:var(--text-primary); }
        .calendar-days span:hover { background:rgba(99,102,241,0.2); }
        .calendar-days .day-name { font-weight:600; color:var(--text-muted); font-size:0.7rem; cursor:default; }
        .calendar-days .day-name:hover { background:transparent; }
        .time-select { display:flex; gap:0.75rem; justify-content:center; margin-bottom:1rem; }
        .time-select select { padding:0.5rem; background:var(--bg-input); border:1px solid var(--border-color); border-radius:8px; color:var(--text-primary); }
        #map { height:180px; border-radius:10px; margin-bottom:1rem; }
        .location-btns { display:flex; flex-wrap:wrap; gap:0.5rem; }
        .location-btns button { padding:0.4rem 0.8rem; background:rgba(255,255,255,0.1); border:1px solid var(--border-color); border-radius:8px; color:var(--text-primary); cursor:pointer; font-size:0.8rem; }
        .location-btns button:hover { background:var(--primary); border-color:var(--primary); }
    </style>
</head>
<body>
    <div class="bg-animation"><span></span><span></span><span></span><span></span><span></span><span></span></div>
    <?php include 'partials/navbar.php'; ?>

    <main class="main">
        <div class="container-sm">
            <div class="hero">
                <h1><?= $isEdit ? 'Edit' : 'Schedule' ?> Session</h1>
                <p><?= $isEdit ? 'Update session details' : 'Create a new mediation session' ?></p>
            </div>

            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <div><strong>Errors:</strong><ul style="margin:0.5rem 0 0 1rem"><?php foreach($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
            </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <form action="../../Controller/sessionController.php" method="POST" id="sessionForm" novalidate>
                        <input type="hidden" name="action" value="<?= $isEdit ? 'update' : 'add' ?>">
                        <input type="hidden" name="source" value="backoffice">
                        <?php if ($isEdit): ?><input type="hidden" name="id" value="<?= $session['id'] ?>"><?php endif; ?>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Report *</label>
                                <select name="report_id" id="report_id" class="form-control">
                                    <option value="">-- Select --</option>
                                    <?php foreach ($reports as $r): ?>
                                    <option value="<?= $r['id'] ?>" <?= (($old['report_id'] ?? '')==$r['id'])?'selected':'' ?>>#<?= $r['id'] ?> - <?= htmlspecialchars($r['title']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-error" id="report_id-error"></div>
                            </div>
                            <div class="form-group">
                                <label>Mediator *</label>
                                <select name="mediator_id" id="mediator_id" class="form-control">
                                    <option value="">-- Select --</option>
                                    <?php foreach ($mediators as $m): ?>
                                    <option value="<?= $m['id'] ?>" <?= (($old['mediator_id'] ?? '')==$m['id'])?'selected':'' ?>><?= htmlspecialchars($m['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-error" id="mediator_id-error"></div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Date *</label>
                                <div class="picker-container">
                                    <input type="text" name="session_date" id="session_date" class="form-control" placeholder="YYYY-MM-DD" value="<?= $old['session_date'] ?? '' ?>">
                                    <button type="button" class="picker-btn" onclick="toggleCalendar()">Pick</button>
                                    <div class="popup" id="calendarPopup">
                                        <div class="calendar-header"><button type="button" onclick="changeMonth(-1)">&lt;</button><span id="calendarTitle"></span><button type="button" onclick="changeMonth(1)">&gt;</button></div>
                                        <div class="calendar-days" id="calendarDays"></div>
                                    </div>
                                </div>
                                <div class="form-error" id="session_date-error"></div>
                            </div>
                            <div class="form-group">
                                <label>Time *</label>
                                <div class="picker-container">
                                    <input type="text" name="session_time" id="session_time" class="form-control" placeholder="HH:MM" value="<?= $old['session_time'] ?? '' ?>">
                                    <button type="button" class="picker-btn" onclick="toggleTime()">Pick</button>
                                    <div class="popup" id="timePopup">
                                        <div class="time-select">
                                            <select id="hourSelect"><?php for($h=8;$h<=18;$h++): ?><option value="<?= str_pad($h,2,'0',STR_PAD_LEFT) ?>"><?= str_pad($h,2,'0',STR_PAD_LEFT) ?>h</option><?php endfor; ?></select>
                                            <select id="minuteSelect"><option value="00">00</option><option value="15">15</option><option value="30">30</option><option value="45">45</option></select>
                                        </div>
                                        <button type="button" onclick="setTime()" class="btn btn-primary btn-block">Confirm</button>
                                    </div>
                                </div>
                                <div class="form-error" id="session_time-error"></div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label>Session Type</label>
                                <select name="session_type" class="form-control">
                                    <option value="in_person" <?= (($old['session_type'] ?? '')==='in_person')?'selected':'' ?>>In-person</option>
                                    <option value="online" <?= (($old['session_type'] ?? '')==='online')?'selected':'' ?>>Online</option>
                                </select>
                            </div>
                            <?php if ($isEdit): ?>
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="scheduled" <?= (($old['status'] ?? '')==='scheduled')?'selected':'' ?>>Scheduled</option>
                                    <option value="completed" <?= (($old['status'] ?? '')==='completed')?'selected':'' ?>>Completed</option>
                                    <option value="cancelled" <?= (($old['status'] ?? '')==='cancelled')?'selected':'' ?>>Cancelled</option>
                                </select>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label>Location / Link</label>
                            <div class="picker-container">
                                <input type="text" name="location" id="location" class="form-control" value="<?= htmlspecialchars($old['location'] ?? '') ?>">
                                <button type="button" class="picker-btn" onclick="toggleMap()">Map</button>
                                <div class="popup" id="mapPopup" style="right:0;left:auto">
                                    <div id="map"></div>
                                    <div class="location-btns">
                                        <button type="button" onclick="setLocation('Office Tunis')">Office Tunis</button>
                                        <button type="button" onclick="setLocation('Zoom')">Zoom</button>
                                        <button type="button" onclick="setLocation('Google Meet')">Google Meet</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Additional notes..."><?= htmlspecialchars($old['notes'] ?? '') ?></textarea>
                        </div>

                        <div style="display:flex;gap:1rem;margin-top:2rem">
                            <button type="submit" class="btn btn-primary" style="flex:1"><?= $isEdit ? 'Save' : 'Schedule' ?></button>
                            <a href="sessions.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script src="<?= $assets ?>theme.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
    let currentMonth = new Date().getMonth(), currentYear = new Date().getFullYear(), map = null;

    document.addEventListener('DOMContentLoaded', () => {
        buildCalendar();
        document.getElementById('sessionForm').addEventListener('submit', e => {
            let ok = true;
            ['report_id', 'mediator_id', 'session_date', 'session_time'].forEach(id => {
                const el = document.getElementById(id), err = document.getElementById(id + '-error');
                if (!el.value) { el.classList.add('invalid'); err.textContent = 'Required'; ok = false; }
                else { el.classList.remove('invalid'); err.textContent = ''; }
            });
            if (!ok) { e.preventDefault(); alert('Please fill all required fields'); }
        });
    });

    function toggleCalendar() { document.getElementById('calendarPopup').classList.toggle('show'); document.getElementById('timePopup').classList.remove('show'); document.getElementById('mapPopup').classList.remove('show'); }
    function toggleTime() { document.getElementById('timePopup').classList.toggle('show'); document.getElementById('calendarPopup').classList.remove('show'); document.getElementById('mapPopup').classList.remove('show'); }
    function toggleMap() {
        const p = document.getElementById('mapPopup');
        p.classList.toggle('show');
        document.getElementById('calendarPopup').classList.remove('show');
        document.getElementById('timePopup').classList.remove('show');
        if (!map && p.classList.contains('show')) {
            map = L.map('map').setView([36.8, 10.18], 8);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
            map.on('click', e => { document.getElementById('location').value = e.latlng.lat.toFixed(4) + ', ' + e.latlng.lng.toFixed(4); });
        }
    }
    function setTime() { document.getElementById('session_time').value = document.getElementById('hourSelect').value + ':' + document.getElementById('minuteSelect').value; document.getElementById('timePopup').classList.remove('show'); }
    function setLocation(l) { document.getElementById('location').value = l; document.getElementById('mapPopup').classList.remove('show'); }

    function buildCalendar() {
        const c = document.getElementById('calendarDays');
        const mois = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        document.getElementById('calendarTitle').textContent = mois[currentMonth] + ' ' + currentYear;
        c.innerHTML = '';
        ['Su','Mo','Tu','We','Th','Fr','Sa'].forEach(j => { const s = document.createElement('span'); s.className = 'day-name'; s.textContent = j; c.appendChild(s); });
        const f = new Date(currentYear, currentMonth, 1).getDay();
        const d = new Date(currentYear, currentMonth + 1, 0).getDate();
        for (let i = 0; i < f; i++) c.appendChild(document.createElement('span'));
        for (let day = 1; day <= d; day++) { const s = document.createElement('span'); s.textContent = day; s.onclick = () => selectDate(day); c.appendChild(s); }
    }
    function changeMonth(delta) { currentMonth += delta; if (currentMonth > 11) { currentMonth = 0; currentYear++; } else if (currentMonth < 0) { currentMonth = 11; currentYear--; } buildCalendar(); }
    function selectDate(day) { document.getElementById('session_date').value = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`; document.getElementById('calendarPopup').classList.remove('show'); }
    document.addEventListener('click', e => { if (!e.target.closest('.picker-container')) { document.querySelectorAll('.popup').forEach(p => p.classList.remove('show')); } });
    </script>
</body>
</html>



