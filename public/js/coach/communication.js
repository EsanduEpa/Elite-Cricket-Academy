document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('coachSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mainContent = document.querySelector('.main-content');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');

            const icon = this.querySelector('i');
            if (sidebar.classList.contains('collapsed')) {
                icon.classList.remove('fa-angle-left');
                icon.classList.add('fa-angle-right');
                if (mainContent) {
                    mainContent.style.marginLeft = '80px';
                }
            } else {
                icon.classList.remove('fa-angle-right');
                icon.classList.add('fa-angle-left');
                if (mainContent) {
                    mainContent.style.marginLeft = '280px';
                }
            }
        });
    }
});