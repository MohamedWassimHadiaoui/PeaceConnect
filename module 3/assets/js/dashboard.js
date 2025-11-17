/* ============================================
   MODULE 3 - DASHBOARD
   Tableau de bord administrateur
   ============================================ */

document.addEventListener('DOMContentLoaded', function() {
    loadDashboardData();
    updateKPIs();
});

/**
 * Charger les données du tableau de bord
 */
async function loadDashboardData() {
    // TODO: Implémenter l'appel API
    // const response = await fetch('/api/admin/dashboard');
    // const data = await response.json();
    
    // Simuler les données pour la démo
    const data = {
        totalReports: 247,
        pendingReports: 42,
        assignedReports: 89,
        resolvedReports: 116,
        resolutionRate: 87
    };
    
    updateKPIs(data);
}

/**
 * Mettre à jour les KPIs
 */
function updateKPIs(data) {
    if (!data) return;
    
    const totalEl = document.getElementById('totalReports');
    const pendingEl = document.getElementById('pendingReports');
    const assignedEl = document.getElementById('assignedReports');
    const resolvedEl = document.getElementById('resolvedReports');
    
    if (totalEl) animateNumber(totalEl, data.totalReports);
    if (pendingEl) animateNumber(pendingEl, data.pendingReports);
    if (assignedEl) animateNumber(assignedEl, data.assignedReports);
    if (resolvedEl) {
        resolvedEl.textContent = data.resolutionRate + '%';
    }
}

/**
 * Animer un nombre
 */
function animateNumber(element, target) {
    const duration = 1000;
    const start = 0;
    const startTime = performance.now();
    
    function update(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        const current = Math.floor(progress * target);
        element.textContent = current;
        
        if (progress < 1) {
            requestAnimationFrame(update);
        }
    }
    
    requestAnimationFrame(update);
}

/**
 * Charger les signalements récents
 */
async function loadRecentReports() {
    // TODO: Implémenter l'appel API
    // const response = await fetch('/api/admin/reports/recent');
    // const reports = await response.json();
}

