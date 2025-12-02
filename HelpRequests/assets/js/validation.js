/* ============================================
   VALIDATION COMPLÈTE - Form Validation Engine
   ============================================ */

/**
 * Classe de validation côté client
 */
class FormValidator {
    constructor(formElement) {
        this.form = formElement;
        this.errors = {};
        this.fields = {};
        this.setupValidation();
    }

    /**
     * Configure la validation sur tous les champs du formulaire
     */
    setupValidation() {
        const inputs = this.form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            this.fields[input.name] = {
                element: input,
                validators: [],
                value: ''
            };

            // Valide le champ au blur
            input.addEventListener('blur', () => this.validateField(input));

            // Valide en temps réel si le champ a une erreur
            input.addEventListener('input', () => {
                if (this.errors[input.name]) {
                    this.validateField(input);
                }
            });
        });

        // Valide tout le formulaire avant la soumission
        this.form.addEventListener('submit', (e) => {
            e.preventDefault();
            if (this.validateForm()) {
                this.form.dispatchEvent(new CustomEvent('validationSuccess', { detail: this.getFormData() }));
            }
        });
    }

    /**
     * Ajoute une règle de validation à un champ
     */
    addRule(fieldName, validatorName, options = {}) {
        if (this.fields[fieldName]) {
            this.fields[fieldName].validators.push({ name: validatorName, options });
        }
        return this;
    }

    /**
     * Valide un champ spécifique
     */
    validateField(field) {
        const fieldData = this.fields[field.name];
        if (!fieldData) return true;

        this.errors[field.name] = [];
        fieldData.value = field.value.trim();

        // Exécute tous les validateurs du champ
        for (const validator of fieldData.validators) {
            const validatorFunc = this.getValidator(validator.name);
            if (!validatorFunc(fieldData.value, validator.options)) {
                this.errors[field.name].push(this.getErrorMessage(validator.name, validator.options));
            }
        }

        // Affiche ou masque les erreurs
        this.displayFieldErrors(field);

        return this.errors[field.name].length === 0;
    }

    /**
     * Valide tout le formulaire
     */
    validateForm() {
        let isValid = true;

        for (const fieldName in this.fields) {
            const fieldData = this.fields[fieldName];
            if (!this.validateField(fieldData.element)) {
                isValid = false;
            }
        }

        return isValid;
    }

    /**
     * Affiche les erreurs d'un champ
     */
    displayFieldErrors(field) {
        const errorElement = field.closest('.form-group')?.querySelector('.form-error');
        if (!errorElement) return;

        if (this.errors[field.name].length > 0) {
            field.classList.add('error');
            errorElement.classList.add('show');
            errorElement.innerHTML = this.errors[field.name][0];
        } else {
            field.classList.remove('error');
            errorElement.classList.remove('show');
            errorElement.innerHTML = '';
        }
    }

    /**
     * Retourne la fonction de validateur
     */
    getValidator(name) {
        const validators = {
            'required': (value) => value !== '',
            'email': (value) => value === '' || Validators.isValidEmail(value),
            'minLength': (value, opts) => value.length >= (opts.min || 0),
            'maxLength': (value, opts) => value.length <= (opts.max || 999999),
            'phone': (value) => value === '' || Validators.isValidPhone(value),
            'url': (value) => value === '' || Validators.isValidUrl(value),
            'custom': (value, opts) => !opts.regex || opts.regex.test(value),
            'helpType': (value) => value === '' || Validators.isValidHelpType(value),
            'urgencyLevel': (value) => value === '' || Validators.isValidUrgencyLevel(value),
            'contactMethod': (value) => value === '' || Validators.isValidContactMethod(value),
            'status': (value) => value === '' || Validators.isValidStatus(value),
        };
        return validators[name] || (() => true);
    }

    /**
     * Retourne le message d'erreur approprié
     */
    getErrorMessage(validatorName, options = {}) {
        const messages = {
            'required': 'Ce champ est obligatoire',
            'email': 'Veuillez entrer une adresse email valide',
            'minLength': `Minimum ${options.min || 0} caractères requis`,
            'maxLength': `Maximum ${options.max || 999999} caractères autorisés`,
            'phone': 'Veuillez entrer un numéro de téléphone valide',
            'url': 'Veuillez entrer une URL valide',
            'custom': options.message || 'Format invalide',
            'helpType': 'Type d\'aide invalide',
            'urgencyLevel': 'Niveau d\'urgence invalide',
            'contactMethod': 'Moyen de contact invalide',
            'status': 'Statut invalide',
        };
        return messages[validatorName] || 'Erreur de validation';
    }

    /**
     * Récupère les données du formulaire
     */
    getFormData() {
        const data = {};
        for (const fieldName in this.fields) {
            data[fieldName] = this.fields[fieldName].value;
        }
        return data;
    }

    /**
     * Réinitialise le formulaire
     */
    reset() {
        this.form.reset();
        this.errors = {};
        const inputs = this.form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.classList.remove('error');
            const errorElement = input.closest('.form-group')?.querySelector('.form-error');
            if (errorElement) {
                errorElement.classList.remove('show');
                errorElement.innerHTML = '';
            }
        });
    }
}

/**
 * Classe utilitaire pour les validateurs réutilisables
 */
class Validators {
    /**
     * Valide une adresse email
     */
    static isValidEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }

    /**
     * Valide une URL
     */
    static isValidUrl(url) {
        try {
            new URL(url);
            return true;
        } catch (e) {
            return false;
        }
    }

    /**
     * Valide un numéro de téléphone (basique)
     */
    static isValidPhone(phone) {
        const cleaned = phone.replace(/[^0-9+\-\s()]/g, '');
        return cleaned.replace(/\D/g, '').length >= 10;
    }

    /**
     * Valide un type d'aide
     */
    static isValidHelpType(value) {
        const allowedTypes = ['legal', 'psychological', 'mediation', 'emergency', 'information', 'other'];
        return allowedTypes.includes(value);
    }

    /**
     * Valide un niveau d'urgence
     */
    static isValidUrgencyLevel(value) {
        const allowedLevels = ['low', 'medium', 'high', 'critical'];
        return allowedLevels.includes(value);
    }

    /**
     * Valide un moyen de contact
     */
    static isValidContactMethod(value) {
        const allowedMethods = ['email', 'phone', 'both'];
        return allowedMethods.includes(value);
    }

    /**
     * Valide un statut
     */
    static isValidStatus(value) {
        const allowedStatus = ['en_attente', 'en_cours', 'resolu', 'ferme'];
        return allowedStatus.includes(value);
    }

    /**
     * Nettoie une chaîne de caractères
     */
    static sanitizeString(value) {
        const div = document.createElement('div');
        div.textContent = value;
        return div.innerHTML;
    }

    /**
     * Valide une force de mot de passe
     */
    static validatePasswordStrength(password) {
        const strength = {
            score: 0,
            level: 'weak',
            feedback: []
        };

        if (password.length >= 8) strength.score++;
        else strength.feedback.push('Au moins 8 caractères');

        if (/[A-Z]/.test(password)) strength.score++;
        else strength.feedback.push('Une lettre majuscule');

        if (/[a-z]/.test(password)) strength.score++;
        else strength.feedback.push('Une lettre minuscule');

        if (/[0-9]/.test(password)) strength.score++;
        else strength.feedback.push('Un chiffre');

        if (/[^A-Za-z0-9]/.test(password)) strength.score++;
        else strength.feedback.push('Un caractère spécial');

        // Déterminer le niveau
        if (strength.score <= 1) strength.level = 'very weak';
        else if (strength.score <= 2) strength.level = 'weak';
        else if (strength.score <= 3) strength.level = 'fair';
        else if (strength.score <= 4) strength.level = 'good';
        else strength.level = 'very strong';

        return strength;
    }

    /**
     * Compare deux valeurs (ex: confirmation de mot de passe)
     */
    static isEqual(value1, value2) {
        return value1 === value2;
    }

    /**
     * Valide un motif personnalisé (regex)
     */
    static matchesPattern(value, pattern) {
        return new RegExp(pattern).test(value);
    }

    /**
     * Valide une chaîne non vide
     */
    static isNotEmpty(value) {
        return value.trim() !== '';
    }

    /**
     * Valide une longueur minimale
     */
    static hasMinLength(value, min) {
        return value.length >= min;
    }

    /**
     * Valide une longueur maximale
     */
    static hasMaxLength(value, max) {
        return value.length <= max;
    }

    /**
     * Valide qu'une valeur est dans une liste
     */
    static isInList(value, list) {
        return list.includes(value);
    }

    /**
     * Valide qu'une chaîne contient uniquement des lettres et des espaces
     */
    static isAlphabetic(value) {
        return /^[a-zA-ZÀ-ÿ\s]+$/.test(value);
    }

    /**
     * Valide qu'une chaîne contient uniquement des lettres, chiffres et espaces
     */
    static isAlphanumeric(value) {
        return /^[a-zA-Z0-9À-ÿ\s]+$/.test(value);
    }

    /**
     * Valide une adresse (simple)
     */
    static isValidAddress(value) {
        return value.length >= 5 && value.length <= 200;
    }

    /**
     * Valide un code postal (France)
     */
    static isValidFrenchPostalCode(value) {
        return /^[0-9]{5}$/.test(value.replace(/\s/g, ''));
    }

    /**
     * Affiche un message d'erreur formaté
     */
    static displayError(field, message) {
        field.classList.add('error');
        const errorElement = field.closest('.form-group')?.querySelector('.form-error');
        if (errorElement) {
            errorElement.classList.add('show');
            errorElement.textContent = message;
        }
    }

    /**
     * Efface les erreurs d'un champ
     */
    static clearError(field) {
        field.classList.remove('error');
        const errorElement = field.closest('.form-group')?.querySelector('.form-error');
        if (errorElement) {
            errorElement.classList.remove('show');
            errorElement.textContent = '';
        }
    }
}

/**
 * Export des classes
 */
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { FormValidator, Validators };
}
