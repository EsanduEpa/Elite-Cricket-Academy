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

    initializePerformanceViewActions();
}

function initializePerformanceViewActions() {
    document.addEventListener('click', function (event) {
        const actionTrigger = event.target.closest('[data-performance-action]');
        if (actionTrigger) {
            const action = actionTrigger.dataset.performanceAction;
            const achievementId = actionTrigger.dataset.achievementId;

            if (action === 'add-achievement') {
                event.preventDefault();
                showAddAchievementModal();
                return;
            }

            if (action === 'close-achievement-modal') {
                event.preventDefault();
                closeAchievementModal();
                return;
            }

            if (action === 'close-achievement-view-modal') {
                event.preventDefault();
                closeAchievementViewModal();
                return;
            }

            if (action === 'close-details-modal') {
                event.preventDefault();
                closeDetailsModal();
                return;
            }

            if (action === 'close-performance-modal') {
                event.preventDefault();
                closePerformanceModal();
                return;
            }

            if (action === 'open-performance-modal') {
                event.preventDefault();
                openPerformanceModal();
                return;
            }

            if (action === 'view-achievement' && achievementId) {
                event.preventDefault();
                viewAchievement(achievementId);
                return;
            }

            if (action === 'edit-achievement' && achievementId) {
                event.preventDefault();
                editAchievement(achievementId);
                return;
            }

            if (action === 'delete-achievement' && achievementId) {
                event.preventDefault();
                deleteAchievement(achievementId);
                return;
            }

            const performanceId = actionTrigger.dataset.performanceId;

            if (action === 'view-match-performance' && performanceId) {
                event.preventDefault();
                viewMatchDetails(performanceId);
                return;
            }

            if (action === 'edit-match-performance' && performanceId) {
                event.preventDefault();
                editMatchPerformance(performanceId);
                return;
            }

            if (action === 'delete-match-performance' && performanceId) {
                event.preventDefault();
                deleteMatchPerformance(performanceId);
            }
        }

        const placeholderTrigger = event.target.closest('[data-placeholder-message]');
        if (placeholderTrigger) {
            event.preventDefault();
            alert(placeholderTrigger.dataset.placeholderMessage);
        }
    });
}

function openAppModalById(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.add('app-modal--visible');
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('modal-open');
}

function closeAppModalById(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.remove('app-modal--visible');
    modal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('modal-open');
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
    
    const performanceForm = document.getElementById('performanceStatsForm');
    
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
    if (!modal) {
        return;
    }

    modal.classList.add('app-modal--visible');
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('modal-open');
}

function closePerformanceModal() {
    const modal = document.getElementById('performanceModal');
    if (!modal) {
        return;
    }

    modal.classList.remove('app-modal--visible');
    modal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('modal-open');
    
    // Reset form
    const form = document.getElementById('performanceStatsForm');
    if (form) {
        form.reset();
    }
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
    const form = document.getElementById('performanceStatsForm');
    // Example: form.querySelector('input[type="number"]').value = existingRuns;
}

function showDetailsModal(title, card) {
    const modal = document.getElementById('detailsModal');
    if (!modal) return;

    const titleEl = document.getElementById('detailsModalTitle');
    if (titleEl) titleEl.textContent = title || 'Details';

    const generic = document.getElementById('detailsGenericMessage');
    const matchInfo = document.getElementById('detailsMatchInfo');
    const statsGrid = document.getElementById('detailsStatsGrid');
    const rating = document.getElementById('detailsRating');
    const meta = document.getElementById('detailsMeta');

    if (generic) {
        generic.style.display = 'block';
        generic.textContent = 'Detailed information will be shown here.';
    }
    if (matchInfo) matchInfo.style.display = 'none';
    if (statsGrid) statsGrid.style.display = 'none';
    if (rating) rating.style.display = 'none';
    if (meta) meta.style.display = 'none';

    openAppModalById('detailsModal');
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
        const activeModal = document.querySelector('.app-modal.app-modal--visible');
        if (activeModal) {
            if (activeModal.id) {
                closeAppModalById(activeModal.id);
            } else {
                activeModal.classList.remove('app-modal--visible');
                activeModal.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('modal-open');
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

function updatePerformanceModalHeader(mode = 'add') {
    const modal = document.getElementById('performanceModal');
    if (!modal) {
        return;
    }

    const title = modal.querySelector('.app-modal__title');
    const subtitle = modal.querySelector('.app-modal__subtitle');
    const icon = modal.querySelector('.app-modal__icon i');
    const header = modal.querySelector('.app-modal__header');

    if (header) {
        header.classList.remove('app-modal__header--success', 'app-modal__header--danger', 'app-modal__header--neutral');
        header.style.background = '';
    }

    if (mode === 'edit') {
        if (title) {
            title.textContent = 'Edit Performance Statistics';
        }
        if (subtitle) {
            subtitle.textContent = 'Update an existing performance entry using the same shared modal and register-style form layout.';
        }
        if (icon) {
            icon.className = 'fas fa-pen-to-square';
        }
        return;
    }

    if (title) {
        title.textContent = 'Add Performance Statistics';
    }
    if (subtitle) {
        subtitle.textContent = 'Submit your latest batting, bowling, and fielding figures in the same register-style layout used across player forms.';
    }
    if (icon) {
        icon.className = 'fas fa-chart-bar';
    }
}

// Open Performance Statistics Modal
function openPerformanceModal(selectedMatchId = '') {
    const modal = document.getElementById('performanceModal');
    if (modal) {
        modal.classList.add('app-modal--visible');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');
        
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
            
            updatePerformanceModalHeader('add');
            
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
            modal.classList.remove('app-modal--visible');
            modal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('modal-open');
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

                const modal = document.getElementById('detailsModal');
                if (!modal) {
                    showNotification('Details modal is missing on this page', 'error');
                    return;
                }

                const generic = document.getElementById('detailsGenericMessage');
                const matchInfo = document.getElementById('detailsMatchInfo');
                const statsGrid = document.getElementById('detailsStatsGrid');
                const rating = document.getElementById('detailsRating');
                const meta = document.getElementById('detailsMeta');

                if (generic) generic.style.display = 'none';
                if (matchInfo) matchInfo.style.display = '';
                if (statsGrid) statsGrid.style.display = '';
                if (rating) rating.style.display = '';
                if (meta) meta.style.display = '';

                const dateText = perf.Date ? new Date(perf.Date).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) : 'N/A';
                const statusText = perf.VerifiedStatus === 'verified' ? 'Verified' : perf.VerifiedStatus === 'pending' ? 'Pending Review' : perf.VerifiedStatus === 'rejected' ? 'Rejected' : 'N/A';

                const setText = (id, value) => {
                    const el = document.getElementById(id);
                    if (el) el.textContent = (value === null || value === undefined || value === '') ? '-' : String(value);
                };

                setText('detailsDate', dateText);
                setText('detailsTournament', perf.TournamentName || 'N/A');
                setText('detailsOpponent', perf.OpponentTeam || 'N/A');
                setText('detailsVenue', perf.Venue || 'N/A');
                setText('detailsResult', perf.Result || 'N/A');

                const runs = Number(perf.RunsScored || 0);
                const balls = Number(perf.BallsFaced || 0);
                const wickets = Number(perf.WicketsTaken || 0);
                const overs = Number(perf.OversBowled || 0);
                const conceded = Number(perf.RunsConceded || 0);
                const catches = Number(perf.Catches || 0);
                const stumpings = Number(perf.Stumpings || 0);

                setText('detailsRuns', runs);
                setText('detailsBalls', balls);
                setText('detailsWickets', wickets);
                setText('detailsOvers', overs);
                setText('detailsConceded', conceded);
                setText('detailsCatches', catches);
                setText('detailsStumpings', stumpings);
                setText('detailsFieldingTotal', catches + stumpings);
                setText('detailsRatingValue', Number(perf.Rating || 0));
                setText('detailsStatus', statusText);

                const addedByRow = document.getElementById('detailsAddedByRow');
                const verifiedByRow = document.getElementById('detailsVerifiedByRow');

                if (perf.AddedByName) {
                    setText('detailsAddedBy', perf.AddedByName);
                    if (addedByRow) addedByRow.style.display = '';
                } else if (addedByRow) {
                    addedByRow.style.display = 'none';
                }

                if (perf.VerifiedByName) {
                    setText('detailsVerifiedBy', perf.VerifiedByName);
                    if (verifiedByRow) verifiedByRow.style.display = '';
                } else if (verifiedByRow) {
                    verifiedByRow.style.display = 'none';
                }

                openAppModalById('detailsModal');
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
    closeAppModalById('detailsModal');
}

function closeAchievementViewModal() {
    closeAppModalById('achievementViewModal');
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
                    updatePerformanceModalHeader('edit');
                    
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

        if (modal) {
            modal.classList.add('app-modal--visible');
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('modal-open');
        }

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
        if (modal) {
            modal.classList.add('app-modal--visible');
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('modal-open');
        }
    }

    function closeAchievementModal() {
        const modal = document.getElementById('achievementModal');
        const form = document.getElementById('achievementForm');

        if (modal) {
            modal.classList.remove('app-modal--visible');
            modal.setAttribute('aria-hidden', 'true');
        }
        document.body.classList.remove('modal-open');
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

                const modal = document.getElementById('achievementViewModal');
                if (!modal) {
                    showNotification('Achievement view modal is missing on this page', 'error');
                    return;
                }

                const statusText = achievement.VerifiedStatus === 'verified' ? 'Verified' :
                    achievement.VerifiedStatus === 'pending' ? 'Pending Review' :
                    achievement.VerifiedStatus === 'rejected' ? 'Rejected' : 'N/A';

                const dateText = achievement.Date ? new Date(achievement.Date).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) : 'N/A';
                const createdText = achievement.CreatedAt ? new Date(achievement.CreatedAt).toLocaleDateString() : 'N/A';

                const setText = (id, value) => {
                    const el = document.getElementById(id);
                    if (el) el.textContent = (value === null || value === undefined || value === '') ? '-' : String(value);
                };

                setText('achievementViewDate', dateText);
                setText('achievementViewTournament', achievement.Tournament || 'N/A');
                setText('achievementViewMatch', achievement.MatchName || 'N/A');
                setText('achievementViewText', achievement.Achievement || 'N/A');
                setText('achievementViewStatus', statusText);
                setText('achievementViewSubmitted', createdText);

                const banner = document.getElementById('achievementViewBanner');
                if (banner) {
                    banner.textContent = '';
                    if (achievement.VerifiedStatus === 'verified') {
                        banner.textContent = 'Congratulations! This achievement has been officially verified.';
                    } else if (achievement.VerifiedStatus === 'pending') {
                        banner.textContent = 'This achievement is under review by the coaching staff.';
                    } else if (achievement.VerifiedStatus === 'rejected') {
                        banner.textContent = 'This achievement could not be verified. Please contact your coach for details.';
                    }
                }

                openAppModalById('achievementViewModal');
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
        if (modal && modal.classList.contains('app-modal--visible') && e.key === 'Escape') {
            closeAchievementModal();
        }
    });
})();

