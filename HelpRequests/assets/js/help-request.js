/* ============================================
   GESTION DU FORMULAIRE DE DEMANDE D'AIDE
   ============================================ */

document.addEventListener('DOMContentLoaded', function() {
    initHelpRequestForm();
});

/**
 * Initialise le formulaire de demande d'aide
 */
function initHelpRequestForm() {
    const form = document.getElementById('helpRequestForm');
    if (!form) return;

    // Créer l'instance du validateur
    const validator = new FormValidator(form);

    // Ajouter les règles de validation pour chaque champ
    validator.addRule('help_type', 'required')
             .addRule('help_type', 'helpType');

    validator.addRule('urgency_level', 'required')
             .addRule('urgency_level', 'urgencyLevel');

    validator.addRule('situation', 'required')
             .addRule('situation', 'minLength', { min: 10 })
             .addRule('situation', 'maxLength', { max: 5000 });

    validator.addRule('location', 'maxLength', { max: 100 });

    validator.addRule('contact_method', 'required')
             .addRule('contact_method', 'contactMethod');

    // Soumettre le formulaire si validé
    form.addEventListener('validationSuccess', async function(e) {
        await submitForm(e.detail, validator);
    });

    // Gestion du bouton "Obtenir des suggestions IA"
    const suggestBtn = document.getElementById('generateSuggestionBtn');
    if (suggestBtn) {
        suggestBtn.addEventListener('click', handleGenerateSuggestion);
    }
}

/**
 * Génère les suggestions IA
 */
async function handleGenerateSuggestion() {
    const description = document.getElementById('helpDescription').value.trim();

    if (!description) {
        showToast('Veuillez d\'abord décrire votre situation', 'warning');
        document.getElementById('helpDescription').focus();
        return;
    }

    if (description.length < 10) {
        showToast('La description doit contenir au minimum 10 caractères', 'warning');
        return;
    }

    const btn = this;
    const spinner = document.getElementById('suggestionSpinner');
    const suggestionsDiv = document.getElementById('aiSuggestions');

    // Afficher le spinner
    btn.disabled = true;
    spinner.style.display = 'inline-block';
    btn.textContent = 'Analyse en cours...';

    try {
        const suggestion = await generateAISuggestion(description);

        // Afficher la suggestion
        suggestionsDiv.innerHTML = `
            <div class="alert alert-success" style="margin-bottom: 1rem;">
                <strong>✅ Analyse terminée</strong>
            </div>
            <div style="background: var(--color-background); padding: 1.5rem; border-radius: var(--radius-md); margin-bottom: 1rem;">
                <h3 style="margin-bottom: 1rem; font-size: 1.125rem;">💡 Recommandations personnalisées :</h3>
                <p style="line-height: 1.8; color: var(--color-text);">${escapeHtml(suggestion)}</p>
            </div>
            <div style="border-top: 1px solid var(--color-border); padding-top: 1rem;">
                <h4 style="margin-bottom: 0.75rem; font-size: 1rem;">📋 Actions suggérées :</h4>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 0.5rem;">✓ Documenter l'incident avec dates et preuves</li>
                    <li style="margin-bottom: 0.5rem;">✓ Contacter les services appropriés selon votre situation</li>
                    <li style="margin-bottom: 0.5rem;">✓ Envisager une médiation si applicable</li>
                    <li style="margin-bottom: 0.5rem;">✓ Chercher un soutien psychologique si nécessaire</li>
                </ul>
            </div>
            <div style="margin-top: 1rem;">
                <button class="btn btn-outline btn-sm" type="button" onclick="document.getElementById('generateSuggestionBtn').click()">
                    🔄 Régénérer les suggestions
                </button>
            </div>
        `;

        showToast('Suggestions générées avec succès !', 'success');
    } catch (error) {
        console.error('Erreur génération suggestions:', error);
        showToast('Erreur lors de la génération des suggestions', 'error');
    } finally {
        btn.disabled = false;
        spinner.style.display = 'none';
        btn.innerHTML = 'Obtenir des suggestions IA';
    }
}

/**
 * Soumet le formulaire au serveur
 */
async function submitForm(formData, validator) {
    const form = document.getElementById('helpRequestForm');
    const btn = form.querySelector('button[type="submit"]');

    // Vérifier que le serveur est accessible
    const serverUp = await checkServerHealth();
    if (!serverUp) {
        showToast('Serveur indisponible. Vérifiez que PHP est lancé.', 'error');
        return;
    }

    try {
        btn.disabled = true;
        btn.textContent = 'Envoi en cours...';

        const payload = new FormData();
        for (const key in formData) {
            payload.append(key, formData[key]);
        }

        const response = await fetch('api/help-request.php', {
            method: 'POST',
            body: payload
        });

        const text = await response.text();
        let data;

        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('Réponse non-JSON:', text);
            showToast('Erreur serveur : réponse invalide', 'error');
            return;
        }

        if (data.success) {
            showToast('Demande d\'aide envoyée avec succès !', 'success');
            validator.reset();
            
            // Rediriger après 2 secondes
            setTimeout(() => {
                window.location.href = 'index.html';
            }, 2000);
        } else {
            const errorMsg = data.error || 'Erreur lors de l\'envoi';
            showToast(errorMsg, 'error');
            console.error('Erreur serveur:', data);
        }
    } catch (error) {
        console.error('Erreur réseau:', error);
        showToast('Impossible de contacter le serveur', 'error');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Envoyer la demande';
    }
}

/**
 * Vérifie la santé du serveur
 */
async function checkServerHealth() {
    try {
        const response = await fetch('api/help-request.php', {
            method: 'GET',
            cache: 'no-store'
        });
        return response.ok;
    } catch (error) {
        console.error('Serveur indisponible:', error);
        return false;
    }
}

/**
 * Échappe les caractères HTML dangereux
 */
function escapeHtml(unsafe) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return unsafe.replace(/[&<>"']/g, function(m) { return map[m]; });
}

/**
 * Ajoute une nouvelle classe au validateur pour les règles personnalisées
 */
class FormValidator {
    constructor(formElement) {
        this.form = formElement;
        this.errors = {};
        this.fields = {};
        this.setupValidation();
    }

    setupValidation() {
        const inputs = this.form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            this.fields[input.name] = {
                element: input,
                validators: [],
                value: ''
            };

            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('input', () => {
                if (this.errors[input.name]) {
                    this.validateField(input);
                }
            });
        });

        this.form.addEventListener('submit', (e) => {
            e.preventDefault();
            if (this.validateForm()) {
                this.form.dispatchEvent(new CustomEvent('validationSuccess', { detail: this.getFormData() }));
            } else {
                showToast('Veuillez corriger les erreurs dans le formulaire', 'error');
            }
        });
    }

    addRule(fieldName, validatorName, options = {}) {
        if (this.fields[fieldName]) {
            this.fields[fieldName].validators.push({ name: validatorName, options });
        }
        return this;
    }

    validateField(field) {
        const fieldData = this.fields[field.name];
        if (!fieldData) return true;

        this.errors[field.name] = [];
        fieldData.value = field.value.trim();

        for (const validator of fieldData.validators) {
            const validatorFunc = this.getValidator(validator.name);
            if (!validatorFunc(fieldData.value, validator.options)) {
                this.errors[field.name].push(this.getErrorMessage(validator.name, validator.options));
            }
        }

        this.displayFieldErrors(field);
        return this.errors[field.name].length === 0;
    }

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

    getFormData() {
        const data = {};
        for (const fieldName in this.fields) {
            data[fieldName] = this.fields[fieldName].value;
        }
        return data;
    }

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
