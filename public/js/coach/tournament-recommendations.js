/**
 * Tournament Recommendations Page - JavaScript
 * Handles modal interactions, form submissions, filtering, and AJAX operations
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeUI();
    attachEventListeners();
    loadAvailableTournaments();
});

const APP_URLROOT = window.APP_URLROOT || '';
const RECOMMENDATION_DATA = window.__COACH_RECOMMENDATION_DATA || { players: [], tournaments: [] };

// ==================== INITIALIZATION ====================

function initializeUI() {
    // Initialize modals
    const modal = document.getElementById('recommendationModal');
    const modalOverlay = document.getElementById('modalOverlay');
    
    if (!modal) {
        console.warn('Recommendation modal not found');
        return;
    }

    // Close modal when clicking overlay
    if (modalOverlay) {
        modalOverlay.addEventListener('click', closeModal);
    }
}

// ==================== EVENT LISTENERS ====================

function attachEventListeners() {
    // New Recommendation Button
    const newBtn = document.getElementById('newRecommendationBtn');
    const emptyStateBtn = document.getElementById('emptyStateBtn');
    
    if (newBtn) newBtn.addEventListener('click', openNewModal);
    if (emptyStateBtn) emptyStateBtn.addEventListener('click', openNewModal);

    // Modal Controls
    const modalCloseBtn = document.getElementById('modalCloseBtn');
    const modalCancelBtn = document.getElementById('modalCancelBtn');
    
    if (modalCloseBtn) modalCloseBtn.addEventListener('click', closeModal);
    if (modalCancelBtn) modalCancelBtn.addEventListener('click', closeModal);

    // Form Submission
    const form = document.getElementById('recommendationForm');
    if (form) {
        form.addEventListener('submit', handleFormSubmit);
    }

    // Edit Buttons
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(btn => {
        btn.addEventListener('click', handleEditClick);
    });

    // Delete Buttons
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', handleDeleteClick);
    });

    // Filters
    const filterStatus = document.getElementById('filterStatus');
    const filterSort = document.getElementById('filterSort');
    const filterSearch = document.getElementById('filterSearch');
    const clearFiltersBtn = document.getElementById('clearFiltersBtn');

    if (filterStatus) filterStatus.addEventListener('change', applyFilters);
    if (filterSort) filterSort.addEventListener('change', applyFilters);
    if (filterSearch) filterSearch.addEventListener('input', debounce(applyFilters, 300));
    if (clearFiltersBtn) clearFiltersBtn.addEventListener('click', clearFilters);
}

// ==================== MODAL FUNCTIONS ====================

function openNewModal() {
    const modal = document.getElementById('recommendationModal');
    const modalOverlay = document.getElementById('modalOverlay');
    const form = document.getElementById('recommendationForm');
    const modalTitle = document.getElementById('modalTitle');
    const submitBtnText = document.getElementById('submitBtnText');

    // Reset form
    form.reset();
    form.dataset.mode = 'new';
    
    modalTitle.textContent = 'New Recommendation';
    submitBtnText.textContent = 'Save Recommendation';

    // Clear hidden ID field
    delete form.dataset.recommendationId;

    // Load tournaments
    loadTournamentSelect();

    // Load players
    loadPlayerSelect();

    const tournamentSelect = document.getElementById('tournamentSelect');
    if (tournamentSelect && !tournamentSelect.dataset.boundAgeGroupFilter) {
        tournamentSelect.addEventListener('change', handleTournamentPlayerFilter);
        tournamentSelect.dataset.boundAgeGroupFilter = '1';
    }

    // Show modal
    modal.style.display = 'flex';
    modalOverlay.style.display = 'block';
}

function openEditModal(recommendationId) {
    const modal = document.getElementById('recommendationModal');
    const modalOverlay = document.getElementById('modalOverlay');
    const form = document.getElementById('recommendationForm');
    const modalTitle = document.getElementById('modalTitle');
    const submitBtnText = document.getElementById('submitBtnText');

    // Set mode and ID
    form.dataset.mode = 'edit';
    form.dataset.recommendationId = recommendationId;

    modalTitle.textContent = 'Edit Recommendation';
    submitBtnText.textContent = 'Update Recommendation';

    // Load recommendation data
    fetch(`${APP_URLROOT}/coach/recommendation-details/${recommendationId}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const rec = data.recommendation;
                document.getElementById('tournamentSelect').value = rec.TournamentID;
                document.getElementById('playerSelect').value = rec.PlayerID;
                document.getElementById('roleSelect').value = rec.RecommendedRole;
                document.getElementById('reasonInput').value = rec.Reason || '';
                document.getElementById('commentsInput').value = rec.Comments || '';
                
                // Disable tournament and player selects
                document.getElementById('tournamentSelect').disabled = true;
                document.getElementById('playerSelect').disabled = true;

                modal.style.display = 'flex';
                modalOverlay.style.display = 'block';
            } else {
                showToast('Failed to load recommendation', 'error');
            }
        })
        .catch(err => {
            console.error('Error loading recommendation:', err);
            showToast('Error loading recommendation', 'error');
        });
}

function closeModal() {
    const modal = document.getElementById('recommendationModal');
    const modalOverlay = document.getElementById('modalOverlay');
    const form = document.getElementById('recommendationForm');

    modal.style.display = 'none';
    modalOverlay.style.display = 'none';
    form.reset();

    // Re-enable selects
    document.getElementById('tournamentSelect').disabled = false;
    document.getElementById('playerSelect').disabled = false;
}

// ==================== FORM HANDLING ====================

function handleFormSubmit(e) {
    e.preventDefault();

    const form = e.target;
    const mode = form.dataset.mode || 'new';
    const recommendationId = form.dataset.recommendationId;

    // Validate form
    if (!validateForm(form)) {
        return;
    }

    // Get form data
    const formData = new FormData(form);
    const data = {
        tournamentId: parseInt(formData.get('tournament')),
        playerId: parseInt(formData.get('player')),
        recommendedRole: formData.get('role'),
        reason: formData.get('reason'),
        comments: formData.get('comments')
    };

    // Show loading
    showLoadingSpinner(true);

    // Determine endpoint
    const endpoint = mode === 'new'
        ? `${APP_URLROOT}/coach/save-recommendation`
        : `${APP_URLROOT}/coach/update-recommendation/${recommendationId}`;

    const method = mode === 'new' ? 'POST' : 'PUT';

    // Send request
    fetch(endpoint, {
        method: method,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(result => {
        showLoadingSpinner(false);

        if (result.success) {
            showToast(result.message || 'Recommendation saved successfully', 'success');
            closeModal();
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showToast(result.message || 'Failed to save recommendation', 'error');
        }
    })
    .catch(err => {
        showLoadingSpinner(false);
        console.error('Error:', err);
        showToast('Error: ' + err.message, 'error');
    });
}

function validateForm(form) {
    let isValid = true;
    
    // Check required fields
    const tournament = form.elements['tournament'].value;
    const player = form.elements['player'].value;
    const role = form.elements['role'].value;

    if (!tournament) {
        showFieldError('tournamentError', 'Please select a tournament');
        isValid = false;
    } else {
        clearFieldError('tournamentError');
    }

    if (!player) {
        showFieldError('playerError', 'Please select a player');
        isValid = false;
    } else {
        clearFieldError('playerError');
    }

    if (!role) {
        showFieldError('roleError', 'Please select a role');
        isValid = false;
    } else {
        clearFieldError('roleError');
    }

    return isValid;
}

function showFieldError(elementId, message) {
    const errorEl = document.getElementById(elementId);
    if (errorEl) {
        errorEl.textContent = message;
        errorEl.classList.add('show');
    }
}

function clearFieldError(elementId) {
    const errorEl = document.getElementById(elementId);
    if (errorEl) {
        errorEl.textContent = '';
        errorEl.classList.remove('show');
    }
}

// ==================== EDIT & DELETE HANDLERS ====================

function handleEditClick(e) {
    e.preventDefault();
    const recommendationId = this.getAttribute('data-id');
    openEditModal(recommendationId);
}

function handleDeleteClick(e) {
    e.preventDefault();
    const recommendationId = this.getAttribute('data-id');

    if (!confirm('Are you sure you want to delete this recommendation?')) {
        return;
    }

    showLoadingSpinner(true);

    fetch(`${APP_URLROOT}/coach/delete-recommendation/${recommendationId}`, {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' }
    })
    .then(r => r.json())
    .then(result => {
        showLoadingSpinner(false);

        if (result.success) {
            showToast('Recommendation deleted successfully', 'success');
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showToast(result.message || 'Failed to delete recommendation', 'error');
        }
    })
    .catch(err => {
        showLoadingSpinner(false);
        console.error('Error:', err);
        showToast('Error: ' + err.message, 'error');
    });
}

// ==================== FILTERING & SORTING ====================

function applyFilters() {
    const grid = document.getElementById('recommendationsGrid');
    if (!grid) return;

    const statusFilter = document.getElementById('filterStatus')?.value || '';
    const searchFilter = document.getElementById('filterSearch')?.value.toLowerCase() || '';
    const sortBy = document.getElementById('filterSort')?.value || 'date_desc';

    let cards = Array.from(grid.querySelectorAll('.recommendation-card'));

    // Apply filters
    cards = cards.filter(card => {
        const status = card.dataset.status || '';
        const tournament = card.dataset.tournament || '';
        const player = card.dataset.player || '';

        // Status filter
        if (statusFilter && status !== statusFilter) {
            return false;
        }

        // Search filter
        if (searchFilter) {
            const searchText = `${player} ${tournament}`.toLowerCase();
            if (!searchText.includes(searchFilter)) {
                return false;
            }
        }

        return true;
    });

    // Apply sorting
    cards.sort((a, b) => {
        switch (sortBy) {
            case 'date_asc':
                return a.dataset.date?.localeCompare(b.dataset.date) || 0;
            case 'tournament':
                return a.dataset.tournament?.localeCompare(b.dataset.tournament) || 0;
            case 'player':
                return a.dataset.player?.localeCompare(b.dataset.player) || 0;
            case 'date_desc':
            default:
                return b.dataset.date?.localeCompare(a.dataset.date) || 0;
        }
    });

    // Update grid
    grid.innerHTML = '';
    if (cards.length === 0) {
        grid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #999;">No recommendations match your filters</div>';
    } else {
        cards.forEach(card => grid.appendChild(card));
    }
}

function clearFilters() {
    document.getElementById('filterStatus').value = '';
    document.getElementById('filterSort').value = 'date_desc';
    document.getElementById('filterSearch').value = '';
    applyFilters();
}

// ==================== DATA LOADING ====================

function loadTournamentSelect() {
    const select = document.getElementById('tournamentSelect');
    if (!select) {
        return;
    }

    const tournaments = Array.isArray(RECOMMENDATION_DATA.tournaments) ? RECOMMENDATION_DATA.tournaments : [];
    select.innerHTML = '<option value="">Select a tournament...</option>';

    tournaments.forEach(tournament => {
        const option = document.createElement('option');
        option.value = tournament.TournamentID || tournament.id || '';
        option.textContent = tournament.Name || tournament.TournamentName || `Tournament ${option.value}`;
        option.dataset.ageGroup = (tournament.AgeGroup || tournament.age_group || '').toString().toLowerCase();
        select.appendChild(option);
    });
}

function loadPlayerSelect() {
    const select = document.getElementById('playerSelect');

    if (!select) {
        return;
    }

    const players = Array.isArray(RECOMMENDATION_DATA.players) ? RECOMMENDATION_DATA.players : [];
    select.innerHTML = '<option value="">Select a player...</option>';

    const selectedTournament = document.getElementById('tournamentSelect');
    const selectedTournamentOption = selectedTournament ? selectedTournament.options[selectedTournament.selectedIndex] : null;
    const tournamentAgeGroup = (selectedTournamentOption?.dataset?.ageGroup || '').toLowerCase();

    if (!tournamentAgeGroup) {
        const option = document.createElement('option');
        option.disabled = true;
        option.textContent = 'Select a tournament first';
        select.appendChild(option);
        select.disabled = true;
        return;
    }

    const matchingPlayers = players.filter(player => {
        const playerAgeGroup = (player.PlayerAgeGroup || player.AgeGroup || '').toString().toLowerCase();
        return playerAgeGroup === tournamentAgeGroup || playerAgeGroup === 'open' || tournamentAgeGroup === 'open';
    });

    select.disabled = false;

    if (!matchingPlayers.length) {
        const option = document.createElement('option');
        option.disabled = true;
        option.textContent = 'No matching players for this age group';
        select.appendChild(option);
        return;
    }

    matchingPlayers.forEach(player => {
        const option = document.createElement('option');
        option.value = player.PlayerID || player.UserID || '';
        option.textContent = player.Name || player.PlayerName || `Player ${option.value}`;
        select.appendChild(option);
    });
}

function handleTournamentPlayerFilter() {
    const playerSelect = document.getElementById('playerSelect');
    if (playerSelect) {
        playerSelect.value = '';
    }
    loadPlayerSelect();
}

function loadAvailableTournaments() {
    // This could be called to pre-load tournaments for filtering
    // Implement based on your Event model structure
}

// ==================== UTILITY FUNCTIONS ====================

function showLoadingSpinner(show) {
    const spinner = document.getElementById('loadingSpinner');
    if (spinner) {
        if (show) {
            spinner.classList.add('show');
        } else {
            spinner.classList.remove('show');
        }
    }
}

function showToast(message, type = 'info') {
    const toast = document.getElementById('toast');
    if (!toast) return;

    toast.textContent = message;
    toast.className = `toast show ${type}`;

    // Auto-hide after 3 seconds
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// ==================== KEYBOARD SHORTCUTS ====================

document.addEventListener('keydown', function(e) {
    const modal = document.getElementById('recommendationModal');
    
    // Close modal with Escape key
    if (e.key === 'Escape' && modal && modal.style.display === 'flex') {
        closeModal();
    }

    // Open new modal with Ctrl+N
    if (e.ctrlKey && e.key === 'n') {
        e.preventDefault();
        openNewModal();
    }
});

console.log('Tournament Recommendations JS loaded');
