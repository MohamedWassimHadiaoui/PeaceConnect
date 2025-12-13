const Validator = {
    rules: {
        required: (value) => value.trim() !== '',
        minLength: (value, min) => value.trim().length >= min,
        maxLength: (value, max) => value.trim().length <= max,
        email: (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
        phone: (value) => value === '' || /^[0-9]{8,15}$/.test(value.replace(/\s/g, '')),
        password: (value) => value.length >= 6,
        passwordMatch: (value, confirmValue) => value === confirmValue,
        date: (value) => value === '' || !isNaN(Date.parse(value)),
        number: (value) => value === '' || !isNaN(value),
        positiveNumber: (value) => value === '' || (!isNaN(value) && parseFloat(value) >= 0)
    },

    messages: {
        required: 'This field is required',
        minLength: 'Minimum {min} characters required',
        maxLength: 'Maximum {max} characters allowed',
        email: 'Please enter a valid email address',
        phone: 'Please enter a valid phone number',
        password: 'Password must be at least 6 characters',
        passwordMatch: 'Passwords do not match',
        date: 'Please enter a valid date',
        number: 'Please enter a valid number',
        positiveNumber: 'Please enter a positive number'
    },

    showError: function(input, message) {
        this.clearError(input);
        input.classList.add('is-invalid');
        input.style.borderColor = '#ef4444';
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'form-error';
        errorDiv.style.color = '#ef4444';
        errorDiv.style.fontSize = '0.85rem';
        errorDiv.style.marginTop = '0.25rem';
        errorDiv.textContent = message;
        
        input.parentNode.appendChild(errorDiv);
    },

    clearError: function(input) {
        input.classList.remove('is-invalid');
        input.style.borderColor = '';
        const existingError = input.parentNode.querySelector('.form-error');
        if (existingError) existingError.remove();
    },

    showSuccess: function(input) {
        this.clearError(input);
        input.style.borderColor = '#10b981';
    },

    validateField: function(input, rules) {
        const value = input.value;
        
        for (const rule of rules) {
            let isValid = true;
            let message = '';

            if (rule.type === 'required') {
                isValid = this.rules.required(value);
                message = rule.message || this.messages.required;
            }
            else if (rule.type === 'minLength') {
                isValid = this.rules.minLength(value, rule.min);
                message = rule.message || this.messages.minLength.replace('{min}', rule.min);
            }
            else if (rule.type === 'maxLength') {
                isValid = this.rules.maxLength(value, rule.max);
                message = rule.message || this.messages.maxLength.replace('{max}', rule.max);
            }
            else if (rule.type === 'email') {
                isValid = this.rules.email(value);
                message = rule.message || this.messages.email;
            }
            else if (rule.type === 'phone') {
                isValid = this.rules.phone(value);
                message = rule.message || this.messages.phone;
            }
            else if (rule.type === 'password') {
                isValid = this.rules.password(value);
                message = rule.message || this.messages.password;
            }
            else if (rule.type === 'passwordMatch') {
                const confirmInput = document.querySelector(rule.confirmField);
                isValid = this.rules.passwordMatch(value, confirmInput ? confirmInput.value : '');
                message = rule.message || this.messages.passwordMatch;
            }
            else if (rule.type === 'date') {
                isValid = this.rules.date(value);
                message = rule.message || this.messages.date;
            }
            else if (rule.type === 'custom') {
                isValid = rule.validator(value);
                message = rule.message || 'Invalid value';
            }

            if (!isValid) {
                this.showError(input, message);
                return false;
            }
        }

        this.showSuccess(input);
        return true;
    },

    validateForm: function(formId, fieldsConfig) {
        const form = document.getElementById(formId);
        if (!form) return false;

        let isFormValid = true;

        for (const [fieldName, rules] of Object.entries(fieldsConfig)) {
            const input = form.querySelector(`[name="${fieldName}"]`);
            if (input) {
                const fieldValid = this.validateField(input, rules);
                if (!fieldValid) isFormValid = false;
            }
        }

        return isFormValid;
    },

    setupRealTimeValidation: function(formId, fieldsConfig) {
        const form = document.getElementById(formId);
        if (!form) return;

        for (const [fieldName, rules] of Object.entries(fieldsConfig)) {
            const input = form.querySelector(`[name="${fieldName}"]`);
            if (input) {
                input.addEventListener('blur', () => this.validateField(input, rules));
                input.addEventListener('input', () => {
                    if (input.classList.contains('is-invalid')) {
                        this.validateField(input, rules);
                    }
                });
            }
        }
    },

    init: function(formId, fieldsConfig, onSuccess) {
        const form = document.getElementById(formId);
        if (!form) return;

        form.setAttribute('novalidate', 'true');
        this.setupRealTimeValidation(formId, fieldsConfig);

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            
            if (this.validateForm(formId, fieldsConfig)) {
                if (onSuccess) {
                    onSuccess(form);
                } else {
                    form.submit();
                }
            }
        });
    }
};

window.Validator = Validator;
