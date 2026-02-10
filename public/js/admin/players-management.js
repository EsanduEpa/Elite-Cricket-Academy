// Players Management JavaScript

// Search and Filter Functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('playerSearch');
    const statusFilter = document.getElementById('statusFilter');
    const subscriptionFilter = document.getElementById('subscriptionFilter');
    const battingFilter = document.getElementById('battingFilter');
    
    // Search function
    if (searchInput) {
        searchInput.addEventListener('input', filterPlayers);
    }
    
    // Filter functions
    if (statusFilter) {
        statusFilter.addEventListener('change', filterPlayers);
    }
    
    if (subscriptionFilter) {
        subscriptionFilter.addEventListener('change', filterPlayers);
    }
    
    if (battingFilter) {
        battingFilter.addEventListener('change', filterPlayers);
    }
    
    function filterPlayers() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const statusValue = statusFilter ? statusFilter.value.toLowerCase() : 'all';
        const subscriptionValue = subscriptionFilter ? subscriptionFilter.value.toLowerCase() : 'all';
        const battingValue = battingFilter ? battingFilter.value.toLowerCase() : 'all';
        
        const playerRows = document.querySelectorAll('.staff-table tbody tr');
        
        playerRows.forEach(row => {
            const playerName = row.querySelector('.staff-info h4')?.textContent.toLowerCase() || '';
            const playerAge = row.querySelector('.staff-info p')?.textContent.toLowerCase() || '';
            const jerseyNumber = row.cells[2]?.textContent.toLowerCase() || '';
            const playerEmail = row.cells[3]?.textContent.toLowerCase() || '';
            
            // Get status from badge class
            const statusBadge = row.querySelector('.status-badge');
            const playerStatus = statusBadge ? statusBadge.textContent.trim().toLowerCase() : '';
            
            // Get subscription from badge in column 6
            const subscriptionCell = row.cells[6];
            const playerSubscription = subscriptionCell ? subscriptionCell.textContent.trim().toLowerCase() : '';
            
            // Get batting style from column 5
            const battingCell = row.cells[5];
            const playerBattingStyle = battingCell ? battingCell.textContent.trim().toLowerCase() : '';
            
            let showRow = true;
            
            // Apply search filter
            if (searchTerm && 
                !playerName.includes(searchTerm) && 
                !playerEmail.includes(searchTerm) && 
                !jerseyNumber.includes(searchTerm) &&
                !playerAge.includes(searchTerm)) {
                showRow = false;
            }
            
            // Apply status filter
            if (statusValue !== 'all' && playerStatus !== statusValue) {
                showRow = false;
            }
            
            // Apply subscription filter
            if (subscriptionValue !== 'all' && playerSubscription !== subscriptionValue) {
                showRow = false;
            }
            
            // Apply batting style filter
            if (battingValue !== 'all' && !playerBattingStyle.includes(battingValue.replace('-', ' '))) {
                showRow = false;
            }
            
            row.style.display = showRow ? '' : 'none';
        });
        
        updateResultsCount();
    }
    
    function updateResultsCount() {
        const allRows = document.querySelectorAll('.staff-table tbody tr');
        const visibleRows = Array.from(allRows).filter(row => row.style.display !== 'none');
        console.log(`Showing ${visibleRows.length} of ${allRows.length} players`);
    }
});

// Reset Filters
function resetFilters() {
    document.getElementById('playerSearch').value = '';
    document.getElementById('statusFilter').value = 'all';
    document.getElementById('subscriptionFilter').value = 'all';
    document.getElementById('battingFilter').value = 'all';
    
    const playerRows = document.querySelectorAll('.staff-table tbody tr');
    playerRows.forEach(row => {
        row.style.display = '';
    });
    
    console.log('Filters reset');
}

// Suspend Player Modal
function openSuspendModal(playerId, playerName) {
    const modal = document.getElementById('suspendModal');
    document.getElementById('suspendPlayerId').value = playerId;
    document.getElementById('suspendPlayerName').textContent = playerName;
    modal.style.display = 'flex';
    
    // Reset form
    document.getElementById('suspendDuration').value = '24h';
    document.getElementById('customHours').style.display = 'none';
    document.getElementById('suspendReason').value = '';
}

function closeSuspendModal() {
    document.getElementById('suspendModal').style.display = 'none';
}

// Handle custom duration input
document.addEventListener('DOMContentLoaded', function() {
    const durationSelect = document.getElementById('suspendDuration');
    const customHoursDiv = document.getElementById('customHours');
    
    if (durationSelect) {
        durationSelect.addEventListener('change', function() {
            if (this.value === 'custom') {
                customHoursDiv.style.display = 'block';
            } else {
                customHoursDiv.style.display = 'none';
            }
        });
    }
});

// Confirm Suspend
function confirmSuspend() {
    const playerId = document.getElementById('suspendPlayerId').value;
    const duration = document.getElementById('suspendDuration').value;
    const customHours = document.getElementById('customHoursInput').value;
    const reason = document.getElementById('suspendReason').value;
    
    if (!reason.trim()) {
        alert('Please provide a reason for suspension');
        return;
    }
    
    if (duration === 'custom' && (!customHours || customHours <= 0)) {
        alert('Please enter valid custom hours');
        return;
    }
    
    // In production, send AJAX request to backend
    console.log('Suspending player:', {
        playerId,
        duration: duration === 'custom' ? `${customHours}h` : duration,
        reason
    });
    
    // Show success message
    alert(`Player suspended successfully!\nDuration: ${duration === 'custom' ? customHours + ' hours' : duration}\nReason: ${reason}`);
    
    closeSuspendModal();
    
    // In production, reload the page or update the player row
    // location.reload();
}

// Unsuspend Player
function unsuspendPlayer(playerId, playerName) {
    if (confirm(`Are you sure you want to unsuspend ${playerName}?`)) {
        // In production, send AJAX request to backend
        console.log('Unsuspending player:', playerId);
        
        alert(`${playerName} has been unsuspended successfully!`);
        
        // In production, reload the page or update the player row
        // location.reload();
    }
}

// Delete Player Modal
function openDeleteModal(playerId, playerName) {
    const modal = document.getElementById('deleteModal');
    document.getElementById('deletePlayerId').value = playerId;
    document.getElementById('deletePlayerName').textContent = playerName;
    document.getElementById('confirmDeleteInput').value = '';
    document.getElementById('confirmDeleteBtn').disabled = true;
    modal.style.display = 'flex';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

// Enable delete button when "DELETE" is typed
document.addEventListener('DOMContentLoaded', function() {
    const confirmInput = document.getElementById('deleteConfirmation');
    const deleteBtn = document.getElementById('confirmDeleteBtn');
    
    if (confirmInput && deleteBtn) {
        confirmInput.addEventListener('input', function() {
            deleteBtn.disabled = this.value !== 'DELETE';
        });
    }
});

// Confirm Delete
function confirmDelete() {
    const playerId = document.getElementById('deletePlayerId').value;
    const playerName = document.getElementById('deletePlayerName').textContent;
    
    // In production, send AJAX request to backend
    console.log('Deleting player:', playerId);
    
    alert(`${playerName} has been permanently deleted from the system.`);
    
    closeDeleteModal();
    
    // In production, remove the row or reload the page
    // location.reload();
}

// View Player Statistics
function viewPlayerStatistics(playerId) {
    // Navigate to player statistics page
    window.location.href = `/admin/player_statistics/${playerId}`;
}

// Export Players Data
function exportPlayers() {
    // In production, generate and download CSV/Excel file
    console.log('Exporting players data...');
    
    // Sample CSV generation
    const headers = ['Player ID', 'Name', 'Email', 'Jersey', 'Batting Style', 'Subscription', 'Status', 'Performance'];
    const data = [];
    
    document.querySelectorAll('.player-row').forEach(row => {
        if (row.style.display !== 'none') {
            const cells = row.cells;
            data.push([
                cells[0].textContent.trim(),
                cells[1].querySelector('h4').textContent.trim(),
                cells[1].querySelector('p').textContent.trim(),
                cells[2].textContent.trim(),
                cells[3].textContent.trim(),
                cells[4].textContent.trim(),
                cells[5].textContent.trim(),
                cells[6].textContent.trim()
            ]);
        }
    });
    
    let csv = headers.join(',') + '\n';
    data.forEach(row => {
        csv += row.map(cell => `"${cell}"`).join(',') + '\n';
    });
    
    // Download CSV
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `players_export_${new Date().toISOString().split('T')[0]}.csv`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
    
    alert('Players data exported successfully!');
}

// Pagination
function goToPage(page) {
    console.log('Going to page:', page);
    // In production, load data for the specific page
    // This would typically involve an AJAX request to fetch paginated data
}

// Close modals when clicking outside
window.addEventListener('click', function(event) {
    const suspendModal = document.getElementById('suspendModal');
    const deleteModal = document.getElementById('deleteModal');
    
    if (event.target === suspendModal) {
        closeSuspendModal();
    }
    
    if (event.target === deleteModal) {
        closeDeleteModal();
    }
});

// Close modals with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeSuspendModal();
        closeDeleteModal();
    }
});

// Initialize performance animations
document.addEventListener('DOMContentLoaded', function() {
    const performanceBars = document.querySelectorAll('.performance-fill');
    
    performanceBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0';
        setTimeout(() => {
            bar.style.width = width;
        }, 100);
    });
});
