document.addEventListener('DOMContentLoaded', function() {
    const filter = document.getElementById('coachPerformanceAgeGroupFilter');
    const rows = Array.from(document.querySelectorAll('.coach-performance-row'));

    if (!filter || rows.length === 0) {
        return;
    }

    const applyFilter = function() {
        const selected = (filter.value || 'all').toLowerCase();

        rows.forEach(function(row) {
            const raw = (row.getAttribute('data-age-groups') || '').toLowerCase();

            if (selected === 'all') {
                row.style.display = '';
                return;
            }

            const groups = raw.split(',').map(function(group) {
                return group.trim();
            }).filter(Boolean);

            row.style.display = groups.includes(selected) ? '' : 'none';
        });
    };

    filter.addEventListener('change', applyFilter);
    applyFilter();
});
