/* ============================================
   EXEMPLE D'INTÉGRATION - Validation pour tous formulaires
   ============================================ */

/**
 * Template pour intégrer la validation sur n'importe quel formulaire
 * 
 * 1. HTML (simple)
 * -----------
 * <form id="monFormulaire" method="post">
 *     <div class="form-group">
 *         <label for="email">Email *</label>
 *         <input type="text" id="email" name="email" class="form-control">
 *         <span class="form-error"></span>
 *     </div>
 *     <button type="submit">Envoyer</button>
 * </form>
 * 
 * 2. JavaScript
 * -----------
 */

document.addEventListener('DOMContentLoaded', function() {
    // Récupérer le formulaire
    const form = document.getElementById('monFormulaire');
    if (!form) return;

    // Créer l'instance du validateur
    const validator = new FormValidator(form);

    // Ajouter les règles de validation
    validator
        .addRule('email', 'required')
        .addRule('email', 'email')
        .addRule('password', 'required')
        .addRule('password', 'minLength', { min: 8 })
        .addRule('agree', 'required');

    // Gérer la soumission validée
    form.addEventListener('validationSuccess', async function(e) {
        const data = e.detail;
        console.log('Données validées :', data);

        // Envoyer au serveur
        try {
            const response = await fetch('api/your-endpoint.php', {
                method: 'POST',
                body: new URLSearchParams(data)
            });

            const result = await response.json();
            if (result.success) {
                showToast('Succès !', 'success');
                validator.reset();
            } else {
                showToast('Erreur : ' + result.error, 'error');
            }
        } catch (error) {
            console.error(error);
            showToast('Erreur réseau', 'error');
        }
    });
});

// ======================================
// EXEMPLES DE RÈGLES DE VALIDATION
// ======================================

/*
// Email
validator.addRule('email', 'required')
         .addRule('email', 'email');

// Mot de passe (forte)
validator.addRule('password', 'required')
         .addRule('password', 'minLength', { min: 8 })
         .addRule('password', 'custom', {
             regex: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/,
             message: 'Au moins 1 maj, 1 min, 1 chiffre, 1 caractère spécial'
         });

// Téléphone
validator.addRule('phone', 'required')
         .addRule('phone', 'phone');

// URL
validator.addRule('website', 'url');

// Texte court
validator.addRule('username', 'required')
         .addRule('username', 'minLength', { min: 3 })
         .addRule('username', 'maxLength', { max: 20 });

// Texte long
validator.addRule('bio', 'maxLength', { max: 500 });

// Code postal France
validator.addRule('postal_code', 'custom', {
    regex: /^[0-9]{5}$/,
    message: 'Code postal invalide (5 chiffres)'
});

// Confirmation (ex: 2 mots de passe)
document.getElementById('password').addEventListener('blur', function() {
    const pwd = this.value;
    const pwd_confirm = document.getElementById('password_confirm');
    if (pwd && pwd_confirm.value && pwd !== pwd_confirm.value) {
        Validators.displayError(pwd_confirm, 'Les mots de passe ne correspondent pas');
    }
});
*/

// ======================================
// VALIDATEURS PERSONNALISÉS
// ======================================

/**
 * Validation personnalisée pour URL
 */
class CustomValidators {
    static isValidSIRET(value) {
        // 14 chiffres
        return /^\d{14}$/.test(value);
    }

    static isValidSIREN(value) {
        // 9 chiffres
        return /^\d{9}$/.test(value);
    }

    static isValidIPAddress(value) {
        return /^(\d{1,3}\.){3}\d{1,3}$/.test(value) &&
               value.split('.').every(octet => parseInt(octet) <= 255);
    }

    static isValidHexColor(value) {
        return /^#(?:[0-9a-f]{3}){1,2}$/i.test(value);
    }

    static isValidCreditCard(value) {
        const cleaned = value.replace(/\s/g, '');
        return /^\d{13,19}$/.test(cleaned);
    }

    static isValidIBAN(value) {
        // Validation IBAN (Europe)
        const cleaned = value.replace(/\s/g, '');
        return /^[A-Z]{2}\d{2}[A-Z0-9]{1,30}$/.test(cleaned);
    }

    static isValidDate(value) {
        const date = new Date(value);
        return date instanceof Date && !isNaN(date);
    }

    static isValidFutureDate(value) {
        const date = new Date(value);
        return date > new Date();
    }

    static isValidPastDate(value) {
        const date = new Date(value);
        return date < new Date();
    }

    static isValidAge(value, minAge = 18) {
        const birthDate = new Date(value);
        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        return age >= minAge;
    }

    static isValidUsername(value) {
        // Alphanumérique, tirets et underscores, 3-20 caractères
        return /^[a-zA-Z0-9_-]{3,20}$/.test(value);
    }

    static isValidSlug(value) {
        // Minuscules, chiffres, tirets, 1-100 caractères
        return /^[a-z0-9-]{1,100}$/.test(value);
    }

    static isValidJSON(value) {
        try {
            JSON.parse(value);
            return true;
        } catch (e) {
            return false;
        }
    }
}

// ======================================
// EXEMPLE COMPLET : FORMULAIRE D'INSCRIPTION
// ======================================

/*
<form id="registerForm" method="post">
    <div class="form-group">
        <label for="username">Nom d'utilisateur *</label>
        <input type="text" id="username" name="username" class="form-control">
        <span class="form-error"></span>
        <span class="form-help">3-20 caractères, alphanumérique</span>
    </div>

    <div class="form-group">
        <label for="email">Email *</label>
        <input type="text" id="email" name="email" class="form-control">
        <span class="form-error"></span>
    </div>

    <div class="form-group">
        <label for="password">Mot de passe *</label>
        <input type="password" id="password" name="password" class="form-control">
        <span class="form-error"></span>
        <span class="form-help">Au moins 8 caractères, avec majuscules, chiffres et caractères spéciaux</span>
    </div>

    <div class="form-group">
        <label for="password_confirm">Confirmer le mot de passe *</label>
        <input type="password" id="password_confirm" name="password_confirm" class="form-control">
        <span class="form-error"></span>
    </div>

    <div class="form-group">
        <label for="birthdate">Date de naissance *</label>
        <input type="text" id="birthdate" name="birthdate" class="form-control" placeholder="YYYY-MM-DD">
        <span class="form-error"></span>
    </div>

    <div class="form-group">
        <label>
            <input type="checkbox" name="agree" value="1">
            J'accepte les conditions d'utilisation *
        </label>
        <span class="form-error"></span>
    </div>

    <button type="submit" class="btn btn-primary">S'inscrire</button>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registerForm');
    const validator = new FormValidator(form);

    validator
        .addRule('username', 'required')
        .addRule('username', 'minLength', { min: 3 })
        .addRule('username', 'maxLength', { max: 20 })
        .addRule('username', 'custom', {
            regex: /^[a-zA-Z0-9_-]{3,20}$/,
            message: 'Alphanumérique, tirets et underscores uniquement'
        })
        .addRule('email', 'required')
        .addRule('email', 'email')
        .addRule('password', 'required')
        .addRule('password', 'minLength', { min: 8 })
        .addRule('password', 'custom', {
            regex: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])/,
            message: 'Min 1 maj, 1 min, 1 chiffre, 1 caractère spécial'
        })
        .addRule('password_confirm', 'required')
        .addRule('birthdate', 'required')
        .addRule('birthdate', 'custom', {
            regex: /^\d{4}-\d{2}-\d{2}$/,
            message: 'Format : YYYY-MM-DD'
        })
        .addRule('agree', 'required');

    // Validation personnalisée : mots de passe correspondent
    document.getElementById('password_confirm').addEventListener('blur', function() {
        const pwd1 = document.getElementById('password').value;
        const pwd2 = this.value;
        if (pwd1 && pwd2 && pwd1 !== pwd2) {
            Validators.displayError(this, 'Les mots de passe ne correspondent pas');
        } else {
            Validators.clearError(this);
        }
    });

    // Validation personnalisée : âge >= 18 ans
    document.getElementById('birthdate').addEventListener('blur', function() {
        if (this.value && !CustomValidators.isValidAge(this.value, 18)) {
            Validators.displayError(this, 'Vous devez avoir au moins 18 ans');
        } else {
            Validators.clearError(this);
        }
    });
});
</script>
*/

console.log('Exemples d\'intégration chargés. Consultez les commentaires pour les templates.');
