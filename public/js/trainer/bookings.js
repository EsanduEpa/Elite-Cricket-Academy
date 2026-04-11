// Trainer Bookings JavaScript - Clean Version

document.addEventListener('DOMContentLoaded', function() {
    console.log('=== INITIALIZING TRAINER BOOKINGS PAGE ===');
    console.log('Bookings page initialization complete');
});

// Table Action Functions
function markCompleted(sessionId) {
    if (confirm('Mark this session as completed?')) {
        // Find the row and update status
        const button = event.target.closest('button');
        const row = button.closest('tr');
        const statusCell = row.querySelector('.table-badge');
        
        // Update status to completed
        statusCell.textContent = 'Completed';
        statusCell.className = 'table-badge status-completed';
        
        // Show success message
        showSuccessMessage('Session marked as completed!');
        
        console.log(`Session ${sessionId} marked as completed`);
    }
}

function viewNotes(sessionId) {
    openModal('notesModal');
    
    // Load session notes from server data
    const sessionNotes = window.trainerBookingsData?.sessionNotes?.[sessionId] || {};
    const modal = document.getElementById('notesModal');
    const modalBody = modal.querySelector('.modal-body');
    
    modalBody.innerHTML = `
        <div class="form-group">
            <label>Session Notes:</label>
            <textarea class="form-control" rows="4" placeholder="Add session notes...">${sessionNotes.notes || ''}</textarea>
        </div>
        <div class="form-group">
            <label>Client Feedback:</label>
            <textarea class="form-control" rows="3" placeholder="Client feedback...">${sessionNotes.feedback || ''}</textarea>
        </div>
    `;
    
    console.log(`Viewing notes for session ${sessionId}`);
}

function editBooking(bookingId) {
    openModal('editModal');
    
    // Load booking data from server
    const booking = window.trainerBookingsData?.bookings?.find(b => b.id === bookingId) || {};
    const modal = document.getElementById('editModal');
    const modalBody = modal.querySelector('.modal-body');
    
    modalBody.innerHTML = `
        <div class="form-row">
            <div class="form-group">
                <label>Client Name:</label>
                <input type="text" value="${booking.clientName || ''}" class="form-control">
            </div>
            <div class="form-group">
                <label>Session Type:</label>
                <select class="form-control">
                    <option ${booking.sessionType === 'Strength & Conditioning' ? 'selected' : ''}>Strength & Conditioning</option>
                    <option ${booking.sessionType === 'Cardio Training' ? 'selected' : ''}>Cardio Training</option>
                    <option ${booking.sessionType === 'Agility Training' ? 'selected' : ''}>Agility Training</option>
                    <option ${booking.sessionType === 'Recovery Session' ? 'selected' : ''}>Recovery Session</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Date:</label>
                <input type="date" value="${booking.date || ''}" class="form-control">
            </div>
            <div class="form-group">
                <label>Time:</label>
                <input type="time" value="${booking.time || ''}" class="form-control">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Duration (hours):</label>
                <input type="number" value="${booking.duration || 1}" step="0.5" class="form-control">
            </div>
            <div class="form-group">
                <label>Location:</label>
                <select class="form-control">
                    <option ${booking.location === 'Gym B - Weight Room' ? 'selected' : ''}>Gym B - Weight Room</option>
                    <option ${booking.location === 'Gym A - General' ? 'selected' : ''}>Gym A - General</option>
                    <option ${booking.location === 'Cardio Zone' ? 'selected' : ''}>Cardio Zone</option>
                    <option ${booking.location === 'Court 1' ? 'selected' : ''}>Court 1</option>
                    <option ${booking.location === 'Court 2' ? 'selected' : ''}>Court 2</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label>Special Notes:</label>
            <textarea class="form-control" rows="3" placeholder="Any special instructions or notes..."></textarea>
        </div>
    `;
    
    console.log(`Editing booking ${bookingId}`);
}

function cancelBooking(bookingId) {
    if (confirm('Are you sure you want to cancel this booking?')) {
        // Find the row and update status
        const button = event.target.closest('button');
        const row = button.closest('tr');
        const statusCell = row.querySelector('.table-badge');
        
        // Update status to cancelled
        statusCell.textContent = 'Cancelled';
        statusCell.className = 'table-badge status-completed'; // Using grey style
        
        // Show success message
        showSuccessMessage('Booking cancelled successfully!');
        
        console.log(`Booking ${bookingId} cancelled`);
    }
}

// Modal Management Functions
function openModal(modalId) {
    let modal = document.getElementById(modalId);
    
    // Create modal if it doesn't exist
    if (!modal) {
        modal = createModal(modalId);
    }
    
    modal.style.display = 'flex';
    setTimeout(() => modal.classList.add('show'), 10);
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('show');
        setTimeout(() => modal.style.display = 'none', 300);
    }
}

function createModal(modalId) {
    const modal = document.createElement('div');
    modal.id = modalId;
    modal.className = 'modal-overlay';
    
    let title = 'Modal';
    if (modalId === 'notesModal') title = 'Session Notes';
    if (modalId === 'editModal') title = 'Edit Booking';
    if (modalId === 'addBookingModal') title = 'Add New Booking';
    if (modalId === 'availabilityModal') title = 'Check Availability';
    
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-${modalId === 'notesModal' ? 'sticky-note' : 'edit'}"></i> ${title}</h3>
                <button class="modal-close" onclick="closeModal('${modalId}')">&times;</button>
            </div>
            <div class="modal-body">
                <!-- Content will be populated by specific functions -->
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeModal('${modalId}')">Cancel</button>
                <button class="btn-primary" onclick="saveChanges('${modalId}')">Save Changes</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Close modal when clicking outside
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal(modalId);
        }
    });
    
    return modal;
}

function saveChanges(modalId) {
    showSuccessMessage('Changes saved successfully!');
    closeModal(modalId);
}

// Enhanced Quick Action Functions
function addNewBooking() {
    openModal('addBookingModal');
    
    // Load client list from server data
    const clients = window.trainerBookingsData?.clients || [];
    const clientOptions = clients.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
    
    const modal = document.getElementById('addBookingModal');
    const modalBody = modal.querySelector('.modal-body');
    
    modalBody.innerHTML = `
        <div class="form-row">
            <div class="form-group">
                <label>Client Name:</label>
                <select class="form-control">
                    <option value="">Select Client...</option>
                    ${clientOptions}
                </select>
            </div>
            <div class="form-group">
                <label>Session Type:</label>
                <select class="form-control">
                    <option value="">Select Type...</option>
                    <option>Strength & Conditioning</option>
                    <option>Cardio Training</option>
                    <option>Agility Training</option>
                    <option>Recovery Session</option>
                    <option>Sports-Specific Training</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Date:</label>
                <input type="date" class="form-control" min="${new Date().toISOString().split('T')[0]}">
            </div>
            <div class="form-group">
                <label>Time:</label>
                <select class="form-control">
                    <option value="">Select Time...</option>
                    <option>06:00</option>
                    <option>07:00</option>
                    <option>08:00</option>
                    <option>09:00</option>
                    <option>10:00</option>
                    <option>14:00</option>
                    <option>15:00</option>
                    <option>16:00</option>
                    <option>17:00</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Duration (hours):</label>
                <select class="form-control">
                    <option>0.5</option>
                    <option>1</option>
                    <option selected>1.5</option>
                    <option>2</option>
                    <option>2.5</option>
                </select>
            </div>
            <div class="form-group">
                <label>Location:</label>
                <select class="form-control">
                    <option>Gym A - General</option>
                    <option>Gym B - Weight Room</option>
                    <option>Cardio Zone</option>
                    <option>Court 1</option>
                    <option>Court 2</option>
                    <option>Recovery Room</option>
                </select>
            </div>
        </div>
    `;
}

function checkAvailability() {
    openModal('availabilityModal');
    
    // Load availability from server data
    const availability = window.trainerBookingsData?.availability || [];
    const modal = document.getElementById('availabilityModal');
    const modalBody = modal.querySelector('.modal-body');
    
    let slotsHtml = '';
    if (availability.length > 0) {
        slotsHtml = availability.map(day => `
            <div class="day-slot">
                <h5>${day.label}</h5>
                <div class="slots">
                    ${day.slots.map(slot => `<span class="slot ${slot.booked ? 'booked' : 'available'}">${slot.time}</span>`).join('')}
                </div>
            </div>
        `).join('');
    } else {
        slotsHtml = '<p>No availability data loaded. Please refresh the page.</p>';
    }
    
    modalBody.innerHTML = `
        <div class="availability-calendar">
            <h4>Available Time Slots</h4>
            <div class="time-slots">
                ${slotsHtml}
            </div>
        </div>
        <style>
            .day-slot { margin-bottom: 1.5rem; }
            .day-slot h5 { color: #4A90E2; margin-bottom: 0.5rem; }
            .slots { display: flex; flex-wrap: wrap; gap: 0.5rem; }
            .slot { padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.9rem; }
            .slot.available { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
            .slot.booked { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        </style>
    `;
}

function exportBookings() {
    // Export actual bookings data
    showSuccessMessage('Exporting bookings report...');
    
    setTimeout(() => {
        const today = new Date().toISOString().split('T')[0];
        const filename = `trainer-bookings-${today}.csv`;
        
        // Build CSV from server data
        const bookings = window.trainerBookingsData?.bookingsForExport || [];
        let csvContent = 'Date,Client,Session Type,Duration,Location,Status\n';
        bookings.forEach(b => {
            csvContent += `${b.date},${b.client},${b.sessionType},${b.duration},${b.location},${b.status}\n`;
        });
        
        // Create and download file
        const blob = new Blob([csvContent], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
        
        showSuccessMessage('Bookings report downloaded successfully!');
    }, 1500);
}

function showSuccessMessage(message) {
    const successDiv = document.createElement('div');
    successDiv.className = 'success-message';
    successDiv.textContent = message;
    successDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: linear-gradient(135deg, #2ed573 0%, #26c565 100%);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(46, 213, 115, 0.3);
        z-index: 10001;
        font-weight: 600;
        animation: slideIn 0.3s ease-out;
    `;
    
    document.body.appendChild(successDiv);
    
    setTimeout(() => {
        successDiv.style.animation = 'slideOut 0.3s ease-in';
        setTimeout(() => successDiv.remove(), 300);
    }, 2000);
}