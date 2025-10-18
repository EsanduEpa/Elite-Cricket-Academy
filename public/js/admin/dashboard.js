// Admin Dashboard JavaScript - Elite Cricket Academy
console.log('✅ dashboard.js file loaded successfully!');

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing dashboard...');
    initializeDashboard();
    initializeSidebar();
    
    // Wait for Chart.js to be available
    function waitForChart() {
        if (typeof Chart !== 'undefined' && window.chartJsLoaded) {
            console.log('Chart.js is available, initializing charts...');
            initializeCharts();
        } else {
            console.log('Chart.js not ready, waiting... Chart available:', typeof Chart !== 'undefined', 'Flag set:', window.chartJsLoaded);
            setTimeout(waitForChart, 100);
        }
    }
    
    // Give Chart.js some time to load
    setTimeout(waitForChart, 500);
    
    updateCurrentTime();
    
    // Update time every second
    setInterval(updateCurrentTime, 1000);
});

// Dashboard Initialization
function initializeDashboard() {
    // Animate dashboard cards on load
    const cards = document.querySelectorAll('.summary-card');
    cards.forEach((card, index) => {
        setTimeout(() => {
            card.classList.add('fade-in');
        }, index * 100);
    });

    // Initialize notification system
    showWelcomeNotification();
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
