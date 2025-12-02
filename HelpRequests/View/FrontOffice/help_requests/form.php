<?php
/**
 * View/FrontOffice/help_requests/form.php
 * Formulaire de création/édition de demande d'aide
 */
$isEdit = isset($request) && $request;
$formTitle = $isEdit ? 'Éditer la demande' : 'Créer une demande d\'aide';
$submitText = $isEdit ? 'Mettre à jour' : 'Créer';
$error = isset($error) ? $error : null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($formTitle) ?></title>
    <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
    <div class="container">
        <header>
            <h1>🕊️ PeaceConnect</h1>
            <nav>
                <a href="index.php">Accueil</a> |
                <a href="index.php?action=help-requests">Demandes d'aide</a>
            </nav>
        </header>

        <main>
            <section class="form-section">
                <h2><?= htmlspecialchars($formTitle) ?></h2>

                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <strong>Erreur :</strong> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="help-request-form">
                    <div class="form-group">
                        <label for="help_type">Type d'aide *</label>
                        <input type="text" id="help_type" name="help_type" required 
                               value="<?= htmlspecialchars($request['help_type'] ?? '') ?>" />
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="urgency_level">Niveau d'urgence *</label>
                            <select id="urgency_level" name="urgency_level" required>
                                <option value="">-- Sélectionner --</option>
                                <option value="basse" <?= (($request['urgency_level'] ?? '') === 'basse') ? 'selected' : '' ?>>Basse</option>
                                <option value="moyenne" <?= (($request['urgency_level'] ?? '') === 'moyenne') ? 'selected' : '' ?>>Moyenne</option>
                                <option value="haute" <?= (($request['urgency_level'] ?? '') === 'haute') ? 'selected' : '' ?>>Haute</option>
                                <option value="critique" <?= (($request['urgency_level'] ?? '') === 'critique') ? 'selected' : '' ?>>Critique</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="location">Localisation</label>
                            <input type="text" id="location" name="location" 
                                   value="<?= htmlspecialchars($request['location'] ?? '') ?>" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="situation">Description de la situation *</label>
                        <textarea id="situation" name="situation" required rows="5"><?= htmlspecialchars($request['situation'] ?? '') ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="contact_method">Méthode de contact</label>
                            <input type="text" id="contact_method" name="contact_method" 
                                   value="<?= htmlspecialchars($request['contact_method'] ?? '') ?>" />
                        </div>

                        <div class="form-group">
                            <label for="responsable">Responsable</label>
                            <input type="text" id="responsable" name="responsable" 
                                   value="<?= htmlspecialchars($request['responsable'] ?? '') ?>" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="status">Statut</label>
                        <select id="status" name="status">
                            <option value="en_attente" <?= (($request['status'] ?? 'en_attente') === 'en_attente') ? 'selected' : '' ?>>En attente</option>
                            <option value="en_cours" <?= (($request['status'] ?? '') === 'en_cours') ? 'selected' : '' ?>>En cours</option>
                            <option value="resolue" <?= (($request['status'] ?? '') === 'resolue') ? 'selected' : '' ?>>Résolue</option>
                            <option value="fermee" <?= (($request['status'] ?? '') === 'fermee') ? 'selected' : '' ?>>Fermée</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary"><?= htmlspecialchars($submitText) ?></button>
                        <a href="index.php?action=help-requests" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>
            </section>
        </main>

        <footer>
            <p>&copy; 2025 PeaceConnect</p>
        </footer>
    </div>
</body>
</html>

