/* ============================================
   MODULE 3 - CREATE REPORT
   Gestion du formulaire multi-étapes de création de signalement
   ============================================ */

let currentStep = 1;

// ============================================
// VALIDATION FUNCTIONS (JavaScript only - No HTML5)
// ============================================

function validateStep1() {
    let isValid = true;
    
    // Validate incidentType (radio button)
    const incidentType = document.querySelector('input[name="incidentType"]:checked');
    const typeError = document.querySelector('.type-cards').closest('.form-step').querySelector('.form-error');
    
    if (!incidentType) {
        if (typeError) typeError.textContent = 'Veuillez sélectionner un type de situation';
        isValid = false;
    } else {
        if (typeError) typeError.textContent = '';
    }
    
    // Validate role (radio button)
    const role = document.querySelector('input[name="role"]:checked');
    const roleGroup = document.querySelector('input[name="role"]').closest('.form-group');
    const roleError = roleGroup.querySelector('.form-error');
    
    if (!role) {
        roleError.textContent = 'Veuillez sélectionner votre rôle';
        roleGroup.classList.add('error');
        isValid = false;
    } else {
        roleError.textContent = '';
        roleGroup.classList.remove('error');
    }
    
    return isValid;
}

function validateStep2() {
    let isValid = true;
    
    // Validate title (required, max 100 chars)
    const title = document.getElementById('title');
    const titleError = title.parentElement.querySelector('.form-error');
    const titleValue = title.value.trim();
    
    if (titleValue === '') {
        titleError.textContent = 'Le titre est obligatoire';
        title.classList.add('error');
        isValid = false;
    } else if (titleValue.length > 100) {
        titleError.textContent = 'Le titre ne doit pas dépasser 100 caractères';
        title.classList.add('error');
        isValid = false;
    } else {
        titleError.textContent = '';
        title.classList.remove('error');
    }
    
    // Validate description (required, min 10 chars)
    const description = document.getElementById('description');
    const descError = description.parentElement.querySelector('.form-error');
    const descValue = description.value.trim();
    
    if (descValue === '') {
        descError.textContent = 'La description est obligatoire';
        description.classList.add('error');
        isValid = false;
    } else if (descValue.length < 10) {
        descError.textContent = 'La description doit contenir au moins 10 caractères';
        description.classList.add('error');
        isValid = false;
    } else {
        descError.textContent = '';
        description.classList.remove('error');
    }
    
    // Validate incidentDate (required, format DD/MM/YYYY, cannot be future)
    const incidentDate = document.getElementById('incidentDate');
    const dateError = incidentDate.parentElement.querySelector('.form-error');
    const dateValue = incidentDate.value.trim();
    
    if (dateValue === '') {
        dateError.textContent = 'La date de l\'incident est obligatoire';
        incidentDate.classList.add('error');
        isValid = false;
    } else {
        // Validate format DD/MM/YYYY
        const dateRegex = /^(\d{2})\/(\d{2})\/(\d{4})$/;
        const match = dateValue.match(dateRegex);
        
        if (!match) {
            dateError.textContent = 'Format invalide. Utilisez JJ/MM/AAAA';
            incidentDate.classList.add('error');
            isValid = false;
        } else {
            const day = parseInt(match[1], 10);
            const month = parseInt(match[2], 10);
            const year = parseInt(match[3], 10);
            
            // Validate date components
            if (month < 1 || month > 12 || day < 1 || day > 31) {
                dateError.textContent = 'Date invalide';
                incidentDate.classList.add('error');
                isValid = false;
            } else {
                // Convert to Date object (month is 0-indexed in JS)
                const selectedDate = new Date(year, month - 1, day);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                
                if (selectedDate > today) {
                    dateError.textContent = 'La date ne peut pas être dans le futur';
                    incidentDate.classList.add('error');
                    isValid = false;
                } else {
                    dateError.textContent = '';
                    incidentDate.classList.remove('error');
                }
            }
        }
    }
    
    return isValid;
}

function validateStep3() {
    let isValid = true;
    
    // Validate file sizes (max 10MB per file)
    const fileInput = document.getElementById('evidence');
    if (fileInput && fileInput.files.length > 0) {
        const maxSize = 10 * 1024 * 1024; // 10MB in bytes
        
        for (let file of fileInput.files) {
            if (file.size > maxSize) {
                showToast(`Le fichier "${file.name}" dépasse la taille maximale de 10 MB`, 'error', 5000);
                isValid = false;
                break;
            }
        }
    }
    
    return isValid;
}

// Gestion de la prévisualisation des fichiers
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('evidence');
    const filesPreview = document.getElementById('filesPreview');
    
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            filesPreview.innerHTML = '';
            
            Array.from(e.target.files).forEach((file, index) => {
                const fileItem = document.createElement('div');
                fileItem.className = 'file-item';
                fileItem.innerHTML = `
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span>📄</span>
                        <span>${file.name}</span>
                        <span style="color: var(--color-text-light); font-size: 0.875rem;">(${(file.size / 1024).toFixed(1)} KB)</span>
                    </div>
                    <button type="button" class="btn-remove-file" data-index="${index}">&times;</button>
                `;
                filesPreview.appendChild(fileItem);
            });
            
            // Gérer la suppression de fichiers
            document.querySelectorAll('.btn-remove-file').forEach(btn => {
                btn.addEventListener('click', function() {
                    const index = parseInt(this.getAttribute('data-index'));
                    const dt = new DataTransfer();
                    const files = fileInput.files;
                    
                    for (let i = 0; i < files.length; i++) {
                        if (i !== index) {
                            dt.items.add(files[i]);
                        }
                    }
                    
                    fileInput.files = dt.files;
                    fileInput.dispatchEvent(new Event('change'));
                });
            });
        });
    }
});

// ============================================
// MULTI-STEP NAVIGATION WITH VALIDATION
// ============================================

// Handle "Next" button clicks
document.querySelectorAll('[data-next-step]').forEach(btn => {
    btn.addEventListener('click', function() {
        const currentStepElement = document.querySelector('.form-step.active');
        const stepNumber = parseInt(currentStepElement.getAttribute('data-step'));
        
        let isValid = false;
        
        // Validate current step
        if (stepNumber === 1) {
            isValid = validateStep1();
        } else if (stepNumber === 2) {
            isValid = validateStep2();
        }
        
        // Only proceed if validation passes
        if (isValid) {
            goToStep(stepNumber + 1);
        } else {
            showToast('Veuillez corriger les erreurs avant de continuer', 'error', 3000);
        }
    });
});

// Handle "Previous" button clicks
document.querySelectorAll('[data-prev-step]').forEach(btn => {
    btn.addEventListener('click', function() {
        const currentStepElement = document.querySelector('.form-step.active');
        const stepNumber = parseInt(currentStepElement.getAttribute('data-step'));
        goToStep(stepNumber - 1);
    });
});

function goToStep(step) {
    // Hide all steps
    document.querySelectorAll('.form-step').forEach(s => s.classList.remove('active'));
    
    // Show target step
    const targetStep = document.querySelector(`.form-step[data-step="${step}"]`);
    if (targetStep) {
        targetStep.classList.add('active');
        currentStep = step;
        
        // Update progress bar
        const progress = (step / 3) * 100;
        const progressBar = document.querySelector('.progress-bar');
        if (progressBar) {
            progressBar.style.width = progress + '%';
        }
        
        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

// ============================================
// FORM SUBMISSION WITH VALIDATION
// ============================================

document.getElementById('reportForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Final validation on submit
    if (!validateStep3()) {
        showToast('Veuillez corriger les erreurs avant de soumettre', 'error', 3000);
        return;
    }
    
    // Récupération des données du formulaire
    const formData = new FormData(this);
    const reportData = {
        type: formData.get('incidentType'),
        role: formData.get('role'),
        title: formData.get('title'),
        description: formData.get('description'),
        location: formData.get('location'),
        date: formData.get('incidentDate'),
        time: formData.get('incidentTime'),
        witnesses: formData.get('witnesses'),
        reportedTo: formData.getAll('reportedTo'),
        additionalInfo: formData.get('additionalInfo'),
        createdAt: new Date().toISOString()
    };
    
    // TODO: Envoyer les données au serveur via API
    console.log('Données du signalement:', reportData);
    
    // Afficher un message de succès
    showToast('✓ Signalement créé avec succès ! Un conseiller vous contactera sous peu.', 'success', 5000);
    
    // Rediriger vers la liste des signalements après 2 secondes
    setTimeout(() => {
        window.location.href = 'my-reports.html';
    }, 2000);
});

// Animation des cartes de type au survol
document.querySelectorAll('.type-card').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-5px) scale(1.02)';
    });
    
    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0) scale(1)';
    });
});

