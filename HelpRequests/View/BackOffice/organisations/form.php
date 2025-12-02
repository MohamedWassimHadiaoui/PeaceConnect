<?php
/**
 * View/BackOffice/organisations/form.php
 * Formulaire de création/édition d'organisation (Admin)
 */
$isEdit = isset($organisation) && $organisation;
$formTitle = $isEdit ? 'Éditer l\'organisation' : 'Créer une nouvelle organisation';
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
    <style>
        body {
            display: grid;
            grid-template-columns: 250px 1fr;
            grid-template-rows: 70px 1fr;
            min-height: 100vh;
        }
        .sidebar {
            grid-column: 1;
            grid-row: 1 / 3;
            background: var(--color-text-dark);
            color: var(--color-bg-white);
            padding: var(--spacing-lg) 0;
            position: fixed;
            width: 250px;
            height: 100vh;
            overflow-y: auto;
            z-index: 999;
        }
        .sidebar-header {
            padding: var(--spacing-lg);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: var(--spacing-lg);
        }
        .sidebar-logo {
            font-size: 1.25rem;
            font-weight: 800;
            margin-bottom: var(--spacing-sm);
        }
        .sidebar-user {
            font-size: 0.875rem;
            color: var(--color-text-light);
        }
        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
            padding: var(--spacing-md) var(--spacing-lg);
            color: var(--color-text-light);
            text-decoration: none;
            transition: all var(--duration-base) var(--ease-smooth);
            border-left: 3px solid transparent;
        }
        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background: rgba(255, 255, 255, 0.1);
            color: var(--color-bg-white);
            border-left-color: var(--color-primary);
        }
        .sidebar-nav a .icon {
            font-size: 1.25rem;
            width: 24px;
        }
        header {
            grid-column: 2;
            grid-row: 1;
            background: var(--color-bg-white);
            border-bottom: 1px solid var(--color-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 var(--spacing-lg);
            margin-left: 250px;
        }
        header h1 {
            font-size: 1.5rem;
            margin: 0;
        }
        main {
            grid-column: 2;
            grid-row: 2;
            margin-left: 250px;
            padding: var(--spacing-2xl);
            overflow-y: auto;
        }
        .form-card {
            background: var(--color-bg-white);
            border-radius: var(--radius-2xl);
            padding: var(--spacing-2xl);
            box-shadow: var(--shadow-lg);
        }
        .form-card h2 {
            margin-bottom: var(--spacing-2xl);
        }
        .form-actions {
            display: flex;
            gap: var(--spacing-md);
            margin-top: var(--spacing-2xl);
            padding-top: var(--spacing-2xl);
            border-top: 1px solid var(--color-border);
        }
        @media (max-width: 768px) {
            body {
                grid-template-columns: 1fr;
                grid-template-rows: 70px auto 1fr;
            }
            .sidebar {
                grid-column: 1;
                grid-row: 2;
                width: 100%;
                height: auto;
                position: relative;
                display: none;
            }
            .sidebar.active {
                display: block;
            }
            header {
                margin-left: 0;
                grid-column: 1;
            }
            main {
                margin-left: 0;
                grid-column: 1;
                padding: var(--spacing-lg);
            }
            .form-actions {
                flex-direction: column;
            }
            .hamburger {
                display: block;
                background: none;
                border: none;
                cursor: pointer;
                font-size: 1.5rem;
            }
        }
        .hamburger {
            display: none;
        }
    </style>
</head>
<body>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">🕊️ Admin</div>
            <div class="sidebar-user">Administrateur</div>
        </div>
        <nav>
            <ul class="sidebar-nav">
                <li><a href="index.php?action=dashboard">
                    <span class="icon">📊</span>
                    <span>Dashboard</span>
                </a></li>
                <li><a href="index.php?action=organisations&section=backoffice" class="active">
                    <span class="icon">🏢</span>
                    <span>Organisations</span>
                </a></li>
                <li><a href="index.php?action=help-requests&section=backoffice">
                    <span class="icon">📋</span>
                    <span>Demandes d'aide</span>
                </a></li>
                <li style="margin-top: var(--spacing-2xl); border-top: 1px solid rgba(255,255,255,0.1); padding-top: var(--spacing-xl);"><a href="index.php?action=logout" style="color: var(--color-danger);">
                    <span class="icon">🚪</span>
                    <span>Déconnexion</span>
                </a></li>
            </ul>
        </nav>
    </aside>
    <header>
        <button class="hamburger" id="hamburger">☰</button>
        <h1><?= htmlspecialchars($formTitle) ?></h1>
        <div></div>
    </header>
    <main>
        <div class="form-card">
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <strong>Erreur :</strong> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Nom *</label>
                        <input type="text" id="name" name="name" required 
                               value="<?= htmlspecialchars($organisation['name'] ?? '') ?>" />
                    </div>
                    <div class="form-group">
                        <label for="sector">Secteur</label>
                        <input type="text" id="sector" name="sector" 
                               value="<?= htmlspecialchars($organisation['sector'] ?? '') ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4"><?= htmlspecialchars($organisation['description'] ?? '') ?></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" 
                               value="<?= htmlspecialchars($organisation['email'] ?? '') ?>" />
                    </div>
                    <div class="form-group">
                        <label for="phone">Téléphone</label>
                        <input type="tel" id="phone" name="phone" 
                               value="<?= htmlspecialchars($organisation['phone'] ?? '') ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label for="website">Site Web</label>
                    <input type="url" id="website" name="website" 
                           value="<?= htmlspecialchars($organisation['website'] ?? '') ?>" />
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="address">Adresse</label>
                        <input type="text" id="address" name="address" 
                               value="<?= htmlspecialchars($organisation['address'] ?? '') ?>" />
                    </div>
                    <div class="form-group">
                        <label for="city">Ville</label>
                        <input type="text" id="city" name="city" 
                               value="<?= htmlspecialchars($organisation['city'] ?? '') ?>" />
                    </div>
                </div>
                <div class="form-group">
                    <label for="status">Statut</label>
                    <select id="status" name="status">
                        <option value="active" <?= (($organisation['status'] ?? 'active') === 'active') ? 'selected' : '' ?>>Actif</option>
                        <option value="inactive" <?= (($organisation['status'] ?? '') === 'inactive') ? 'selected' : '' ?>>Inactif</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><?= htmlspecialchars($submitText) ?></button>
                    <a href="index.php?action=organisations&section=backoffice" class="btn btn-outline">Annuler</a>
                </div>
            </form>
        </div>
    </main>
    <script>
        const hamburger = document.getElementById('hamburger');
        const sidebar = document.getElementById('sidebar');
        hamburger.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
    </script>
</body>
</html>
                    <div class="form-group">
                        <label for="name">Nom de l'organisation *</label>
                        <input type="text" id="name" name="name" required 
                               value="<?= htmlspecialchars($organisation['name'] ?? '') ?>" />
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="acronym">Acronyme</label>
                            <input type="text" id="acronym" name="acronym" 
                                   value="<?= htmlspecialchars($organisation['acronym'] ?? '') ?>" />
                        </div>

                        <div class="form-group">
                            <label for="category">Catégorie</label>
                            <input type="text" id="category" name="category" 
                                   value="<?= htmlspecialchars($organisation['category'] ?? '') ?>" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="4"><?= htmlspecialchars($organisation['description'] ?? '') ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" 
                                   value="<?= htmlspecialchars($organisation['email'] ?? '') ?>" />
                        </div>

                        <div class="form-group">
                            <label for="phone">Téléphone</label>
                            <input type="tel" id="phone" name="phone" 
                                   value="<?= htmlspecialchars($organisation['phone'] ?? '') ?>" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="website">Site Web</label>
                        <input type="url" id="website" name="website" 
                               value="<?= htmlspecialchars($organisation['website'] ?? '') ?>" />
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="address">Adresse</label>
                            <input type="text" id="address" name="address" 
                                   value="<?= htmlspecialchars($organisation['address'] ?? '') ?>" />
                        </div>

                        <div class="form-group">
                            <label for="city">Ville</label>
                            <input type="text" id="city" name="city" 
                                   value="<?= htmlspecialchars($organisation['city'] ?? '') ?>" />
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="postal_code">Code Postal</label>
                            <input type="text" id="postal_code" name="postal_code" 
                                   value="<?= htmlspecialchars($organisation['postal_code'] ?? '') ?>" />
                        </div>

                        <div class="form-group">
                            <label for="country">Pays</label>
                            <input type="text" id="country" name="country" 
                                   value="<?= htmlspecialchars($organisation['country'] ?? '') ?>" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="logo_path">Chemin du Logo</label>
                        <input type="text" id="logo_path" name="logo_path" 
                               value="<?= htmlspecialchars($organisation['logo_path'] ?? '') ?>" />
                    </div>

                    <div class="form-group">
                        <label for="mission">Mission</label>
                        <textarea id="mission" name="mission" rows="3"><?= htmlspecialchars($organisation['mission'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="vision">Vision</label>
                        <textarea id="vision" name="vision" rows="3"><?= htmlspecialchars($organisation['vision'] ?? '') ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="status">Statut</label>
                            <select id="status" name="status">
                                <option value="active" <?= (($organisation['status'] ?? 'active') === 'active') ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= (($organisation['status'] ?? '') === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                                <option value="suspended" <?= (($organisation['status'] ?? '') === 'suspended') ? 'selected' : '' ?>>Suspendue</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="updated_by">Modifié par</label>
                            <input type="text" id="updated_by" name="updated_by" 
                                   value="<?= htmlspecialchars($organisation['updated_by'] ?? '') ?>" />
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary"><?= htmlspecialchars($submitText) ?></button>
                        <a href="index.php?action=organisations&section=<?= htmlspecialchars($section) ?>" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>
            </section>
        </main>

        <footer>
            <p>&copy; 2025 PeaceConnect - Administration</p>
        </footer>
    </div>
</body>
</html>

