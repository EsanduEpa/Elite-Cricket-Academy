// Coach Tournaments Page - JavaScript

// Tournaments Data - loaded from server via window.coachTournamentsData
const tournamentsData = window.coachTournamentsData?.tournaments || [];

const playersData = window.coachTournamentsData?.players || [];

let currentFilter = { status: 'all', category: 'all', search: '' };
let selectedPlayers = [];
let currentTournamentForRecommendation = null;

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    loadTournaments();
    setupEventListeners();
});

// Setup event listeners
function setupEventListeners() {
    // Filter listeners
    document.getElementById('statusFilter').addEventListener('change', function(e) {
        currentFilter.status = e.target.value;
        loadTournaments();
    });

    document.getElementById('categoryFilter').addEventListener('change', function(e) {
        currentFilter.category = e.target.value;
        loadTournaments();
    });

    document.getElementById('tournamentSearch').addEventListener('input', function(e) {
        currentFilter.search = e.target.value.toLowerCase();
        loadTournaments();
    });

    // Recommend player button
    document.getElementById('recommendPlayerBtn').addEventListener('click', openRecommendModal);
    document.getElementById('closeRecommendModal').addEventListener('click', closeRecommendModal);
    document.getElementById('cancelRecommend').addEventListener('click', closeRecommendModal);

    // Tournament details modal
    document.getElementById('closeTournamentModal').addEventListener('click', closeTournamentModal);

    // Player stats modal
    document.getElementById('closePlayerStatsModal').addEventListener('click', closePlayerStatsModal);

    // Player search
    document.getElementById('playerSearch').addEventListener('input', handlePlayerSearch);

    // Recommend form
    document.getElementById('recommendForm').addEventListener('submit', handleRecommendSubmit);
}

// Load and display tournaments
function loadTournaments() {
    const grid = document.getElementById('tournamentsGrid');
    
    let filtered = tournamentsData.filter(tournament => {
        const matchesStatus = currentFilter.status === 'all' || tournament.status === currentFilter.status;
        const matchesCategory = currentFilter.category === 'all' || tournament.category === currentFilter.category;
        const matchesSearch = !currentFilter.search || 
            tournament.name.toLowerCase().includes(currentFilter.search) ||
            tournament.location.toLowerCase().includes(currentFilter.search);
        
        return matchesStatus && matchesCategory && matchesSearch;
    });

    if (filtered.length === 0) {
        grid.innerHTML = `
            <div class="no-tournaments">
                <i class="fas fa-trophy"></i>
                <h3>No tournaments found</h3>
                <p>Try adjusting your filters</p>
            </div>
        `;
        return;
    }

    grid.innerHTML = filtered.map(tournament => createTournamentCard(tournament)).join('');

    // Add click listeners to tournament cards
    document.querySelectorAll('.tournament-card').forEach(card => {
        card.addEventListener('click', function(e) {
            if (!e.target.closest('button')) {
                const tournamentId = parseInt(this.dataset.tournamentId);
                viewTournamentDetails(tournamentId);
            }
        });
    });

    // Add click listeners to recommend buttons
    document.querySelectorAll('.btn-recommend').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const tournamentId = parseInt(this.dataset.tournamentId);
            openRecommendModalForTournament(tournamentId);
        });
    });
}

// Create tournament card HTML
function createTournamentCard(tournament) {
    const statusClass = tournament.status;
    const statusIcon = {
        upcoming: 'fa-calendar-plus',
        ongoing: 'fa-play-circle',
        completed: 'fa-check-circle'
    }[tournament.status];

    const selectionBadge = {
        open: '<span class="selection-badge open"><i class="fas fa-hourglass-start"></i> Open for Selection</span>',
        pending: '<span class="selection-badge pending"><i class="fas fa-clock"></i> Selection Pending</span>',
        in_progress: '<span class="selection-badge in-progress"><i class="fas fa-spinner"></i> Selection in Progress</span>',
        finalized: '<span class="selection-badge finalized"><i class="fas fa-check"></i> Team Finalized</span>'
    }[tournament.selectionStatus];

    return `
        <div class="tournament-card ${statusClass}" data-tournament-id="${tournament.id}">
            <div class="tournament-header">
                <div class="tournament-status ${statusClass}">
                    <i class="fas ${statusIcon}"></i>
                    ${tournament.status.charAt(0).toUpperCase() + tournament.status.slice(1)}
                </div>
                <div class="tournament-category">${tournament.category.toUpperCase()}</div>
            </div>
            
            <div class="tournament-body">
                <h3 class="tournament-name">${tournament.name}</h3>
                <p class="tournament-description">${tournament.description}</p>
                
                <div class="tournament-info">
                    <div class="info-item">
                        <i class="fas fa-calendar"></i>
                        <span>${formatDate(tournament.startDate)} - ${formatDate(tournament.endDate)}</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>${tournament.location}</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-users"></i>
                        <span>Max Team Size: ${tournament.maxTeamSize}</span>
                    </div>
                    ${tournament.status !== 'completed' ? `
                    <div class="info-item">
                        <i class="fas fa-clock"></i>
                        <span>Deadline: ${formatDate(tournament.registrationDeadline)}</span>
                    </div>
                    ` : ''}
                </div>

                ${selectionBadge}

                <div class="tournament-recommendations">
                    <div class="recommendation-count">
                        <i class="fas fa-user-plus"></i>
                        <span>${tournament.recommendedPlayers.length} recommended</span>
                    </div>
                    <div class="selection-count">
                        <i class="fas fa-check-circle"></i>
                        <span>${tournament.selectedPlayers.length} selected</span>
                    </div>
                </div>
            </div>

            <div class="tournament-footer">
                ${tournament.status !== 'completed' && tournament.selectionStatus !== 'finalized' ? `
                <button class="btn-recommend" data-tournament-id="${tournament.id}">
                    <i class="fas fa-user-plus"></i>
                    Recommend Player
                </button>
                ` : ''}
                <button class="btn-view-details">
                    <i class="fas fa-eye"></i>
                    View Details
                </button>
            </div>
        </div>
    `;
}

// View tournament details
function viewTournamentDetails(tournamentId) {
    const tournament = tournamentsData.find(t => t.id === tournamentId);
    if (!tournament) return;

    const modal = document.getElementById('tournamentDetailsModal');
    const title = document.getElementById('tournamentModalTitle');
    const body = document.getElementById('tournamentDetailsBody');

    title.textContent = tournament.name;

    body.innerHTML = `
        <div class="tournament-details-content">
            <div class="details-section">
                <h4><i class="fas fa-info-circle"></i> Tournament Information</h4>
                <div class="details-grid">
                    <div class="detail-item">
                        <label>Category:</label>
                        <span>${tournament.category.toUpperCase()}</span>
                    </div>
                    <div class="detail-item">
                        <label>Status:</label>
                        <span class="status-badge ${tournament.status}">${tournament.status}</span>
                    </div>
                    <div class="detail-item">
                        <label>Start Date:</label>
                        <span>${formatDate(tournament.startDate)}</span>
                    </div>
                    <div class="detail-item">
                        <label>End Date:</label>
                        <span>${formatDate(tournament.endDate)}</span>
                    </div>
                    <div class="detail-item">
                        <label>Location:</label>
                        <span>${tournament.location}</span>
                    </div>
                    <div class="detail-item">
                        <label>Organizer:</label>
                        <span>${tournament.organizer}</span>
                    </div>
                    <div class="detail-item">
                        <label>Max Team Size:</label>
                        <span>${tournament.maxTeamSize}</span>
                    </div>
                    ${tournament.status !== 'completed' ? `
                    <div class="detail-item">
                        <label>Registration Deadline:</label>
                        <span>${formatDate(tournament.registrationDeadline)}</span>
                    </div>
                    ` : ''}
                </div>
                <p class="tournament-full-description">${tournament.description}</p>
            </div>

            ${tournament.results ? `
            <div class="details-section">
                <h4><i class="fas fa-chart-line"></i> Tournament Results</h4>
                <div class="results-grid">
                    <div class="result-item">
                        <i class="fas fa-trophy"></i>
                        <span>${tournament.results.position}</span>
                    </div>
                    <div class="result-item">
                        <i class="fas fa-clipboard-list"></i>
                        <span>${tournament.results.totalMatches} Matches</span>
                    </div>
                    <div class="result-item">
                        <i class="fas fa-check-circle"></i>
                        <span>${tournament.results.won} Won</span>
                    </div>
                    <div class="result-item">
                        <i class="fas fa-times-circle"></i>
                        <span>${tournament.results.lost} Lost</span>
                    </div>
                </div>
            </div>
            ` : ''}

            <div class="details-section">
                <h4><i class="fas fa-users"></i> Recommended Players (${tournament.recommendedPlayers.length})</h4>
                ${tournament.recommendedPlayers.length > 0 ? `
                <div class="players-list">
                    ${tournament.recommendedPlayers.map(playerName => {
                        const player = playersData.find(p => p.name === playerName);
                        const isSelected = tournament.selectedPlayers.includes(playerName);
                        return `
                            <div class="player-item ${isSelected ? 'selected' : ''}">
                                <div class="player-info">
                                    <i class="fas fa-user-circle"></i>
                                    <span>${playerName}</span>
                                    ${isSelected ? '<span class="selected-badge"><i class="fas fa-check"></i> Selected</span>' : ''}
                                </div>
                                ${player ? `
                                <button class="btn-view-stats" onclick="viewPlayerStats(${player.id})">
                                    <i class="fas fa-chart-bar"></i>
                                    View Stats
                                </button>
                                ` : ''}
                            </div>
                        `;
                    }).join('')}
                </div>
                ` : '<p class="no-data">No players recommended yet</p>'}
            </div>

            ${tournament.selectedPlayers.length > 0 ? `
            <div class="details-section">
                <h4><i class="fas fa-check-double"></i> Final Selected Team (${tournament.selectedPlayers.length})</h4>
                <div class="players-list">
                    ${tournament.selectedPlayers.map(playerName => `
                        <div class="player-item selected">
                            <div class="player-info">
                                <i class="fas fa-user-circle"></i>
                                <span>${playerName}</span>
                                <span class="selected-badge"><i class="fas fa-check"></i> Selected</span>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
            ` : ''}
        </div>
    `;

    modal.classList.add('active');
}

// Close tournament modal
function closeTournamentModal() {
    document.getElementById('tournamentDetailsModal').classList.remove('active');
}

// Open recommend modal
function openRecommendModal() {
    selectedPlayers = [];
    currentTournamentForRecommendation = null;
    
    const modal = document.getElementById('recommendModal');
    const select = document.getElementById('tournamentSelect');
    
    // Populate tournament select with upcoming tournaments
    const upcomingTournaments = tournamentsData.filter(t => 
        t.status === 'upcoming' && t.selectionStatus !== 'finalized'
    );
    
    select.innerHTML = '<option value="">Choose tournament...</option>' + 
        upcomingTournaments.map(t => 
            `<option value="${t.id}">${t.name} - ${formatDate(t.startDate)}</option>`
        ).join('');
    
    document.getElementById('recommendForm').reset();
    document.getElementById('selectedPlayers').innerHTML = '';
    document.getElementById('playerSearchResults').innerHTML = '';
    
    modal.classList.add('active');
}

// Open recommend modal for specific tournament
function openRecommendModalForTournament(tournamentId) {
    openRecommendModal();
    document.getElementById('tournamentSelect').value = tournamentId;
    currentTournamentForRecommendation = tournamentId;
}

// Close recommend modal
function closeRecommendModal() {
    document.getElementById('recommendModal').classList.remove('active');
}

// Handle player search
function handlePlayerSearch(e) {
    const searchTerm = e.target.value.toLowerCase();
    const resultsDiv = document.getElementById('playerSearchResults');
    
    if (searchTerm.length < 2) {
        resultsDiv.innerHTML = '';
        resultsDiv.style.display = 'none';
        return;
    }

    const tournamentId = document.getElementById('tournamentSelect').value;
    if (!tournamentId) {
        resultsDiv.innerHTML = '<div class="search-message">Please select a tournament first</div>';
        resultsDiv.style.display = 'block';
        return;
    }

    const tournament = tournamentsData.find(t => t.id === parseInt(tournamentId));
    const filtered = playersData.filter(player => 
        player.name.toLowerCase().includes(searchTerm) &&
        player.category === tournament.category &&
        !selectedPlayers.includes(player.id)
    );

    if (filtered.length === 0) {
        resultsDiv.innerHTML = '<div class="search-message">No players found</div>';
        resultsDiv.style.display = 'block';
        return;
    }

    resultsDiv.innerHTML = filtered.map(player => `
        <div class="player-search-item" onclick="selectPlayer(${player.id})">
            <div class="player-basic-info">
                <i class="fas fa-user-circle"></i>
                <div>
                    <strong>${player.name}</strong>
                    <span>Age: ${player.age} | ${player.category}</span>
                </div>
            </div>
            <div class="player-fitness">
                <span class="fitness-badge ${player.fitnessStatus}">${player.fitnessStatus.replace('_', ' ')}</span>
            </div>
        </div>
    `).join('');
    
    resultsDiv.style.display = 'block';
}

// Select player for recommendation
function selectPlayer(playerId) {
    if (selectedPlayers.includes(playerId)) return;
    
    selectedPlayers.push(playerId);
    const player = playersData.find(p => p.id === playerId);
    
    const selectedDiv = document.getElementById('selectedPlayers');
    const playerDiv = document.createElement('div');
    playerDiv.className = 'selected-player-item';
    playerDiv.innerHTML = `
        <div class="selected-player-info">
            <i class="fas fa-user-circle"></i>
            <span>${player.name}</span>
            <button type="button" class="btn-view-stats-small" onclick="viewPlayerStats(${player.id})">
                <i class="fas fa-chart-bar"></i> Stats
            </button>
        </div>
        <button type="button" class="btn-remove-player" onclick="removeSelectedPlayer(${player.id})">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    selectedDiv.appendChild(playerDiv);
    
    // Clear search
    document.getElementById('playerSearch').value = '';
    document.getElementById('playerSearchResults').innerHTML = '';
    document.getElementById('playerSearchResults').style.display = 'none';
}

// Remove selected player
function removeSelectedPlayer(playerId) {
    selectedPlayers = selectedPlayers.filter(id => id !== playerId);
    loadSelectedPlayers();
}

// Load selected players display
function loadSelectedPlayers() {
    const selectedDiv = document.getElementById('selectedPlayers');
    selectedDiv.innerHTML = selectedPlayers.map(playerId => {
        const player = playersData.find(p => p.id === playerId);
        return `
            <div class="selected-player-item">
                <div class="selected-player-info">
                    <i class="fas fa-user-circle"></i>
                    <span>${player.name}</span>
                    <button type="button" class="btn-view-stats-small" onclick="viewPlayerStats(${player.id})">
                        <i class="fas fa-chart-bar"></i> Stats
                    </button>
                </div>
                <button type="button" class="btn-remove-player" onclick="removeSelectedPlayer(${player.id})">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
    }).join('');
}

// Handle recommend form submit
function handleRecommendSubmit(e) {
    e.preventDefault();
    
    const tournamentId = parseInt(document.getElementById('tournamentSelect').value);
    const reason = document.getElementById('recommendationReason').value;
    
    if (!tournamentId || selectedPlayers.length === 0) {
        alert('Please select a tournament and at least one player');
        return;
    }

    // Update tournament data (in real app, this would be API call)
    const tournament = tournamentsData.find(t => t.id === tournamentId);
    selectedPlayers.forEach(playerId => {
        const player = playersData.find(p => p.id === playerId);
        if (!tournament.recommendedPlayers.includes(player.name)) {
            tournament.recommendedPlayers.push(player.name);
        }
    });

    // Show success message
    alert(`Successfully recommended ${selectedPlayers.length} player(s) for ${tournament.name}`);
    
    // Reload tournaments and close modal
    loadTournaments();
    closeRecommendModal();
}

// View player stats
function viewPlayerStats(playerId) {
    const player = playersData.find(p => p.id === playerId);
    if (!player) return;

    const modal = document.getElementById('playerStatsModal');
    const title = document.getElementById('playerStatsTitle');
    const body = document.getElementById('playerStatsBody');

    title.textContent = `${player.name} - Statistics`;

    body.innerHTML = `
        <div class="player-stats-content">
            <div class="player-stats-header">
                <div class="player-avatar-large">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="player-basic-stats">
                    <h3>${player.name}</h3>
                    <p>Age: ${player.age} | Category: ${player.category.toUpperCase()}</p>
                    <span class="fitness-badge ${player.fitnessStatus}">${player.fitnessStatus.replace('_', ' ')}</span>
                    <span class="availability-badge ${player.availability}">${player.availability}</span>
                </div>
            </div>

            <div class="stats-overview">
                <div class="stat-box">
                    <i class="fas fa-clipboard-list"></i>
                    <h4>${player.stats.matchesPlayed}</h4>
                    <p>Matches Played</p>
                </div>
            </div>

            <div class="stats-section">
                <h4><i class="fas fa-baseball-ball"></i> Batting Statistics</h4>
                <div class="stats-grid">
                    <div class="stat-item">
                        <label>Average:</label>
                        <span>${player.stats.batting.average}</span>
                    </div>
                    <div class="stat-item">
                        <label>High Score:</label>
                        <span>${player.stats.batting.highScore}</span>
                    </div>
                    <div class="stat-item">
                        <label>Strike Rate:</label>
                        <span>${player.stats.batting.strikeRate}</span>
                    </div>
                </div>
            </div>

            <div class="stats-section">
                <h4><i class="fas fa-bowling-ball"></i> Bowling Statistics</h4>
                <div class="stats-grid">
                    <div class="stat-item">
                        <label>Wickets:</label>
                        <span>${player.stats.bowling.wickets}</span>
                    </div>
                    <div class="stat-item">
                        <label>Average:</label>
                        <span>${player.stats.bowling.average}</span>
                    </div>
                    <div class="stat-item">
                        <label>Economy:</label>
                        <span>${player.stats.bowling.economy}</span>
                    </div>
                </div>
            </div>

            <div class="stats-section">
                <h4><i class="fas fa-hand-rock"></i> Fielding Statistics</h4>
                <div class="stats-grid">
                    <div class="stat-item">
                        <label>Catches:</label>
                        <span>${player.stats.fielding.catches}</span>
                    </div>
                    <div class="stat-item">
                        <label>Run Outs:</label>
                        <span>${player.stats.fielding.runOuts}</span>
                    </div>
                </div>
            </div>
        </div>
    `;

    modal.classList.add('active');
}

// Close player stats modal
function closePlayerStatsModal() {
    document.getElementById('playerStatsModal').classList.remove('active');
}

// Helper function to format date
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'short', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

// Close modals when clicking outside
window.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal')) {
        e.target.classList.remove('active');
    }
});
