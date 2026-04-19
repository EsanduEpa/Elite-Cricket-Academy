// Players Management JavaScript

// Search and Filter Functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('playerSearch');
    const statusFilter = document.getElementById('statusFilter');
    const ageGroupFilter = document.getElementById('ageGroupFilter');
    const subscriptionFilter = document.getElementById('subscriptionFilter');
    const battingFilter = document.getElementById('battingFilter');
    const pageSize = 10;
    let currentPage = 1;
    
    // Search function
    if (searchInput) {
        searchInput.addEventListener('input', filterPlayers);
    }
    
    // Filter functions
    if (statusFilter) {
        statusFilter.addEventListener('change', filterPlayers);
    }

    if (ageGroupFilter) {
        ageGroupFilter.addEventListener('change', filterPlayers);
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
        const ageGroupValue = ageGroupFilter ? ageGroupFilter.value.toLowerCase() : 'all';
        const subscriptionValue = normalizeFilterValue(subscriptionFilter ? subscriptionFilter.value : 'all');
        const battingValue = battingFilter ? battingFilter.value.toLowerCase() : 'all';
        
        const playerRows = document.querySelectorAll('#playersTableBody tr[data-player-age]');
        
        playerRows.forEach(row => {
            const playerName = row.querySelector('.staff-info h4')?.textContent.toLowerCase() || '';
            const playerAge = row.querySelector('.staff-info p')?.textContent.toLowerCase() || '';
            const jerseyNumber = row.cells[2]?.textContent.toLowerCase() || '';
            const playerEmail = row.cells[3]?.textContent.toLowerCase() || '';
            const ageValue = parseInt(row.dataset.playerAge || '0', 10);

            function getAgeGroup(age) {
                if (!age || Number.isNaN(age) || age <= 0) return '';
                if (age < 11) return 'under 11';
                if (age < 13) return 'under 13';
                if (age < 15) return 'under 15';
                if (age < 17) return 'under 17';
                if (age < 19) return 'under 19';
                if (age < 21) return 'under 21';
                return 'open';
            }

            const playerAgeGroup = getAgeGroup(ageValue);
            
            // Get status from badge class
            const statusBadge = row.querySelector('.status-badge');
            const playerStatus = statusBadge ? statusBadge.textContent.trim().toLowerCase() : '';
            
            // Get subscription from badge in column 6
            const subscriptionCell = row.cells[6];
            const playerSubscription = row.dataset.playerSubscription || normalizeFilterValue(subscriptionCell ? subscriptionCell.textContent.trim() : '');
            
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

            if (ageGroupValue !== 'all' && playerAgeGroup !== ageGroupValue) {
                showRow = false;
            }
            
            // Apply subscription filter
            if (subscriptionValue !== 'all' && !playerSubscription.split(/\s+/).includes(subscriptionValue)) {
                showRow = false;
            }
            
            // Apply batting style filter
            if (battingValue !== 'all' && !playerBattingStyle.includes(battingValue.replace('-', ' '))) {
                showRow = false;
            }
            
            row.dataset.filterMatch = showRow ? '1' : '0';
        });
        
        currentPage = 1;
        renderPlayerPage();
    }

    function normalizeFilterValue(value) {
        return String(value || '')
            .trim()
            .toLowerCase()
            .replace(/_/g, '-')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }
    
    function renderPlayerPage() {
        const allRows = Array.from(document.querySelectorAll('#playersTableBody tr[data-player-age]'));
        const matchedRows = allRows.filter(row => row.dataset.filterMatch !== '0');
        const totalPages = Math.max(1, Math.ceil(matchedRows.length / pageSize));
        currentPage = Math.min(Math.max(currentPage, 1), totalPages);
        const startIndex = (currentPage - 1) * pageSize;
        const endIndex = startIndex + pageSize;
        const visibleRows = matchedRows.slice(startIndex, endIndex);

        allRows.forEach(row => {
            row.style.display = visibleRows.includes(row) ? '' : 'none';
        });

        const showingStart = document.getElementById('showingStart');
        const showingEnd = document.getElementById('showingEnd');
        const totalPlayers = document.getElementById('totalPlayers');

        if (showingStart) {
            showingStart.textContent = matchedRows.length > 0 ? String(startIndex + 1) : '0';
        }
        if (showingEnd) {
            showingEnd.textContent = String(Math.min(endIndex, matchedRows.length));
        }
        if (totalPlayers) {
            totalPlayers.textContent = String(matchedRows.length);
        }

        renderPaginationControls(totalPages);
    }

    function renderPaginationControls(totalPages) {
        const pagination = document.getElementById('playersPagination');
        if (!pagination) return;

        pagination.innerHTML = '';
        pagination.appendChild(createPageButton('prev', '<i class="fas fa-chevron-left"></i>', currentPage === 1, () => {
            currentPage--;
            renderPlayerPage();
        }));

        for (let page = 1; page <= totalPages; page++) {
            if (page > 1 && page < totalPages && Math.abs(page - currentPage) > 1) {
                if (!pagination.querySelector(`[data-ellipsis="${page < currentPage ? 'left' : 'right'}"]`)) {
                    const ellipsis = document.createElement('span');
                    ellipsis.className = 'page-ellipsis';
                    ellipsis.dataset.ellipsis = page < currentPage ? 'left' : 'right';
                    ellipsis.textContent = '...';
                    pagination.appendChild(ellipsis);
                }
                continue;
            }

            pagination.appendChild(createPageButton(page, String(page), false, () => {
                currentPage = page;
                renderPlayerPage();
            }, page === currentPage));
        }

        pagination.appendChild(createPageButton('next', '<i class="fas fa-chevron-right"></i>', currentPage === totalPages, () => {
            currentPage++;
            renderPlayerPage();
        }));
    }

    function createPageButton(value, html, disabled, onClick, active = false) {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = active ? 'page-btn active' : 'page-btn';
        button.dataset.page = value;
        button.innerHTML = html;
        button.disabled = disabled;
        button.addEventListener('click', onClick);
        return button;
    }

    document.querySelectorAll('#playersTableBody tr[data-player-age]').forEach(row => {
        row.dataset.filterMatch = '1';
    });
    renderPlayerPage();
    window.renderPlayerPage = renderPlayerPage;
    window.resetPlayerPagination = function() {
        currentPage = 1;
        renderPlayerPage();
    };
});

// Reset Filters
function resetFilters() {
    document.getElementById('playerSearch').value = '';
    document.getElementById('statusFilter').value = 'all';
    document.getElementById('ageGroupFilter').value = 'all';
    document.getElementById('subscriptionFilter').value = 'all';
    document.getElementById('battingFilter').value = 'all';
    
    const playerRows = document.querySelectorAll('#playersTableBody tr[data-player-age]');
    playerRows.forEach(row => {
        row.dataset.filterMatch = '1';
    });

    if (typeof window.resetPlayerPagination === 'function') {
        window.resetPlayerPagination();
    }
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
    
    // Show success message
    alert(`Player suspended successfully!\nDuration: ${duration === 'custom' ? customHours + ' hours' : duration}\nReason: ${reason}`);
    
    closeSuspendModal();
    
    // In production, reload the page or update the player row
    // location.reload();
}

// Unsuspend Player
function unsuspendPlayer(playerId, playerName) {
    if (confirm(`Are you sure you want to unsuspend ${playerName}?`)) {
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
    document.getElementById('deleteConfirmation').value = '';
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
    
    alert(`${playerName} has been permanently deleted from the system.`);
    
    closeDeleteModal();
    
    // In production, remove the row or reload the page
    // location.reload();
}

// View Player Statistics
function viewPlayerStatistics(playerId) {
    const urlRoot = document.querySelector('.admin-layout')?.dataset.urlroot || '/Elite';
    window.location.href = `${urlRoot}/admin/player_statistics/${playerId}`;
}

// Export Players Data
function exportPlayers() {
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
    void page;
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
