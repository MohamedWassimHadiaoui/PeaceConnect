/* ============================================
   PEACECONNECT - SCRIPTS PRINCIPAUX
   ============================================ */

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    initNavigation();
    initModals();
    initForms();
    initTabs();
    initTables();
    initFilters();
    initTooltips();
    initFloatingLabels();
    initStatCounters();
});

/**
 * Floating labels: toggle .is-active on labels when inputs have focus or value
 */
function initFloatingLabels() {
    const groups = document.querySelectorAll('.form-group');
    groups.forEach(group => {
        const label = group.querySelector('.form-label');
        const input = group.querySelector('.form-control');
        if (!label || !input) return;

        // Ensure label has floating class for styling
        label.classList.add('floating');

        const setActive = () => {
            if (input.value && input.value.trim() !== '') {
                label.classList.add('is-active');
                input.classList.add('filled');
            } else {
                label.classList.remove('is-active');
                input.classList.remove('filled');
            }
        };

        input.addEventListener('focus', () => label.classList.add('is-active'));
        input.addEventListener('blur', setActive);
        input.addEventListener('input', setActive);

        // initialize state
        setActive();
    });
}

/**
 * Stat counters: animate numbers from 0 -> value when visible
 */
function initStatCounters() {
    const counters = document.querySelectorAll('.stat-value');
    if (!counters.length) return;

    const animate = (el, to) => {
        const duration = 800;
        const start = performance.now();
        const from = 0;
        const raf = (now) => {
            const t = Math.min(1, (now - start) / duration);
            const eased = t < 0.5 ? 2 * t * t : -1 + (4 - 2 * t) * t; // easeInOut
            el.textContent = Math.round(from + (to - from) * eased);
            if (t < 1) requestAnimationFrame(raf);
        };
        requestAnimationFrame(raf);
    };

    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                const value = parseInt(el.dataset-value || el.textContent || '0', 10) || 0;
                animate(el, value);
                observer.unobserve(el);
            }
        });
    }, { threshold: 0.3 });

    counters.forEach(el => {
        // store numeric target in data-value if empty
        const v = parseInt(el.textContent.replace(/[^0-9]/g, ''), 10) || 0;
        el.dataset.value = v;
        el.textContent = '0';
        observer.observe(el);
    });
}

/**
 * Initialisation de la navigation mobile
 */
function initNavigation() {
    const navbarToggle = document.querySelector('.navbar-toggle');
    const navbarMenu = document.querySelector('.navbar-menu');
    
    if (navbarToggle && navbarMenu) {
        navbarToggle.addEventListener('click', function() {
            navbarMenu.classList.toggle('active');
            const icon = navbarToggle.querySelector('i') || navbarToggle;
            icon.textContent = navbarMenu.classList.contains('active') ? '✕' : '☰';
        });
        
        // Fermer le menu en cliquant sur un lien
        const menuLinks = navbarMenu.querySelectorAll('a');
        menuLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    navbarMenu.classList.remove('active');
                    const icon = navbarToggle.querySelector('i') || navbarToggle;
                    icon.textContent = '☰';
                }
            });
        });
        
        // Fermer le menu en cliquant en dehors
        document.addEventListener('click', function(e) {
            if (!navbarMenu.contains(e.target) && !navbarToggle.contains(e.target)) {
                navbarMenu.classList.remove('active');
                const icon = navbarToggle.querySelector('i') || navbarToggle;
                icon.textContent = '☰';
            }
        });
    }
    
    // Navigation sticky avec ombre
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        window.addEventListener('scroll', throttle(function() {
            if (window.scrollY > 10) {
                navbar.style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.15)';
            } else {
                navbar.style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.1)';
            }
        }, 100));
    }
}

/**
 * Initialisation des modales
 */
function initModals() {
    // Ouvrir une modale
    const modalTriggers = document.querySelectorAll('[data-modal]');
    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            const modalId = this.getAttribute('data-modal');
            openModal(modalId);
        });
    });
    
    // Fermer une modale
    const modalCloses = document.querySelectorAll('.modal-close, [data-close-modal]');
    modalCloses.forEach(close => {
        close.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) {
                closeModal(modal.id);
            }
        });
    });
    
    // Fermer en cliquant en dehors
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal(this.id);
            }
        });
    });
    
    // Fermer avec la touche Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const openModal = document.querySelector('.modal.show');
            if (openModal) {
                closeModal(openModal.id);
            }
        }
    });
}

/**
 * Ouvrir une modale
 * @param {string} modalId - ID de la modale
 */
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
        
        // Focus sur le premier élément interactif
        const firstInput = modal.querySelector('input, textarea, select, button');
        if (firstInput) {
            setTimeout(() => firstInput.focus(), 100);
        }
    }
}

/**
 * Fermer une modale
 * @param {string} modalId - ID de la modale
 */
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = '';
    }
}

/**
 * Initialisation des formulaires
 */
function initForms() {
    // Validation en temps réel
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
            
            input.addEventListener('input', debounce(function() {
                if (this.classList.contains('error')) {
                    validateField(this);
                }
            }, 300));
        });
        
        // Validation à la soumission
        form.addEventListener('submit', function(e) {
            let isValid = true;
            inputs.forEach(input => {
                if (!validateField(input)) {
                    isValid = false;
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showToast('Veuillez corriger les erreurs dans le formulaire', 'error');
            }
        });
    });
    
    // Formulaires multi-étapes
    const multiStepForms = document.querySelectorAll('.multi-step-form');
    multiStepForms.forEach(form => {
        initMultiStepForm(form);
    });
}

/**
 * Validation d'un champ de formulaire
 * @param {HTMLElement} field - Champ à valider
 * @returns {boolean} True si valide
 */
function validateField(field) {
    const value = field.value.trim();
    const type = field.type;
    const required = field.hasAttribute('required');
    const errorElement = field.parentElement.querySelector('.form-error');
    
    let isValid = true;
    let errorMessage = '';
    
    // Vérification du champ requis
    if (required && !value) {
        isValid = false;
        errorMessage = 'Ce champ est obligatoire';
    }
    
    // Validation email
    if (type === 'email' && value && !isValidEmail(value)) {
        isValid = false;
        errorMessage = 'Veuillez entrer une adresse email valide';
    }
    
    // Validation mot de passe
    if (type === 'password' && value) {
        const validation = validatePassword(value);
        if (!validation.valid) {
            isValid = false;
            errorMessage = validation.errors[0];
        }
    }
    
    // Validation confirmation mot de passe
    if (field.hasAttribute('data-confirm-password')) {
        const passwordField = document.querySelector(field.getAttribute('data-confirm-password'));
        if (passwordField && value !== passwordField.value) {
            isValid = false;
            errorMessage = 'Les mots de passe ne correspondent pas';
        }
    }
    
    // Affichage de l'erreur
    if (errorElement) {
        if (isValid) {
            field.classList.remove('error');
            errorElement.classList.remove('show');
        } else {
            field.classList.add('error');
            errorElement.classList.add('show');
            errorElement.textContent = errorMessage;
        }
    }
    
    return isValid;
}

/**
 * Initialisation d'un formulaire multi-étapes
 * @param {HTMLElement} form - Formulaire
 */
function initMultiStepForm(form) {
    const steps = form.querySelectorAll('.form-step');
    const nextButtons = form.querySelectorAll('[data-next-step]');
    const prevButtons = form.querySelectorAll('[data-prev-step]');
    const progressBar = form.querySelector('.progress-bar');
    
    let currentStep = 0;
    
    // Afficher la première étape
    showStep(0);
    
    // Boutons suivant
    nextButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const currentStepElement = steps[currentStep];
            const inputs = currentStepElement.querySelectorAll('input[required], textarea[required], select[required]');
            
            let isValid = true;
            inputs.forEach(input => {
                if (!validateField(input)) {
                    isValid = false;
                }
            });
            
            if (isValid) {
                if (currentStep < steps.length - 1) {
                    currentStep++;
                    showStep(currentStep);
                    updateProgress();
                }
            }
        });
    });
    
    // Boutons précédent
    prevButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            if (currentStep > 0) {
                currentStep--;
                showStep(currentStep);
                updateProgress();
            }
        });
    });
    
    function showStep(stepIndex) {
        steps.forEach((step, index) => {
            if (index === stepIndex) {
                step.classList.add('active');
            } else {
                step.classList.remove('active');
            }
        });
    }
    
    function updateProgress() {
        if (progressBar) {
            const progress = ((currentStep + 1) / steps.length) * 100;
            progressBar.style.width = progress + '%';
        }
    }
}

/**
 * Initialisation des onglets
 */
function initTabs() {
    const tabContainers = document.querySelectorAll('.tabs-container');
    
    tabContainers.forEach(container => {
        const tabs = container.querySelectorAll('.tab-item');
        const contents = container.querySelectorAll('.tab-content');
        
        tabs.forEach((tab, index) => {
            tab.addEventListener('click', function() {
                // Désactiver tous les onglets
                tabs.forEach(t => t.classList.remove('active'));
                contents.forEach(c => c.classList.remove('active'));
                
                // Activer l'onglet cliqué
                tab.classList.add('active');
                if (contents[index]) {
                    contents[index].classList.add('active');
                }
            });
        });
    });
}

/**
 * Initialisation des tableaux triables
 */
function initTables() {
    const sortableHeaders = document.querySelectorAll('.table th.sortable');
    
    sortableHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const table = this.closest('table');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const columnIndex = Array.from(this.parentElement.children).indexOf(this);
            const currentSort = this.classList.contains('sort-asc') ? 'asc' : 
                              this.classList.contains('sort-desc') ? 'desc' : null;
            
            // Réinitialiser les autres en-têtes
            sortableHeaders.forEach(h => {
                h.classList.remove('sort-asc', 'sort-desc');
            });
            
            // Trier les lignes
            rows.sort((a, b) => {
                const aText = a.children[columnIndex].textContent.trim();
                const bText = b.children[columnIndex].textContent.trim();
                
                // Essayer de parser comme nombre
                const aNum = parseFloat(aText);
                const bNum = parseFloat(bText);
                
                let comparison = 0;
                if (!isNaN(aNum) && !isNaN(bNum)) {
                    comparison = aNum - bNum;
                } else {
                    comparison = aText.localeCompare(bText, 'fr');
                }
                
                return currentSort === 'asc' ? -comparison : comparison;
            });
            
            // Réinsérer les lignes triées
            rows.forEach(row => tbody.appendChild(row));
            
            // Mettre à jour l'état de tri
            this.classList.remove('sort-asc', 'sort-desc');
            this.classList.add(currentSort === 'asc' ? 'sort-desc' : 'sort-asc');
        });
    });
}

/**
 * Initialisation des filtres
 */
function initFilters() {
    const filterInputs = document.querySelectorAll('.filter-input');
    
    filterInputs.forEach(input => {
        input.addEventListener('input', debounce(function() {
            const filterValue = this.value.toLowerCase();
            const filterTarget = this.getAttribute('data-filter');
            const items = document.querySelectorAll(filterTarget);
            
            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                if (text.includes(filterValue)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        }, 300));
    });
}

/**
 * Initialisation des tooltips
 */
function initTooltips() {
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', function(e) {
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = this.getAttribute('data-tooltip');
            tooltip.style.position = 'absolute';
            tooltip.style.background = '#1f2937';
            tooltip.style.color = '#fff';
            tooltip.style.padding = '8px 12px';
            tooltip.style.borderRadius = '4px';
            tooltip.style.fontSize = '0.875rem';
            tooltip.style.zIndex = '10000';
            tooltip.style.pointerEvents = 'none';
            document.body.appendChild(tooltip);
            
            const rect = this.getBoundingClientRect();
            tooltip.style.top = (rect.top - tooltip.offsetHeight - 8) + 'px';
            tooltip.style.left = (rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2)) + 'px';
            
            this._tooltip = tooltip;
        });
        
        element.addEventListener('mouseleave', function() {
            if (this._tooltip) {
                document.body.removeChild(this._tooltip);
                this._tooltip = null;
            }
        });
    });
}

/**
 * Export des fonctions principales
 */
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        openModal,
        closeModal,
        validateField,
        initNavigation,
        initModals,
        initForms,
        initTabs,
        initTables,
        initFilters
    };
}

