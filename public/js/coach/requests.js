document.addEventListener('DOMContentLoaded', function() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    const requestItems = document.querySelectorAll('.request-item');

    tabBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            tabBtns.forEach(function(tab) {
                tab.classList.remove('active');
            });

            btn.classList.add('active');
            const filter = btn.getAttribute('data-filter') || 'all';

            requestItems.forEach(function(item) {
                const status = item.getAttribute('data-status') || '';
                item.style.display = filter === 'all' || status === filter ? 'block' : 'none';
            });
        });
    });
});
