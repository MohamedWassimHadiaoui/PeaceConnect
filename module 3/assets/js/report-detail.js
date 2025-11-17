/* ============================================
   MODULE 3 - REPORT DETAIL
   Affichage des détails d'un signalement
   ============================================ */

document.addEventListener('DOMContentLoaded', function() {
    // Récupérer l'ID du signalement depuis l'URL
    const reportId = getUrlParameter('id');
    
    if (reportId) {
        loadReportDetails(reportId);
    }
});

/**
 * Charger les détails du signalement
 */
async function loadReportDetails(reportId) {
    // TODO: Implémenter l'appel API
    // const response = await fetch(`/api/reports/${reportId}`);
    // const report = await response.json();
    
    // Simuler les données pour la démo
    const report = {
        id: reportId,
        type: 'conflict',
        title: 'Conflit au travail avec collègue',
        description: 'Un conflit est survenu avec un collègue lors d\'une réunion d\'équipe. Des propos déplacés ont été tenus concernant mes compétences professionnelles, créant un climat de travail hostile. Plusieurs témoins étaient présents lors de l\'incident.',
        status: 'in_mediation',
        location: 'Bureau Tunis',
        date: '2025-11-14',
        time: '10:30',
        witnesses: 'yes',
        createdAt: '2025-11-15T14:20:00Z',
        mediator: {
            name: 'Ahmed Ben Ali',
            specialty: 'Conflits professionnels',
            rating: 4.8,
            nextSession: '2025-11-20T14:00:00Z',
            location: 'Bureau de médiation, Tunis'
        },
        evidence: [
            { name: 'Email_echange.pdf', type: 'pdf' },
            { name: 'Photo_incident.jpg', type: 'image' }
        ]
    };
    
    renderReportDetails(report);
}

/**
 * Afficher les détails du signalement
 */
function renderReportDetails(report) {
    // Mettre à jour les éléments de la page
    document.getElementById('reportId').textContent = report.id;
    document.getElementById('reportType').innerHTML = `<span class="tag tag-primary">${getTypeLabel(report.type)}</span>`;
    document.getElementById('reportTitle').textContent = report.title;
    document.getElementById('reportStatus').innerHTML = getStatusBadge(report.status);
    document.getElementById('reportDescription').textContent = report.description;
    document.getElementById('reportLocation').textContent = report.location;
    document.getElementById('reportDate').textContent = formatDate(report.date) + ' à ' + report.time;
    document.getElementById('reportWitnesses').textContent = report.witnesses === 'yes' ? 'Oui' : 'Non';
    document.getElementById('reportCreatedDate').textContent = formatDate(report.createdAt);
    
    // Mettre à jour la timeline selon le statut
    updateTimeline(report.status);
    
    // Afficher les informations du médiateur si assigné
    if (report.mediator) {
        document.getElementById('mediationDate').textContent = formatDate(report.mediator.nextSession);
        document.getElementById('mediationLocation').textContent = report.mediator.location;
    }
}

/**
 * Mettre à jour la timeline selon le statut
 */
function updateTimeline(status) {
    const steps = document.querySelectorAll('.timeline-step');
    
    // Réinitialiser
    steps.forEach(step => {
        step.classList.remove('completed', 'active');
    });
    
    // Marquer les étapes selon le statut
    if (status === 'pending') {
        steps[0].classList.add('completed');
        steps[1].classList.add('active');
    } else if (status === 'assigned') {
        steps[0].classList.add('completed');
        steps[1].classList.add('completed');
        steps[2].classList.add('active');
    } else if (status === 'in_mediation') {
        steps[0].classList.add('completed');
        steps[1].classList.add('completed');
        steps[2].classList.add('active');
    } else if (status === 'resolved') {
        steps[0].classList.add('completed');
        steps[1].classList.add('completed');
        steps[2].classList.add('completed');
        steps[3].classList.add('completed');
    }
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

