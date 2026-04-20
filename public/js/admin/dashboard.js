// Admin Dashboard JavaScript - Elite Cricket Academy

document.addEventListener('DOMContentLoaded', function() {
    initializeDashboard();
    initializeSidebar();
    initializeActivityFilters();
    
    // Initialize custom calendar
    if (document.getElementById('monthView')) {
        initializeCalendar();
    }
    
    if (document.querySelector('.summary-card canvas')) {
        // Wait for Chart.js only on dashboard variants that still render chart canvases.
        function waitForChart() {
            if (typeof Chart !== 'undefined' && window.chartJsLoaded) {
                console.log('Chart.js is available, initializing charts...');
                initializeCharts();
            } else {
                console.log('Chart.js not ready, waiting... Chart available:', typeof Chart !== 'undefined', 'Flag set:', window.chartJsLoaded);
                setTimeout(waitForChart, 100);
            }
        }

        setTimeout(waitForChart, 500);
    }
    
    updateCurrentTime();
    
    // Update time every second
    setInterval(updateCurrentTime, 1000);
});

function initializeActivityFilters() {
    const typeFilter = document.getElementById('activityTypeFilter');
    const timeFilter = document.getElementById('activityTimeFilter');

    if (typeFilter) typeFilter.addEventListener('change', filterActivities);
    if (timeFilter) timeFilter.addEventListener('change', filterActivities);
    filterActivities();
}

function filterActivities() {
    const typeFilter = document.getElementById('activityTypeFilter')?.value || 'all';
    const timeFilter = document.getElementById('activityTimeFilter')?.value || 'all';
    const rows = document.querySelectorAll('#activityTableBody tr[data-activity-type]');
    const emptyRow = document.getElementById('activityNoResultsRow');
    let visibleCount = 0;

    rows.forEach(row => {
        const activityType = row.dataset.activityType || '';
        const activityDate = row.dataset.activityDate || '';
        let showRow = typeFilter === 'all' || activityType === typeFilter;

        if (timeFilter !== 'all') {
            showRow = showRow && matchesActivityTimeFilter(activityDate, timeFilter);
        }

        row.style.display = showRow ? '' : 'none';
        if (showRow) visibleCount++;
    });

    if (emptyRow) {
        emptyRow.style.display = visibleCount === 0 ? '' : 'none';
    }
}

function matchesActivityTimeFilter(activityDate, timeFilter) {
    if (!activityDate) return false;

    const rowDate = new Date(`${activityDate}T00:00:00`);
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    if (Number.isNaN(rowDate.getTime())) return false;

    if (timeFilter === 'today') {
        return rowDate.getTime() === today.getTime();
    }

    if (timeFilter === 'week') {
        const weekStart = new Date(today);
        const dayOfWeek = weekStart.getDay() || 7; // Convert Sunday from 0 to 7.
        weekStart.setDate(weekStart.getDate() - dayOfWeek + 1);

        const weekEnd = new Date(weekStart);
        weekEnd.setDate(weekEnd.getDate() + 6);

        return rowDate >= weekStart && rowDate <= weekEnd;
    }

    if (timeFilter === 'month') {
        return rowDate.getMonth() === today.getMonth() && rowDate.getFullYear() === today.getFullYear();
    }

    return true;
}

function clearActivityFilters() {
    const typeFilter = document.getElementById('activityTypeFilter');
    const timeFilter = document.getElementById('activityTimeFilter');

    if (typeFilter) typeFilter.value = 'all';
    if (timeFilter) timeFilter.value = 'all';
    filterActivities();
}

// Dashboard Initialization
function initializeDashboard() {
    // Animate dashboard cards on load
    const cards = document.querySelectorAll('.summary-card');
    cards.forEach((card, index) => {
        setTimeout(() => {
            card.classList.add('fade-in');
        }, index * 100);
    });

    // Do not show the old automatic welcome toast on every dashboard load.
    // Real notification helpers remain available for actions that need feedback.
}

// Sidebar Functionality
function initializeSidebar() {
    const sidebar = document.getElementById('adminSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mainContent = document.getElementById('mainContent');

    // Debug logging
    console.log('Sidebar elements:', { sidebar, sidebarToggle, mainContent });

    // Sidebar toggle functionality
    if (sidebarToggle && sidebar && mainContent) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Toggle clicked, current collapsed:', sidebar.classList.contains('collapsed'));
            
            sidebar.classList.toggle('collapsed');
            
            if (sidebar.classList.contains('collapsed')) {
                console.log('Collapsing sidebar');
                mainContent.style.marginLeft = '80px';
                sidebar.style.width = '80px';
            } else {
                console.log('Expanding sidebar');
                mainContent.style.marginLeft = '280px';
                sidebar.style.width = '280px';
            }
        });
        console.log('Sidebar toggle listener added successfully');
    } else {
        console.error('Missing elements:', { 
            sidebar: !!sidebar, 
            sidebarToggle: !!sidebarToggle, 
            mainContent: !!mainContent 
        });
    }

    // Navigation item active states
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            // Only prevent default for hash links (placeholder sections)
            if (href.startsWith('#')) {
                e.preventDefault();
                
                // Remove active class from all items
                document.querySelectorAll('.nav-item').forEach(item => {
                    item.classList.remove('active');
                });
                
                // Add active class to clicked item
                this.closest('.nav-item').classList.add('active');
                
                // Show relevant content based on navigation
                const target = href.substring(1);
                showDashboardSection(target);
            }
            // For actual routes (like /admin/events), let the browser navigate normally
            // Do not prevent default - allows normal navigation
        });
    });

    // Mobile responsiveness
    handleMobileView();
}

// Chart Initialization with comprehensive data
function initializeCharts() {
    console.log('initializeCharts called, Chart.js available:', typeof Chart !== 'undefined');
    
    if (typeof Chart === 'undefined') {
        console.warn('Chart.js not loaded - loading from CDN');
        // Try to load Chart.js if not available
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
        script.onload = function() {
            console.log('Chart.js loaded dynamically');
            setTimeout(initializeCharts, 500);
        };
        document.head.appendChild(script);
        return;
    }
    
    console.log('Starting chart initialization...');

    // Destroy existing charts to prevent conflicts
    if (window.chartInstances) {
        Object.values(window.chartInstances).forEach(chart => {
            if (chart && typeof chart.destroy === 'function') {
                chart.destroy();
            }
        });
    }
    window.chartInstances = {};

    // Common chart options
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'bottom',
                labels: {
                    padding: 15,
                    usePointStyle: true,
                    font: {
                        size: 11
                    }
                }
            }
        },
        elements: {
            point: {
                radius: 3
            }
        }
    };

    // Staff Distribution Chart (Summary Card)
    const staffCtx = document.getElementById('staffChart');
    console.log('Staff chart canvas element:', staffCtx);
    if (staffCtx) {
        console.log('Creating staff chart...');
        try {
            window.chartInstances.staffChart = new Chart(staffCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Coaches', 'Trainers', 'Admin', 'Support'],
                    datasets: [{
                        data: [12, 6, 3, 3],
                        backgroundColor: ['#3498db', '#2ecc71', '#f39c12', '#e74c3c'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    ...commonOptions,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.parsed + ' staff';
                                }
                            }
                        }
                    }
                }
            });
            console.log('Staff chart created successfully!');
        } catch (error) {
            console.error('Error creating staffChart:', error);
        }
    } else {
        console.error('Staff chart canvas element not found!');
    }

    // Player Progress Chart (Summary Card)
    const playerCtx = document.getElementById('playerChart');
    if (playerCtx) {
        try {
            window.chartInstances.playerChart = new Chart(playerCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Active Players',
                        data: [120, 135, 142, 156, 148, 156],
                        borderColor: '#2ecc71',
                        backgroundColor: 'rgba(46, 204, 113, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2,
                        pointBackgroundColor: '#2ecc71',
                        pointBorderColor: '#27ae60'
                    }]
                },
                options: {
                    ...commonOptions,
                    plugins: { 
                        legend: { display: false },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: {
                        y: { 
                            display: false,
                            beginAtZero: true
                        },
                        x: { 
                            display: false 
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error creating playerChart:', error);
        }
    }

    // Events Timeline Chart (Summary Card)
    const eventsCtx = document.getElementById('eventsChart');
    if (eventsCtx) {
        try {
            window.chartInstances.eventsChart = new Chart(eventsCtx, {
                type: 'bar',
                data: {
                    labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                    datasets: [{
                        label: 'Events',
                        data: [4, 6, 3, 5],
                        backgroundColor: ['#f39c12', '#f39c12', '#f39c12', '#f39c12'],
                        borderRadius: 4,
                        borderSkipped: false
                    }]
                },
                options: {
                    ...commonOptions,
                    plugins: { 
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Events: ' + context.parsed.y;
                                }
                            }
                        }
                    },
                    scales: {
                        y: { 
                            display: false,
                            beginAtZero: true
                        },
                        x: { 
                            display: false 
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error creating eventsChart:', error);
        }
    }

    // Feedback Satisfaction Chart (Summary Card)
    const feedbackCtx = document.getElementById('feedbackChart');
    if (feedbackCtx) {
        try {
            window.chartInstances.feedbackChart = new Chart(feedbackCtx, {
                type: 'polarArea',
                data: {
                    labels: ['5 Star', '4 Star', '3 Star', '2 Star', '1 Star'],
                    datasets: [{
                        data: [65, 25, 8, 1, 1],
                        backgroundColor: [
                            'rgba(155, 89, 182, 0.8)',
                            'rgba(155, 89, 182, 0.6)',
                            'rgba(155, 89, 182, 0.4)',
                            'rgba(155, 89, 182, 0.2)',
                            'rgba(155, 89, 182, 0.1)'
                        ],
                        borderWidth: 1,
                        borderColor: '#9b59b6'
                    }]
                },
                options: {
                    ...commonOptions,
                    plugins: { 
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.parsed.r + '%';
                                }
                            }
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error creating feedbackChart:', error);
        }
    }

    // Finance Trend Chart (Summary Card)
    const financeCtx = document.getElementById('financeChart');
    if (financeCtx) {
        try {
            window.chartInstances.financeChart = new Chart(financeCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Revenue',
                        data: [35000, 42000, 38000, 45680, 48000, 52000],
                        borderColor: '#e74c3c',
                        backgroundColor: 'rgba(231, 76, 60, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2,
                        pointBackgroundColor: '#e74c3c'
                    }]
                },
                options: {
                    ...commonOptions,
                    plugins: { 
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Revenue: $' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        y: { 
                            display: false,
                            beginAtZero: true
                        },
                        x: { 
                            display: false 
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error creating financeChart:', error);
        }
    }

    // Main Revenue Chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        try {
            window.chartInstances.revenueChart = new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: ['Jul 2023', 'Aug 2023', 'Sep 2023', 'Oct 2023', 'Nov 2023', 'Dec 2023', 'Jan 2024', 'Feb 2024', 'Mar 2024', 'Apr 2024', 'May 2024', 'Jun 2024'],
                    datasets: [{
                        label: 'Monthly Revenue ($)',
                        data: [32000, 35000, 38000, 42000, 39000, 45000, 48000, 52000, 49000, 55000, 58000, 62000],
                        borderColor: '#3498db',
                        backgroundColor: 'rgba(52, 152, 219, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointBackgroundColor: '#3498db',
                        pointBorderColor: '#2980b9',
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    ...commonOptions,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.1)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error creating revenueChart:', error);
        }
    }

    // Student Distribution Chart
    const studentDistCtx = document.getElementById('studentDistributionChart');
    if (studentDistCtx) {
        try {
            window.chartInstances.studentDistChart = new Chart(studentDistCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Beginners', 'Intermediate', 'Advanced', 'Professional'],
                    datasets: [{
                        data: [45, 65, 32, 14],
                        backgroundColor: ['#3498db', '#2ecc71', '#f39c12', '#e74c3c'],
                        borderWidth: 2,
                        borderColor: '#fff',
                        hoverOffset: 8
                    }]
                },
                options: {
                    ...commonOptions,
                    plugins: {
                        ...commonOptions.plugins,
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((context.parsed * 100) / total);
                                    return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error creating studentDistChart:', error);
        }
    }

    // Training Attendance Chart
    const attendanceCtx = document.getElementById('attendanceChart');
    if (attendanceCtx) {
        try {
            window.chartInstances.attendanceChart = new Chart(attendanceCtx, {
                type: 'bar',
                data: {
                    labels: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                    datasets: [{
                        label: 'Attendance %',
                        data: [88, 92, 85, 94, 89, 96, 78],
                        backgroundColor: [
                            'rgba(46, 204, 113, 0.8)',
                            'rgba(46, 204, 113, 0.8)',
                            'rgba(46, 204, 113, 0.8)',
                            'rgba(46, 204, 113, 0.8)',
                            'rgba(46, 204, 113, 0.8)',
                            'rgba(46, 204, 113, 0.8)',
                            'rgba(46, 204, 113, 0.8)'
                        ],
                        borderColor: '#27ae60',
                        borderWidth: 1,
                        borderRadius: 6,
                        borderSkipped: false
                    }]
                },
                options: {
                    ...commonOptions,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            grid: {
                                color: 'rgba(0,0,0,0.1)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error creating attendanceChart:', error);
        }
    }

    // Performance Metrics Chart
    const performanceCtx = document.getElementById('performanceChart');
    if (performanceCtx) {
        try {
            window.chartInstances.performanceChart = new Chart(performanceCtx, {
                type: 'radar',
                data: {
                    labels: ['Batting', 'Bowling', 'Fielding', 'Fitness', 'Technique', 'Mental'],
                    datasets: [{
                        label: 'Team Average',
                        data: [78, 82, 75, 88, 85, 79],
                        borderColor: '#f39c12',
                        backgroundColor: 'rgba(243, 156, 18, 0.2)',
                        borderWidth: 2,
                        pointBackgroundColor: '#f39c12',
                        pointBorderColor: '#e67e22',
                        pointRadius: 4
                    }, {
                        label: 'Top Performers',
                        data: [92, 88, 89, 95, 93, 87],
                        borderColor: '#e74c3c',
                        backgroundColor: 'rgba(231, 76, 60, 0.2)',
                        borderWidth: 2,
                        pointBackgroundColor: '#e74c3c',
                        pointBorderColor: '#c0392b',
                        pointRadius: 4
                    }]
                },
                options: {
                    ...commonOptions,
                    scales: {
                        r: {
                            beginAtZero: true,
                            max: 100,
                            grid: {
                                color: 'rgba(0,0,0,0.1)'
                            },
                            pointLabels: {
                                font: {
                                    size: 11
                                }
                            },
                            ticks: {
                                stepSize: 20,
                                font: {
                                    size: 10
                                }
                            }
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error creating performanceChart:', error);
        }
    }

    console.log('All charts initialized successfully');
}

// Utility Functions
function updateCurrentTime() {
    const timeElement = document.getElementById('currentTime');
    if (timeElement) {
        const now = new Date();
        const options = {
            weekday: 'short',
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        timeElement.textContent = now.toLocaleDateString('en-US', options);
    }
}

function showWelcomeNotification() {
    setTimeout(() => {
        showNotification('Welcome to Elite Cricket Academy Admin Dashboard!', 'success');
    }, 1000);
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${getNotificationColor(type)};
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 10000;
        transform: translateX(100%);
        transition: transform 0.3s ease;
        display: flex;
        align-items: center;
        gap: 10px;
        max-width: 300px;
    `;
    
    notification.innerHTML = `
        <i class="fas fa-${getNotificationIcon(type)}"></i>
        <span>${message}</span>
        <button onclick="this.parentElement.remove()" style="background:none;border:none;color:white;cursor:pointer;margin-left:auto;">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

function getNotificationColor(type) {
    const colors = {
        success: '#2ecc71',
        error: '#e74c3c',
        warning: '#f39c12',
        info: '#3498db'
    };
    return colors[type] || '#3498db';
}

function getNotificationIcon(type) {
    const icons = {
        success: 'check-circle',
        error: 'exclamation-circle',
        warning: 'exclamation-triangle',
        info: 'info-circle'
    };
    return icons[type] || 'info-circle';
}

function showDashboardSection(section) {
    // Hide all sections first
    document.querySelectorAll('.dashboard-section').forEach(el => {
        el.style.display = 'none';
    });
    
    // Show notification about section change
    const sectionNames = {
        'dashboard': 'Dashboard Overview',
        'staff-management': 'Staff Management',
        'player-management': 'Player Management',
        'events-tournaments': 'Events & Tournaments',
        'feedback-monitoring': 'Feedback Monitoring',
        'finance-management': 'Finance Management'
    };
    
    const sectionName = sectionNames[section] || 'Dashboard';
    showNotification(`Switched to ${sectionName}`, 'info');
}

function handleMobileView() {
    const sidebar = document.getElementById('adminSidebar');
    const mainContent = document.getElementById('mainContent');
    
    function checkMobile() {
        if (window.innerWidth <= 768) {
            sidebar.classList.add('mobile');
            mainContent.style.marginLeft = '0';
        } else {
            sidebar.classList.remove('mobile');
            if (!sidebar.classList.contains('collapsed')) {
                mainContent.style.marginLeft = '280px';
            }
        }
    }
    
    window.addEventListener('resize', checkMobile);
    checkMobile(); // Initial check
}

// Dashboard Action Functions
function refreshDashboard() {
    showNotification('Refreshing dashboard data...', 'info');
    
    // Add loading class to dashboard
    document.body.classList.add('loading');
    
    // Simulate data refresh
    setTimeout(() => {
        // Re-initialize charts with new data
        try {
            initializeCharts();
            showNotification('Dashboard refreshed successfully!', 'success');
        } catch (error) {
            console.error('Error refreshing dashboard:', error);
            showNotification('Error refreshing dashboard. Please try again.', 'error');
        } finally {
            document.body.classList.remove('loading');
        }
    }, 1500);
}

function openModal(modalType) {
    const modalTitles = {
        'addPlayer': 'Add New Player',
        'scheduleEvent': 'Schedule New Event',
        'generateReport': 'Generate Report',
        'sendNotification': 'Send Notification',
        'manageStaff': 'Manage Staff',
        'reviewFeedback': 'Review Feedback'
    };
    
    const title = modalTitles[modalType] || 'Open Modal';
    showNotification(`Opening ${title} form...`, 'info');
    
    // In a real application, this would open an actual modal
    // For demo purposes, we're just showing a notification
}

// Export functions for global access
window.refreshDashboard = refreshDashboard;
window.openModal = openModal;

// ============================================
// Custom Calendar Implementation
// ============================================

// Calendar Data - Events, Coaching Sessions, Tournaments, Meetings (with times)
const calendarEvents = [
    // Events
    { date: '2025-10-20', time: '09:00', type: 'event', title: 'Annual Sports Day' },
    { date: '2025-10-25', time: '14:00', type: 'event', title: 'Player Awards Ceremony' },
    { date: '2025-10-30', time: '10:00', type: 'event', title: 'Community Cricket Festival' },
    { date: '2025-11-05', time: '11:00', type: 'event', title: 'Academy Open House' },
    { date: '2025-11-15', time: '18:00', type: 'event', title: 'Fundraising Gala' },
    
    // Coaching Sessions
    { date: '2025-10-21', time: '08:00', type: 'coaching', title: 'Advanced Batting Techniques' },
    { date: '2025-10-22', time: '09:00', type: 'coaching', title: 'Bowling Masterclass' },
    { date: '2025-10-23', time: '10:00', type: 'coaching', title: 'Fielding Drills' },
    { date: '2025-10-24', time: '08:30', type: 'coaching', title: 'Wicket Keeping Session' },
    { date: '2025-10-28', time: '15:00', type: 'coaching', title: 'Youth Cricket Training' },
    { date: '2025-10-29', time: '16:00', type: 'coaching', title: 'Senior Team Practice' },
    { date: '2025-11-01', time: '09:30', type: 'coaching', title: 'Spin Bowling Workshop' },
    { date: '2025-11-04', time: '14:00', type: 'coaching', title: 'Power Hitting Clinic' },
    { date: '2025-11-07', time: '07:00', type: 'coaching', title: 'Fitness & Conditioning' },
    { date: '2025-11-11', time: '13:00', type: 'coaching', title: 'Mental Skills Training' },
    
    // Tournaments
    { date: '2025-10-26', time: '09:00', type: 'tournament', title: 'Junior Championship Qualifier' },
    { date: '2025-10-27', time: '10:00', type: 'tournament', title: 'Junior Championship Finals' },
    { date: '2025-11-08', time: '08:00', type: 'tournament', title: 'Inter-Academy T20 Tournament' },
    { date: '2025-11-09', time: '09:00', type: 'tournament', title: 'Inter-Academy T20 Semi-Finals' },
    { date: '2025-11-10', time: '10:00', type: 'tournament', title: 'Inter-Academy T20 Finals' },
    { date: '2025-11-16', time: '08:30', type: 'tournament', title: 'U-16 State Championship' },
    
    // Meetings
    { date: '2025-10-21', time: '10:00', type: 'meeting', title: 'Staff Coordination Meeting' },
    { date: '2025-10-24', time: '11:00', type: 'meeting', title: 'Parent-Coach Discussion' },
    { date: '2025-10-31', time: '15:00', type: 'meeting', title: 'Monthly Finance Review' },
    { date: '2025-11-06', time: '14:00', type: 'meeting', title: 'Curriculum Planning' },
    { date: '2025-11-12', time: '16:00', type: 'meeting', title: 'Equipment Procurement' },
    { date: '2025-11-14', time: '10:30', type: 'meeting', title: 'Board Meeting' }
];

let currentMonth = new Date().getMonth();
let currentYear = new Date().getFullYear();
let currentDay = new Date().getDate();
let currentView = 'month';
let currentWeekStart = null;
let selectedDate = new Date(); // Track selected date

// Initialize Calendar
function initializeCalendar() {
    console.log('🗓️ Initializing calendar...');
    
    // View toggle buttons
    const viewButtons = document.querySelectorAll('.view-btn');
    viewButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            viewButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentView = this.getAttribute('data-view');
            renderCalendarView();
        });
    });
    
    // Today button
    const todayBtn = document.getElementById('todayBtn');
    if (todayBtn) {
        todayBtn.addEventListener('click', () => {
            const today = new Date();
            selectedDate = new Date(today);
            currentDay = today.getDate();
            currentMonth = today.getMonth();
            currentYear = today.getFullYear();
            currentWeekStart = null; // Reset week start
            renderCalendarView();
        });
    }
    
    // Navigation buttons
    const prevBtn = document.getElementById('prevPeriod');
    const nextBtn = document.getElementById('nextPeriod');
    
    if (prevBtn) {
        prevBtn.addEventListener('click', () => navigatePeriod(-1));
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', () => navigatePeriod(1));
    }
    
    renderCalendarView();
}

// Navigate between periods
function navigatePeriod(direction) {
    if (currentView === 'month') {
        currentMonth += direction;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        } else if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
    } else if (currentView === 'week') {
        if (!currentWeekStart) {
            currentWeekStart = new Date(selectedDate);
            currentWeekStart.setDate(currentWeekStart.getDate() - currentWeekStart.getDay());
        }
        currentWeekStart.setDate(currentWeekStart.getDate() + (direction * 7));
        selectedDate = new Date(currentWeekStart);
        currentMonth = currentWeekStart.getMonth();
        currentYear = currentWeekStart.getFullYear();
        currentDay = currentWeekStart.getDate();
    } else if (currentView === 'day') {
        selectedDate.setDate(selectedDate.getDate() + direction);
        currentDay = selectedDate.getDate();
        currentMonth = selectedDate.getMonth();
        currentYear = selectedDate.getFullYear();
    }
    
    renderCalendarView();
}

// Render current view
function renderCalendarView() {
    document.getElementById('monthView').style.display = 'none';
    document.getElementById('weekView').style.display = 'none';
    document.getElementById('dayView').style.display = 'none';
    
    if (currentView === 'month') {
        document.getElementById('monthView').style.display = 'grid';
        renderMonthView();
    } else if (currentView === 'week') {
        document.getElementById('weekView').style.display = 'grid';
        renderWeekView();
    } else if (currentView === 'day') {
        document.getElementById('dayView').style.display = 'grid';
        renderDayView();
    }
}

// Render Month View (with dots only)
function renderMonthView() {
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'];
    
    document.getElementById('currentPeriod').textContent = `${monthNames[currentMonth]} ${currentYear}`;
    
    const calendarGrid = document.getElementById('monthView');
    calendarGrid.innerHTML = '';
    
    // Add day headers
    const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    dayNames.forEach(day => {
        const dayHeader = document.createElement('div');
        dayHeader.className = 'calendar-day header';
        dayHeader.textContent = day;
        calendarGrid.appendChild(dayHeader);
    });
    
    // Get first day of month and number of days
    const firstDay = new Date(currentYear, currentMonth, 1).getDay();
    const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
    const daysInPrevMonth = new Date(currentYear, currentMonth, 0).getDate();
    
    // Add previous month's days
    for (let i = firstDay - 1; i >= 0; i--) {
        const dayCell = createMonthDayCell(daysInPrevMonth - i, true, currentMonth - 1);
        calendarGrid.appendChild(dayCell);
    }
    
    // Add current month's days
    const today = new Date();
    for (let day = 1; day <= daysInMonth; day++) {
        const isToday = day === today.getDate() && 
                       currentMonth === today.getMonth() && 
                       currentYear === today.getFullYear();
        const dayCell = createMonthDayCell(day, false, currentMonth, isToday);
        calendarGrid.appendChild(dayCell);
    }
    
    // Add next month's days
    const totalCells = firstDay + daysInMonth;
    const remainingCells = 7 - (totalCells % 7);
    if (remainingCells < 7) {
        for (let day = 1; day <= remainingCells; day++) {
            const dayCell = createMonthDayCell(day, true, currentMonth + 1);
            calendarGrid.appendChild(dayCell);
        }
    }
}

// Create day cell for month view
function createMonthDayCell(day, isOtherMonth, month, isToday = false) {
    const dayCell = document.createElement('div');
    dayCell.className = 'calendar-day';
    
    if (isOtherMonth) {
        dayCell.classList.add('other-month');
    }
    if (isToday) {
        dayCell.classList.add('today');
    }
    
    // Check if this is the selected date
    const cellDate = new Date(currentYear, month, day);
    if (!isOtherMonth && 
        cellDate.getDate() === selectedDate.getDate() && 
        cellDate.getMonth() === selectedDate.getMonth() && 
        cellDate.getFullYear() === selectedDate.getFullYear()) {
        dayCell.classList.add('selected');
    }
    
    const dateNumber = document.createElement('div');
    dateNumber.className = 'date-number';
    dateNumber.textContent = day;
    dayCell.appendChild(dateNumber);
    
    // Check for events on this day (show dots only)
    const dateStr = `${currentYear}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
    const dayEvents = calendarEvents.filter(event => event.date === dateStr);
    
    if (dayEvents.length > 0 && !isOtherMonth) {
        const eventIndicator = document.createElement('div');
        eventIndicator.className = 'event-indicator';
        
        dayEvents.forEach(event => {
            const eventDot = document.createElement('span');
            eventDot.className = `event-dot ${event.type}`;
            eventDot.title = event.title;
            eventIndicator.appendChild(eventDot);
        });
        
        dayCell.appendChild(eventIndicator);
    }
    
    // Add click handler to select date
    if (!isOtherMonth) {
        dayCell.addEventListener('click', () => {
            selectedDate = new Date(currentYear, month, day);
            currentDay = day;
            renderCalendarView();
            console.log('Selected date:', selectedDate.toDateString());
        });
    }
    
    return dayCell;
}

// Render Week View (with small text activities)
function renderWeekView() {
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'];
    
    // Initialize week start if not set
    if (!currentWeekStart) {
        currentWeekStart = new Date(selectedDate);
        currentWeekStart.setDate(currentWeekStart.getDate() - currentWeekStart.getDay());
    }
    
    const weekEnd = new Date(currentWeekStart);
    weekEnd.setDate(weekEnd.getDate() + 6);
    
    document.getElementById('currentPeriod').textContent = 
        `${monthNames[currentWeekStart.getMonth()]} ${currentWeekStart.getDate()} - ${monthNames[weekEnd.getMonth()]} ${weekEnd.getDate()}, ${currentWeekStart.getFullYear()}`;
    
    const weekView = document.getElementById('weekView');
    weekView.innerHTML = '';
    
    // Add empty corner cell
    const corner = document.createElement('div');
    corner.className = 'time-slot';
    corner.textContent = 'Time';
    weekView.appendChild(corner);
    
    // Add day headers
    const dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    const today = new Date();
    
    for (let i = 0; i < 7; i++) {
        const dayDate = new Date(currentWeekStart);
        dayDate.setDate(dayDate.getDate() + i);
        
        const dayHeader = document.createElement('div');
        dayHeader.className = 'day-header';
        
        const isToday = dayDate.toDateString() === today.toDateString();
        const isSelected = dayDate.toDateString() === selectedDate.toDateString();
        
        if (isToday) dayHeader.classList.add('today');
        if (isSelected) dayHeader.classList.add('selected');
        
        dayHeader.innerHTML = `<strong>${dayNames[i].substring(0, 3)}</strong><br>${dayDate.getDate()}`;
        
        // Add click handler to select date and switch to day view
        dayHeader.addEventListener('click', () => {
            selectedDate = new Date(dayDate);
            currentDay = selectedDate.getDate();
            currentMonth = selectedDate.getMonth();
            currentYear = selectedDate.getFullYear();
            
            // Switch to day view
            document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
            document.querySelector('.view-btn[data-view="day"]').classList.add('active');
            currentView = 'day';
            renderCalendarView();
        });
        
        weekView.appendChild(dayHeader);
    }
    
    // Time slots from 6 AM to 8 PM
    const times = ['6 AM', '8 AM', '10 AM', '12 PM', '2 PM', '4 PM', '6 PM', '8 PM'];
    
    times.forEach(time => {
        // Time label
        const timeLabel = document.createElement('div');
        timeLabel.className = 'time-slot';
        timeLabel.textContent = time;
        weekView.appendChild(timeLabel);
        
        // Day columns
        for (let i = 0; i < 7; i++) {
            const dayDate = new Date(currentWeekStart);
            dayDate.setDate(dayDate.getDate() + i);
            const dateStr = dayDate.toISOString().split('T')[0];
            
            const dayColumn = document.createElement('div');
            dayColumn.className = 'day-column';
            
            // Filter events for this day and time range
            const dayEvents = calendarEvents.filter(event => {
                if (event.date !== dateStr) return false;
                const eventHour = parseInt(event.time.split(':')[0]);
                const slotHour = time.includes('AM') ? 
                    (time === '12 PM' ? 12 : parseInt(time)) : 
                    (time === '12 PM' ? 12 : parseInt(time) + 12);
                return eventHour >= slotHour && eventHour < slotHour + 2;
            });
            
            dayEvents.forEach(event => {
                const eventDiv = document.createElement('div');
                eventDiv.className = `week-event ${event.type}`;
                eventDiv.textContent = event.title;
                eventDiv.title = `${event.time} - ${event.title}`;
                dayColumn.appendChild(eventDiv);
            });
            
            weekView.appendChild(dayColumn);
        }
    });
}

// Render Day View (with timeline)
function renderDayView() {
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'];
    const dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    
    const currentDate = new Date(selectedDate);
    const dayOfWeek = currentDate.getDay();
    
    document.getElementById('currentPeriod').textContent = 
        `${dayNames[dayOfWeek]}, ${monthNames[currentDate.getMonth()]} ${currentDate.getDate()}, ${currentDate.getFullYear()}`;
    
    const dayView = document.getElementById('dayView');
    dayView.innerHTML = '';
    
    const dateStr = `${currentDate.getFullYear()}-${String(currentDate.getMonth() + 1).padStart(2, '0')}-${String(currentDate.getDate()).padStart(2, '0')}`;
    const dayEvents = calendarEvents.filter(event => event.date === dateStr);
    
    // Sort events by time
    dayEvents.sort((a, b) => a.time.localeCompare(b.time));
    
    // Time slots from 6 AM to 9 PM
    for (let hour = 6; hour <= 21; hour++) {
        const timeLabel = document.createElement('div');
        timeLabel.className = 'time-label';
        const ampm = hour < 12 ? 'AM' : 'PM';
        const displayHour = hour <= 12 ? hour : hour - 12;
        timeLabel.textContent = `${displayHour}:00 ${ampm}`;
        dayView.appendChild(timeLabel);
        
        const timeContent = document.createElement('div');
        timeContent.className = 'time-content';
        
        // Find events in this hour
        const hourEvents = dayEvents.filter(event => {
            const eventHour = parseInt(event.time.split(':')[0]);
            return eventHour === hour;
        });
        
        if (hourEvents.length > 0) {
            hourEvents.forEach(event => {
                const eventDiv = document.createElement('div');
                eventDiv.className = `day-event ${event.type}`;
                
                eventDiv.innerHTML = `
                    <div class="event-time">${event.time}</div>
                    <div class="event-title">${event.title}</div>
                    <div class="event-type">${event.type}</div>
                `;
                
                timeContent.appendChild(eventDiv);
            });
        } else {
            const noEvents = document.createElement('div');
            noEvents.className = 'no-events';
            noEvents.textContent = 'No activities scheduled';
            timeContent.appendChild(noEvents);
        }
        
        dayView.appendChild(timeContent);
    }
}



// Show Activity Details Function
function showActivityDetails(activityId) {
    const activityDetails = {
        1: {
            title: 'New Player Registration',
            details: 'Sarah Johnson (Age 14) has been registered for the Youth Cricket Program.',
            time: 'Oct 19, 2025 - 10:30 AM',
            additionalInfo: 'Contact: sarah.j@email.com | Parent: Mr. Johnson'
        },
        2: {
            title: 'Tournament Scheduled',
            details: 'Junior Championship 2025 has been scheduled for October 26-27.',
            time: 'Oct 19, 2025 - 08:15 AM',
            additionalInfo: 'Venue: Main Ground | Teams: 8 | Prize: $5,000'
        },
        3: {
            title: '5-Star Feedback',
            details: 'Excellent coaching and facilities. Alex has improved tremendously!',
            time: 'Oct 18, 2025 - 04:45 PM',
            additionalInfo: 'From: Parent of Alex Kumar | Rating: 5/5'
        },
        4: {
            title: 'Payment Received',
            details: '$450 payment received for monthly coaching fees.',
            time: 'Oct 18, 2025 - 02:20 PM',
            additionalInfo: 'From: Emma Wilson | Method: Credit Card | Ref: PMT-45678'
        },
        5: {
            title: 'New Coach Hired',
            details: 'Michael Roberts, former state player, joins as head coach.',
            time: 'Oct 17, 2025 - 09:00 AM',
            additionalInfo: 'Experience: 15 years | Specialization: Batting & Strategy'
        },
        6: {
            title: 'Training Session',
            details: 'Advanced batting session completed successfully with 15 participants.',
            time: 'Oct 16, 2025 - 05:30 PM',
            additionalInfo: 'Instructor: Coach Roberts | Duration: 2 hours'
        },
        7: {
            title: 'Equipment Maintenance',
            details: 'Routine maintenance completed for Ground A equipment.',
            time: 'Oct 16, 2025 - 11:00 AM',
            additionalInfo: 'Items: Pitch roller, nets, stumps | Status: All functional'
        },
        8: {
            title: 'New Player Registration',
            details: 'David Chen (Age 12) registered for Junior Cricket Program.',
            time: 'Oct 15, 2025 - 03:15 PM',
            additionalInfo: 'Contact: david.c@email.com | Parent: Mrs. Chen'
        }
    };
    
    const activity = activityDetails[activityId];
    if (activity) {
        const message = `${activity.title}\n\n${activity.details}\n\nTime: ${activity.time}\n\n${activity.additionalInfo}`;
        alert(message);
    }
}

// Initialize calendar when DOM is ready
if (document.getElementById('calendarGrid')) {
    initializeCalendar();
}

// Export functions
window.showActivityDetails = showActivityDetails;
