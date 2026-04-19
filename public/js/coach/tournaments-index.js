function applyCoachFilters() {
    const ageGroupFilter = (document.getElementById('coachAgeGroupFilter')?.value || '').toLowerCase();
    const statusFilter = (document.getElementById('coachStatusFilter')?.value || '').toLowerCase();
    const cards = document.querySelectorAll('.t-card');

    cards.forEach(function(card) {
        const cardAgeGroup = (card.getAttribute('data-age-group') || '').toLowerCase();
        const cardStatus = (card.getAttribute('data-status') || '').toLowerCase();
        const matchesAgeGroup = !ageGroupFilter || cardAgeGroup === ageGroupFilter;
        const matchesStatus = !statusFilter || cardStatus === statusFilter;

        card.classList.toggle('hidden', !(matchesAgeGroup && matchesStatus));
    });
}

function resetCoachFilters() {
    const ageGroupFilter = document.getElementById('coachAgeGroupFilter');
    const statusFilter = document.getElementById('coachStatusFilter');

    if (ageGroupFilter) {
        ageGroupFilter.value = '';
    }

    if (statusFilter) {
        statusFilter.value = '';
    }

    applyCoachFilters();
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('coachAgeGroupFilter')?.addEventListener('change', applyCoachFilters);
    document.getElementById('coachStatusFilter')?.addEventListener('change', applyCoachFilters);
});

window.resetCoachFilters = resetCoachFilters;
