<?php
session_start();
$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
$old = isset($_SESSION['old']) ? $_SESSION['old'] : [];
unset($_SESSION['errors'], $_SESSION['old']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un signalement - PeaceConnect</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8fafc; }
        
        /* Navbar */
        .navbar {
            background: white;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .navbar-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
        }
        .navbar-links { display: flex; gap: 0.5rem; }
        .navbar-links a {
            padding: 0.5rem 1rem;
            text-decoration: none;
            color: #64748b;
            font-weight: 500;
            border-radius: 6px;
        }
        .navbar-links a:hover { color: #2563eb; background: #f1f5f9; }
        .navbar-links a.active { color: #2563eb; background: #eff6ff; }
        
        /* Hero */
        .hero {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            padding: 3rem 2rem;
            text-align: center;
            color: white;
        }
        .hero h1 { font-size: 2rem; margin-bottom: 0.5rem; }
        .hero p { opacity: 0.9; }
        
        /* Container */
        .container {
            max-width: 700px;
            margin: -2rem auto 3rem;
            padding: 0 1rem;
            position: relative;
        }
        
        /* Card */
        .card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }
        
        /* Form */
        .form-group { margin-bottom: 1.5rem; }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #1e293b;
        }
        .form-control {
            width: 100%;
            padding: 0.875rem;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.2s;
        }
        .form-control:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }
        .form-control.valid { border-color: #10b981; }
        .form-control.invalid { border-color: #ef4444; }
        .error-text { color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; }
        .char-count { color: #64748b; font-size: 0.75rem; margin-top: 0.25rem; }
        .help-text { color: #64748b; font-size: 0.875rem; margin-top: 0.25rem; }
        
        /* Alert */
        .alert {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        
        /* Buttons */
        .btn {
            padding: 0.875rem 1.5rem;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }
        .btn-primary {
            background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
            color: white;
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(37,99,235,0.3); }
        .btn-secondary { background: #64748b; color: white; }
        
        /* File input */
        input[type="file"].form-control {
            padding: 0.6rem;
            background: #f9fafb;
        }
        
        /* Picker */
        .picker-container { position: relative; }
        .picker-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: #2563eb;
            color: white;
            border: none;
            padding: 0.5rem;
            border-radius: 6px;
            cursor: pointer;
        }
        .picker-btn:hover { background: #1d4ed8; }
        
        /* Calendar */
        .calendar-popup {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background: white;
            border-radius: 12px;
            padding: 1rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            z-index: 100;
            margin-top: 0.5rem;
        }
        .calendar-popup.show { display: block; }
        .calendar-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
        .calendar-header button { background: #f1f5f9; border: none; padding: 0.5rem; border-radius: 6px; cursor: pointer; }
        .calendar-days { display: grid; grid-template-columns: repeat(7, 1fr); gap: 4px; text-align: center; }
        .calendar-days span { padding: 8px; cursor: pointer; border-radius: 6px; }
        .calendar-days span:hover { background: #eff6ff; }
        .calendar-days .day-name { font-weight: 600; color: #64748b; font-size: 0.75rem; cursor: default; }
        .calendar-days .day-name:hover { background: transparent; }
        
        /* Map */
        .map-popup {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border-radius: 12px;
            padding: 1rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            z-index: 100;
            margin-top: 0.5rem;
        }
        .map-popup.show { display: block; }
        #map { height: 200px; border-radius: 8px; margin-bottom: 1rem; }
        .location-btns { display: flex; flex-wrap: wrap; gap: 0.5rem; }
        .location-btns button {
            padding: 0.5rem 1rem;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            cursor: pointer;
        }
        .location-btns button:hover { background: #e2e8f0; }
        
        /* Info Box */
        .info-box {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border: 1px solid #a7f3d0;
            border-radius: 12px;
            padding: 1.5rem;
        }
        .info-box h3 { color: #065f46; margin-bottom: 0.5rem; }
        .info-box p { color: #047857; font-size: 0.875rem; }

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
    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-container">
            <a href="../index.html" class="navbar-brand">🕊️ PeaceConnect</a>
            <div class="navbar-links">
                <a href="../index.html">Accueil</a>
                <a href="create_report.php" class="active">Signaler</a>
                <a href="my_reports.php">Mes signalements</a>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero">
        <h1>📢 Créer un signalement</h1>
        <p>Décrivez la situation que vous souhaitez signaler</p>
    </section>

    <!-- Form -->
    <div class="container">
        <?php if (!empty($errors)): ?>
        <div class="alert">
            <strong>⚠️ Erreurs:</strong>
            <ul style="margin: 0.5rem 0 0 1.5rem;">
                <?php foreach ($errors as $err): ?><li><?php echo htmlspecialchars($err); ?></li><?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <div class="card">
            <form action="../controller/reportController.php" method="POST" id="reportForm" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="source" value="frontoffice">
                
                <div class="form-group">
                    <label>Type d'incident *</label>
                    <select name="type" id="type" class="form-control">
                        <option value="">-- Choisir le type --</option>
                        <option value="conflict" <?php echo (isset($old['type']) && $old['type']=='conflict') ? 'selected' : ''; ?>>⚡ Conflit</option>
                        <option value="harassment" <?php echo (isset($old['type']) && $old['type']=='harassment') ? 'selected' : ''; ?>>⚠️ Harcèlement</option>
                        <option value="violence" <?php echo (isset($old['type']) && $old['type']=='violence') ? 'selected' : ''; ?>>🚨 Violence</option>
                        <option value="discrimination" <?php echo (isset($old['type']) && $old['type']=='discrimination') ? 'selected' : ''; ?>>❌ Discrimination</option>
                        <option value="other" <?php echo (isset($old['type']) && $old['type']=='other') ? 'selected' : ''; ?>>📋 Autre</option>
                    </select>
                    <div class="error-text" id="type-error"></div>
                </div>
                
                <div class="form-group">
                    <label>Titre du signalement *</label>
                    <input type="text" name="title" id="title" class="form-control" placeholder="Résumez la situation en quelques mots" value="<?php echo htmlspecialchars(isset($old['title']) ? $old['title'] : ''); ?>">
                    <div class="char-count" id="title-count">0/100 caractères</div>
                    <div class="error-text" id="title-error"></div>
                </div>
                
                <div class="form-group">
                    <label>Description détaillée *</label>
                    <textarea name="description" id="description" class="form-control" rows="5" placeholder="Décrivez la situation en détail..."><?php echo htmlspecialchars(isset($old['description']) ? $old['description'] : ''); ?></textarea>
                    <div class="char-count" id="desc-count">0/500 caractères</div>
                    <div class="error-text" id="description-error"></div>
                </div>
                
                <div class="form-group">
                    <label>Lieu de l'incident</label>
                    <div class="picker-container">
                        <input type="text" name="location" id="location" class="form-control" placeholder="Cliquez sur 📍 pour choisir" value="<?php echo htmlspecialchars(isset($old['location']) ? $old['location'] : ''); ?>">
                        <button type="button" class="picker-btn" onclick="toggleMap()">📍</button>
                        <div class="map-popup" id="mapPopup">
                            <div id="map"></div>
                            <div class="location-btns">
                                <button type="button" onclick="setLocation('Tunis')">Tunis</button>
                                <button type="button" onclick="setLocation('Sousse')">Sousse</button>
                                <button type="button" onclick="setLocation('Sfax')">Sfax</button>
                                <button type="button" onclick="setLocation('Ariana')">Ariana</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Date de l'incident</label>
                    <div class="picker-container">
                        <input type="text" name="incident_date" id="incident_date" class="form-control" placeholder="Cliquez sur 📅 pour choisir" value="<?php echo isset($old['incident_date']) ? $old['incident_date'] : ''; ?>">
                        <button type="button" class="picker-btn" onclick="toggleCalendar()">📅</button>
                        <div class="calendar-popup" id="calendarPopup">
                            <div class="calendar-header">
                                <button type="button" onclick="changeMonth(-1)">◀</button>
                                <span id="calendarTitle"></span>
                                <button type="button" onclick="changeMonth(1)">▶</button>
                            </div>
                            <div class="calendar-days" id="calendarDays"></div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Pièce jointe (capture, document...)</label>
                    <div class="custom-file-box" id="attachmentBox">
                        <span class="custom-file-icon">📎</span>
                        <span class="custom-file-label" id="attachmentLabel">Cliquer pour choisir un fichier</span>
                    </div>
                    <input type="file" name="attachment" id="attachmentInput" class="file-hidden">
                    <div class="help-text">Optionnel – taille max conseillée: 2 Mo (images ou PDF).</div>
                </div>
                
                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">📤 Soumettre le signalement</button>
                    <a href="../index.html" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
        
        <div class="info-box">
            <h3>🔒 Vos données sont protégées</h3>
            <p>Toutes les informations sont confidentielles. Un médiateur qualifié sera assigné à votre cas.</p>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
    var currentMonth = new Date().getMonth();
    var currentYear = new Date().getFullYear();
    var map = null;
    
    document.addEventListener('DOMContentLoaded', function() {
        buildCalendar();
        updateCharCount('title', 'title-count', 100);
        updateCharCount('description', 'desc-count', 500);
        
        document.getElementById('type').addEventListener('change', validateType);
        document.getElementById('title').addEventListener('input', function() { validateTitle(); updateCharCount('title', 'title-count', 100); });
        document.getElementById('description').addEventListener('input', function() { validateDescription(); updateCharCount('description', 'desc-count', 500); });
        
        document.getElementById('reportForm').addEventListener('submit', function(e) {
            if (!validateType() || !validateTitle() || !validateDescription()) {
                e.preventDefault();
                alert('Veuillez corriger les erreurs');
            }
        });

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
        }
    });
    
    function validateType() {
        var el = document.getElementById('type');
        var err = document.getElementById('type-error');
        if (el.value === '') { el.classList.add('invalid'); el.classList.remove('valid'); err.textContent = 'Choisissez un type'; return false; }
        el.classList.add('valid'); el.classList.remove('invalid'); err.textContent = ''; return true;
    }
    function validateTitle() {
        var el = document.getElementById('title');
        var err = document.getElementById('title-error');
        if (el.value.trim().length < 5) { el.classList.add('invalid'); el.classList.remove('valid'); err.textContent = 'Minimum 5 caractères'; return false; }
        el.classList.add('valid'); el.classList.remove('invalid'); err.textContent = ''; return true;
    }
    function validateDescription() {
        var el = document.getElementById('description');
        var err = document.getElementById('description-error');
        if (el.value.trim().length < 10) { el.classList.add('invalid'); el.classList.remove('valid'); err.textContent = 'Minimum 10 caractères'; return false; }
        el.classList.add('valid'); el.classList.remove('invalid'); err.textContent = ''; return true;
    }
    function updateCharCount(id, countId, max) {
        var el = document.getElementById(id);
        document.getElementById(countId).textContent = el.value.length + '/' + max + ' caractères';
    }
    
    function toggleCalendar() {
        document.getElementById('calendarPopup').classList.toggle('show');
        document.getElementById('mapPopup').classList.remove('show');
    }
    function toggleMap() {
        var popup = document.getElementById('mapPopup');
        popup.classList.toggle('show');
        document.getElementById('calendarPopup').classList.remove('show');
        if (map === null && popup.classList.contains('show')) {
            map = L.map('map').setView([36.8, 10.18], 8);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
            map.on('click', function(e) { document.getElementById('location').value = e.latlng.lat.toFixed(4) + ', ' + e.latlng.lng.toFixed(4); });
        }
    }
    function setLocation(lieu) { document.getElementById('location').value = lieu; document.getElementById('mapPopup').classList.remove('show'); }
    
    function buildCalendar() {
        var container = document.getElementById('calendarDays');
        var mois = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
        document.getElementById('calendarTitle').textContent = mois[currentMonth] + ' ' + currentYear;
        container.innerHTML = '';
        ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'].forEach(function(j) { var s = document.createElement('span'); s.className = 'day-name'; s.textContent = j; container.appendChild(s); });
        var firstDay = new Date(currentYear, currentMonth, 1).getDay();
        var daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
        for (var i = 0; i < firstDay; i++) container.appendChild(document.createElement('span'));
        for (var d = 1; d <= daysInMonth; d++) { var s = document.createElement('span'); s.textContent = d; s.onclick = (function(day) { return function() { selectDate(day); }; })(d); container.appendChild(s); }
    }
    function changeMonth(delta) { currentMonth += delta; if (currentMonth > 11) { currentMonth = 0; currentYear++; } else if (currentMonth < 0) { currentMonth = 11; currentYear--; } buildCalendar(); }
    function selectDate(day) {
        var m = (currentMonth + 1).toString().padStart(2, '0');
        var d = day.toString().padStart(2, '0');
        document.getElementById('incident_date').value = currentYear + '-' + m + '-' + d;
        document.getElementById('calendarPopup').classList.remove('show');
    }
    
    document.addEventListener('click', function(e) { if (!e.target.closest('.picker-container')) { document.getElementById('calendarPopup').classList.remove('show'); document.getElementById('mapPopup').classList.remove('show'); } });
    </script>
</body>
</html>
