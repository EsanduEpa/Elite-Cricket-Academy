document.addEventListener('DOMContentLoaded', function() {
    const reportData = window.coachReportsData || {};

    const typeCtx = document.getElementById('performanceChart');
    if (typeCtx) {
        new Chart(typeCtx, {
            type: 'doughnut',
            data: {
                labels: ['Private', 'Group'],
                datasets: [{
                    data: [
                        reportData.privateSessions || 0,
                        reportData.groupSessions || 0
                    ],
                    backgroundColor: ['#8b5cf6', '#4A90E2'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    const statusCtx = document.getElementById('attendanceChart');
    if (statusCtx) {
        new Chart(statusCtx, {
            type: 'bar',
            data: {
                labels: ['Active', 'Completed', 'Cancelled'],
                datasets: [{
                    label: 'Sessions',
                    data: [
                        reportData.activeSessions || 0,
                        reportData.completedSessions || 0,
                        Math.max((reportData.totalSessions || 0) - (reportData.activeSessions || 0) - (reportData.completedSessions || 0), 0)
                    ],
                    backgroundColor: ['#10b981', '#4A90E2', '#ef4444'],
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    }

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