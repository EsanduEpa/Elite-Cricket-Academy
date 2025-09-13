// Performance Analytics JavaScript

document.addEventListener('DOMContentLoaded', function() {
    initializePerformancePage();
});

function initializePerformancePage() {
    // Initialize tabs
    initializeTabs();
    
    // Initialize chart
    initializePerformanceChart();
    
    // Initialize modals
    initializeModals();
    
    // Initialize action buttons
    initializeActionButtons();
    
    // Initialize export functionality
    initializeExportReport();
}

// Tab functionality
function initializeTabs() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    const performanceSections = document.querySelectorAll('.performance-section');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const category = this.dataset.category;
            
            // Update active tab
            tabBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Show/hide sections
            performanceSections.forEach(section => {
                if (section.id === category) {
                    section.classList.add('active');
                } else {
                    section.classList.remove('active');
                }
            });
        });
    });
}

// Performance chart initialization
function initializePerformanceChart() {
    const ctx = document.getElementById('performanceChart');
    if (!ctx) return;

    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [
                {
                    label: 'Batting Average',
                    data: [35.2, 38.5, 42.1, 39.8, 45.3, 42.5],
                    borderColor: '#4A90E2',
                    backgroundColor: 'rgba(74, 144, 226, 0.1)',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Bowling Average',
                    data: [32.1, 29.8, 31.2, 28.9, 26.5, 28.3],
                    borderColor: '#e74c3c',
                    backgroundColor: 'rgba(231, 76, 60, 0.1)',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Strike Rate',
                    data: [118.5, 125.2, 132.8, 128.4, 135.6, 130.2],
                    borderColor: '#27ae60',
                    backgroundColor: 'rgba(39, 174, 96, 0.1)',
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                    titleColor: '#2c3e50',
                    bodyColor: '#666',
                    borderColor: 'rgba(74, 144, 226, 0.2)',
                    borderWidth: 1,
                    cornerRadius: 10,
                    padding: 12
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#666'
                    }
                },
                y: {
                    grid: {
                        color: 'rgba(74, 144, 226, 0.1)'
                    },
                    ticks: {
                        color: '#666'
                    }
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        }
    });

    // Chart period change handler
    const chartPeriodSelect = document.getElementById('chartPeriod');
    if (chartPeriodSelect) {
        chartPeriodSelect.addEventListener('change', function() {
            updateChartData(chart, this.value);
        });
    }
}

// Update chart data based on period
function updateChartData(chart, period) {
    let labels, battingData, bowlingData, strikeRateData;
    
    switch(period) {
        case '3':
            labels = ['Oct', 'Nov', 'Dec'];
            battingData = [39.8, 45.3, 42.5];
            bowlingData = [28.9, 26.5, 28.3];
            strikeRateData = [128.4, 135.6, 130.2];
            break;
        case '6':
            labels = ['Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            battingData = [38.2, 41.1, 39.8, 45.3, 42.5, 44.2];
            bowlingData = [30.1, 29.5, 28.9, 26.5, 28.3, 27.8];
            strikeRateData = [125.8, 129.3, 128.4, 135.6, 130.2, 133.1];
            break;
        case '12':
            labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            battingData = [35.2, 38.5, 42.1, 39.8, 45.3, 42.5, 38.2, 41.1, 39.8, 45.3, 42.5, 44.2];
            bowlingData = [32.1, 29.8, 31.2, 28.9, 26.5, 28.3, 30.1, 29.5, 28.9, 26.5, 28.3, 27.8];
            strikeRateData = [118.5, 125.2, 132.8, 128.4, 135.6, 130.2, 125.8, 129.3, 128.4, 135.6, 130.2, 133.1];
            break;
    }
    
    chart.data.labels = labels;
    chart.data.datasets[0].data = battingData;
    chart.data.datasets[1].data = bowlingData;
    chart.data.datasets[2].data = strikeRateData;
    chart.update();
}

// Modal functionality
function initializeModals() {
    const performanceModal = document.getElementById('performanceModal');
    const modalClose = performanceModal.querySelector('.modal-close');
    const cancelBtn = performanceModal.querySelector('.btn-outline');
    
    // Close modal handlers
    modalClose.addEventListener('click', closePerformanceModal);
    cancelBtn.addEventListener('click', closePerformanceModal);
    
    // Close modal when clicking outside
    performanceModal.addEventListener('click', function(e) {
        if (e.target === performanceModal) {
            closePerformanceModal();
        }
    });
    
    // Form submission
    const performanceForm = performanceModal.querySelector('.performance-form');
    performanceForm.addEventListener('submit', handlePerformanceSubmission);
    
    // Auto-calculate strike rate
    const runsInput = performanceForm.querySelector('input[type="number"]:nth-of-type(1)');
    const ballsInput = performanceForm.querySelector('input[type="number"]:nth-of-type(2)');
    const strikeRateInput = performanceForm.querySelector('input[readonly]');
    
    function calculateStrikeRate() {
        const runs = parseFloat(runsInput.value) || 0;
        const balls = parseFloat(ballsInput.value) || 0;
        
        if (balls > 0) {
            const strikeRate = ((runs / balls) * 100).toFixed(2);
            strikeRateInput.value = strikeRate;
        } else {
            strikeRateInput.value = '';
        }
    }
    
    runsInput.addEventListener('input', calculateStrikeRate);
    ballsInput.addEventListener('input', calculateStrikeRate);
}

function openPerformanceModal() {
    const modal = document.getElementById('performanceModal');
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closePerformanceModal() {
    const modal = document.getElementById('performanceModal');
    modal.classList.remove('active');
    document.body.style.overflow = '';
    
    // Reset form
    const form = modal.querySelector('.performance-form');
    form.reset();
}

function handlePerformanceSubmission(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    
    // Show loading state
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
    submitBtn.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        showSuccessMessage('Performance data saved successfully!');
        closePerformanceModal();
        
        // Reset button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        
        // Refresh data (in real app, would reload from server)
        setTimeout(() => {
            // Could refresh specific sections here
        }, 1000);
        
    }, 2000);
}

// Action button handlers
function initializeActionButtons() {
    // Add Performance buttons
    const addPerformanceBtns = document.querySelectorAll('.pending-card .btn-primary');
    addPerformanceBtns.forEach(btn => {
        btn.addEventListener('click', openPerformanceModal);
    });
    
    // View Details buttons
    const viewDetailsBtns = document.querySelectorAll('.btn-outline');
    viewDetailsBtns.forEach(btn => {
        if (btn.textContent.includes('View Details') || btn.textContent.includes('View All Matches')) {
            btn.addEventListener('click', handleViewDetails);
        }
    });
    
    // Edit Performance buttons
    const editBtns = document.querySelectorAll('.btn-primary.btn-sm');
    editBtns.forEach(btn => {
        if (btn.textContent.includes('Edit Performance')) {
            btn.addEventListener('click', handleEditPerformance);
        }
    });
}

function handleViewDetails(e) {
    e.preventDefault();
    e.stopPropagation();
    
    const card = e.target.closest('.match-card, .tournament-card');
    if (card) {
        // Get match/tournament details
        const title = card.querySelector('h4, .teams')?.textContent || 'Match Details';
        showDetailsModal(title, card);
    }
}

function handleEditPerformance(e) {
    e.preventDefault();
    e.stopPropagation();
    
    // Pre-populate form with existing data
    openPerformanceModal();
    
    // In a real app, would populate form with existing performance data
    const form = document.querySelector('.performance-form');
    // Example: form.querySelector('input[type="number"]').value = existingRuns;
}

function showDetailsModal(title, card) {
    const modal = document.createElement('div');
    modal.className = 'modal details-modal';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-info-circle"></i> ${title}</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <p>Detailed information about this match/tournament would be displayed here.</p>
                <p>This could include:</p>
                <ul>
                    <li>Complete scorecard</li>
                    <li>Player statistics</li>
                    <li>Match summary</li>
                    <li>Video highlights</li>
                    <li>Performance analysis</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline close-details">Close</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    modal.classList.add('active');
    
    // Event listeners
    modal.querySelector('.modal-close').addEventListener('click', () => {
        modal.classList.remove('active');
        setTimeout(() => document.body.removeChild(modal), 300);
    });
    
    modal.querySelector('.close-details').addEventListener('click', () => {
        modal.classList.remove('active');
        setTimeout(() => document.body.removeChild(modal), 300);
    });
    
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.remove('active');
            setTimeout(() => document.body.removeChild(modal), 300);
        }
    });
}

// Export report functionality
function initializeExportReport() {
    const exportBtn = document.getElementById('exportReport');
    if (exportBtn) {
        exportBtn.addEventListener('click', handleExportReport);
    }
}

function handleExportReport() {
    const btn = document.getElementById('exportReport');
    const originalText = btn.innerHTML;
    
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
    btn.disabled = true;
    
    // Simulate report generation
    setTimeout(() => {
        // In a real app, would generate and download PDF/CSV
        showSuccessMessage('Performance report downloaded successfully!');
        
        btn.innerHTML = originalText;
        btn.disabled = false;
    }, 2000);
}

// Utility functions
function showSuccessMessage(message) {
    const successMsg = document.createElement('div');
    successMsg.className = 'success-message';
    successMsg.innerHTML = `
        <i class="fas fa-check-circle"></i>
        <span>${message}</span>
    `;
    
    successMsg.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: linear-gradient(135deg, #27ae60, #2ecc71);
        color: white;
        padding: 15px 20px;
        border-radius: 10px;
        box-shadow: 0 8px 25px rgba(46, 204, 113, 0.3);
        z-index: 10001;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
        transform: translateX(400px);
        transition: transform 0.3s ease;
    `;
    
    document.body.appendChild(successMsg);
    
    setTimeout(() => {
        successMsg.style.transform = 'translateX(0)';
    }, 100);
    
    setTimeout(() => {
        successMsg.style.transform = 'translateX(400px)';
        setTimeout(() => {
            if (document.body.contains(successMsg)) {
                document.body.removeChild(successMsg);
            }
        }, 300);
    }, 3000);
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const activeModal = document.querySelector('.modal.active');
        if (activeModal) {
            activeModal.classList.remove('active');
            document.body.style.overflow = '';
            
            // Remove dynamically created modals
            if (activeModal.classList.contains('details-modal')) {
                setTimeout(() => {
                    if (document.body.contains(activeModal)) {
                        document.body.removeChild(activeModal);
                    }
                }, 300);
            }
        }
    }
    
    // Quick export with Ctrl+E
    if (e.ctrlKey && e.key === 'e') {
        e.preventDefault();
        handleExportReport();
    }
});

// Initialize animations on scroll
function initializeScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Observe stat cards and other elements
    document.querySelectorAll('.stat-card, .match-card, .tournament-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });
}

// Initialize scroll animations when page loads
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(initializeScrollAnimations, 500);
});