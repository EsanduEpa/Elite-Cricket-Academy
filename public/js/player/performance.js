// Performance Analytics JavaScript

function getPerformanceBaseUrl() {
    const page = document.getElementById('performancePage');
    const urlRoot = page?.dataset?.urlroot || `${window.location.origin}/Elite`;
    return `${urlRoot}/performance`;
}

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

    // Chart data - injected from server via PHP
    const serverChartData = window.performanceData?.chartData || {};
    const defaultLabels = serverChartData.labels || [];
    const battingData = serverChartData.batting || [];
    const bowlingData = serverChartData.bowling || [];
    const strikeRateData = serverChartData.strikeRate || [];

    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: defaultLabels,
            datasets: [
                {
                    label: 'Batting Average',
                    data: battingData,
                    borderColor: '#4A90E2',
                    backgroundColor: 'rgba(74, 144, 226, 0.1)',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Bowling Average',
                    data: bowlingData,
                    borderColor: '#e74c3c',
                    backgroundColor: 'rgba(231, 76, 60, 0.1)',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Strike Rate',
                    data: strikeRateData,
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

// Update chart data based on period - data from server via PHP
function updateChartData(chart, period) {
    const periodData = window.performanceData?.periodData?.[period] || {};
    const labels = periodData.labels || [];
    const battingData = periodData.batting || [];
    const bowlingData = periodData.bowling || [];
    const strikeRateData = periodData.strikeRate || [];
    
    chart.data.labels = labels;
    chart.data.datasets[0].data = battingData;
    chart.data.datasets[1].data = bowlingData;
    chart.data.datasets[2].data = strikeRateData;
    chart.update();
}

// Modal functionality
function initializeModals() {
    const performanceModal = document.getElementById('performanceModal');
    if (!performanceModal) {
        return;
    }

    const modalClose = performanceModal.querySelector('.close, .modal-close');
    const cancelBtn = performanceModal.querySelector('.btn-outline');
    
    // Close modal handlers
    if (modalClose) {
        modalClose.addEventListener('click', closePerformanceModal);
    }
    if (cancelBtn) {
        cancelBtn.addEventListener('click', closePerformanceModal);
    }
    
    // Close modal when clicking outside
    performanceModal.addEventListener('click', function(e) {
        if (e.target === performanceModal) {
            closePerformanceModal();
        }
    });
    
    const performanceForm = document.getElementById('performanceStatsForm') || performanceModal.querySelector('.performance-form');
    
    // Auto-calculate strike rate
    const runsInput = document.getElementById('runsScored');
    const ballsInput = document.getElementById('ballsFaced');
    const strikeRateInput = performanceForm ? performanceForm.querySelector('input[readonly]') : null;
    
    function calculateStrikeRate() {
        const runs = parseFloat(runsInput.value) || 0;
        const balls = parseFloat(ballsInput.value) || 0;
        
        if (!strikeRateInput) {
            return;
        }

        if (balls > 0) {
            const strikeRate = ((runs / balls) * 100).toFixed(2);
            strikeRateInput.value = strikeRate;
        } else {
            strikeRateInput.value = '';
        }
    }
    
    if (runsInput) {
        runsInput.addEventListener('input', calculateStrikeRate);
    }
    if (ballsInput) {
        ballsInput.addEventListener('input', calculateStrikeRate);
    }
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

// ==================== PERFORMANCE STATISTICS FUNCTIONS ====================

// Open Performance Statistics Modal
function openPerformanceModal(selectedMatchId = '') {
    const modal = document.getElementById('performanceModal');
    if (modal) {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
        
        // Load available matches
        loadAvailableMatches(selectedMatchId);
        
        // Reset form to "add" mode
        const form = document.getElementById('performanceStatsForm');
        if (form) {
            form.reset();
            form.dataset.mode = 'add';
            
            // Remove edit performance ID if it exists
            const perfIdInput = document.getElementById('performanceIdEdit');
            if (perfIdInput) {
                perfIdInput.remove();
            }
            
            // Reset modal title and styling
            const modalTitle = modal.querySelector('h2');
            if (modalTitle) {
                modalTitle.innerHTML = '<i class="fas fa-chart-bar" style="color: #fff;"></i> Add Performance Statistics';
            }
            
            const modalHeader = modal.querySelector('.modal-header');
            if (modalHeader) {
                modalHeader.style.background = 'linear-gradient(135deg, #27ae60, #2ecc71)';
            }
            
            // Reset submit button
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-save"></i> Submit Performance Statistics';
            }
        }
        
        // Animate modal in
        setTimeout(() => {
            const modalContent = modal.querySelector('.modal-content');
            if (modalContent) {
                modalContent.style.animation = 'slideIn 0.3s ease-out';
            }
        }, 10);
    }
}

// Close Performance Statistics Modal
function closePerformanceModal() {
    const modal = document.getElementById('performanceModal');
    if (modal) {
        const modalContent = modal.querySelector('.modal-content');
        if (modalContent) {
            modalContent.style.animation = 'slideOut 0.3s ease-in';
        }
        
        setTimeout(() => {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }, 300);
    }
}

// Load Available Matches for dropdown
function loadAvailableMatches(selectedMatchId = '') {
    const matchSelect = document.getElementById('matchSelect');
    if (!matchSelect) return;
    
    // Show loading state
    matchSelect.innerHTML = '<option value="">Loading matches...</option>';
    matchSelect.disabled = true;
    
    fetch(`${getPerformanceBaseUrl()}/getAvailableMatches`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.matches) {
                matchSelect.innerHTML = '<option value="">-- Select a match --</option>';
                
                data.matches.forEach(match => {
                    const option = document.createElement('option');
                    option.value = match.MatchID;
                    
                    const date = new Date(match.Date).toLocaleDateString('en-US', { 
                        month: 'short', 
                        day: 'numeric', 
                        year: 'numeric' 
                    });
                    
                    option.textContent = `${date} - ${match.OpponentTeam} at ${match.Venue} (${match.TournamentName})`;
                    matchSelect.appendChild(option);
                });

                if (selectedMatchId) {
                    matchSelect.value = String(selectedMatchId);
                }
                
                matchSelect.disabled = false;
            } else {
                matchSelect.innerHTML = '<option value="">No matches available</option>';
                showNotification('No matches available to add performance for', 'warning');
            }
        })
        .catch(error => {
            console.error('Error loading matches:', error);
            matchSelect.innerHTML = '<option value="">Error loading matches</option>';
            showNotification('Failed to load matches. Please try again.', 'error');
        });
}

// Handle Performance Statistics Form Submission
document.addEventListener('DOMContentLoaded', function() {
    const performanceForm = document.getElementById('performanceStatsForm');
    if (performanceForm) {
        performanceForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(performanceForm);
            
            // Validate required fields
            const matchId = formData.get('match_id');
            if (!matchId) {
                showNotification('Please select a match', 'error');
                return;
            }
            
            // Disable submit button
            const submitBtn = performanceForm.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
            
            // Determine if we're adding or editing based on presence of performance_id
            const isEditMode = performanceForm.dataset.mode === 'edit';
            const performanceId = document.getElementById('performanceIdEdit')?.value;
            
            const url = isEditMode && performanceId 
                ? `${getPerformanceBaseUrl()}/editPerformanceStats`
                : `${getPerformanceBaseUrl()}/addPerformanceStats`;
            
            // Submit form via AJAX
            fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    closePerformanceModal();
                    
                    // Reload page after 1.5 seconds to show updated performance
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showNotification(data.message || 'Failed to save performance statistics', 'error');
                    
                    // Show validation errors if any
                    if (data.errors && data.errors.length > 0) {
                        data.errors.forEach(error => {
                            showNotification(error, 'error');
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error submitting performance:', error);
                showNotification('An error occurred. Please try again.', 'error');
            })
            .finally(() => {
                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            });
        });
    }
});

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    const performanceModal = document.getElementById('performanceModal');
    if (event.target === performanceModal) {
        closePerformanceModal();
    }
    
    const detailsModal = document.getElementById('detailsModal');
    if (event.target === detailsModal) {
        closeDetailsModal();
    }
});

// Add notification function if not already present
if (typeof showNotification === 'undefined') {
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = 'notification';
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 18px 25px;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            z-index: 10002;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            transform: translateX(400px);
            transition: transform 0.3s ease;
            min-width: 300px;
        `;
        
        switch(type) {
            case 'success':
                notification.style.background = 'linear-gradient(135deg, #27ae60, #2ecc71)';
                notification.innerHTML = '<i class="fas fa-check-circle"></i> ' + message;
                break;
            case 'error':
                notification.style.background = 'linear-gradient(135deg, #e74c3c, #c0392b)';
                notification.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + message;
                break;
            case 'warning':
                notification.style.background = 'linear-gradient(135deg, #f39c12, #e67e22)';
                notification.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + message;
                break;
            default:
                notification.style.background = 'linear-gradient(135deg, #3498db, #2980b9)';
                notification.innerHTML = '<i class="fas fa-info-circle"></i> ' + message;
        }
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        // Remove after 4 seconds
        setTimeout(() => {
            notification.style.transform = 'translateX(400px)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 4000);
    }
}

// ==================== MATCH PERFORMANCE CRUD FUNCTIONS ====================

// View Match Performance Details
function viewMatchDetails(performanceId) {
    fetch(`${getPerformanceBaseUrl()}/getPerformanceRecord?id=${performanceId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.performance) {
                const perf = data.performance;
                
                // Create details modal
                const detailsHtml = `
                    <div class="modal" id="detailsModal" style="display: block; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
                        <div class="modal-content" style="position: relative; background-color: #fefefe; margin: 5% auto; padding: 0; border-radius: 12px; width: 90%; max-width: 650px; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
                            <div class="modal-header" style="background: linear-gradient(135deg, #3498db, #2980b9); color: white; padding: 25px; border-radius: 12px 12px 0 0; display: flex; justify-content: space-between; align-items: center;">
                                <h2 style="margin: 0; font-size: 22px; font-weight: 600;">
                                    <i class="fas fa-chart-line"></i> Match Performance Details
                                </h2>
                                <span onclick="closeDetailsModal()" style="color: #fff; font-size: 32px; font-weight: bold; cursor: pointer; padding: 5px; border-radius: 50%; opacity: 0.8;">&times;</span>
                            </div>
                            <div class="modal-body" style="padding: 35px;">
                                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                                    <h3 style="margin: 0 0 15px 0; color: #2c3e50;"><i class="fas fa-info-circle"></i> Match Information</h3>
                                    <p><strong>📅 Date:</strong> ${perf.Date ? new Date(perf.Date).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) : 'N/A'}</p>
                                    <p><strong>🏆 Tournament:</strong> ${perf.TournamentName || 'N/A'}</p>
                                    <p><strong>⚔️ Opponent:</strong> ${perf.OpponentTeam || 'N/A'}</p>
                                    <p><strong>📍 Venue:</strong> ${perf.Venue || 'N/A'}</p>
                                    <p><strong>🎯 Result:</strong> ${perf.Result || 'N/A'}</p>
                                </div>
                                
                                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                                    <div style="background: #e3f2fd; padding: 15px; border-radius: 8px; text-align: center;">
                                        <h4 style="margin: 0 0 10px 0; color: #3498db;"><i class="fas fa-baseball-ball"></i> Batting</h4>
                                        <p style="font-size: 24px; font-weight: bold; margin: 5px 0;">${perf.RunsScored || 0}</p>
                                        <p style="font-size: 12px; color: #7f8c8d; margin: 0;">Runs (${perf.BallsFaced || 0} balls)</p>
                                    </div>
                                    
                                    <div style="background: #ffebee; padding: 15px; border-radius: 8px; text-align: center;">
                                        <h4 style="margin: 0 0 10px 0; color: #e74c3c;"><i class="fas fa-fire"></i> Bowling</h4>
                                        <p style="font-size: 24px; font-weight: bold; margin: 5px 0;">${perf.WicketsTaken || 0}</p>
                                        <p style="font-size: 12px; color: #7f8c8d; margin: 0;">Wickets (${perf.OversBowled || 0} overs)</p>
                                        <p style="font-size: 12px; color: #7f8c8d; margin: 5px 0 0 0;">${perf.RunsConceded || 0} runs conceded</p>
                                    </div>
                                    
                                    <div style="background: #e8f5e9; padding: 15px; border-radius: 8px; text-align: center;">
                                        <h4 style="margin: 0 0 10px 0; color: #27ae60;"><i class="fas fa-hand-paper"></i> Fielding</h4>
                                        <p style="font-size: 24px; font-weight: bold; margin: 5px 0;">${(perf.Catches || 0) + (perf.Stumpings || 0)}</p>
                                        <p style="font-size: 12px; color: #7f8c8d; margin: 0;">C: ${perf.Catches || 0} | S: ${perf.Stumpings || 0}</p>
                                    </div>
                                </div>
                                
                                <div style="background: #fff3cd; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                                    <p style="margin: 0; font-size: 14px; color: #856404;"><strong>⭐ Overall Rating:</strong> ${perf.Rating || 0}/10</p>
                                </div>
                                
                                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                                    <p style="margin: 0; font-size: 13px; color: #7f8c8d;">
                                        <strong>Status:</strong> 
                                        ${perf.VerifiedStatus === 'verified' ? '✅ Verified' : perf.VerifiedStatus === 'pending' ? '⏳ Pending Review' : '❌ Rejected'}
                                    </p>
                                    ${perf.AddedByName ? `<p style="margin: 5px 0 0 0; font-size: 13px; color: #7f8c8d;"><strong>Added by:</strong> ${perf.AddedByName}</p>` : ''}
                                    ${perf.VerifiedByName ? `<p style="margin: 5px 0 0 0; font-size: 13px; color: #7f8c8d;"><strong>Verified by:</strong> ${perf.VerifiedByName}</p>` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                document.body.insertAdjacentHTML('beforeend', detailsHtml);
            } else {
                showNotification('Failed to load performance details', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred while loading details', 'error');
        });
}

function closeDetailsModal() {
    const modal = document.getElementById('detailsModal');
    if (modal) {
        modal.remove();
    }
}

// Edit Match Performance
function editMatchPerformance(performanceId) {
    // Fetch performance data
    fetch(`${getPerformanceBaseUrl()}/getPerformanceRecord?id=${performanceId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.performance) {
                const perf = data.performance;
                
                // Open the modal
                openPerformanceModal(perf.MatchID || '');
                
                // Wait for modal to be fully loaded
                setTimeout(() => {
                    // Change modal title
                    const modalTitle = document.querySelector('#performanceModal h2');
                    if (modalTitle) {
                        modalTitle.innerHTML = '<i class="fas fa-edit" style="color: #fff;"></i> Edit Performance Statistics';
                    }
                    
                    // Change modal header color
                    const modalHeader = document.querySelector('#performanceModal .modal-header');
                    if (modalHeader) {
                        modalHeader.style.background = 'linear-gradient(135deg, #f39c12, #e67e22)';
                    }
                    
                    // Populate form fields
                    document.getElementById('matchSelect').value = perf.MatchID || '';
                    document.getElementById('runsScored').value = perf.RunsScored || 0;
                    document.getElementById('ballsFaced').value = perf.BallsFaced || 0;
                    document.getElementById('wicketsTaken').value = perf.WicketsTaken || 0;
                    document.getElementById('oversBowled').value = perf.OversBowled || 0;
                    document.getElementById('runsConceded').value = perf.RunsConceded || 0;
                    document.getElementById('catches').value = perf.Catches || 0;
                    document.getElementById('stumpings').value = perf.Stumpings || 0;
                    document.getElementById('performanceRating').value = perf.Rating || 0;
                    
                    // Add hidden field for performance ID
                    let perfIdInput = document.getElementById('performanceIdEdit');
                    if (!perfIdInput) {
                        perfIdInput = document.createElement('input');
                        perfIdInput.type = 'hidden';
                        perfIdInput.id = 'performanceIdEdit';
                        perfIdInput.name = 'performance_id';
                        document.getElementById('performanceStatsForm').appendChild(perfIdInput);
                    }
                    perfIdInput.value = performanceId;
                    
                    // Change submit button text
                    const submitBtn = document.querySelector('#performanceStatsForm button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.innerHTML = '<i class="fas fa-save"></i> Update Performance Statistics';
                    }
                    
                    // Update form action
                    document.getElementById('performanceStatsForm').dataset.mode = 'edit';
                }, 300);
            } else {
                showNotification('Failed to load performance data', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred while loading performance data', 'error');
        });
}

// Delete Match Performance
function deleteMatchPerformance(performanceId) {
    if (!confirm('Are you sure you want to delete this performance record? This action cannot be undone.')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('performance_id', performanceId);
    
    fetch(`${getPerformanceBaseUrl()}/deletePerformanceStats`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            
            // Reload page after 1 second
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showNotification(data.message || 'Failed to delete performance record', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
    });
}

// ==================== ACHIEVEMENTS MODAL/ACTIONS (extracted from view) ====================

(function performanceAchievementsModule() {
    function getPerformanceUrlRoot() {
        const page = document.getElementById('performancePage');
        return (page && page.dataset && page.dataset.urlroot) ? page.dataset.urlroot : '';
    }

    function showAddAchievementModal() {
        const modalTitle = document.getElementById('modalTitle');
        const submitText = document.getElementById('submitText');
        const verificationStatus = document.getElementById('verificationStatus');
        const form = document.getElementById('achievementForm');
        const achievementId = document.getElementById('achievementId');
        const modal = document.getElementById('achievementModal');

        if (modalTitle) {
            modalTitle.innerHTML = '<i class="fas fa-trophy" style="color: #f1c40f;"></i> Add New Achievement';
        }
        if (submitText) submitText.textContent = 'Save Achievement';
        if (verificationStatus) verificationStatus.style.display = 'none';
        if (form) form.reset();
        if (achievementId) achievementId.value = '';

        if (modal) modal.style.display = 'block';

        const dateInput = document.getElementById('achievementDate');
        if (dateInput) {
            const today = new Date().toISOString().split('T')[0];
            dateInput.value = today;
            window.setTimeout(() => dateInput.focus(), 300);
        }
    }

    function showEditAchievementModal(achievementData) {
        const modalTitle = document.getElementById('modalTitle');
        const submitText = document.getElementById('submitText');
        const verificationStatus = document.getElementById('verificationStatus');

        if (modalTitle) {
            modalTitle.innerHTML = '<i class="fas fa-edit" style="color: #3498db;"></i> Edit Achievement';
        }
        if (submitText) submitText.textContent = 'Update Achievement';
        if (verificationStatus) verificationStatus.style.display = 'block';

        const setValue = (id, value) => {
            const el = document.getElementById(id);
            if (el) el.value = value ?? '';
        };

        setValue('achievementId', achievementData?.AchievementID);
        setValue('achievementDate', achievementData?.Date);
        setValue('matchName', achievementData?.MatchName);
        setValue('tournamentName', achievementData?.Tournament);
        setValue('achievementText', achievementData?.Achievement);
        setValue('verifiedStatus', achievementData?.VerifiedStatus);

        const modal = document.getElementById('achievementModal');
        if (modal) modal.style.display = 'block';
    }

    function closeAchievementModal() {
        const modal = document.getElementById('achievementModal');
        const form = document.getElementById('achievementForm');

        if (modal) modal.style.display = 'none';
        if (form) form.reset();

        if (modal) {
            const inputs = modal.querySelectorAll('input, textarea, select');
            inputs.forEach((input) => {
                input.style.borderColor = '#ddd';
                input.style.backgroundColor = '#fafafa';
                input.style.boxShadow = 'none';
            });
        }
    }

    function validateAchievementForm() {
        const form = document.getElementById('achievementForm');
        if (!form) return false;

        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        let firstInvalidField = null;

        requiredFields.forEach((field) => {
            if (!String(field.value || '').trim()) {
                field.style.borderColor = '#e74c3c';
                field.style.backgroundColor = '#fdf2f2';
                field.style.boxShadow = '0 0 0 3px rgba(231,76,60,0.1)';
                if (!firstInvalidField) firstInvalidField = field;
                isValid = false;
            } else {
                field.style.borderColor = '#27ae60';
                field.style.backgroundColor = '#f8fff8';
                field.style.boxShadow = '0 0 0 3px rgba(39,174,96,0.1)';
            }
        });

        if (!isValid && firstInvalidField) {
            firstInvalidField.focus();
            showNotification('Please fill in all required fields', 'error');
        }

        return isValid;
    }

    function viewAchievement(achievementId) {
        showNotification('Loading achievement details...', 'info');

        const url = getPerformanceUrlRoot() + '/player/getAchievement?id=' + encodeURIComponent(achievementId);
        fetch(url)
            .then((response) => response.json())
            .then((data) => {
                if (!data.success) {
                    showNotification('Error: ' + data.message, 'error');
                    return;
                }

                const achievement = data.achievement;
                const statusIcon = achievement.VerifiedStatus === 'verified' ? '✅' :
                    achievement.VerifiedStatus === 'pending' ? '⏳' : '❌';
                const statusText = achievement.VerifiedStatus === 'verified' ? 'Verified' :
                    achievement.VerifiedStatus === 'pending' ? 'Pending Review' : 'Rejected';

                const detailsHtml = `
                    <div style="font-family: Arial, sans-serif; line-height: 1.6;">
                        <h3 style="color: #2c3e50; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-trophy" style="color: #f1c40f;"></i> Achievement Details
                        </h3>
                        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 15px;">
                            <p><strong>📅 Date:</strong> ${new Date(achievement.Date).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</p>
                            <p><strong>🏆 Tournament:</strong> ${achievement.Tournament}</p>
                            <p><strong>⚾ Match:</strong> ${achievement.MatchName}</p>
                            <p><strong>🎯 Achievement:</strong> ${achievement.Achievement}</p>
                            <p><strong>✅ Status:</strong> ${statusIcon} ${statusText}</p>
                            <p><strong>📝 Submitted:</strong> ${new Date(achievement.CreatedAt).toLocaleDateString()}</p>
                        </div>
                        ${achievement.VerifiedStatus === 'verified' ?
                            '<div style="background: #d5f4e6; color: #27ae60; padding: 15px; border-radius: 8px; text-align: center;"><i class="fas fa-medal"></i> <strong>Congratulations! This achievement has been officially verified.</strong></div>' :
                            achievement.VerifiedStatus === 'pending' ?
                                '<div style="background: #fef9e7; color: #f39c12; padding: 15px; border-radius: 8px; text-align: center;"><i class="fas fa-clock"></i> <strong>This achievement is under review by the coaching staff.</strong></div>' :
                                '<div style="background: #fadbd8; color: #e74c3c; padding: 15px; border-radius: 8px; text-align: center;"><i class="fas fa-times-circle"></i> <strong>This achievement could not be verified. Please contact your coach for details.</strong></div>'
                        }
                    </div>
                `;

                const viewModal = document.createElement('div');
                viewModal.style.cssText = 'position: fixed; z-index: 1100; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center;';
                viewModal.innerHTML = `
                    <div style="background: white; border-radius: 12px; max-width: 600px; width: 90%; max-height: 80vh; overflow-y: auto; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
                        <div style="padding: 30px;">
                            ${detailsHtml}
                            <div style="text-align: center; margin-top: 25px;">
                                <button onclick="this.parentElement.parentElement.parentElement.parentElement.remove()"
                                        style="padding: 12px 30px; background: #3498db; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 14px;">
                                    <i class="fas fa-times"></i> Close
                                </button>
                            </div>
                        </div>
                    </div>
                `;

                viewModal.addEventListener('click', function (e) {
                    if (e.target === viewModal) viewModal.remove();
                });

                document.body.appendChild(viewModal);
            })
            .catch((error) => {
                console.error('Error:', error);
                showNotification('Failed to load achievement details. Please try again.', 'error');
            });
    }

    function editAchievement(achievementId) {
        showNotification('Loading achievement for editing...', 'info');

        const url = getPerformanceUrlRoot() + '/player/getAchievement?id=' + encodeURIComponent(achievementId);
        fetch(url)
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    showEditAchievementModal(data.achievement);
                } else {
                    showNotification('Error: ' + data.message, 'error');
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                showNotification('Failed to load achievement details. Please try again.', 'error');
            });
    }

    function deleteAchievement(achievementId) {
        const userConfirmed = confirm('Are you sure you want to delete this rejected achievement? This action cannot be undone.');
        if (!userConfirmed) return;

        showNotification('Deleting achievement...', 'info');

        const formData = new FormData();
        formData.append('achievement_id', achievementId);

        fetch(getPerformanceUrlRoot() + '/player/deleteAchievement', {
            method: 'POST',
            body: formData
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    window.setTimeout(() => window.location.reload(), 1500);
                } else {
                    showNotification('Error: ' + data.message, 'error');
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                showNotification('Failed to delete achievement. Please try again.', 'error');
            });
    }

    function addAchievement() {
        showAddAchievementModal();
    }

    function initAchievementFormSubmit() {
        const form = document.getElementById('achievementForm');
        if (!form || form.dataset.jsBound === '1') return;
        form.dataset.jsBound = '1';

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            if (!validateAchievementForm()) return;

            const formData = new FormData(form);
            const achievementId = document.getElementById('achievementId')?.value;
            const url = achievementId ?
                (getPerformanceUrlRoot() + '/player/editAchievement') :
                (getPerformanceUrlRoot() + '/player/addAchievement');

            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const formFields = document.getElementById('formFields');
            const formLoading = document.getElementById('formLoading');
            const originalText = submitText ? submitText.textContent : '';

            if (submitBtn) submitBtn.disabled = true;
            if (formFields) formFields.style.display = 'none';
            if (formLoading) formLoading.style.display = 'block';

            fetch(url, {
                method: 'POST',
                body: formData
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        closeAchievementModal();
                        window.setTimeout(() => window.location.reload(), 1500);
                    } else {
                        showNotification('Error: ' + data.message, 'error');
                        if (data.errors) {
                            showNotification('Validation errors: ' + data.errors.join(', '), 'error');
                        }
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                    showNotification('Failed to save achievement. Please try again.', 'error');
                })
                .finally(() => {
                    if (submitBtn) submitBtn.disabled = false;
                    if (formFields) formFields.style.display = 'block';
                    if (formLoading) formLoading.style.display = 'none';
                    if (submitText) submitText.textContent = originalText;
                });
        });
    }

    window.showAddAchievementModal = showAddAchievementModal;
    window.showEditAchievementModal = showEditAchievementModal;
    window.closeAchievementModal = closeAchievementModal;
    window.viewAchievement = viewAchievement;
    window.editAchievement = editAchievement;
    window.deleteAchievement = deleteAchievement;
    window.addAchievement = addAchievement;

    document.addEventListener('DOMContentLoaded', function () {
        initAchievementFormSubmit();
    });

    window.addEventListener('click', function (event) {
        const modal = document.getElementById('achievementModal');
        if (modal && event.target === modal) {
            closeAchievementModal();
        }
    });

    document.addEventListener('keydown', function (e) {
        const modal = document.getElementById('achievementModal');
        if (modal && modal.style.display === 'block' && e.key === 'Escape') {
            closeAchievementModal();
        }
    });
})();

