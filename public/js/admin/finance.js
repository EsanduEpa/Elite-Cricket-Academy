// Finance Management JavaScript

let paymentCurrentPage = 1;
const paymentPageSize = 10;

// Initialize filters and search
function initializeFilters() {
    const searchInput = document.getElementById('paymentSearch');
    const typeFilter = document.getElementById('paymentFilter');
    const statusFilter = document.getElementById('statusFilter');
    
    // Search functionality
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            paymentCurrentPage = 1;
            filterPayments();
        });
    }
    
    // Type filter
    if (typeFilter) {
        typeFilter.addEventListener('change', function() {
            paymentCurrentPage = 1;
            filterPayments();
        });
    }
    
    // Status filter
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            paymentCurrentPage = 1;
            filterPayments();
        });
    }
    
    // Period selector
    const periodSelector = document.getElementById('revenuePeriod');
    if (periodSelector) {
        periodSelector.addEventListener('change', function() {
            showNotification('Revenue period selector is ready; backend period filtering can be connected next.', 'info');
        });
    }

    filterPayments();
}

// Filter payments table
function filterPayments() {
    const searchTerm = (document.getElementById('paymentSearch')?.value || '').toLowerCase();
    const typeFilter = document.getElementById('paymentFilter')?.value || 'all';
    const statusFilter = document.getElementById('statusFilter')?.value || 'all';
    
    const rows = Array.from(document.querySelectorAll('.payment-row'));
    const matchedRows = rows.filter(row => {
        const customerName = row.querySelector('.customer-name')?.textContent.toLowerCase() || '';
        const paymentId = row.querySelector('.payment-id')?.textContent.toLowerCase() || '';
        const paymentType = row.getAttribute('data-type') || '';
        const paymentStatus = row.getAttribute('data-status') || '';
        
        const matchesSearch = customerName.includes(searchTerm) || paymentId.includes(searchTerm);
        const matchesType = typeFilter === 'all' || paymentType === typeFilter;
        const matchesStatus = statusFilter === 'all' || paymentStatus === statusFilter;

        return matchesSearch && matchesType && matchesStatus;
    });

    const totalPages = Math.max(1, Math.ceil(matchedRows.length / paymentPageSize));
    paymentCurrentPage = Math.min(Math.max(paymentCurrentPage, 1), totalPages);
    const start = (paymentCurrentPage - 1) * paymentPageSize;
    const end = start + paymentPageSize;
    const visibleRows = matchedRows.slice(start, end);

    rows.forEach(row => {
        row.style.display = visibleRows.includes(row) ? '' : 'none';
    });
    
    const paginationInfo = document.querySelector('.pagination-info');
    if (paginationInfo) {
        const from = matchedRows.length ? start + 1 : 0;
        const to = Math.min(end, matchedRows.length);
        paginationInfo.textContent = `Showing ${from}-${to} of ${matchedRows.length} transactions`;
    }

    renderPaymentPagination(totalPages);
}

function renderPaymentPagination(totalPages) {
    const pagination = document.querySelector('.pagination');
    if (!pagination) return;

    pagination.innerHTML = '';
    pagination.appendChild(createPaymentPageButton('prev', '<i class="fas fa-chevron-left"></i>', paymentCurrentPage === 1, () => {
        paymentCurrentPage--;
        filterPayments();
    }));

    for (let page = 1; page <= totalPages; page++) {
        if (page > 1 && page < totalPages && Math.abs(page - paymentCurrentPage) > 1) {
            const side = page < paymentCurrentPage ? 'left' : 'right';
            if (!pagination.querySelector(`[data-ellipsis="${side}"]`)) {
                const ellipsis = document.createElement('span');
                ellipsis.className = 'page-ellipsis';
                ellipsis.dataset.ellipsis = side;
                ellipsis.textContent = '...';
                pagination.appendChild(ellipsis);
            }
            continue;
        }

        pagination.appendChild(createPaymentPageButton(page, String(page), false, () => {
            paymentCurrentPage = page;
            filterPayments();
        }, page === paymentCurrentPage));
    }

    pagination.appendChild(createPaymentPageButton('next', '<i class="fas fa-chevron-right"></i>', paymentCurrentPage === totalPages, () => {
        paymentCurrentPage++;
        filterPayments();
    }));
}

function createPaymentPageButton(value, html, disabled, onClick, active = false) {
    const button = document.createElement('button');
    button.type = 'button';
    button.className = active ? 'page-btn active' : 'page-btn';
    button.dataset.page = value;
    button.innerHTML = html;
    button.disabled = disabled;
    button.addEventListener('click', onClick);
    return button;
}

// Payment action functions
function viewPayment(paymentId) {
    const payment = getPaymentDetailsFromRow(paymentId);
    if (!payment) {
        showNotification(`Payment ${paymentId} was not found in the current table.`, 'error');
        return;
    }

    const modal = createPaymentModal(payment);
    document.body.appendChild(modal);
    
    // Add close functionality
    modal.addEventListener('click', function(e) {
        if (e.target === modal || e.target.classList.contains('modal-close')) {
            document.body.removeChild(modal);
        }
    });
}

function approvePayment(paymentId) {
    if (confirm(`Are you sure you want to approve payment ${paymentId}?`)) {
        // Find the row and update status
        const row = document.querySelector(`[data-payment-id="${paymentId}"]`);
        if (row) {
            const statusBadge = row.querySelector('.status-badge');
            statusBadge.className = 'status-badge completed';
            statusBadge.innerHTML = '<i class="fas fa-check-circle"></i> Completed';
            
            // Remove approve button
            const approveBtn = row.querySelector('.action-btn.approve');
            if (approveBtn) {
                approveBtn.remove();
            }
        }
        
        showNotification(`Payment ${paymentId} has been approved successfully`, 'success');
    }
}

function downloadReceipt(paymentId) {
    showNotification(`Downloading receipt for payment ${paymentId}`, 'info');
    
    // Simulate receipt download
    setTimeout(() => {
        showNotification(`Receipt for payment ${paymentId} downloaded successfully`, 'success');
    }, 1000);
}

function getPaymentDetailsFromRow(paymentId) {
    const escapedPaymentId = window.CSS?.escape ? CSS.escape(paymentId) : String(paymentId).replace(/"/g, '\\"');
    const row = document.querySelector(`.payment-row[data-payment-id="${escapedPaymentId}"]`);
    if (!row) return null;

    return {
        id: row.querySelector('.payment-id')?.textContent.trim() || paymentId,
        type: row.querySelector('.payment-type')?.textContent.trim() || '-',
        customer: row.querySelector('.customer-name')?.textContent.trim() || '-',
        amount: row.querySelector('.payment-amount')?.textContent.trim() || '-',
        date: row.querySelector('.payment-date')?.textContent.trim() || '-',
        method: row.querySelector('.payment-method')?.textContent.trim() || '-',
        status: row.querySelector('.payment-status')?.textContent.trim() || '-'
    };
}

// Create payment details modal
function createPaymentModal(payment) {
    const modal = document.createElement('div');
    modal.className = 'payment-modal';
    modal.innerHTML = `
        <div class="modal-overlay"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h3>Payment Details - ${escapeHtml(payment.id)}</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="payment-details-grid">
                    <div class="detail-group">
                        <label>Payment ID:</label>
                        <span>${escapeHtml(payment.id)}</span>
                    </div>
                    <div class="detail-group">
                        <label>Type:</label>
                        <span>${escapeHtml(payment.type)}</span>
                    </div>
                    <div class="detail-group">
                        <label>Customer:</label>
                        <span>${escapeHtml(payment.customer)}</span>
                    </div>
                    <div class="detail-group">
                        <label>Amount:</label>
                        <span>${escapeHtml(payment.amount)}</span>
                    </div>
                    <div class="detail-group">
                        <label>Date:</label>
                        <span>${escapeHtml(payment.date)}</span>
                    </div>
                    <div class="detail-group">
                        <label>Payment Method:</label>
                        <span>${escapeHtml(payment.method)}</span>
                    </div>
                    <div class="detail-group">
                        <label>Status:</label>
                        <span class="status-completed">${escapeHtml(payment.status)}</span>
                    </div>
                    <div class="detail-group">
                        <label>Transaction ID:</label>
                        <span>${escapeHtml(payment.id)}</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="downloadReceipt('${escapeAttribute(payment.id)}')">
                    <i class="fas fa-download"></i> Download Receipt
                </button>
                <button class="btn btn-secondary modal-close">Close</button>
            </div>
        </div>
    `;
    
    // Add modal styles
    const style = document.createElement('style');
    style.textContent = `
        .payment-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }
        
        .modal-content {
            background: white;
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            position: relative;
            z-index: 1;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        .modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid #E0E0E0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-header h3 {
            margin: 0;
            color: #333;
        }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #666;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-body {
            padding: 1.5rem;
        }
        
        .payment-details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .detail-group {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        
        .detail-group label {
            font-weight: 600;
            color: #666;
            font-size: 0.875rem;
        }
        
        .detail-group span {
            color: #333;
            font-size: 1rem;
        }
        
        .status-completed {
            color: #2E7D32;
            font-weight: 600;
        }
        
        .modal-footer {
            padding: 1.5rem;
            border-top: 1px solid #E0E0E0;
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }
        
        .modal-footer .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .modal-footer .btn-primary {
            background: #2E7D32;
            color: white;
        }
        
        .modal-footer .btn-primary:hover {
            background: #1B5E20;
        }
        
        .modal-footer .btn-secondary {
            background: #F5F5F5;
            color: #333;
        }
        
        .modal-footer .btn-secondary:hover {
            background: #E0E0E0;
        }
    `;
    
    document.head.appendChild(style);
    
    return modal;
}

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function escapeAttribute(value) {
    return String(value ?? '').replace(/'/g, "\\'");
}

// Report generation functions
document.addEventListener('DOMContentLoaded', function() {
    const generateReportBtn = document.getElementById('generateReportBtn');
    const exportDataBtn = document.getElementById('exportDataBtn');
    
    if (generateReportBtn) {
        generateReportBtn.addEventListener('click', function() {
            showNotification('Financial report generation can be connected to a backend export endpoint next.', 'info');
        });
    }
    
    if (exportDataBtn) {
        exportDataBtn.addEventListener('click', function() {
            showNotification('Finance CSV export can be connected to real table data next.', 'info');
        });
    }
});

// Notification system
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'times-circle' : 'info-circle'}"></i>
        <span>${message}</span>
        <button class="notification-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    // Add notification styles
    const style = document.createElement('style');
    style.textContent = `
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            border-radius: 8px;
            padding: 1rem 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 10001;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            min-width: 300px;
            max-width: 500px;
            animation: slideInRight 0.3s ease;
            border-left: 4px solid #2E7D32;
        }
        
        .notification-success {
            border-left-color: #2E7D32;
            color: #2E7D32;
        }
        
        .notification-error {
            border-left-color: #D32F2F;
            color: #D32F2F;
        }
        
        .notification-info {
            border-left-color: #1976D2;
            color: #1976D2;
        }
        
        .notification-close {
            background: none;
            border: none;
            cursor: pointer;
            color: #666;
            margin-left: auto;
            padding: 0.25rem;
            border-radius: 4px;
        }
        
        .notification-close:hover {
            background: rgba(0, 0, 0, 0.1);
        }
        
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    `;
    
    if (!document.querySelector('style[data-notification-styles]')) {
        style.setAttribute('data-notification-styles', 'true');
        document.head.appendChild(style);
    }
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }
    }, 5000);
}
