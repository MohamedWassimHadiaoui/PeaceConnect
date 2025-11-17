/* ============================================
   PEACECONNECT - FONCTIONS UTILITAIRES
   ============================================ */

/**
 * Formatage de date
 * @param {Date|string} date - Date à formater
 * @param {string} format - Format souhaité (default: 'dd/mm/yyyy')
 * @returns {string} Date formatée
 */
function formatDate(date, format = 'dd/mm/yyyy') {
    const d = new Date(date);
    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = d.getFullYear();
    const hours = String(d.getHours()).padStart(2, '0');
    const minutes = String(d.getMinutes()).padStart(2, '0');
    
    return format
        .replace('dd', day)
        .replace('mm', month)
        .replace('yyyy', year)
        .replace('hh', hours)
        .replace('ii', minutes);
}

/**
 * Validation d'email
 * @param {string} email - Email à valider
 * @returns {boolean} True si valide
 */
function isValidEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

/**
 * Validation de mot de passe
 * @param {string} password - Mot de passe à valider
 * @param {number} minLength - Longueur minimale (default: 8)
 * @returns {object} {valid: boolean, errors: array}
 */
function validatePassword(password, minLength = 8) {
    const errors = [];
    
    if (password.length < minLength) {
        errors.push(`Le mot de passe doit contenir au moins ${minLength} caractères`);
    }
    
    if (!/[A-Z]/.test(password)) {
        errors.push('Le mot de passe doit contenir au moins une majuscule');
    }
    
    if (!/[a-z]/.test(password)) {
        errors.push('Le mot de passe doit contenir au moins une minuscule');
    }
    
    if (!/[0-9]/.test(password)) {
        errors.push('Le mot de passe doit contenir au moins un chiffre');
    }
    
    if (!/[^A-Za-z0-9]/.test(password)) {
        errors.push('Le mot de passe doit contenir au moins un caractère spécial');
    }
    
    return {
        valid: errors.length === 0,
        errors: errors
    };
}

/**
 * Debounce pour limiter les appels de fonction
 * @param {Function} func - Fonction à débouncer
 * @param {number} wait - Délai en ms (default: 300)
 * @returns {Function} Fonction débouncée
 */
function debounce(func, wait = 300) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Throttle pour limiter les appels de fonction
 * @param {Function} func - Fonction à throttler
 * @param {number} limit - Délai en ms (default: 300)
 * @returns {Function} Fonction throttlée
 */
function throttle(func, limit = 300) {
    let inThrottle;
    return function(...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

/**
 * Génération d'un ID unique
 * @returns {string} ID unique
 */
function generateId() {
    return Date.now().toString(36) + Math.random().toString(36).substr(2);
}

/**
 * Copie de texte dans le presse-papiers
 * @param {string} text - Texte à copier
 * @returns {Promise<boolean>} True si succès
 */
async function copyToClipboard(text) {
    try {
        await navigator.clipboard.writeText(text);
        return true;
    } catch (err) {
        // Fallback pour navigateurs plus anciens
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.opacity = '0';
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            document.body.removeChild(textArea);
            return true;
        } catch (err) {
            document.body.removeChild(textArea);
            return false;
        }
    }
}

/**
 * Affichage d'une notification toast
 * @param {string} message - Message à afficher
 * @param {string} type - Type (success, error, warning, info)
 * @param {number} duration - Durée d'affichage en ms (default: 3000)
 */
function showToast(message, type = 'info', duration = 3000) {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type}`;
    toast.textContent = message;
    toast.style.position = 'fixed';
    toast.style.top = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '3000';
    toast.style.minWidth = '300px';
    toast.style.animation = 'slideIn 0.3s ease-out';
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'fadeOut 0.3s ease-out';
        setTimeout(() => {
            if (toast.parentElement) {
                document.body.removeChild(toast);
            }
        }, 300);
    }, duration);
}

/**
 * Formatage de nombre avec séparateurs
 * @param {number} num - Nombre à formater
 * @returns {string} Nombre formaté
 */
function formatNumber(num) {
    return new Intl.NumberFormat('fr-FR').format(num);
}

/**
 * Troncature de texte
 * @param {string} text - Texte à tronquer
 * @param {number} maxLength - Longueur maximale (default: 100)
 * @returns {string} Texte tronqué
 */
function truncateText(text, maxLength = 100) {
    if (text.length <= maxLength) return text;
    return text.substr(0, maxLength) + '...';
}

/**
 * Récupération de paramètres URL
 * @param {string} param - Nom du paramètre
 * @returns {string|null} Valeur du paramètre
 */
function getUrlParameter(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
}

/**
 * Définition d'un paramètre URL
 * @param {string} param - Nom du paramètre
 * @param {string} value - Valeur du paramètre
 */
function setUrlParameter(param, value) {
    const url = new URL(window.location);
    url.searchParams.set(param, value);
    window.history.pushState({}, '', url);
}

/**
 * Suppression d'un paramètre URL
 * @param {string} param - Nom du paramètre
 */
function removeUrlParameter(param) {
    const url = new URL(window.location);
    url.searchParams.delete(param);
    window.history.pushState({}, '', url);
}

/**
 * Vérification si un élément est visible dans le viewport
 * @param {HTMLElement} element - Élément à vérifier
 * @returns {boolean} True si visible
 */
function isInViewport(element) {
    const rect = element.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

/**
 * Animation de scroll fluide vers un élément
 * @param {HTMLElement|string} target - Élément ou sélecteur
 * @param {number} offset - Décalage en px (default: 0)
 */
function smoothScrollTo(target, offset = 0) {
    const element = typeof target === 'string' ? document.querySelector(target) : target;
    if (element) {
        const elementPosition = element.getBoundingClientRect().top;
        const offsetPosition = elementPosition + window.pageYOffset - offset;
        
        window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth'
        });
    }
}

/**
 * Simulation de suggestion IA (pour démonstration)
 * @param {string} input - Texte d'entrée
 * @returns {Promise<string>} Suggestion générée
 */
async function generateAISuggestion(input) {
    // Simulation d'un délai d'API
    await new Promise(resolve => setTimeout(resolve, 1000));
    
    // Suggestions prédéfinies basées sur des mots-clés
    const suggestions = {
        'violence': 'Je recommande de contacter immédiatement les services d\'urgence (197) et de signaler l\'incident sur la plateforme.',
        'harcèlement': 'Pour le harcèlement, documentez tous les incidents avec dates et preuves. Contactez un conseiller juridique si nécessaire.',
        'discrimination': 'La discrimination est illégale. Rassemblez des preuves et contactez le Défenseur des droits.',
        'aide': 'Plusieurs ressources sont disponibles : assistance juridique, soutien psychologique, et médiation communautaire.',
        'conflit': 'Pour résoudre un conflit, je suggère de commencer par une médiation avec un tiers neutre.'
    };
    
    const lowerInput = input.toLowerCase();
    for (const [keyword, suggestion] of Object.entries(suggestions)) {
        if (lowerInput.includes(keyword)) {
            return suggestion;
        }
    }
    
    return 'Je vous recommande de remplir le formulaire avec le maximum de détails. Un conseiller vous contactera dans les plus brefs délais pour vous accompagner.';
}

/**
 * Export des fonctions utilitaires
 */
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        formatDate,
        isValidEmail,
        validatePassword,
        debounce,
        throttle,
        generateId,
        copyToClipboard,
        showToast,
        formatNumber,
        truncateText,
        getUrlParameter,
        setUrlParameter,
        removeUrlParameter,
        isInViewport,
        smoothScrollTo,
        generateAISuggestion
    };
}

