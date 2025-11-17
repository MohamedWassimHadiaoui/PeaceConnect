/* ============================================
   MODULE 3 - ASSIGN MEDIATOR
   Assignation de médiateur
   ============================================ */

// ============================================
// VALIDATION FUNCTIONS (JavaScript only - No HTML5)
// ============================================

function validateAssignmentForm() {
    let isValid = true;
    
    // Validate mediator selection (required)
    const mediator = document.getElementById('mediator');
    const mediatorError = mediator.parentElement.querySelector('.form-error');
    
    if (!mediator.value || mediator.value === '') {
        mediatorError.textContent = 'Veuillez sélectionner un médiateur';
        mediator.classList.add('error');
        isValid = false;
    } else {
        mediatorError.textContent = '';
        mediator.classList.remove('error');
    }
    
    // Validate mediation date (required, cannot be in past)
    const mediationDate = document.getElementById('mediationDate');
    const dateError = mediationDate.parentElement.querySelector('.form-error');
    const dateValue = mediationDate.value;
    
    if (dateValue === '') {
        dateError.textContent = 'La date et l\'heure de la séance sont obligatoires';
        mediationDate.classList.add('error');
        isValid = false;
    } else {
        const selectedDateTime = new Date(dateValue);
        const now = new Date();
        
        if (selectedDateTime < now) {
            dateError.textContent = 'La date ne peut pas être dans le passé';
            mediationDate.classList.add('error');
            isValid = false;
        } else {
            dateError.textContent = '';
            mediationDate.classList.remove('error');
        }
    }
    
    // Validate location (required, min 3 chars)
    const location = document.getElementById('location');
    const locationError = location.parentElement.querySelector('.form-error');
    const locationValue = location.value.trim();
    
    if (locationValue === '') {
        locationError.textContent = 'Le lieu de la médiation est obligatoire';
        location.classList.add('error');
        isValid = false;
    } else if (locationValue.length < 3) {
        locationError.textContent = 'Le lieu doit contenir au moins 3 caractères';
        location.classList.add('error');
        isValid = false;
    } else {
        locationError.textContent = '';
        location.classList.remove('error');
    }
    
    return isValid;
}

document.addEventListener('DOMContentLoaded', function() {
    const reportId = getUrlParameter('id');
    
    if (reportId) {
        loadReportForAssignment(reportId);
    }
    
    initMediatorSelection();
    initFormSubmission();
});

/**
 * Charger le signalement pour assignation
 */
async function loadReportForAssignment(reportId) {
    // TODO: Implémenter l'appel API
    // const response = await fetch(`/api/admin/reports/${reportId}`);
    // const report = await response.json();
    
    document.getElementById('reportId').textContent = reportId;
    
    // Charger les médiateurs disponibles
    loadAvailableMediators(reportId);
}

/**
 * Charger les médiateurs disponibles avec recommandation IA
 */
async function loadAvailableMediators(reportId) {
    // TODO: Implémenter l'appel API avec IA
    // const response = await fetch(`/api/admin/mediators/recommend?reportId=${reportId}`);
    // const mediators = await response.json();
}

/**
 * Initialiser la sélection de médiateur
 */
function initMediatorSelection() {
    const mediatorSelect = document.getElementById('mediator');
    
    if (mediatorSelect) {
        mediatorSelect.addEventListener('change', function() {
            const mediatorId = this.value;
            if (mediatorId) {
                updateMediatorProfile(mediatorId);
            }
        });
    }
}

/**
 * Mettre à jour le profil du médiateur affiché
 */
function updateMediatorProfile(mediatorId) {
    // TODO: Récupérer les détails du médiateur depuis l'API
    // const response = await fetch(`/api/admin/mediators/${mediatorId}`);
    // const mediator = await response.json();
    
    // Mettre à jour l'affichage du profil
    console.log('Médiateur sélectionné:', mediatorId);
}

/**
 * Initialiser la soumission du formulaire
 */
function initFormSubmission() {
    const assignForm = document.getElementById('assignForm');
    
    if (assignForm) {
        assignForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Validate form before submission
            if (!validateAssignmentForm()) {
                showToast('Veuillez corriger les erreurs avant de soumettre', 'error', 3000);
                return;
            }
            
            const formData = new FormData(this);
            const assignmentData = {
                reportId: document.getElementById('reportId').textContent,
                mediatorId: formData.get('mediator_id'),
                mediationDate: formData.get('mediation_date'),
                location: formData.get('location'),
                mediationType: formData.get('mediationType'),
                notes: formData.get('notes'),
                notifyUser: formData.get('notifyUser') === 'on',
                notifyMediator: formData.get('notifyMediator') === 'on',
                assignedAt: new Date().toISOString()
            };
            
            // TODO: Envoyer les données au serveur
            console.log('Données d\'assignation:', assignmentData);
            
            // Afficher un message de succès
            showToast('✓ Médiateur assigné avec succès !', 'success');
            
            // Rediriger après 2 secondes
            setTimeout(() => {
                window.location.href = 'manage-reports.html';
            }, 2000);
        });
    }
}

/**
 * Obtenir la recommandation IA pour un médiateur
 */
async function getAIRecommendation(reportData) {
    // TODO: Implémenter l'appel à l'API IA
    // const response = await fetch('/api/ai/recommend-mediator', {
    //     method: 'POST',
    //     body: JSON.stringify(reportData)
    // });
    // return await response.json();
}

