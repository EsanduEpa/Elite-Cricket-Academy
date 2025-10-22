// Reports JavaScript

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    initReportTypeSelector();
    initCharts();
});

// Report Type Selector
function initReportTypeSelector() {
    const reportType = document.getElementById('reportType');
    const optionsPanels = document.querySelectorAll('.report-options');
    
    if (reportType) {
        reportType.addEventListener('change', function() {
            // Hide all option panels
            optionsPanels.forEach(panel => {
                panel.classList.remove('active');
            });
            
            // Show selected panel
            const selectedPanel = document.getElementById(this.value + 'Options');
            if (selectedPanel) {
                selectedPanel.classList.add('active');
            }
        });
    }
}

// Update Report Options (called from HTML onchange)
function updateReportOptions() {
    const reportType = document.getElementById('reportType');
    const optionsPanels = document.querySelectorAll('.report-options');
    
    // Hide all option panels
    optionsPanels.forEach(panel => {
        panel.style.display = 'none';
        panel.classList.remove('active');
    });
    
    // Show selected panel
    if (reportType && reportType.value) {
        const selectedPanel = document.getElementById(reportType.value + 'Options');
        if (selectedPanel) {
            selectedPanel.style.display = 'block';
            selectedPanel.classList.add('active');
        }
    }
}

// Generate Report
function generateReport() {
    const reportType = document.getElementById('reportType').value;
    const timePeriod = document.getElementById('timePeriod').value;
    const format = document.querySelector('input[name="format"]:checked')?.value;
    
    if (!reportType) {
        alert('Please select a report type');
        return;
    }
    
    if (!format) {
        alert('Please select an export format');
        return;
    }
    
    // Get selected options based on report type
    const options = getSelectedOptions(reportType);
    
    // Show loading modal
    showLoadingModal();
    
    // Simulate report generation
    let progress = 0;
    const progressInterval = setInterval(() => {
        progress += 10;
        updateProgress(progress);
        
        if (progress >= 100) {
            clearInterval(progressInterval);
            setTimeout(() => {
                hideLoadingModal();
                showSuccessMessage(reportType, format);
            }, 500);
        }
    }, 300);
    
    console.log('Generating report:', {
        type: reportType,
        period: timePeriod,
        format: format,
        options: options
    });
}

// Get selected options for a report type
function getSelectedOptions(reportType) {
    const panel = document.getElementById(reportType + 'Options');
    if (!panel) return [];
    
    const checkboxes = panel.querySelectorAll('input[type="checkbox"]:checked');
    return Array.from(checkboxes).map(cb => cb.value);
}

// Generate Quick Template
function generateTemplate(templateName) {
    console.log('Generating template:', templateName);
    
    // Show loading modal
    showLoadingModal();
    
    // Simulate template generation
    let progress = 0;
    const progressInterval = setInterval(() => {
        progress += 15;
        updateProgress(progress);
        
        if (progress >= 100) {
            clearInterval(progressInterval);
            setTimeout(() => {
                hideLoadingModal();
                showSuccessMessage(templateName, 'PDF');
            }, 500);
        }
    }, 200);
}

// Generate Template Report (alternative function name used in HTML)
function generateTemplateReport(templateName) {
    console.log('Generating template report:', templateName);
    
    // Show loading modal
    showLoadingModal();
    
    // Simulate template generation
    let progress = 0;
    const progressInterval = setInterval(() => {
        progress += 15;
        updateProgress(progress);
        
        if (progress >= 100) {
            clearInterval(progressInterval);
            setTimeout(() => {
                hideLoadingModal();
                
                // Format template name for display
                const displayName = templateName.split('-').map(word => 
                    word.charAt(0).toUpperCase() + word.slice(1)
                ).join(' ');
                
                showSuccessMessage(displayName + ' Report', 'PDF');
            }, 500);
        }
    }, 200);
}

// Loading Modal Functions
function showLoadingModal() {
    const modal = document.getElementById('loadingModal');
    if (modal) {
        modal.style.display = 'flex';
        modal.classList.add('active');
    }
}

function hideLoadingModal() {
    const modal = document.getElementById('loadingModal');
    if (modal) {
        modal.style.display = 'none';
        modal.classList.remove('active');
        // Reset progress
        setTimeout(() => {
            updateProgress(0);
        }, 300);
    }
}

function updateProgress(percentage) {
    const progressFill = document.querySelector('.progress-fill');
    const progressText = document.querySelector('.progress-text');
    
    if (progressFill) {
        progressFill.style.width = percentage + '%';
    }
    
    if (progressText) {
        progressText.textContent = percentage + '%';
    }
}

// Show Success Message
function showSuccessMessage(reportName, format) {
    const message = `
        <div style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background:white;padding:40px;border-radius:16px;box-shadow:0 8px 30px rgba(0,0,0,0.2);z-index:10000;text-align:center;max-width:400px;">
            <div style="width:80px;height:80px;background:rgba(16,185,129,0.1);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;">
                <i class="fas fa-check-circle" style="font-size:40px;color:#10b981;"></i>
            </div>
            <h3 style="margin:0 0 15px 0;color:#333;font-size:22px;">Report Generated!</h3>
            <p style="margin:0 0 25px 0;color:#666;line-height:1.6;">Your ${reportName} report has been generated successfully in ${format} format.</p>
            <button onclick="closeSuccessMessage()" style="padding:14px 32px;background:linear-gradient(135deg,#667eea,#764ba2);color:white;border:none;border-radius:8px;font-weight:600;cursor:pointer;">Close</button>
        </div>
        <div onclick="closeSuccessMessage()" style="position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:9999;"></div>
    `;
    
    const successDiv = document.createElement('div');
    successDiv.id = 'successMessage';
    successDiv.innerHTML = message;
    document.body.appendChild(successDiv);
}

function closeSuccessMessage() {
    const successMsg = document.getElementById('successMessage');
    if (successMsg) {
        document.body.removeChild(successMsg);
    }
}

// View Report
function viewReport(reportId) {
    console.log('Viewing report:', reportId);
    alert('Opening report viewer...\n\nIn production, this would open the report in a new window or modal.');
}

// Download Report
function downloadReport(reportId, format) {
    console.log('Downloading report:', reportId, format);
    
    // Show download notification
    const notification = document.createElement('div');
    notification.style.cssText = 'position:fixed;top:20px;right:20px;background:white;padding:15px 25px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.15);z-index:9999;display:flex;align-items:center;gap:12px;';
    notification.innerHTML = `
        <i class="fas fa-download" style="color:#667eea;"></i>
        <span style="color:#333;font-weight:600;">Downloading report...</span>
    `;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.querySelector('i').className = 'fas fa-check-circle';
        notification.querySelector('i').style.color = '#10b981';
        notification.querySelector('span').textContent = 'Download complete!';
        
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 2000);
    }, 1500);
}

// Share Report
function shareReport(reportId) {
    console.log('Sharing report:', reportId);
    
    const shareData = {
        title: 'Academy Report',
        text: 'Check out this report from Elite Cricket Academy',
        url: window.location.href
    };
    
    if (navigator.share) {
        navigator.share(shareData)
            .then(() => console.log('Shared successfully'))
            .catch(err => console.log('Error sharing:', err));
    } else {
        // Show share modal with options
        alert('Share Report\n\nIn production, this would show options to:\n- Copy link\n- Email\n- Share via social media\n- Generate shareable link');
    }
}

// Initialize Charts
function initCharts() {
    initReportsByTypeChart();
    initGenerationTrendChart();
}

// Reports by Type Pie Chart
function initReportsByTypeChart() {
    const ctx = document.getElementById('reportsByTypeChart');
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Player Reports', 'Revenue Reports', 'Event Reports', 'Attendance Reports', 'Other'],
            datasets: [{
                data: [35, 25, 20, 15, 5],
                backgroundColor: [
                    '#667eea',
                    '#764ba2',
                    '#f093fb',
                    '#4facfe',
                    '#fa709a'
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
                        padding: 15,
                        usePointStyle: true,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed + '%';
                        }
                    }
                }
            }
        }
    });
}

// Generation Trend Line Chart
function initGenerationTrendChart() {
    const ctx = document.getElementById('generationTrendChart');
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
            datasets: [{
                label: 'Reports Generated',
                data: [12, 15, 18, 14, 22, 25, 20, 28, 24, 30],
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointBackgroundColor: '#667eea',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointHoverRadius: 7
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
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return 'Reports: ' + context.parsed.y;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
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

// Custom Date Range Handler
document.addEventListener('DOMContentLoaded', function() {
    const reportPeriod = document.getElementById('reportPeriod');
    const customDateGroup = document.getElementById('customDateGroup');
    const customDateGroup2 = document.getElementById('customDateGroup2');
    
    if (reportPeriod) {
        reportPeriod.addEventListener('change', function() {
            if (this.value === 'custom') {
                if (customDateGroup) customDateGroup.style.display = 'block';
                if (customDateGroup2) customDateGroup2.style.display = 'block';
            } else {
                if (customDateGroup) customDateGroup.style.display = 'none';
                if (customDateGroup2) customDateGroup2.style.display = 'none';
            }
        });
    }
    
    // Legacy support
    const timePeriod = document.getElementById('timePeriod');
    const customDateRange = document.getElementById('customDateRange');
    
    if (timePeriod && customDateRange) {
        timePeriod.addEventListener('change', function() {
            if (this.value === 'custom') {
                customDateRange.style.display = 'grid';
            } else {
                customDateRange.style.display = 'none';
            }
        });
    }
});

// Schedule Report Generation
function scheduleReport() {
    alert('Schedule Report\n\nThis feature allows you to:\n- Set recurring report generation\n- Choose frequency (daily, weekly, monthly)\n- Select recipients for automatic email delivery\n- Configure custom scheduling options\n\nComing soon!');
}

// Export Multiple Reports
function exportMultipleReports() {
    const checkboxes = document.querySelectorAll('.report-checkbox:checked');
    
    if (checkboxes.length === 0) {
        alert('Please select at least one report to export');
        return;
    }
    
    console.log('Exporting multiple reports:', checkboxes.length);
    showLoadingModal();
    
    let progress = 0;
    const progressInterval = setInterval(() => {
        progress += 12;
        updateProgress(Math.min(progress, 100));
        
        if (progress >= 100) {
            clearInterval(progressInterval);
            setTimeout(() => {
                hideLoadingModal();
                alert(`Successfully exported ${checkboxes.length} reports!`);
            }, 500);
        }
    }, 250);
}

// Delete Report
function deleteReport(reportId) {
    if (confirm('Are you sure you want to delete this report? This action cannot be undone.')) {
        console.log('Deleting report:', reportId);
        
        // Show notification
        const notification = document.createElement('div');
        notification.style.cssText = 'position:fixed;top:20px;right:20px;background:#ef4444;color:white;padding:15px 25px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.15);z-index:9999;';
        notification.innerHTML = '<i class="fas fa-trash" style="margin-right:10px;"></i>Report deleted successfully';
        document.body.appendChild(notification);
        
        setTimeout(() => {
            document.body.removeChild(notification);
            // In production, remove the table row
        }, 2000);
    }
}

// Filter Reports
function filterReports(filterType) {
    console.log('Filtering reports by:', filterType);
    // In production, filter the reports table
}

// Search Reports
function searchReports(query) {
    console.log('Searching reports:', query);
    // In production, filter table rows based on search query
}

// Keyboard shortcuts
document.addEventListener('keydown', function(event) {
    // Ctrl/Cmd + G to generate report
    if ((event.ctrlKey || event.metaKey) && event.key === 'g') {
        event.preventDefault();
        document.getElementById('reportType')?.focus();
    }
    
    // Escape to close modals
    if (event.key === 'Escape') {
        hideLoadingModal();
        closeSuccessMessage();
    }
});

// Auto-save form state
let formState = {};

function saveFormState() {
    formState = {
        reportType: document.getElementById('reportType')?.value,
        timePeriod: document.getElementById('timePeriod')?.value,
        format: document.querySelector('input[name="format"]:checked')?.value
    };
    localStorage.setItem('reportFormState', JSON.stringify(formState));
}

function loadFormState() {
    const saved = localStorage.getItem('reportFormState');
    if (saved) {
        formState = JSON.parse(saved);
        // Restore form values
        if (formState.reportType) {
            document.getElementById('reportType').value = formState.reportType;
        }
        if (formState.timePeriod) {
            document.getElementById('timePeriod').value = formState.timePeriod;
        }
        if (formState.format) {
            const formatRadio = document.querySelector(`input[name="format"][value="${formState.format}"]`);
            if (formatRadio) formatRadio.checked = true;
        }
    }
}

// Save form state on change
document.addEventListener('DOMContentLoaded', function() {
    loadFormState();
    
    const formInputs = document.querySelectorAll('#reportType, #timePeriod, input[name="format"]');
    formInputs.forEach(input => {
        input.addEventListener('change', saveFormState);
    });
});
