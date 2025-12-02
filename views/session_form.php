<?php
session_start();
require_once "../controller/sessionController.php";
require_once "../controller/reportController.php";
require_once "../controller/mediatorController.php";

$sc = new SessionController();
$rc = new ReportController();
$mc = new MediatorController();

$reports = $rc->listReports();
$mediators = $mc->listMediators();

$isEdit = isset($_GET['id']);
$session = $isEdit ? $sc->getSessionById($_GET['id']) : null;

$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
$old = isset($_SESSION['old']) ? $_SESSION['old'] : ($session ? $session : []);
unset($_SESSION['errors'], $_SESSION['old']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo $isEdit ? 'Modifier' : 'Planifier'; ?> Séance - PeaceConnect</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8fafc; }
        
        .navbar { background: white; padding: 1rem 0; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .navbar-container { max-width: 1200px; margin: 0 auto; padding: 0 2rem; display: flex; justify-content: space-between; align-items: center; }
        .navbar-brand { display: flex; align-items: center; gap: 0.5rem; text-decoration: none; font-size: 1.5rem; font-weight: 700; color: #1e293b; }
        .navbar-links { display: flex; gap: 0.5rem; }
        .navbar-links a { padding: 0.5rem 1rem; text-decoration: none; color: #64748b; font-weight: 500; border-radius: 6px; }
        .navbar-links a:hover { color: #2563eb; background: #f1f5f9; }
        .navbar-links a.active { color: #2563eb; background: #eff6ff; }
        
        .hero { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding: 2rem; color: white; }
        .hero-container { max-width: 1200px; margin: 0 auto; }
        .hero h1 { font-size: 1.75rem; }
        
        .container { max-width: 700px; margin: 2rem auto; padding: 0 2rem; }
        
        .card { background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); padding: 2rem; }
        
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: #1e293b; }
        .form-control { width: 100%; padding: 0.875rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 1rem; }
        .form-control:focus { outline: none; border-color: #2563eb; }
        .form-control.invalid { border-color: #ef4444; }
        .error-text { color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; }
        
        .alert { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; }
        
        .btn { padding: 0.875rem 1.5rem; border-radius: 8px; font-size: 1rem; font-weight: 600; border: none; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-primary { background: #2563eb; color: white; }
        .btn-secondary { background: #64748b; color: white; }
        
        .picker-container { position: relative; }
        .picker-btn { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: #2563eb; color: white; border: none; padding: 0.5rem; border-radius: 6px; cursor: pointer; }
        .popup { display: none; position: absolute; top: 100%; left: 0; background: white; border-radius: 12px; padding: 1rem; box-shadow: 0 10px 40px rgba(0,0,0,0.15); z-index: 100; margin-top: 0.5rem; }
        .popup.show { display: block; }
        .calendar-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
        .calendar-header button { background: #f1f5f9; border: none; padding: 0.5rem; border-radius: 6px; cursor: pointer; }
        .calendar-days { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; text-align: center; }
        .calendar-days span { padding: 8px; cursor: pointer; border-radius: 6px; }
        .calendar-days span:hover { background: #eff6ff; }
        .calendar-days .day-name { font-weight: 600; color: #64748b; font-size: 0.75rem; cursor: default; }
        .time-select { display: flex; gap: 1rem; justify-content: center; margin-bottom: 1rem; }
        .time-select select { padding: 0.5rem; border: 1px solid #e2e8f0; border-radius: 6px; }
        #map { height: 200px; border-radius: 8px; margin-bottom: 1rem; }
        .location-btns { display: flex; flex-wrap: wrap; gap: 0.5rem; }
        .location-btns button { padding: 0.5rem 1rem; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 6px; cursor: pointer; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <a href="../index.html" class="navbar-brand">🕊️ PeaceConnect</a>
            <div class="navbar-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="reports.php">Signalements</a>
                <a href="mediators.php">Médiateurs</a>
                <a href="sessions.php" class="active">Séances</a>
                <a href="search.php">Recherche</a>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-container">
            <h1><?php echo $isEdit ? '✏️ Modifier' : '📅 Planifier'; ?> Séance</h1>
        </div>
    </section>

    <div class="container">
        <?php if (!empty($errors)): ?>
        <div class="alert"><strong>⚠️ Erreurs:</strong><ul style="margin:0.5rem 0 0 1.5rem"><?php foreach($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>

        <div class="card">
            <form action="../controller/sessionController.php" method="POST" id="sessionForm">
                <input type="hidden" name="action" value="<?php echo $isEdit ? 'update' : 'add'; ?>">
                <?php if ($isEdit): ?><input type="hidden" name="id" value="<?php echo $session['id']; ?>"><?php endif; ?>
                
                <div class="form-group">
                    <label>Signalement *</label>
                    <select name="report_id" id="report_id" class="form-control">
                        <option value="">-- Sélectionner --</option>
                        <?php foreach ($reports as $r): ?>
                        <option value="<?php echo $r['id']; ?>" <?php echo (isset($old['report_id']) && $old['report_id']==$r['id'])?'selected':''; ?>>#<?php echo $r['id']; ?> - <?php echo htmlspecialchars($r['title']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="error-text" id="report_id-error"></div>
                </div>
                
                <div class="form-group">
                    <label>Médiateur *</label>
                    <select name="mediator_id" id="mediator_id" class="form-control">
                        <option value="">-- Sélectionner --</option>
                        <?php foreach ($mediators as $m): ?>
                        <option value="<?php echo $m['id']; ?>" <?php echo (isset($old['mediator_id']) && $old['mediator_id']==$m['id'])?'selected':''; ?>><?php echo htmlspecialchars($m['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="error-text" id="mediator_id-error"></div>
                </div>
                
                <div class="form-group">
                    <label>Date *</label>
                    <div class="picker-container">
                        <input type="text" name="session_date" id="session_date" class="form-control" placeholder="AAAA-MM-JJ" value="<?php echo isset($old['session_date'])?$old['session_date']:''; ?>">
                        <button type="button" class="picker-btn" onclick="toggleCalendar()">📅</button>
                        <div class="popup" id="calendarPopup">
                            <div class="calendar-header"><button type="button" onclick="changeMonth(-1)">◀</button><span id="calendarTitle"></span><button type="button" onclick="changeMonth(1)">▶</button></div>
                            <div class="calendar-days" id="calendarDays"></div>
                        </div>
                    </div>
                    <div class="error-text" id="session_date-error"></div>
                </div>
                
                <div class="form-group">
                    <label>Heure *</label>
                    <div class="picker-container">
                        <input type="text" name="session_time" id="session_time" class="form-control" placeholder="HH:MM" value="<?php echo isset($old['session_time'])?$old['session_time']:''; ?>">
                        <button type="button" class="picker-btn" onclick="toggleTime()">🕐</button>
                        <div class="popup" id="timePopup">
                            <div class="time-select">
                                <select id="hourSelect"><?php for($h=8;$h<=18;$h++): ?><option value="<?php echo str_pad($h,2,'0',STR_PAD_LEFT); ?>"><?php echo str_pad($h,2,'0',STR_PAD_LEFT); ?>h</option><?php endfor; ?></select>
                                <select id="minuteSelect"><option value="00">00</option><option value="15">15</option><option value="30">30</option><option value="45">45</option></select>
                            </div>
                            <button type="button" onclick="setTime()" class="btn btn-primary" style="width:100%">Confirmer</button>
                        </div>
                    </div>
                    <div class="error-text" id="session_time-error"></div>
                </div>
                
                <div class="form-group">
                    <label>Type de séance</label>
                    <select name="session_type" class="form-control">
                        <option value="in_person" <?php echo (isset($old['session_type']) && $old['session_type']=='in_person')?'selected':''; ?>>🏢 En présentiel</option>
                        <option value="online" <?php echo (isset($old['session_type']) && $old['session_type']=='online')?'selected':''; ?>>💻 En ligne</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Lieu / Lien</label>
                    <div class="picker-container">
                        <input type="text" name="location" id="location" class="form-control" value="<?php echo htmlspecialchars(isset($old['location'])?$old['location']:''); ?>">
                        <button type="button" class="picker-btn" onclick="toggleMap()">📍</button>
                        <div class="popup" id="mapPopup" style="right:0">
                            <div id="map"></div>
                            <div class="location-btns">
                                <button type="button" onclick="setLocation('Bureau Tunis')">Bureau Tunis</button>
                                <button type="button" onclick="setLocation('Zoom')">Zoom</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php if ($isEdit): ?>
                <div class="form-group">
                    <label>Statut</label>
                    <select name="status" class="form-control">
                        <option value="scheduled" <?php echo (isset($old['status']) && $old['status']=='scheduled')?'selected':''; ?>>📅 Planifiée</option>
                        <option value="completed" <?php echo (isset($old['status']) && $old['status']=='completed')?'selected':''; ?>>✅ Terminée</option>
                        <option value="cancelled" <?php echo (isset($old['status']) && $old['status']=='cancelled')?'selected':''; ?>>❌ Annulée</option>
                    </select>
                </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes" class="form-control" rows="3"><?php echo htmlspecialchars(isset($old['notes'])?$old['notes']:''); ?></textarea>
                </div>
                
                <div style="display:flex;gap:1rem;margin-top:2rem">
                    <button type="submit" class="btn btn-primary" style="flex:1"><?php echo $isEdit ? 'Enregistrer' : 'Planifier'; ?></button>
                    <a href="sessions.php" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
    var currentMonth=new Date().getMonth(),currentYear=new Date().getFullYear(),map=null;
    document.addEventListener('DOMContentLoaded',function(){buildCalendar();
    document.getElementById('sessionForm').addEventListener('submit',function(e){var ok=true;
    ['report_id','mediator_id','session_date','session_time'].forEach(function(id){var el=document.getElementById(id),err=document.getElementById(id+'-error');if(el.value===''){el.classList.add('invalid');err.textContent='Champ obligatoire';ok=false;}else{el.classList.remove('invalid');err.textContent='';}});
    if(!ok){e.preventDefault();alert('Remplissez tous les champs obligatoires');}});});
    function toggleCalendar(){document.getElementById('calendarPopup').classList.toggle('show');document.getElementById('timePopup').classList.remove('show');document.getElementById('mapPopup').classList.remove('show');}
    function toggleTime(){document.getElementById('timePopup').classList.toggle('show');document.getElementById('calendarPopup').classList.remove('show');document.getElementById('mapPopup').classList.remove('show');}
    function toggleMap(){var p=document.getElementById('mapPopup');p.classList.toggle('show');document.getElementById('calendarPopup').classList.remove('show');document.getElementById('timePopup').classList.remove('show');if(map===null&&p.classList.contains('show')){map=L.map('map').setView([36.8,10.18],8);L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);map.on('click',function(e){document.getElementById('location').value=e.latlng.lat.toFixed(4)+', '+e.latlng.lng.toFixed(4);});}}
    function setTime(){document.getElementById('session_time').value=document.getElementById('hourSelect').value+':'+document.getElementById('minuteSelect').value;document.getElementById('timePopup').classList.remove('show');}
    function setLocation(l){document.getElementById('location').value=l;document.getElementById('mapPopup').classList.remove('show');}
    function buildCalendar(){var c=document.getElementById('calendarDays'),mois=['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];document.getElementById('calendarTitle').textContent=mois[currentMonth]+' '+currentYear;c.innerHTML='';['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'].forEach(function(j){var s=document.createElement('span');s.className='day-name';s.textContent=j;c.appendChild(s);});var f=new Date(currentYear,currentMonth,1).getDay(),d=new Date(currentYear,currentMonth+1,0).getDate();for(var i=0;i<f;i++)c.appendChild(document.createElement('span'));for(var day=1;day<=d;day++){var s=document.createElement('span');s.textContent=day;s.onclick=(function(dd){return function(){selectDate(dd);};})(day);c.appendChild(s);}}
    function changeMonth(delta){currentMonth+=delta;if(currentMonth>11){currentMonth=0;currentYear++;}else if(currentMonth<0){currentMonth=11;currentYear--;}buildCalendar();}
    function selectDate(day){var m=(currentMonth+1).toString().padStart(2,'0'),dd=day.toString().padStart(2,'0');document.getElementById('session_date').value=currentYear+'-'+m+'-'+dd;document.getElementById('calendarPopup').classList.remove('show');}
    document.addEventListener('click',function(e){if(!e.target.closest('.picker-container')){document.querySelectorAll('.popup').forEach(function(p){p.classList.remove('show');});}});
    </script>
</body>
</html>
