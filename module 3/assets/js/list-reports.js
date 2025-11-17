/* ============================================
   MODULE 3 - LIST REPORTS
   Gestion de la liste des signalements utilisateur
   ============================================ */

document.addEventListener('DOMContentLoaded', function() {
    // Filtrage par statut
    const statusFilter = document.getElementById('statusFilter');
    const typeFilter = document.getElementById('typeFilter');
    const tableRows = document.querySelectorAll('#reportsTable tbody tr');
    const emptyState = document.getElementById('emptyState');
    
    function applyFilters() {
        const selectedStatus = statusFilter ? statusFilter.value : '';
        const selectedType = typeFilter ? typeFilter.value : '';
        let visibleCount = 0;
        
        tableRows.forEach(row => {
            const rowStatus = row.getAttribute('data-status');
            const rowType = row.getAttribute('data-type');
            
            const statusMatch = !selectedStatus || rowStatus === selectedStatus;
            const typeMatch = !selectedType || rowType === selectedType;
            
            if (statusMatch && typeMatch) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Afficher l'état vide si aucun résultat
        if (emptyState) {
            emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }
    
    if (statusFilter) {
        statusFilter.addEventListener('change', applyFilters);
    }
    
    if (typeFilter) {
        typeFilter.addEventListener('change', applyFilters);
    }
    
    // Initialiser l'affichage
    applyFilters();
});

/**
 * Charger les signalements depuis le serveur (à implémenter)
 */
async function loadReports() {
    // TODO: Implémenter l'appel API
    // const response = await fetch('/api/reports/user');
    // const reports = await response.json();
    // renderReports(reports);
}

/**
 * Rendre les signalements dans le tableau
 */
function renderReports(reports) {
    const tbody = document.querySelector('#reportsTable tbody');
    tbody.innerHTML = '';
    
    reports.forEach(report => {
        const row = document.createElement('tr');
        row.setAttribute('data-status', report.status);
        row.setAttribute('data-type', report.type);
        
        row.innerHTML = `
            <td>${report.id}</td>
            <td><span class="tag tag-primary">${getTypeLabel(report.type)}</span></td>
            <td>${report.title}</td>
            <td>${getStatusBadge(report.status)}</td>
            <td>${formatDate(report.createdAt)}</td>
            <td>
                <a href="report-detail.html?id=${report.id}" class="btn btn-outline btn-sm">Voir détails</a>
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
        pending: '<span class="badge badge-warning">En attente</span>',
        assigned: '<span class="badge badge-info">Assigné</span>',
        in_mediation: '<span class="badge badge-warning">En médiation</span>',
        resolved: '<span class="badge badge-success">Résolu</span>'
    };
    return statuses[status] || status;
}

