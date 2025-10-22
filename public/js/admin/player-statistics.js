// Player Statistics JavaScript

// Initialize Charts
document.addEventListener('DOMContentLoaded', function() {
    initPerformanceTrendChart();
    initBattingStatsChart();
});

// Performance Trend Chart
function initPerformanceTrendChart() {
    const ctx = document.getElementById('performanceChart');
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['April', 'May', 'June', 'July', 'August', 'September'],
            datasets: [{
                label: 'Performance Score',
                data: [75, 78, 82, 80, 83, 85],
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 6,
                pointBackgroundColor: '#667eea',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return 'Performance: ' + context.parsed.y + '%';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
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
}

// Batting Statistics Pie Chart
function initBattingStatsChart() {
    const ctx = document.getElementById('battingChart');
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Runs Scored', 'Boundaries (4s)', 'Sixes', 'Strike Rate'],
            datasets: [{
                data: [456, 45, 18, 135],
                backgroundColor: [
                    '#667eea',
                    '#764ba2',
                    '#f093fb',
                    '#4facfe'
                ],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            return label + ': ' + value;
                        }
                    }
                }
            }
        }
    });
}

// Print Report
function printReport() {
    window.print();
}

// Export to PDF
function exportToPDF() {
    // In production, use a library like jsPDF or html2pdf
    console.log('Exporting to PDF...');
    
    // Show loading message
    const loadingMsg = document.createElement('div');
    loadingMsg.style.cssText = 'position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background:white;padding:30px;border-radius:12px;box-shadow:0 8px 30px rgba(0,0,0,0.2);z-index:9999;text-align:center;';
    loadingMsg.innerHTML = `
        <div style="width:50px;height:50px;border:4px solid #e8eaed;border-top-color:#667eea;border-radius:50%;margin:0 auto 15px;animation:spin 1s linear infinite;"></div>
        <p style="margin:0;color:#333;font-weight:600;">Generating PDF Report...</p>
        <style>@keyframes spin{to{transform:rotate(360deg);}}</style>
    `;
    document.body.appendChild(loadingMsg);
    
    // Simulate PDF generation
    setTimeout(() => {
        document.body.removeChild(loadingMsg);
        alert('PDF report generated successfully!\n\nIn production, this would download the PDF file.');
    }, 2000);
    
    // In production, you would use something like:
    /*
    const playerName = document.querySelector('.player-details h2').textContent;
    const opt = {
        margin: 1,
        filename: `${playerName}_Statistics_${new Date().toISOString().split('T')[0]}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
    };
    html2pdf().set(opt).from(document.querySelector('.main-content')).save();
    */
}

// Animate metric values on page load
document.addEventListener('DOMContentLoaded', function() {
    const metricValues = document.querySelectorAll('.metric-value');
    
    metricValues.forEach(metric => {
        const targetValue = parseInt(metric.textContent);
        if (isNaN(targetValue)) return;
        
        let currentValue = 0;
        const increment = targetValue / 50;
        const suffix = metric.textContent.replace(/[0-9]/g, '');
        
        const timer = setInterval(() => {
            currentValue += increment;
            if (currentValue >= targetValue) {
                metric.textContent = targetValue + suffix;
                clearInterval(timer);
            } else {
                metric.textContent = Math.floor(currentValue) + suffix;
            }
        }, 20);
    });
});

// Back button functionality
function goBack() {
    window.history.back();
}

// Toggle stat sections (for mobile)
function toggleStatSection(sectionId) {
    const section = document.getElementById(sectionId);
    if (section) {
        const table = section.querySelector('.stats-table');
        if (table.style.display === 'none') {
            table.style.display = 'table';
        } else {
            table.style.display = 'none';
        }
    }
}

// Share statistics
function shareStatistics() {
    const playerName = document.querySelector('.player-details h2').textContent;
    const performance = document.querySelector('.metric-value').textContent;
    
    const shareData = {
        title: `${playerName} - Player Statistics`,
        text: `Check out ${playerName}'s cricket performance: ${performance} overall performance!`,
        url: window.location.href
    };
    
    if (navigator.share) {
        navigator.share(shareData)
            .then(() => console.log('Shared successfully'))
            .catch(err => console.log('Error sharing:', err));
    } else {
        // Fallback: Copy to clipboard
        const url = window.location.href;
        navigator.clipboard.writeText(url).then(() => {
            alert('Link copied to clipboard!');
        });
    }
}

// Compare with other players (future feature)
function compareWithPlayers() {
    alert('Player comparison feature coming soon!\n\nThis will allow you to compare this player\'s statistics with other players in the academy.');
}

// Refresh statistics
function refreshStatistics() {
    // In production, fetch latest data from backend
    console.log('Refreshing statistics...');
    
    // Show loading indicator
    const loadingIndicator = document.createElement('div');
    loadingIndicator.style.cssText = 'position:fixed;top:20px;right:20px;background:white;padding:15px 25px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.15);z-index:9999;';
    loadingIndicator.innerHTML = '<i class="fas fa-sync fa-spin" style="color:#667eea;margin-right:10px;"></i>Refreshing...';
    document.body.appendChild(loadingIndicator);
    
    setTimeout(() => {
        document.body.removeChild(loadingIndicator);
        
        // Show success message
        const successMsg = document.createElement('div');
        successMsg.style.cssText = 'position:fixed;top:20px;right:20px;background:#10b981;color:white;padding:15px 25px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.15);z-index:9999;';
        successMsg.innerHTML = '<i class="fas fa-check-circle" style="margin-right:10px;"></i>Statistics updated!';
        document.body.appendChild(successMsg);
        
        setTimeout(() => {
            document.body.removeChild(successMsg);
        }, 2000);
    }, 1500);
}

// Download match report
function downloadMatchReport(matchId) {
    console.log('Downloading match report:', matchId);
    alert('Match report download feature coming soon!');
}

// View full match details
function viewMatchDetails(matchId) {
    console.log('Viewing match details:', matchId);
    // In production, navigate to match details page
    // window.location.href = `/admin/matches/${matchId}`;
    alert('Match details page coming soon!');
}

// Add custom styling for print
const printStyles = `
    @media print {
        .sidebar,
        .back-button,
        .action-buttons {
            display: none !important;
        }
        .main-content {
            margin-left: 0 !important;
            padding: 20px !important;
        }
        .match-card,
        .chart-card,
        .stats-section,
        .player-header {
            box-shadow: none !important;
            border: 1px solid #e8eaed !important;
            page-break-inside: avoid;
        }
    }
`;

const style = document.createElement('style');
style.textContent = printStyles;
document.head.appendChild(style);
