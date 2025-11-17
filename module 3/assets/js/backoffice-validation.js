/* ============================================
   BACK-OFFICE VALIDATION
   JavaScript validation for all back-office forms
   ============================================ */

// Validation for Add/Update Report Form
function validateReportForm() {
    let isValid = true;
    
    // Validate type
    const type = document.querySelector('input[name="type"]');
    if (type) {
        const typeValue = type.value.trim();
        if (typeValue === '') {
            showError(type, 'Le type est obligatoire');
            isValid = false;
        } else if (typeValue.length < 3) {
            showError(type, 'Le type doit contenir au moins 3 caractères');
            isValid = false;
        } else {
            clearError(type);
        }
    }
    
    // Validate title
    const title = document.querySelector('input[name="title"]');
    if (title) {
        const titleValue = title.value.trim();
        if (titleValue === '') {
            showError(title, 'Le titre est obligatoire');
            isValid = false;
        } else if (titleValue.length < 5) {
            showError(title, 'Le titre doit contenir au moins 5 caractères');
            isValid = false;
        } else if (titleValue.length > 100) {
            showError(title, 'Le titre ne doit pas dépasser 100 caractères');
            isValid = false;
        } else {
            clearError(title);
        }
    }
    
    // Validate description
    const description = document.querySelector('textarea[name="description"]');
    if (description) {
        const descValue = description.value.trim();
        if (descValue === '') {
            showError(description, 'La description est obligatoire');
            isValid = false;
        } else if (descValue.length < 10) {
            showError(description, 'La description doit contenir au moins 10 caractères');
            isValid = false;
        } else {
            clearError(description);
        }
    }
    
    // Validate location (optional but if provided, min 3 chars)
    const location = document.querySelector('input[name="location"]');
    if (location && location.value.trim() !== '') {
        const locValue = location.value.trim();
        if (locValue.length < 3) {
            showError(location, 'Le lieu doit contenir au moins 3 caractères');
            isValid = false;
        } else {
            clearError(location);
        }
    }
    
    // Validate incident_date (format YYYY-MM-DD)
    const incidentDate = document.querySelector('input[name="incident_date"]');
    if (incidentDate && incidentDate.value.trim() !== '') {
        const dateValue = incidentDate.value.trim();
        const dateRegex = /^\d{4}-\d{2}-\d{2}$/;
        
        if (!dateRegex.test(dateValue)) {
            showError(incidentDate, 'Format de date invalide (AAAA-MM-JJ)');
            isValid = false;
        } else {
            const date = new Date(dateValue);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (date > today) {
                showError(incidentDate, 'La date ne peut pas être dans le futur');
                isValid = false;
            } else {
                clearError(incidentDate);
            }
        }
    }
    
    return isValid;
}

// Validation for Add/Update Mediator Form
function validateMediatorForm() {
    let isValid = true;
    
    // Validate name
    const name = document.querySelector('input[name="name"]');
    if (name) {
        const nameValue = name.value.trim();
        if (nameValue === '') {
            showError(name, 'Le nom est obligatoire');
            isValid = false;
        } else if (nameValue.length < 3) {
            showError(name, 'Le nom doit contenir au moins 3 caractères');
            isValid = false;
        } else if (!/^[a-zA-ZÀ-ÿ\s'-]+$/.test(nameValue)) {
            showError(name, 'Le nom contient des caractères invalides');
            isValid = false;
        } else {
            clearError(name);
        }
    }
    
    // Validate email
    const email = document.querySelector('input[name="email"]');
    if (email) {
        const emailValue = email.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (emailValue === '') {
            showError(email, 'L\'email est obligatoire');
            isValid = false;
        } else if (!emailRegex.test(emailValue)) {
            showError(email, 'Format d\'email invalide');
            isValid = false;
        } else {
            clearError(email);
        }
    }
    
    // Validate phone (optional but if provided, must be valid)
    const phone = document.querySelector('input[name="phone"]');
    if (phone && phone.value.trim() !== '') {
        const phoneValue = phone.value.trim();
        const phoneRegex = /^[\d\s\+\-\(\)]{8,}$/;
        
        if (!phoneRegex.test(phoneValue)) {
            showError(phone, 'Numéro de téléphone invalide (min 8 chiffres)');
            isValid = false;
        } else {
            clearError(phone);
        }
    }
    
    // Validate expertise
    const expertise = document.querySelector('input[name="expertise"]');
    if (expertise) {
        const expertiseValue = expertise.value.trim();
        if (expertiseValue === '') {
            showError(expertise, 'L\'expertise est obligatoire');
            isValid = false;
        } else if (expertiseValue.length < 3) {
            showError(expertise, 'L\'expertise doit contenir au moins 3 caractères');
            isValid = false;
        } else {
            clearError(expertise);
        }
    }
    
    return isValid;
}

// Helper function to show error
function showError(input, message) {
    input.style.borderColor = '#e74c3c';
    input.style.backgroundColor = '#fef5f5';
    
    // Create or update error message
    let errorSpan = input.parentElement.querySelector('.error-message');
    if (!errorSpan) {
        errorSpan = document.createElement('span');
        errorSpan.className = 'error-message';
        errorSpan.style.color = '#e74c3c';
        errorSpan.style.fontSize = '0.875rem';
        errorSpan.style.marginTop = '0.25rem';
        errorSpan.style.display = 'block';
        input.parentElement.appendChild(errorSpan);
    }
    errorSpan.textContent = message;
}

// Helper function to clear error
function clearError(input) {
    input.style.borderColor = '';
    input.style.backgroundColor = '';
    
    const errorSpan = input.parentElement.querySelector('.error-message');
    if (errorSpan) {
        errorSpan.remove();
    }
}

// Real-time validation on input
document.addEventListener('DOMContentLoaded', function() {
    // Add blur event listeners to all form inputs
    const inputs = document.querySelectorAll('input[type="text"], textarea, input[type="email"], input[type="tel"]');
    
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            // Determine which form we're in
            const form = this.closest('form');
            if (!form) return;
            
            // Check if it's a report or mediator form
            if (this.name === 'type' || this.name === 'title' || this.name === 'description') {
                validateReportForm();
            } else if (this.name === 'name' || this.name === 'email' || this.name === 'expertise') {
                validateMediatorForm();
            }
        });
    });
});

// Attach validation to forms on page load
window.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let isValid = false;
            
            // Determine which validation to use
            if (this.querySelector('input[name="type"]') || this.querySelector('input[name="title"]')) {
                isValid = validateReportForm();
            } else if (this.querySelector('input[name="name"]') && this.querySelector('input[name="email"]')) {
                isValid = validateMediatorForm();
            }
            
            if (!isValid) {
                e.preventDefault();
                alert('Veuillez corriger les erreurs dans le formulaire');
                return false;
            }
        });
    });
});

