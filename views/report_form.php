<?php
session_start();
require_once "../controller/reportController.php";
require_once "../controller/mediatorController.php";

$rc = new ReportController();
$mc = new MediatorController();
$mediators = $mc->listMediators();

// ADMIN CAN ONLY EDIT, NOT CREATE - redirect if no ID
if (!isset($_GET['id'])) {
    header("Location: reports.php");
    exit;
}

$report = $rc->getReportById($_GET['id']);

// If report doesn't exist, redirect
if (!$report) {
    header("Location: reports.php");
    exit;
}

$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
$old = isset($_SESSION['old']) ? $_SESSION['old'] : $report;
unset($_SESSION['errors'], $_SESSION['old']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Signalement - PeaceConnect</title>
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
        .form-control.valid { border-color: #10b981; }
        .form-control.invalid { border-color: #ef4444; }
        .error-text { color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; }
        .char-count { color: #64748b; font-size: 0.75rem; margin-top: 0.25rem; }
        
        .alert { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; }
        
        .btn { padding: 0.875rem 1.5rem; border-radius: 8px; font-size: 1rem; font-weight: 600; border: none; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; }
        .btn-primary { background: #2563eb; color: white; }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-secondary { background: #64748b; color: white; }
        
        .picker-container { position: relative; }
        .picker-btn { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: #2563eb; color: white; border: none; padding: 0.5rem; border-radius: 6px; cursor: pointer; }
        .calendar-popup, .map-popup { display: none; position: absolute; top: 100%; left: 0; background: white; border-radius: 12px; padding: 1rem; box-shadow: 0 10px 40px rgba(0,0,0,0.15); z-index: 100; margin-top: 0.5rem; }
        .calendar-popup.show, .map-popup.show { display: block; }
        .map-popup { right: 0; }
        #map { height: 200px; border-radius: 8px; margin-bottom: 1rem; }
        .calendar-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
        .calendar-header button { background: #f1f5f9; border: none; padding: 0.5rem; border-radius: 6px; cursor: pointer; }
        .calendar-days { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; text-align: center; }
        .calendar-days span { padding: 8px; cursor: pointer; border-radius: 6px; }
        .calendar-days span:hover { background: #eff6ff; }
        .calendar-days .day-name { font-weight: 600; color: #64748b; font-size: 0.75rem; cursor: default; }
        .location-btns { display: flex; flex-wrap: wrap; gap: 0.5rem; }
        .location-btns button { padding: 0.5rem 1rem; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 6px; cursor: pointer; }

        /* Custom file box */
        .custom-file-box {
            border: 2px dashed #e2e8f0;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            background: #f9fafb;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
        }
        .custom-file-box:hover {
            border-color: #2563eb;
            background: #eff6ff;
        }
        .custom-file-icon {
            font-size: 1.2rem;
            color: #2563eb;
        }
        .custom-file-label {
            font-size: 0.95rem;
            color: #64748b;
        }
        .file-hidden {
            display: none;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <a href="../index.html" class="navbar-brand">🕊️ PeaceConnect</a>
            <div class="navbar-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="reports.php" class="active">Signalements</a>
                <a href="mediators.php">Médiateurs</a>
                <a href="sessions.php">Séances</a>
                <a href="search.php">Recherche</a>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-container">
            <h1>✏️ Modifier Signalement #<?php echo $report['id']; ?></h1>
        </div>
    </section>

    <div class="container">
        <?php if (!empty($errors)): ?>
        <div class="alert"><strong>⚠️ Erreurs:</strong><ul style="margin:0.5rem 0 0 1.5rem"><?php foreach($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>

        <div class="card">
            <form action="../controller/reportController.php" method="POST" id="reportForm" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?php echo $report['id']; ?>">
                <input type="hidden" name="existing_attachment" value="<?php echo htmlspecialchars($report['attachment_path'] ?? ''); ?>">
                
                <div class="form-group">
                    <label>Type d'incident *</label>
                    <select name="type" id="type" class="form-control">
                        <option value="">-- Sélectionner --</option>
                        <option value="conflict" <?php echo (isset($old['type']) && $old['type']=='conflict')?'selected':''; ?>>Conflit</option>
                        <option value="harassment" <?php echo (isset($old['type']) && $old['type']=='harassment')?'selected':''; ?>>Harcèlement</option>
                        <option value="violence" <?php echo (isset($old['type']) && $old['type']=='violence')?'selected':''; ?>>Violence</option>
                        <option value="discrimination" <?php echo (isset($old['type']) && $old['type']=='discrimination')?'selected':''; ?>>Discrimination</option>
                        <option value="other" <?php echo (isset($old['type']) && $old['type']=='other')?'selected':''; ?>>Autre</option>
                    </select>
                    <div class="error-text" id="type-error"></div>
                </div>
                
                <div class="form-group">
                    <label>Titre *</label>
                    <input type="text" name="title" id="title" class="form-control" placeholder="Minimum 5 caractères" value="<?php echo htmlspecialchars($old['title'] ?? ''); ?>">
                    <div class="char-count" id="title-count">0/100</div>
                    <div class="error-text" id="title-error"></div>
                </div>
                
                <div class="form-group">
                    <label>Description *</label>
                    <textarea name="description" id="description" class="form-control" rows="4" placeholder="Minimum 10 caractères"><?php echo htmlspecialchars($old['description'] ?? ''); ?></textarea>
                    <div class="char-count" id="desc-count">0/500</div>
                    <div class="error-text" id="description-error"></div>
                </div>
                
                <div class="form-group">
                    <label>Lieu</label>
                    <div class="picker-container">
                        <input type="text" name="location" id="location" class="form-control" value="<?php echo htmlspecialchars($old['location'] ?? ''); ?>">
                        <button type="button" class="picker-btn" onclick="toggleMap()">📍</button>
                        <div class="map-popup" id="mapPopup">
                            <div id="map"></div>
                            <div class="location-btns">
                                <button type="button" onclick="setLocation('Tunis')">Tunis</button>
                                <button type="button" onclick="setLocation('Sousse')">Sousse</button>
                                <button type="button" onclick="setLocation('Sfax')">Sfax</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Date de l'incident</label>
                    <div class="picker-container">
                        <input type="text" name="incident_date" id="incident_date" class="form-control" placeholder="AAAA-MM-JJ" value="<?php echo $old['incident_date'] ?? ''; ?>">
                        <button type="button" class="picker-btn" onclick="toggleCalendar()">📅</button>
                        <div class="calendar-popup" id="calendarPopup">
                            <div class="calendar-header"><button type="button" onclick="changeMonth(-1)">◀</button><span id="calendarTitle"></span><button type="button" onclick="changeMonth(1)">▶</button></div>
                            <div class="calendar-days" id="calendarDays"></div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Priorité</label>
                    <select name="priority" class="form-control">
                        <option value="low" <?php echo (isset($old['priority']) && $old['priority']=='low')?'selected':''; ?>>🟢 Basse</option>
                        <option value="medium" <?php echo (!isset($old['priority']) || $old['priority']=='medium')?'selected':''; ?>>🟡 Moyenne</option>
                        <option value="high" <?php echo (isset($old['priority']) && $old['priority']=='high')?'selected':''; ?>>🔴 Haute</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Statut</label>
                    <select name="status" class="form-control">
                        <option value="pending" <?php echo (isset($old['status']) && $old['status']=='pending')?'selected':''; ?>>⏳ En attente</option>
                        <option value="assigned" <?php echo (isset($old['status']) && $old['status']=='assigned')?'selected':''; ?>>👤 Assigné</option>
                        <option value="in_mediation" <?php echo (isset($old['status']) && $old['status']=='in_mediation')?'selected':''; ?>>🤝 En médiation</option>
                        <option value="resolved" <?php echo (isset($old['status']) && $old['status']=='resolved')?'selected':''; ?>>✅ Résolu</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Assigner un médiateur</label>
                    <select name="mediator_id" class="form-control">
                        <option value="">-- Aucun --</option>
                        <?php foreach ($mediators as $m): ?>
                        <option value="<?php echo $m['id']; ?>" <?php echo (isset($old['mediator_id']) && $old['mediator_id']==$m['id'])?'selected':''; ?>><?php echo htmlspecialchars($m['name']); ?> - <?php echo htmlspecialchars($m['expertise']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Pièce jointe (capture, document...)</label>
                    <?php if (!empty($report['attachment_path'])): ?>
                        <p style="margin-bottom:0.5rem;">
                            📎 Fichier actuel :
                            <a href="../<?php echo htmlspecialchars($report['attachment_path']); ?>" target="_blank">Ouvrir la pièce jointe</a>
                        </p>
                    <?php endif; ?>
                    <div class="custom-file-box" id="attachmentBox">
                        <span class="custom-file-icon">📎</span>
                        <span class="custom-file-label" id="attachmentLabel">Cliquer pour choisir un fichier</span>
                    </div>
                    <input type="file" name="attachment" id="attachmentInput" class="file-hidden">
                </div>
                
                <div style="display:flex;gap:1rem;margin-top:2rem">
                    <button type="submit" class="btn btn-primary" style="flex:1">💾 Enregistrer</button>
                    <a href="reports.php" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
    var currentMonth=new Date().getMonth(),currentYear=new Date().getFullYear(),map=null;
    document.addEventListener('DOMContentLoaded',function(){buildCalendar();updateCount('title','title-count',100);updateCount('description','desc-count',500);
    document.getElementById('type').addEventListener('change',vType);
    document.getElementById('title').addEventListener('input',function(){vTitle();updateCount('title','title-count',100);});
    document.getElementById('description').addEventListener('input',function(){vDesc();updateCount('description','desc-count',500);});
    document.getElementById('reportForm').addEventListener('submit',function(e){if(!vType()||!vTitle()||!vDesc()){e.preventDefault();alert('Corrigez les erreurs');}});

    // Gestion de la zone de fichier
    var box = document.getElementById('attachmentBox');
    var input = document.getElementById('attachmentInput');
    var label = document.getElementById('attachmentLabel');
    if (box && input && label) {
        box.addEventListener('click', function() {
            input.click();
        });
        input.addEventListener('change', function() {
            if (input.files && input.files.length > 0) {
                label.textContent = input.files[0].name;
            } else {
                label.textContent = 'Cliquer pour choisir un fichier';
            }
        });
    }});
    function vType(){var e=document.getElementById('type'),r=document.getElementById('type-error');if(e.value===''){e.classList.add('invalid');e.classList.remove('valid');r.textContent='Choisissez un type';return false;}e.classList.add('valid');e.classList.remove('invalid');r.textContent='';return true;}
    function vTitle(){var e=document.getElementById('title'),r=document.getElementById('title-error');if(e.value.trim().length<5){e.classList.add('invalid');e.classList.remove('valid');r.textContent='Min 5 caractères';return false;}e.classList.add('valid');e.classList.remove('invalid');r.textContent='';return true;}
    function vDesc(){var e=document.getElementById('description'),r=document.getElementById('description-error');if(e.value.trim().length<10){e.classList.add('invalid');e.classList.remove('valid');r.textContent='Min 10 caractères';return false;}e.classList.add('valid');e.classList.remove('invalid');r.textContent='';return true;}
    function updateCount(id,cid,max){document.getElementById(cid).textContent=document.getElementById(id).value.length+'/'+max;}
    function toggleCalendar(){document.getElementById('calendarPopup').classList.toggle('show');document.getElementById('mapPopup').classList.remove('show');}
    function toggleMap(){var p=document.getElementById('mapPopup');p.classList.toggle('show');document.getElementById('calendarPopup').classList.remove('show');if(map===null&&p.classList.contains('show')){map=L.map('map').setView([36.8,10.18],8);L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);map.on('click',function(e){document.getElementById('location').value=e.latlng.lat.toFixed(4)+', '+e.latlng.lng.toFixed(4);});}}
    function setLocation(l){document.getElementById('location').value=l;document.getElementById('mapPopup').classList.remove('show');}
    function buildCalendar(){var c=document.getElementById('calendarDays'),mois=['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];document.getElementById('calendarTitle').textContent=mois[currentMonth]+' '+currentYear;c.innerHTML='';['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'].forEach(function(j){var s=document.createElement('span');s.className='day-name';s.textContent=j;c.appendChild(s);});var f=new Date(currentYear,currentMonth,1).getDay(),d=new Date(currentYear,currentMonth+1,0).getDate();for(var i=0;i<f;i++)c.appendChild(document.createElement('span'));for(var day=1;day<=d;day++){var s=document.createElement('span');s.textContent=day;s.onclick=(function(dd){return function(){selectDate(dd);};})(day);c.appendChild(s);}}
    function changeMonth(delta){currentMonth+=delta;if(currentMonth>11){currentMonth=0;currentYear++;}else if(currentMonth<0){currentMonth=11;currentYear--;}buildCalendar();}
    function selectDate(day){var m=(currentMonth+1).toString().padStart(2,'0'),dd=day.toString().padStart(2,'0');document.getElementById('incident_date').value=currentYear+'-'+m+'-'+dd;document.getElementById('calendarPopup').classList.remove('show');}
    document.addEventListener('click',function(e){if(!e.target.closest('.picker-container')){document.getElementById('calendarPopup').classList.remove('show');document.getElementById('mapPopup').classList.remove('show');}});
    </script>
</body>
</html>
