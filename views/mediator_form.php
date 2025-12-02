<?php
session_start();
require_once "../controller/mediatorController.php";

$mc = new MediatorController();
$isEdit = isset($_GET['id']);
$mediator = $isEdit ? $mc->getMediatorById($_GET['id']) : null;

$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
$old = isset($_SESSION['old']) ? $_SESSION['old'] : ($mediator ? $mediator : []);
unset($_SESSION['errors'], $_SESSION['old']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo $isEdit ? 'Modifier' : 'Ajouter'; ?> Médiateur - PeaceConnect</title>
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
        
        .container { max-width: 600px; margin: 2rem auto; padding: 0 2rem; }
        
        .card { background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); padding: 2rem; }
        
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: #1e293b; }
        .form-control { width: 100%; padding: 0.875rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 1rem; }
        .form-control:focus { outline: none; border-color: #2563eb; }
        .form-control.valid { border-color: #10b981; }
        .form-control.invalid { border-color: #ef4444; }
        .error-text { color: #ef4444; font-size: 0.875rem; margin-top: 0.25rem; }
        
        .alert { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; }
        
        .btn { padding: 0.875rem 1.5rem; border-radius: 8px; font-size: 1rem; font-weight: 600; border: none; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-primary { background: #2563eb; color: white; }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-secondary { background: #64748b; color: white; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <a href="../index.html" class="navbar-brand">🕊️ PeaceConnect</a>
            <div class="navbar-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="reports.php">Signalements</a>
                <a href="mediators.php" class="active">Médiateurs</a>
                <a href="sessions.php">Séances</a>
                <a href="search.php">Recherche</a>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-container">
            <h1><?php echo $isEdit ? '✏️ Modifier' : '👤 Nouveau'; ?> Médiateur</h1>
        </div>
    </section>

    <div class="container">
        <?php if (!empty($errors)): ?>
        <div class="alert"><strong>⚠️ Erreurs:</strong><ul style="margin:0.5rem 0 0 1.5rem"><?php foreach($errors as $e): ?><li><?php echo htmlspecialchars($e); ?></li><?php endforeach; ?></ul></div>
        <?php endif; ?>

        <div class="card">
            <form action="../controller/mediatorController.php" method="POST" id="mediatorForm">
                <input type="hidden" name="action" value="<?php echo $isEdit ? 'update' : 'add'; ?>">
                <?php if ($isEdit): ?><input type="hidden" name="id" value="<?php echo $mediator['id']; ?>"><?php endif; ?>
                
                <div class="form-group">
                    <label>Nom complet *</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Minimum 3 caractères" value="<?php echo htmlspecialchars(isset($old['name'])?$old['name']:''); ?>">
                    <div class="error-text" id="name-error"></div>
                </div>
                
                <div class="form-group">
                    <label>Email *</label>
                    <input type="text" name="email" id="email" class="form-control" placeholder="exemple@email.com" value="<?php echo htmlspecialchars(isset($old['email'])?$old['email']:''); ?>">
                    <div class="error-text" id="email-error"></div>
                </div>
                
                <div class="form-group">
                    <label>Téléphone</label>
                    <input type="text" name="phone" id="phone" class="form-control" placeholder="+216 XX XXX XXX" value="<?php echo htmlspecialchars(isset($old['phone'])?$old['phone']:''); ?>">
                    <div class="error-text" id="phone-error"></div>
                </div>
                
                <div class="form-group">
                    <label>Expertise *</label>
                    <input type="text" name="expertise" id="expertise" class="form-control" placeholder="Ex: Médiation familiale" value="<?php echo htmlspecialchars(isset($old['expertise'])?$old['expertise']:''); ?>">
                    <div class="error-text" id="expertise-error"></div>
                </div>
                
                <div class="form-group">
                    <label>Disponibilité</label>
                    <select name="availability" class="form-control">
                        <option value="available" <?php echo (!isset($old['availability']) || $old['availability']=='available')?'selected':''; ?>>🟢 Disponible</option>
                        <option value="busy" <?php echo (isset($old['availability']) && $old['availability']=='busy')?'selected':''; ?>>🔴 Occupé</option>
                    </select>
                </div>
                
                <div style="display:flex;gap:1rem;margin-top:2rem">
                    <button type="submit" class="btn btn-primary" style="flex:1"><?php echo $isEdit ? 'Enregistrer' : 'Ajouter'; ?></button>
                    <a href="mediators.php" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded',function(){
        document.getElementById('name').addEventListener('input',vName);
        document.getElementById('email').addEventListener('input',vEmail);
        document.getElementById('expertise').addEventListener('input',vExp);
        document.getElementById('mediatorForm').addEventListener('submit',function(e){if(!vName()||!vEmail()||!vExp()){e.preventDefault();alert('Corrigez les erreurs');}});
    });
    function vName(){var e=document.getElementById('name'),r=document.getElementById('name-error');if(e.value.trim().length<3){e.classList.add('invalid');e.classList.remove('valid');r.textContent='Min 3 caractères';return false;}e.classList.add('valid');e.classList.remove('invalid');r.textContent='';return true;}
    function vEmail(){var e=document.getElementById('email'),r=document.getElementById('email-error'),regex=/^[^\s@]+@[^\s@]+\.[^\s@]+$/;if(!regex.test(e.value)){e.classList.add('invalid');e.classList.remove('valid');r.textContent='Email invalide';return false;}e.classList.add('valid');e.classList.remove('invalid');r.textContent='';return true;}
    function vExp(){var e=document.getElementById('expertise'),r=document.getElementById('expertise-error');if(e.value.trim().length<3){e.classList.add('invalid');e.classList.remove('valid');r.textContent='Min 3 caractères';return false;}e.classList.add('valid');e.classList.remove('invalid');r.textContent='';return true;}
    </script>
</body>
</html>
