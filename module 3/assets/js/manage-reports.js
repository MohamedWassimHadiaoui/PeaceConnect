/* ============================================
   MODULE 3 - MANAGE REPORTS
   Gestion des signalements (Admin)
   ============================================ */

document.addEventListener('DOMContentLoaded', function() {
    initFilters();
    loadAllReports();
});

/**
 * Initialiser les filtres
 */
function initFilters() {
    const statusFilter = document.getElementById('statusFilter');
    const typeFilter = document.getElementById('typeFilter');
    const priorityFilter = document.getElementById('priorityFilter');
    const dateFilter = document.getElementById('dateFilter');
    
    [statusFilter, typeFilter, priorityFilter, dateFilter].forEach(filter => {
        if (filter) {
            filter.addEventListener('change', applyFilters);
        }
    });
}

/**
 * Appliquer les filtres
 */
function applyFilters() {
    const statusValue = document.getElementById('statusFilter')?.value || '';
    const typeValue = document.getElementById('typeFilter')?.value || '';
    const priorityValue = document.getElementById('priorityFilter')?.value || '';
    const dateValue = document.getElementById('dateFilter')?.value || '';
    
    const rows = document.querySelectorAll('.table tbody tr');
    
    rows.forEach(row => {
        const statusMatch = !statusValue || row.querySelector('.badge')?.textContent.includes(statusValue);
        const typeMatch = !typeValue || row.querySelector('.tag')?.textContent.includes(typeValue);
        // Ajouter d'autres conditions de filtrage ici
        
        if (statusMatch && typeMatch) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

/**
 * Charger tous les signalements
 */
async function loadAllReports() {
    // TODO: Implémenter l'appel API
    // const response = await fetch('/api/admin/reports');
    // const reports = await response.json();
    // renderReportsTable(reports);
}

/**
 * Rendre le tableau des signalements
 */
function renderReportsTable(reports) {
    const tbody = document.querySelector('.table tbody');
    tbody.innerHTML = '';
    
    reports.forEach(report => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${report.id}</td>
            <td><span class="tag tag-primary">${getTypeLabel(report.type)}</span></td>
            <td>${report.title}</td>
            <td>${report.author}</td>
            <td>${getStatusBadge(report.status)}</td>
            <td>${getPriorityBadge(report.priority)}</td>
            <td>${formatDate(report.createdAt)}</td>
            <td>
                <button class="btn btn-outline btn-sm" data-modal="reportModal${report.id}">Voir</button>
                <a href="assign-mediator.html?id=${report.id}" class="btn btn-primary btn-sm">Assigner</a>
            </td>
        `;
        tbody.appendChild(row);
    });
}

/**
 * Obtenir le libellé du type
 */
function getTypeLabel(type) {
    const types = {
        conflict: 'Conflit',
        harassment: 'Harcèlement',
        violence: 'Violence',
        discrimination: 'Discrimination'
    };
    return types[type] || type;
}

/**
 * Obtenir le badge de statut
 */
function getStatusBadge(status) {
    const statuses = {
        new: '<span class="badge badge-info">Nouveau</span>',
        pending: '<span class="badge badge-warning">En attente</span>',
        assigned: '<span class="badge badge-info">Assigné</span>',
        in_mediation: '<span class="badge badge-warning">En médiation</span>',
        resolved: '<span class="badge badge-success">Résolu</span>',
        closed: '<span class="badge">Fermé</span>'
    };
    return statuses[status] || status;
}

/**
 * Obtenir le badge de priorité
 */
function getPriorityBadge(priority) {
    const priorities = {
        critical: '<span class="badge badge-danger">Critique</span>',
        high: '<span class="badge badge-danger">Haute</span>',
        medium: '<span class="badge badge-info">Moyenne</span>',
        low: '<span class="badge badge-info">Faible</span>'
    };
    return priorities[priority] || priority;
}

