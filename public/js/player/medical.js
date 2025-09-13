// Medical Records Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize medical dashboard
    initializeMedicalDashboard();

    function initializeMedicalDashboard() {
        initializeMedicalNavigation();
        initializeFilterFunctionality();
        initializeRecordActions();
        initializeVitalSignsAnimation();
        initializeFitnessMetrics();
        initializeProgressRings();
        initializeTimelineAnimations();
        initializeAppointmentActions();
        initializeHealthAlerts();
        updateHealthScore();
    }

    // Initialize medical navigation tabs
    function initializeMedicalNavigation() {
        const navTabs = document.querySelectorAll('.nav-tab');
        const medicalSections = document.querySelectorAll('.medical-section');

        navTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const targetSection = this.getAttribute('data-section');
                
                // Remove active class from all tabs and sections
                navTabs.forEach(t => t.classList.remove('active'));
                medicalSections.forEach(s => s.classList.remove('active'));
                
                // Add active class to clicked tab and corresponding section
                this.classList.add('active');
                const targetSectionElement = document.getElementById(targetSection);
                if (targetSectionElement) {
                    targetSectionElement.classList.add('active');
                    
                    // Animate section transition
                    targetSectionElement.style.opacity = '0';
                    targetSectionElement.style.transform = 'translateY(20px)';
                    
                    setTimeout(() => {
                        targetSectionElement.style.opacity = '1';
                        targetSectionElement.style.transform = 'translateY(0)';
                    }, 150);
                }
                
                // Show notification for section change
                const sectionNames = {
                    'records': 'Medical Records',
                    'injuries': 'Injury History',
                    'assessments': 'Fitness Assessments',
                    'appointments': 'Appointments'
                };
                
                showNotification(`Switched to ${sectionNames[targetSection]}`, 'info');
            });
        });
    }

    // Initialize filter functionality
    function initializeFilterFunctionality() {
        const filterBtn = document.querySelector('.filter-btn');
        if (filterBtn) {
            filterBtn.addEventListener('click', function() {
                showFilterModal();
            });
        }
    }

    // Show filter modal
    function showFilterModal() {
        const modalHTML = `
            <div class="modal-overlay">
                <div class="modal-content filter-modal">
                    <div class="modal-header">
                        <h3><i class="fas fa-filter"></i> Filter Medical Records</h3>
                        <button class="modal-close" onclick="closeModal()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="filter-group">
                            <label>Record Type</label>
                            <select class="filter-select" id="recordType">
                                <option value="">All Types</option>
                                <option value="routine">Routine Checkup</option>
                                <option value="consultation">Consultation</option>
                                <option value="lab">Lab Results</option>
                                <option value="emergency">Emergency</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label>Date Range</label>
                            <div class="date-range">
                                <input type="date" id="startDate" class="filter-input">
                                <span>to</span>
                                <input type="date" id="endDate" class="filter-input">
                            </div>
                        </div>
                        <div class="filter-group">
                            <label>Doctor/Provider</label>
                            <select class="filter-select" id="provider">
                                <option value="">All Providers</option>
                                <option value="dr-johnson">Dr. Sarah Johnson</option>
                                <option value="maria-rodriguez">Maria Rodriguez</option>
                                <option value="elite-medical">Elite Medical Center</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-outline" onclick="clearFilters()">Clear Filters</button>
                        <button class="btn btn-primary" onclick="applyFilters()">Apply Filters</button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHTML);
    }

    // Initialize record actions
    function initializeRecordActions() {
        // Download report buttons
        const downloadBtns = document.querySelectorAll('.btn-link');
        downloadBtns.forEach(btn => {
            if (btn.textContent.includes('Download')) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const recordType = btn.textContent.replace('Download ', '');
                    showNotification(`Downloading ${recordType}...`, 'info');
                    
                    setTimeout(() => {
                        showNotification(`${recordType} downloaded successfully!`, 'success');
                    }, 2000);
                });
            }
            
            if (btn.textContent.includes('View Details')) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    showRecordDetailsModal();
                });
            }
        });

        // Add medical record button
        const addRecordBtn = document.querySelector('.add-record');
        if (addRecordBtn) {
            addRecordBtn.addEventListener('click', function() {
                showAddRecordModal();
            });
        }

        // Report injury button
        const reportInjuryBtn = document.querySelector('.btn:contains("Report Injury")');
        if (reportInjuryBtn) {
            reportInjuryBtn.addEventListener('click', function() {
                showReportInjuryModal();
            });
        }
    }

    // Show record details modal
    function showRecordDetailsModal() {
        const modalHTML = `
            <div class="modal-overlay">
                <div class="modal-content record-details-modal">
                    <div class="modal-header">
                        <h3><i class="fas fa-file-medical"></i> Medical Record Details</h3>
                        <button class="modal-close" onclick="closeModal()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="record-detail-section">
                            <h4>Annual Physical Examination</h4>
                            <div class="detail-grid">
                                <div class="detail-item">
                                    <strong>Date:</strong>
                                    <span>September 10, 2025</span>
                                </div>
                                <div class="detail-item">
                                    <strong>Doctor:</strong>
                                    <span>Dr. Sarah Johnson</span>
                                </div>
                                <div class="detail-item">
                                    <strong>Duration:</strong>
                                    <span>45 minutes</span>
                                </div>
                                <div class="detail-item">
                                    <strong>Type:</strong>
                                    <span>Routine Checkup</span>
                                </div>
                            </div>
                            <div class="detail-section">
                                <h5>Examination Results</h5>
                                <p>Patient presents in excellent health with no acute concerns. All vital signs within normal limits. Physical examination reveals no abnormalities.</p>
                            </div>
                            <div class="detail-section">
                                <h5>Recommendations</h5>
                                <ul>
                                    <li>Continue current fitness regimen</li>
                                    <li>Increase protein intake slightly for muscle recovery</li>
                                    <li>Consider adding flexibility training</li>
                                    <li>Schedule follow-up in 12 months</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-outline" onclick="closeModal()">Close</button>
                        <button class="btn btn-primary">
                            <i class="fas fa-download"></i>
                            Download Full Report
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHTML);
    }

    // Show add record modal
    function showAddRecordModal() {
        const modalHTML = `
            <div class="modal-overlay">
                <div class="modal-content add-record-modal">
                    <div class="modal-header">
                        <h3><i class="fas fa-plus"></i> Add Medical Record</h3>
                        <button class="modal-close" onclick="closeModal()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form class="record-form">
                            <div class="form-group">
                                <label for="recordTitle">Record Title</label>
                                <input type="text" id="recordTitle" class="form-input" placeholder="e.g., Annual Physical Examination">
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="recordDate">Date</label>
                                    <input type="date" id="recordDate" class="form-input">
                                </div>
                                <div class="form-group">
                                    <label for="recordType">Type</label>
                                    <select id="recordType" class="form-select">
                                        <option value="routine">Routine Checkup</option>
                                        <option value="consultation">Consultation</option>
                                        <option value="lab">Lab Results</option>
                                        <option value="emergency">Emergency</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="provider">Healthcare Provider</label>
                                <input type="text" id="provider" class="form-input" placeholder="e.g., Dr. Sarah Johnson">
                            </div>
                            <div class="form-group">
                                <label for="recordDetails">Details/Notes</label>
                                <textarea id="recordDetails" class="form-textarea" rows="4" placeholder="Enter examination results, recommendations, or other relevant details..."></textarea>
                            </div>
                            <div class="form-group">
                                <label for="recordFile">Attach File (Optional)</label>
                                <input type="file" id="recordFile" class="form-file" accept=".pdf,.doc,.docx,.jpg,.png">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-outline" onclick="closeModal()">Cancel</button>
                        <button class="btn btn-primary" onclick="saveRecord()">
                            <i class="fas fa-save"></i>
                            Save Record
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHTML);
    }

    // Initialize vital signs animation
    function initializeVitalSignsAnimation() {
        const vitalCards = document.querySelectorAll('.vital-item');
        
        vitalCards.forEach((card, index) => {
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 200);
        });

        // Animate vital values
        const vitalValues = document.querySelectorAll('.vital-value');
        vitalValues.forEach(value => {
            const text = value.textContent;
            const number = parseInt(text);
            
            if (!isNaN(number)) {
                animateCountUp(value, 0, number, 1500);
            }
        });
    }

    // Initialize fitness metrics
    function initializeFitnessMetrics() {
        const progressBars = document.querySelectorAll('.progress-fill');
        
        progressBars.forEach((bar, index) => {
            const width = bar.style.width;
            bar.style.width = '0%';
            
            setTimeout(() => {
                bar.style.width = width;
            }, index * 300 + 500);
        });
    }

    // Initialize progress rings for fitness assessments
    function initializeProgressRings() {
        const progressRings = document.querySelectorAll('.progress-ring');
        
        progressRings.forEach(ring => {
            const progressElement = ring.querySelector('.progress');
            const valueElement = ring.querySelector('.progress-value');
            
            if (progressElement && valueElement) {
                const value = parseInt(valueElement.textContent);
                const circumference = 2 * Math.PI * 35; // radius = 35
                
                progressElement.style.strokeDasharray = circumference;
                progressElement.style.strokeDashoffset = circumference;
                
                // Animate progress
                setTimeout(() => {
                    const offset = circumference - (value / 100) * circumference;
                    progressElement.style.strokeDashoffset = offset;
                }, 500);
            }
        });
    }

    // Initialize timeline animations
    function initializeTimelineAnimations() {
        const timelineItems = document.querySelectorAll('.timeline-item');
        
        // Intersection Observer for timeline animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateX(0)';
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        timelineItems.forEach((item, index) => {
            item.style.opacity = '0';
            item.style.transform = 'translateX(-30px)';
            item.style.transition = `all 0.6s ease ${index * 0.1}s`;
            observer.observe(item);
        });
    }

    // Appointment actions
    function initializeAppointmentActions() {
        // Reschedule buttons
        document.querySelectorAll('.btn-reschedule').forEach(btn => {
            btn.addEventListener('click', function() {
                const appointmentCard = this.closest('.appointment-card');
                const doctorName = appointmentCard.querySelector('.appointment-doctor').textContent;
                const appointmentDate = appointmentCard.querySelector('.appointment-date').textContent;
                
                showRescheduleModal(doctorName, appointmentDate);
            });
        });

        // Cancel buttons
        document.querySelectorAll('.btn-cancel').forEach(btn => {
            btn.addEventListener('click', function() {
                const appointmentCard = this.closest('.appointment-card');
                const doctorName = appointmentCard.querySelector('.appointment-doctor').textContent;
                
                showCancelConfirmation(doctorName, appointmentCard);
            });
        });

        // View record buttons
        document.querySelectorAll('.btn-view').forEach(btn => {
            btn.addEventListener('click', function() {
                const recordCard = this.closest('.timeline-item');
                const recordTitle = recordCard.querySelector('.record-title').textContent;
                const recordDetails = recordCard.querySelector('.record-details').textContent;
                const recordDate = recordCard.querySelector('.record-date').textContent;
                
                showRecordModal(recordTitle, recordDetails, recordDate);
            });
        });

        // Download buttons
        document.querySelectorAll('.btn-download').forEach(btn => {
            btn.addEventListener('click', function() {
                const recordCard = this.closest('.timeline-item');
                const recordTitle = recordCard.querySelector('.record-title').textContent;
                
                downloadRecord(recordTitle);
            });
        });
    }

    // Health alerts and monitoring
    function initializeHealthAlerts() {
        checkVitalSigns();
        checkAppointmentReminders();
        checkMedicationReminders();
    }

    // Check vital signs for alerts
    function checkVitalSigns() {
        const vitalCards = document.querySelectorAll('.vital-card');
        
        vitalCards.forEach(card => {
            const status = card.querySelector('.vital-status');
            const vitalType = card.querySelector('.vital-label').textContent.toLowerCase();
            
            if (status && status.classList.contains('warning')) {
                showHealthAlert(`Your ${vitalType} readings need attention`, 'warning');
            } else if (status && status.classList.contains('critical')) {
                showHealthAlert(`Critical ${vitalType} levels detected! Please consult your doctor immediately.`, 'critical');
            }
        });
    }

    // Check appointment reminders
    function checkAppointmentReminders() {
        const today = new Date();
        const appointments = document.querySelectorAll('.appointment-card');
        
        appointments.forEach(appointment => {
            const dateText = appointment.querySelector('.appointment-date').textContent;
            const appointmentDate = new Date(dateText);
            const timeDiff = appointmentDate.getTime() - today.getTime();
            const daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24));
            
            if (daysDiff === 1) {
                const doctorName = appointment.querySelector('.appointment-doctor').textContent;
                showNotification(`Reminder: You have an appointment with ${doctorName} tomorrow`, 'info');
            } else if (daysDiff === 0) {
                const doctorName = appointment.querySelector('.appointment-doctor').textContent;
                showNotification(`Today: Appointment with ${doctorName}`, 'warning');
            }
        });
    }

    // Check medication reminders (simulated)
    function checkMedicationReminders() {
        const currentHour = new Date().getHours();
        
        // Simulated medication times
        const medicationTimes = [8, 14, 20]; // 8 AM, 2 PM, 8 PM
        
        if (medicationTimes.includes(currentHour)) {
            showNotification('Time for your medication!', 'info');
        }
    }

    // Update overall health score
    function updateHealthScore() {
        const vitalCards = document.querySelectorAll('.vital-card');
        let totalScore = 0;
        let normalCount = 0;
        
        vitalCards.forEach(card => {
            const status = card.querySelector('.vital-status');
            if (status && status.classList.contains('normal')) {
                normalCount++;
            }
        });
        
        totalScore = Math.round((normalCount / vitalCards.length) * 100);
        
        const healthScoreElement = document.querySelector('.health-score-number');
        if (healthScoreElement) {
            // Animate score update
            animateCountUp(healthScoreElement, 0, totalScore, 1500);
        }
        
        // Update health score color based on value
        const healthScoreContainer = document.querySelector('.health-score');
        if (healthScoreContainer) {
            if (totalScore >= 80) {
                healthScoreContainer.style.background = 'linear-gradient(135deg, #22c55e, #16a34a)';
            } else if (totalScore >= 60) {
                healthScoreContainer.style.background = 'linear-gradient(135deg, #f59e0b, #d97706)';
            } else {
                healthScoreContainer.style.background = 'linear-gradient(135deg, #ef4444, #dc2626)';
            }
        }
    }

    // Show reschedule modal
    function showRescheduleModal(doctorName, currentDate) {
        const modal = document.createElement('div');
        modal.className = 'modal-overlay';
        modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Reschedule Appointment</h3>
                    <button class="modal-close">&times;</button>
                </div>
                <div class="modal-body">
                    <p><strong>Doctor:</strong> ${doctorName}</p>
                    <p><strong>Current Date:</strong> ${currentDate}</p>
                    <div class="form-group">
                        <label for="new-date">New Date:</label>
                        <input type="date" id="new-date" class="form-control" min="${new Date().toISOString().split('T')[0]}">
                    </div>
                    <div class="form-group">
                        <label for="new-time">New Time:</label>
                        <select id="new-time" class="form-control">
                            <option value="09:00">9:00 AM</option>
                            <option value="10:00">10:00 AM</option>
                            <option value="11:00">11:00 AM</option>
                            <option value="14:00">2:00 PM</option>
                            <option value="15:00">3:00 PM</option>
                            <option value="16:00">4:00 PM</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="reason">Reason for rescheduling:</label>
                        <textarea id="reason" class="form-control" rows="3" placeholder="Optional reason..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" onclick="confirmReschedule()">Confirm Reschedule</button>
                    <button class="btn btn-outline" onclick="closeModal()">Cancel</button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        modal.querySelector('.modal-close').addEventListener('click', () => {
            document.body.removeChild(modal);
        });
        
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                document.body.removeChild(modal);
            }
        });
    }

    // Show cancel confirmation
    function showCancelConfirmation(doctorName, appointmentCard) {
        if (confirm(`Are you sure you want to cancel your appointment with ${doctorName}?`)) {
            // Animate card removal
            appointmentCard.style.transition = 'all 0.3s ease';
            appointmentCard.style.opacity = '0';
            appointmentCard.style.transform = 'translateY(-20px)';
            
            setTimeout(() => {
                appointmentCard.remove();
                showNotification('Appointment cancelled successfully', 'info');
            }, 300);
        }
    }

    // Show medical record modal
    function showRecordModal(title, details, date) {
        const modal = document.createElement('div');
        modal.className = 'modal-overlay';
        modal.innerHTML = `
            <div class="modal-content modal-large">
                <div class="modal-header">
                    <h3>${title}</h3>
                    <button class="modal-close">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="record-details-full">
                        <p><strong>Date:</strong> ${date}</p>
                        <div class="record-content">
                            <h4>Details:</h4>
                            <p>${details}</p>
                            
                            <h4>Recommendations:</h4>
                            <ul>
                                <li>Follow prescribed medication schedule</li>
                                <li>Regular exercise and balanced diet</li>
                                <li>Schedule follow-up appointment in 3 months</li>
                                <li>Monitor vital signs weekly</li>
                            </ul>
                            
                            <h4>Test Results:</h4>
                            <div class="test-results">
                                <div class="test-item">
                                    <span class="test-name">Blood Pressure:</span>
                                    <span class="test-value normal">120/80 mmHg</span>
                                </div>
                                <div class="test-item">
                                    <span class="test-name">Heart Rate:</span>
                                    <span class="test-value normal">72 bpm</span>
                                </div>
                                <div class="test-item">
                                    <span class="test-name">BMI:</span>
                                    <span class="test-value normal">23.5</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" onclick="downloadRecord('${title}')">Download PDF</button>
                    <button class="btn btn-outline" onclick="closeModal()">Close</button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        modal.querySelector('.modal-close').addEventListener('click', () => {
            document.body.removeChild(modal);
        });
    }

    // Download medical record
    function downloadRecord(recordTitle) {
        // Simulate download
        showNotification(`Downloading ${recordTitle}...`, 'info');
        
        setTimeout(() => {
            showNotification(`${recordTitle} downloaded successfully!`, 'success');
        }, 2000);
    }

    // Show health alert
    function showHealthAlert(message, type) {
        const alert = document.createElement('div');
        alert.className = `health-alert health-alert-${type}`;
        alert.innerHTML = `
            <div class="alert-content">
                <i class="fas fa-${type === 'critical' ? 'exclamation-triangle' : 'info-circle'}"></i>
                <span>${message}</span>
                <button class="alert-close">&times;</button>
            </div>
        `;
        
        document.body.appendChild(alert);
        
        // Show alert
        setTimeout(() => {
            alert.classList.add('show');
        }, 100);
        
        // Close button functionality
        alert.querySelector('.alert-close').addEventListener('click', () => {
            alert.classList.remove('show');
            setTimeout(() => {
                if (document.body.contains(alert)) {
                    document.body.removeChild(alert);
                }
            }, 300);
        });
        
        // Auto-close after 10 seconds for warnings, keep critical alerts
        if (type !== 'critical') {
            setTimeout(() => {
                if (document.body.contains(alert)) {
                    alert.classList.remove('show');
                    setTimeout(() => {
                        if (document.body.contains(alert)) {
                            document.body.removeChild(alert);
                        }
                    }, 300);
                }
            }, 10000);
        }
    }

    // Show notification
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'}"></i>
            <span>${message}</span>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);
        
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }

    // Animate count up
    function animateCountUp(element, start, end, duration) {
        const startTime = performance.now();
        
        function updateCount(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            const current = Math.floor(progress * (end - start) + start);
            element.textContent = current;
            
            if (progress < 1) {
                requestAnimationFrame(updateCount);
            }
        }
        
        requestAnimationFrame(updateCount);
    }

    // Global functions for modal actions
    window.confirmReschedule = function() {
        const modal = document.querySelector('.modal-overlay');
        const newDate = document.getElementById('new-date').value;
        const newTime = document.getElementById('new-time').value;
        
        if (!newDate || !newTime) {
            showNotification('Please select both date and time', 'warning');
            return;
        }
        
        showNotification('Appointment rescheduled successfully!', 'success');
        document.body.removeChild(modal);
    };

    window.closeModal = function() {
        const modal = document.querySelector('.modal-overlay');
        if (modal) {
            document.body.removeChild(modal);
        }
    };

    // Filter functions
    window.clearFilters = function() {
        document.getElementById('recordType').value = '';
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';
        document.getElementById('provider').value = '';
        showNotification('Filters cleared', 'info');
    };

    window.applyFilters = function() {
        const recordType = document.getElementById('recordType').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const provider = document.getElementById('provider').value;
        
        // Apply filters to records (in a real app, this would filter the displayed records)
        showNotification('Filters applied successfully!', 'success');
        closeModal();
    };

    // Save record function
    window.saveRecord = function() {
        const title = document.getElementById('recordTitle').value;
        const date = document.getElementById('recordDate').value;
        const type = document.getElementById('recordType').value;
        const provider = document.getElementById('provider').value;
        const details = document.getElementById('recordDetails').value;
        
        if (!title || !date || !provider) {
            showNotification('Please fill in all required fields', 'warning');
            return;
        }
        
        showNotification('Medical record saved successfully!', 'success');
        closeModal();
        
        // In a real app, this would send data to the server
        console.log('Saving record:', { title, date, type, provider, details });
    };

    // Reschedule appointment function
    window.showRescheduleModal = function() {
        const modalHTML = `
            <div class="modal-overlay">
                <div class="modal-content reschedule-modal">
                    <div class="modal-header">
                        <h3><i class="fas fa-calendar-alt"></i> Reschedule Appointment</h3>
                        <button class="modal-close" onclick="closeModal()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form class="reschedule-form">
                            <div class="form-group">
                                <label for="new-date">New Date</label>
                                <input type="date" id="new-date" class="form-input" min="${new Date().toISOString().split('T')[0]}">
                            </div>
                            <div class="form-group">
                                <label for="new-time">New Time</label>
                                <select id="new-time" class="form-select">
                                    <option value="">Select a time</option>
                                    <option value="09:00">9:00 AM</option>
                                    <option value="09:30">9:30 AM</option>
                                    <option value="10:00">10:00 AM</option>
                                    <option value="10:30">10:30 AM</option>
                                    <option value="11:00">11:00 AM</option>
                                    <option value="11:30">11:30 AM</option>
                                    <option value="14:00">2:00 PM</option>
                                    <option value="14:30">2:30 PM</option>
                                    <option value="15:00">3:00 PM</option>
                                    <option value="15:30">3:30 PM</option>
                                    <option value="16:00">4:00 PM</option>
                                    <option value="16:30">4:30 PM</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="reschedule-reason">Reason for Rescheduling (Optional)</label>
                                <textarea id="reschedule-reason" class="form-textarea" rows="3" placeholder="Enter reason for rescheduling..."></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-outline" onclick="closeModal()">Cancel</button>
                        <button class="btn btn-primary" onclick="confirmReschedule()">
                            <i class="fas fa-check"></i>
                            Confirm Reschedule
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHTML);
    };

    // Show notification function
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        
        const icon = {
            'success': 'fas fa-check-circle',
            'error': 'fas fa-exclamation-circle',
            'warning': 'fas fa-exclamation-triangle',
            'info': 'fas fa-info-circle'
        }[type];
        
        notification.innerHTML = `
            <i class="${icon}"></i>
            <span>${message}</span>
            <button class="notification-close" onclick="this.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    }

    // Export health data functionality
    const exportBtn = document.querySelector('.btn-export');
    if (exportBtn) {
        exportBtn.addEventListener('click', function() {
            showNotification('Exporting health data...', 'info');
            
            setTimeout(() => {
                showNotification('Health data exported successfully!', 'success');
            }, 2000);
        });
    }

    // Health tips rotation
    function showHealthTips() {
        const tips = [
            "Stay hydrated - drink at least 8 glasses of water daily",
            "Get 7-9 hours of quality sleep each night",
            "Exercise regularly - aim for 30 minutes of activity daily",
            "Eat a balanced diet rich in fruits and vegetables",
            "Practice stress management techniques like meditation",
            "Schedule regular health check-ups",
            "Limit processed foods and added sugars",
            "Take breaks from screen time to rest your eyes"
        ];
        
        let currentTip = 0;
        const tipElement = document.querySelector('.health-tip');
        
        if (tipElement) {
            setInterval(() => {
                tipElement.style.opacity = '0';
                setTimeout(() => {
                    tipElement.textContent = tips[currentTip];
                    tipElement.style.opacity = '1';
                    currentTip = (currentTip + 1) % tips.length;
                }, 300);
            }, 10000); // Change tip every 10 seconds
        }
    }

    // Initialize health tips
    showHealthTips();
});