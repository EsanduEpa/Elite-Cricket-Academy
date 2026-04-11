// Dummy Reports Data
const performanceData = {
    batting: {
        labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
        datasets: [
            {
                label: 'Ashan Perera',
                data: [35, 42, 38, 45],
                borderColor: '#4CAF50',
                backgroundColor: 'rgba(76, 175, 80, 0.1)',
                tension: 0.4
            },
            {
                label: 'Nimal Silva',
                data: [28, 32, 36, 40],
                borderColor: '#2196F3',
                backgroundColor: 'rgba(33, 150, 243, 0.1)',
                tension: 0.4
            },
            {
                label: 'Tharindu J.',
                data: [40, 38, 42, 44],
                borderColor: '#FF9800',
                backgroundColor: 'rgba(255, 152, 0, 0.1)',
                tension: 0.4
            }
        ]
    },
    bowling: {
        labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
        datasets: [
            {
                label: 'Ashan Perera',
                data: [22, 20, 18, 16],
                borderColor: '#4CAF50',
                backgroundColor: 'rgba(76, 175, 80, 0.1)',
                tension: 0.4
            },
            {
                label: 'Nimal Silva',
                data: [25, 23, 21, 19],
                borderColor: '#2196F3',
                backgroundColor: 'rgba(33, 150, 243, 0.1)',
                tension: 0.4
            },
            {
                label: 'Chamara W.',
                data: [18, 17, 16, 15],
                borderColor: '#9C27B0',
                backgroundColor: 'rgba(156, 39, 176, 0.1)',
                tension: 0.4
            }
        ]
    },
    fielding: {
        labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
        datasets: [
            {
                label: 'Ashan Perera',
                data: [75, 78, 82, 85],
                borderColor: '#4CAF50',
                backgroundColor: 'rgba(76, 175, 80, 0.1)',
                tension: 0.4
            },
            {
                label: 'Nimal Silva',
                data: [80, 82, 85, 88],
                borderColor: '#2196F3',
                backgroundColor: 'rgba(33, 150, 243, 0.1)',
                tension: 0.4
            },
            {
                label: 'Kavinda R.',
                data: [70, 74, 77, 80],
                borderColor: '#E91E63',
                backgroundColor: 'rgba(233, 30, 99, 0.1)',
                tension: 0.4
            }
        ]
    }
};

const attendanceData = {
    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
    datasets: [{
        label: 'Attendance Rate',
        data: [90, 85, 92, 88, 86, 95],
        backgroundColor: [
            'rgba(76, 175, 80, 0.8)',
            'rgba(76, 175, 80, 0.7)',
            'rgba(76, 175, 80, 0.9)',
            'rgba(76, 175, 80, 0.75)',
            'rgba(76, 175, 80, 0.7)',
            'rgba(76, 175, 80, 0.95)'
        ],
        borderColor: '#4CAF50',
        borderWidth: 2
    }]
};

const healthData = {
    labels: ['Fit', 'Injured', 'Recovering', 'Evaluation'],
    datasets: [{
        data: [18, 2, 3, 1],
        backgroundColor: [
            '#10b981',
            '#ef4444',
            '#f59e0b',
            '#6b7280'
        ],
        borderWidth: 0
    }]
};

const sessionData = {
    labels: ['Batting', 'Bowling', 'Fielding', 'Fitness', 'Strategy'],
    datasets: [{
        label: 'Sessions',
        data: [15, 12, 8, 10, 3],
        backgroundColor: 'rgba(76, 175, 80, 0.8)',
        borderColor: '#4CAF50',
        borderWidth: 2
    }]
};

const topPerformers = [
    {
        rank: 1,
        name: 'Ashan Perera',
        attendance: 95,
        performance: 88,
        improvement: 15,
        health: 'fit'
    },
    {
        rank: 2,
        name: 'Nimal Silva',
        attendance: 92,
        performance: 85,
        improvement: 12,
        health: 'fit'
    },
    {
        rank: 3,
        name: 'Tharindu Jayasinghe',
        attendance: 90,
        performance: 86,
        improvement: 10,
        health: 'fit'
    },
    {
        rank: 4,
        name: 'Kavinda Rajapaksa',
        attendance: 88,
        performance: 82,
        improvement: 8,
        health: 'recovering'
    },
    {
        rank: 5,
        name: 'Sahan De Silva',
        attendance: 87,
        performance: 80,
        improvement: 7,
        health: 'fit'
    },
    {
        rank: 6,
        name: 'Chamara Wickramasinghe',
        attendance: 85,
        performance: 78,
        improvement: -2,
        health: 'injured'
    },
    {
        rank: 7,
        name: 'Dinesh Fernando',
        attendance: 83,
        performance: 76,
        improvement: 5,
        health: 'fit'
    },
    {
        rank: 8,
        name: 'Lasith Malinga Jr.',
        attendance: 80,
        performance: 75,
        improvement: 3,
        health: 'fit'
    }
];

let performanceChart, attendanceChart, healthChart, sessionChart;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    loadPerformersTable();
    setupEventListeners();
});

// Initialize Charts
function initializeCharts() {
    // Performance Chart
    const perfCtx = document.getElementById('performanceChart').getContext('2d');
    performanceChart = new Chart(perfCtx, {
        type: 'line',
        data: performanceData.batting,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Average Score'
                    }
                }
            }
        }
    });

    // Attendance Chart
    const attCtx = document.getElementById('attendanceChart').getContext('2d');
    attendanceChart = new Chart(attCtx, {
        type: 'bar',
        data: attendanceData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    title: {
                        display: true,
                        text: 'Attendance %'
                    }
                }
            }
        }
    });

    // Health Chart
    const healthCtx = document.getElementById('healthChart').getContext('2d');
    healthChart = new Chart(healthCtx, {
        type: 'doughnut',
        data: healthData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });

    // Session Chart
    const sessionCtx = document.getElementById('sessionChart').getContext('2d');
    sessionChart = new Chart(sessionCtx, {
        type: 'bar',
        data: sessionData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Sessions'
                    }
                }
            }
        }
    });
}

// Load Performers Table
function loadPerformersTable() {
    const tbody = document.getElementById('performersTableBody');
    
    tbody.innerHTML = topPerformers.map(player => {
        const rankClass = player.rank === 1 ? 'gold' : player.rank === 2 ? 'silver' : player.rank === 3 ? 'bronze' : 'default';
        const improvementClass = player.improvement > 0 ? 'positive' : player.improvement < 0 ? 'negative' : 'neutral';
        const improvementIcon = player.improvement > 0 ? 'fa-arrow-up' : player.improvement < 0 ? 'fa-arrow-down' : 'fa-minus';
        
        return `
            <tr>
                <td>
                    <span class="rank-badge ${rankClass}">${player.rank}</span>
                </td>
                <td><strong>${player.name}</strong></td>
                <td>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: ${player.attendance}%"></div>
                    </div>
                    <small style="color: #666;">${player.attendance}%</small>
                </td>
                <td>
                    <strong style="color: #4CAF50;">${player.performance}%</strong>
                </td>
                <td>
                    <span class="improvement-indicator ${improvementClass}">
                        <i class="fas ${improvementIcon}"></i>
                        ${Math.abs(player.improvement)}%
                    </span>
                </td>
                <td>
                    <span class="status-badge ${player.health}">
                        ${player.health.charAt(0).toUpperCase() + player.health.slice(1)}
                    </span>
                </td>
            </tr>
        `;
    }).join('');
}

// Setup Event Listeners
function setupEventListeners() {
    // Performance metric change
    document.getElementById('performanceMetric').addEventListener('change', function(e) {
        const metric = e.target.value;
        performanceChart.data = performanceData[metric];
        performanceChart.update();
    });

    // Report period change
    document.getElementById('reportPeriod').addEventListener('change', function(e) {
        const period = e.target.value;
        // In a real app, would fetch new data based on period
        console.log('Report period changed to:', period);
    });

    // Search players
    document.getElementById('searchPlayers').addEventListener('input', function(e) {
        const search = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('#performersTableBody tr');
        
        rows.forEach(row => {
            const name = row.cells[1].textContent.toLowerCase();
            row.style.display = name.includes(search) ? '' : 'none';
        });
    });

    // Export report modal
    const exportReportBtn = document.getElementById('exportReportBtn');
    const exportModal = document.getElementById('exportModal');
    const closeExportModal = document.getElementById('closeExportModal');
    const cancelExport = document.getElementById('cancelExport');

    exportReportBtn.addEventListener('click', () => {
        exportModal.classList.add('active');
    });

    closeExportModal.addEventListener('click', () => {
        exportModal.classList.remove('active');
    });

    cancelExport.addEventListener('click', () => {
        exportModal.classList.remove('active');
    });

    // Export form submit
    document.getElementById('exportForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const reportTypes = Array.from(document.querySelectorAll('input[name="reportType"]:checked'))
            .map(input => input.value);
        const format = document.getElementById('exportFormat').value;
        const period = document.getElementById('exportPeriod').value;
        const includeCharts = document.getElementById('includeCharts').checked;
        
        // Simulate export
        console.log('Exporting report:', {
            types: reportTypes,
            format: format,
            period: period,
            charts: includeCharts
        });
        
        // Show success message
        alert(`Report exported successfully as ${format.toUpperCase()}!\n\nIncluded:\n- ${reportTypes.join('\n- ')}\n${includeCharts ? '- Charts & Visualizations' : ''}`);
        
        exportModal.classList.remove('active');
        this.reset();
    });

    // Export table as CSV
    document.getElementById('exportTableBtn').addEventListener('click', function() {
        const csv = generateCSV();
        downloadCSV(csv, 'top-performers.csv');
    });

    // Close modal on backdrop click
    exportModal.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
        }
    });
}

// Generate CSV from table
function generateCSV() {
    const headers = ['Rank', 'Player Name', 'Attendance %', 'Performance %', 'Improvement %', 'Health Status'];
    const rows = topPerformers.map(p => [
        p.rank,
        p.name,
        p.attendance,
        p.performance,
        p.improvement,
        p.health
    ]);
    
    const csvContent = [
        headers.join(','),
        ...rows.map(row => row.join(','))
    ].join('\n');
    
    return csvContent;
}

// Download CSV
function downloadCSV(csv, filename) {
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}
