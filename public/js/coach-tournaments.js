// Coach Tournaments Page - JavaScript with Dummy Data

// Dummy Data
const tournamentsData = [
    {
        id: 1,
        name: 'Junior Championship 2025',
        category: 'junior',
        startDate: '2025-11-15',
        endDate: '2025-11-20',
        location: 'Elite Cricket Ground A',
        status: 'upcoming',
        registrationDeadline: '2025-11-01',
        maxTeamSize: 15,
        description: 'Annual junior cricket championship for players under 15 years.',
        organizer: 'Elite Cricket Academy',
        recommendedPlayers: ['Alex Smith', 'Emma Davis'],
        selectedPlayers: [],
        selectionStatus: 'pending'
    },
    {
        id: 2,
        name: 'Regional Tournament 2025',
        category: 'senior',
        startDate: '2025-11-25',
        endDate: '2025-11-28',
        location: 'Regional Stadium',
        status: 'upcoming',
        registrationDeadline: '2025-11-10',
        maxTeamSize: 15,
        description: 'Regional level tournament for senior players (U-19).',
        organizer: 'Regional Sports Council',
        recommendedPlayers: ['Sarah Wilson'],
        selectedPlayers: ['Sarah Wilson'],
        selectionStatus: 'in_progress'
    },
    {
        id: 3,
        name: 'Summer Cricket League',
        category: 'open',
        startDate: '2025-12-05',
        endDate: '2025-12-15',
        location: 'City Sports Complex',
        status: 'upcoming',
        registrationDeadline: '2025-11-20',
        maxTeamSize: 20,
        description: 'Open category summer league with multiple divisions.',
        organizer: 'City Cricket Association',
        recommendedPlayers: [],
        selectedPlayers: [],
        selectionStatus: 'open'
    },
    {
        id: 4,
        name: 'Inter-Academy Championship',
        category: 'junior',
        startDate: '2025-10-20',
        endDate: '2025-10-22',
        location: 'Elite Cricket Ground B',
        status: 'ongoing',
        registrationDeadline: '2025-10-10',
        maxTeamSize: 15,
        description: 'Championship between multiple cricket academies.',
        organizer: 'Cricket Academy Federation',
        recommendedPlayers: ['Alex Smith', 'James Brown', 'Emily Clark'],
        selectedPlayers: ['Alex Smith', 'James Brown'],
        selectionStatus: 'finalized'
    },
    {
        id: 5,
        name: 'Spring Tournament 2025',
        category: 'senior',
        startDate: '2025-09-10',
        endDate: '2025-09-15',
        location: 'Spring Cricket Ground',
        status: 'completed',
        registrationDeadline: '2025-08-25',
        maxTeamSize: 15,
        description: 'Completed spring tournament with excellent participation.',
        organizer: 'Elite Cricket Academy',
        recommendedPlayers: ['Sarah Wilson', 'Michael Lee'],
        selectedPlayers: ['Sarah Wilson', 'Michael Lee'],
        selectionStatus: 'finalized',
        results: {
            position: '2nd Place',
            totalMatches: 8,
            won: 6,
            lost: 2
        }
    }
];

const playersData = [
    {
        id: 1,
        name: 'Alex Smith',
        age: 14,
        category: 'junior',
        stats: {
            matchesPlayed: 25,
            batting: { average: 45.5, highScore: 98, strikeRate: 125.3 },
            bowling: { wickets: 12, average: 28.5, economy: 4.2 },
            fielding: { catches: 8, runOuts: 3 }
        },
        availability: 'available',
        fitnessStatus: 'fit'
    },
    {
        id: 2,
        name: 'Emma Davis',
        age: 13,
        category: 'junior',
        stats: {
            matchesPlayed: 22,
            batting: { average: 38.2, highScore: 75, strikeRate: 110.5 },
            bowling: { wickets: 18, average: 22.3, economy: 3.8 },
            fielding: { catches: 12, runOuts: 5 }
        },
        availability: 'available',
        fitnessStatus: 'fit'
    },
    {
        id: 3,
        name: 'Sarah Wilson',
        age: 17,
        category: 'senior',
        stats: {
            matchesPlayed: 35,
            batting: { average: 52.8, highScore: 125, strikeRate: 135.2 },
            bowling: { wickets: 25, average: 20.1, economy: 3.5 },
            fielding: { catches: 18, runOuts: 7 }
        },
        availability: 'available',
        fitnessStatus: 'fit'
    },
    {
        id: 4,
        name: 'James Brown',
        age: 14,
        category: 'junior',
        stats: {
            matchesPlayed: 20,
            batting: { average: 35.5, highScore: 82, strikeRate: 115.8 },
            bowling: { wickets: 15, average: 25.5, economy: 4.5 },
            fielding: { catches: 6, runOuts: 2 }
        },
        availability: 'available',
        fitnessStatus: 'fit'
    },
    {
        id: 5,
        name: 'Emily Clark',
        age: 13,
        category: 'junior',
        stats: {
            matchesPlayed: 18,
            batting: { average: 30.2, highScore: 65, strikeRate: 105.3 },
            bowling: { wickets: 10, average: 30.2, economy: 5.0 },
            fielding: { catches: 5, runOuts: 1 }
        },
        availability: 'available',
        fitnessStatus: 'minor_injury'
    },
    {
        id: 6,
        name: 'Michael Lee',
        age: 18,
        category: 'senior',
        stats: {
            matchesPlayed: 40,
            batting: { average: 48.5, highScore: 110, strikeRate: 128.5 },
            bowling: { wickets: 30, average: 18.8, economy: 3.2 },
            fielding: { catches: 22, runOuts: 9 }
        },
        availability: 'available',
        fitnessStatus: 'fit'
    }
];

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
