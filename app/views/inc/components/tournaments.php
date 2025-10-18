<?php
/**
 * Common Tournaments Component
 * This component provides a unified tournaments interface that can be used
 * across admin, player, coach, and trainer dashboards
 */

// Default values if not set
$userRole = $userRole ?? 'player';
$tournamentsData = $tournamentsData ?? [];
$eventsData = $eventsData ?? [];
$stats = $stats ?? [
    'upcoming' => 5,
    'enrolled' => 3,
    'completed' => 12,
    'rewards' => 850
];
?>

<!-- Common Tournaments Content -->
<div class="tournaments-dashboard">
    <!-- Enhanced Content Header -->
    <div class="content-header">
        <div>
            <h1><i class="fas fa-medal"></i> Tournaments & Events</h1>
            <p><?php echo getTournamentsDescription($userRole); ?></p>
        </div>
        <?php if ($userRole === 'admin'): ?>
        <div class="header-actions">
            <button class="btn btn-primary" onclick="openCreateEventModal()">
                <i class="fas fa-plus"></i> Create Event
            </button>
            <button class="btn btn-secondary" onclick="openCreateTournamentModal()">
                <i class="fas fa-trophy"></i> Create Tournament
            </button>
        </div>
        <?php endif; ?>
    </div>

    <!-- Tournament Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon upcoming">
                <i class="fas fa-calendar-plus"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?php echo $stats['upcoming']; ?></div>
                <div class="stat-label">Upcoming Tournaments</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon enrolled">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?php echo $stats['enrolled']; ?></div>
                <div class="stat-label"><?php echo $userRole === 'admin' ? 'Active Tournaments' : 'Enrolled'; ?></div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon completed">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?php echo $stats['completed']; ?></div>
                <div class="stat-label">Completed</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon rewards">
                <i class="fas fa-award"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?php echo $stats['rewards']; ?></div>
                <div class="stat-label"><?php echo $userRole === 'admin' ? 'Total Revenue' : 'Reward Points'; ?></div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="filter-tabs">
        <button class="filter-tab active" data-filter="all">
            <i class="fas fa-list"></i> All Tournaments
        </button>
        <button class="filter-tab" data-filter="upcoming">
            <i class="fas fa-clock"></i> Upcoming
        </button>
        <button class="filter-tab" data-filter="enrolled">
            <i class="fas fa-user-check"></i> <?php echo $userRole === 'admin' ? 'Active' : 'Enrolled'; ?>
        </button>
        <button class="filter-tab" data-filter="completed">
            <i class="fas fa-check-circle"></i> Completed
        </button>
    </div>

    <!-- Search and Filters -->
    <div class="search-filters">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="tournament-search" placeholder="Search tournaments...">
        </div>
        <div class="filter-dropdown">
            <select id="category-filter">
                <option value="">All Categories</option>
                <option value="championship">Championship</option>
                <option value="league">League</option>
                <option value="friendly">Friendly</option>
                <option value="training">Training</option>
            </select>
        </div>
    </div>

    <!-- Tournaments Grid -->
    <div class="tournaments-grid">
        <?php
        // Sample tournament data - this would come from your database
        $sampleTournaments = getSampleTournaments($userRole);
        foreach ($sampleTournaments as $tournament):
        ?>
        <div class="tournament-card <?php echo $tournament['status']; ?>" data-tournament-id="<?php echo $tournament['id']; ?>">
            <div class="tournament-header">
                <h3 class="tournament-title"><?php echo htmlspecialchars($tournament['title']); ?></h3>
                <p class="tournament-date"><?php echo htmlspecialchars($tournament['date']); ?></p>
            </div>
            
            <div class="tournament-body">
                <div class="tournament-info">
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?php echo htmlspecialchars($tournament['location']); ?></span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-users"></i>
                        <span><?php echo $tournament['teams']; ?> Teams</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-trophy"></i>
                        <span>$<?php echo number_format($tournament['prize']); ?> Prize</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-calendar"></i>
                        <span class="status-badge <?php echo $tournament['status']; ?>">
                            <?php echo ucfirst($tournament['status']); ?>
                        </span>
                    </div>
                </div>

                <div class="tournament-description">
                    <p><?php echo htmlspecialchars($tournament['description']); ?></p>
                </div>

                <div class="tournament-actions">
                    <?php if ($userRole === 'admin'): ?>
                        <button class="btn btn-primary" onclick="editTournament(<?php echo $tournament['id']; ?>)">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-secondary" onclick="viewParticipants(<?php echo $tournament['id']; ?>)">
                            <i class="fas fa-users"></i> Participants
                        </button>
                    <?php else: ?>
                        <button class="btn btn-primary btn-view-details" data-tournament-id="<?php echo $tournament['id']; ?>">
                            <i class="fas fa-eye"></i> View Details
                        </button>
                        <?php if ($tournament['status'] === 'upcoming'): ?>
                        <button class="btn btn-secondary" onclick="enrollTournament(<?php echo $tournament['id']; ?>)">
                            <i class="fas fa-plus"></i> Enroll
                        </button>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Calendar Integration -->
    <?php if (isset($showCalendar) && $showCalendar): ?>
    <div class="calendar-container">
        <h2><i class="fas fa-calendar-alt"></i> Tournament Calendar</h2>
        <div id="calendar"></div>
    </div>
    <?php endif; ?>
</div>

<?php
/**
 * Helper functions for the tournaments component
 */
function getTournamentsDescription($userRole) {
    switch ($userRole) {
        case 'admin':
            return 'Manage cricket academy events, tournaments, and training sessions';
        case 'coach':
            return 'Monitor tournament schedules and team preparations';
        case 'trainer':
            return 'Track training sessions and performance events';
        default:
            return 'Participate in exciting cricket tournaments and championships';
    }
}

function getSampleTournaments($userRole) {
    // This would typically come from your database
    return [
        [
            'id' => 1,
            'title' => 'Elite Championship 2024',
            'date' => 'March 15-17, 2024',
            'location' => 'Elite Cricket Academy',
            'teams' => 16,
            'prize' => 50000,
            'status' => 'upcoming',
            'description' => 'Premier cricket tournament featuring the best teams from across the region.'
        ],
        [
            'id' => 2,
            'title' => 'Spring League Tournament',
            'date' => 'April 5-7, 2024',
            'location' => 'Community Sports Center',
            'teams' => 12,
            'prize' => 25000,
            'status' => 'upcoming',
            'description' => 'Seasonal league tournament for emerging cricket talents.'
        ],
        [
            'id' => 3,
            'title' => 'Winter Cup 2023',
            'date' => 'December 10-12, 2023',
            'location' => 'Elite Cricket Academy',
            'teams' => 8,
            'prize' => 15000,
            'status' => 'completed',
            'description' => 'Annual winter tournament completed with great success.'
        ],
        [
            'id' => 4,
            'title' => 'Youth Development Cup',
            'date' => 'March 25-26, 2024',
            'location' => 'Youth Cricket Ground',
            'teams' => 10,
            'prize' => 10000,
            'status' => 'enrolled',
            'description' => 'Tournament focused on developing young cricket talent.'
        ]
    ];
}
?>

<style>
/* Component-specific styles */
.filter-tabs {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
    border-bottom: 2px solid #e2e8f0;
    padding-bottom: 1rem;
}

.filter-tab {
    background: none;
    border: none;
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 600;
    color: #718096;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.filter-tab:hover {
    background: rgba(74, 144, 226, 0.1);
    color: #4A90E2;
}

.filter-tab.active {
    background: linear-gradient(135deg, #4A90E2, #357ABD);
    color: white;
    box-shadow: 0 4px 15px rgba(74, 144, 226, 0.3);
}

.search-filters {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
    align-items: center;
}

.search-box {
    position: relative;
    flex: 1;
    max-width: 400px;
}

.search-box i {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #718096;
}

.search-box input {
    width: 100%;
    padding: 12px 15px 12px 45px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    font-size: 1rem;
    transition: all 0.3s ease;
}

.search-box input:focus {
    outline: none;
    border-color: #4A90E2;
    box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
}

.filter-dropdown select {
    padding: 12px 15px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.filter-dropdown select:focus {
    outline: none;
    border-color: #4A90E2;
    box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
}

.header-actions {
    display: flex;
    gap: 1rem;
}

@media (max-width: 768px) {
    .filter-tabs {
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    .search-filters {
        flex-direction: column;
        align-items: stretch;
    }
    
    .search-box {
        max-width: none;
    }
    
    .header-actions {
        flex-direction: column;
        width: 100%;
    }
    
    .content-header {
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
    }
}
</style>