function estVide(value) {
    return value === null || value === undefined || value.trim() === '';
}

function emailValide(email) {
    var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

function dateValide(date) {
    var regex = /^\d{4}-\d{2}-\d{2}$/;
    return regex.test(date);
}

function heureValide(time) {
    var regex = /^\d{2}:\d{2}$/;
    return regex.test(time);
}

function telephoneValide(phone) {
    var regex = /^[\d\s\+\-]{8,}$/;
    return regex.test(phone);
}

function afficherErreur(inputId, message) {
    var input = document.getElementById(inputId);
    var errorDiv = document.getElementById(inputId + '-error');
    if (input && errorDiv) {
        input.classList.add('invalid');
        input.classList.remove('valid');
        errorDiv.textContent = message;
    }
}

function effacerErreur(inputId) {
    var input = document.getElementById(inputId);
    var errorDiv = document.getElementById(inputId + '-error');
    if (input && errorDiv) {
        input.classList.add('valid');
        input.classList.remove('invalid');
        errorDiv.textContent = '';
    }
}

function mettreAJourCompteur(inputId, countId, max) {
    var input = document.getElementById(inputId);
    var countDiv = document.getElementById(countId);
    if (input && countDiv) {
        var length = input.value.length;
        countDiv.textContent = length + '/' + max + ' caractères';
        if (length >= max * 0.9) {
            countDiv.style.color = '#ef4444';
        } else if (length >= max * 0.7) {
            countDiv.style.color = '#f59e0b';
        } else {
            countDiv.style.color = '#666';
        }
    }
}

function fermerTousLesPopups() {
    var popups = document.querySelectorAll('.calendar-popup, .time-popup, .map-popup');
    popups.forEach(function(popup) {
        popup.classList.remove('show');
    });
}

document.addEventListener('click', function(event) {
    if (!event.target.closest('.picker-container')) {
        fermerTousLesPopups();
    }
});

function confirmerSuppression(message) {
    return confirm(message || 'Êtes-vous sûr de vouloir supprimer cet élément?');
}
