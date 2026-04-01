<?php require_once APPROOT . '/views/inc/components/dashboard_header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/player/coach-sessions.css">
    
<div class="player-layout">
    <!-- Sidebar -->
    <div class="player-sidebar" id="playerSidebar">
        <div class="sidebar-header">
            <div class="player-logo">
                <i class="fas fa-user-graduate"></i>
                <h3>Player Dashboard</h3>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/player" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/player/training" class="nav-link">
                        <i class="fas fa-dumbbell"></i>
                        <span>Training</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/player/performance" class="nav-link">
                        <i class="fas fa-chart-line"></i>
                        <span>Performance</span>
                    </a>
                </li>
                <li class="nav-item active">
                    <a href="<?php echo URLROOT; ?>/player/bookings" class="nav-link">
                        <i class="fas fa-calendar"></i>
                        <span>Bookings</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/player/tournaments" class="nav-link">
                        <i class="fas fa-medal"></i>
                        <span>Tournaments</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/player/medical" class="nav-link">
                        <i class="fas fa-heartbeat"></i>
                        <span>Medical</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/player/payments" class="nav-link">
                        <i class="fas fa-credit-card"></i>
                        <span>Payments</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/player/shopping" class="nav-link">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Shopping</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Profile Section -->
        <div class="profile-section">
            <div class="profile-avatar">
                <i class="fas fa-user"></i>
            </div>
            <div class="profile-name"><?php echo isset($data['player']['name']) ? $data['player']['name'] : 'Player'; ?></div>
            <div class="profile-role"><?php echo isset($data['player']['membership_level']) ? $data['player']['membership_level'] : 'Regular'; ?> Member</div>
            <a href="<?php echo URLROOT; ?>/login/logout" class="action-btn" style="margin-top: 15px;">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Dashboard Header - Using Same Blue Theme as Dashboard -->
        <div class="dashboard-header">
            <div class="header-content">
                <div class="header-text">
                    <div class="breadcrumb">
                        <a href="<?php echo URLROOT; ?>/player/bookings">Bookings</a>
                        <i class="fas fa-chevron-right"></i>
                        <span>Coach Sessions</span>
                    </div>
                    <h1>Book Coach Sessions</h1>
                    <p>Schedule personalized coaching sessions with our expert cricket coaches</p>
                </div>
                <div class="header-actions">
                    <a href="<?php echo URLROOT; ?>/player/bookings" class="btn btn-training">
                        <i class="fas fa-arrow-left"></i>
                        Back to Bookings
                    </a>
                    <button class="btn btn-performance" onclick="showMyCoachBookings()">
                        <i class="fas fa-history"></i>
                        My Coach Bookings
                    </button>
                </div>
            </div>
        </div>


        <!-- Filter Section -->
        <div class="coach-filters">
            <?php if (!$data['canAccessAllCoaches']): ?>
            <div class="subscription-notice" style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 8px; padding: 12px 16px; margin-bottom: 15px; color: #856404;">
                <i class="fas fa-info-circle"></i>
                Your <strong><?= htmlspecialchars($data['planName'] ?: 'Basic') ?></strong> plan only allows booking with your assigned coach(es). 
                <a href="<?php echo URLROOT; ?>/player/payments" style="color: #0056b3;">Upgrade to Premium or Standard</a> to access all coaches.
            </div>
            <?php endif; ?>
            <div class="filter-group">
                <div class="filter-item">
                    <label for="coach-filter">Select Coach</label>
                    <select id="coach-filter" onchange="applyCoachFilters()">
                        <?php if ($data['canAccessAllCoaches']): ?>
                            <option value="">All Coaches</option>
                        <?php endif; ?>
                        <?php if (!empty($data['coaches'])): ?>
                            <?php foreach ($data['coaches'] as $coach): ?>
                                <option value="<?= $coach->coach_id ?>"><?= htmlspecialchars($coach->name) ?></option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="">No coaches assigned</option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="filter-item">
                    <label for="date-filter">Select Date</label>
                    <input type="date" id="date-filter" onchange="applyCoachFilters()" min="<?= date('Y-m-d') ?>">
                </div>
                <div class="filter-item">
                    <label for="session-type-filter">Session Type</label>
                    <select id="session-type-filter" onchange="applyCoachFilters()">
                        <option value="">All Types</option>
                        <option value="Group">Group Sessions</option>
                        <option value="Private">Private Sessions</option>
                    </select>
                </div>
                <div class="filter-item">
                    <label for="specialization-filter">Specialization</label>
                    <select id="specialization-filter" onchange="applyCoachFilters()">
                        <option value="">All Specializations</option>
                        <option value="Batting">Batting Specialist</option>
                        <option value="Bowling">Bowling Specialist</option>
                        <option value="Wicket-keeping">Wicket-keeping</option>
                        <option value="All-rounder">All-rounder Coaching</option>
                    </select>
                </div>
                <div class="filter-item">
                    <button class="btn btn-secondary" onclick="clearCoachFilters()">
                        <i class="fas fa-refresh"></i> Clear Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Coaches Overview -->
        <div class="coaches-overview">
            <h2><i class="fas fa-users"></i> <?= $data['canAccessAllCoaches'] ? 'Our Expert Coaches' : 'Your Assigned Coach(es)' ?></h2>
            <div class="coaches-grid" id="coaches-grid">
                <?php if (!empty($data['coaches'])): ?>
                    <?php foreach ($data['coaches'] as $coach): ?>
                    <div class="coach-card" data-coach-id="<?= $coach->coach_id ?>" data-specialization="<?= htmlspecialchars($coach->specialization ?? '') ?>">
                        <div class="coach-avatar">
                            <?php if (!empty($coach->image) && $coach->image !== 'default-profile.jpg'): ?>
                                <img src="<?php echo URLROOT; ?>/uploads/<?= htmlspecialchars($coach->image) ?>" alt="<?= htmlspecialchars($coach->name) ?>">
                            <?php else: ?>
                                <i class="fas fa-user-tie"></i>
                            <?php endif; ?>
                        </div>
                        <div class="coach-info">
                            <h3><?= htmlspecialchars($coach->name) ?></h3>
                            <p class="coach-specialization"><i class="fas fa-star"></i> <?= htmlspecialchars($coach->specialization ?? 'General') ?></p>
                            <p class="coach-experience"><i class="fas fa-clock"></i> <?= $coach->experience_years ?? 0 ?> years experience</p>
                            <?php if (!empty($coach->Certifications)): ?>
                                <p class="coach-certifications"><i class="fas fa-certificate"></i> <?= htmlspecialchars($coach->Certifications) ?></p>
                            <?php endif; ?>
                        </div>
                        <button class="btn btn-primary btn-sm" onclick="selectCoach(<?= $coach->coach_id ?>)">
                            <i class="fas fa-calendar-plus"></i> Book Session
                        </button>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-coaches-message" style="text-align: center; padding: 40px; color: #666;">
                        <i class="fas fa-user-slash" style="font-size: 48px; margin-bottom: 15px; color: #ccc;"></i>
                        <h3>No Coaches Available</h3>
                        <p>No coaches are currently assigned to you. Please contact the academy administration.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Available Coach Sessions -->
        <div class="available-coach-sessions">
            <div class="section-header">
                <h2><i class="fas fa-calendar-alt"></i> Available Coach Sessions</h2>
                <div class="session-stats">
                    <span class="stat-item">
                        <span class="stat-number" id="total-coach-sessions">0</span>
                        <span class="stat-label">Available Sessions</span>
                    </span>
                    <span class="stat-item">
                        <span class="stat-number" id="filtered-coach-sessions">0</span>
                        <span class="stat-label">Filtered Results</span>
                    </span>
                </div>
            </div>
            
            <div class="coach-sessions-container" id="coach-sessions-container">
                <?php if (!empty($data['availableSlots'])): ?>
                    <?php foreach ($data['availableSlots'] as $slot): ?>
                    <div class="session-slot-card" 
                         data-coach-id="<?= $slot->coach_id ?>" 
                         data-date="<?= $slot->date ?>" 
                         data-session-type="<?= htmlspecialchars($slot->session_mode ?? '') ?>" 
                         data-specialization="<?= htmlspecialchars($slot->coach_specialization ?? '') ?>">
                        <div class="session-slot-header">
                            <div class="session-type-badge <?= ($slot->session_mode === 'Private') ? 'badge-private' : 'badge-group' ?>">
                                <i class="fas fa-<?= ($slot->session_mode === 'Private') ? 'user' : 'users' ?>"></i>
                                <?= htmlspecialchars($slot->session_mode ?? 'Group') ?> Session
                            </div>
                            <span class="session-price">
                                <?= $slot->price ? 'Rs. ' . number_format($slot->price, 2) : 'Free' ?>
                            </span>
                        </div>
                        <div class="session-slot-body">
                            <h3 class="session-title"><?= htmlspecialchars($slot->description ?? 'Coaching Session') ?></h3>
                            <div class="session-details">
                                <div class="detail-item">
                                    <i class="fas fa-user-tie"></i>
                                    <span><?= htmlspecialchars($slot->coach_name) ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-star"></i>
                                    <span><?= htmlspecialchars($slot->coach_specialization ?? 'General') ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-calendar"></i>
                                    <span><?= date('M d, Y', strtotime($slot->date)) ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-clock"></i>
                                    <span><?= date('g:i A', strtotime($slot->start_time)) ?> - <?= date('g:i A', strtotime($slot->end_time)) ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?= htmlspecialchars($slot->location ?? 'TBA') ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-users"></i>
                                    <span><?= $slot->current_bookings ?>/<?= $slot->max_participants ?> spots filled</span>
                                </div>
                            </div>
                        </div>
                        <div class="session-slot-footer">
                            <span class="spots-left">
                                <?php $spotsLeft = $slot->max_participants - $slot->current_bookings; ?>
                                <?= $spotsLeft ?> spot<?= $spotsLeft !== 1 ? 's' : '' ?> left
                            </span>
                            <button class="btn btn-primary btn-sm" onclick="bookSession(<?= $slot->slot_id ?>)">
                                <i class="fas fa-calendar-check"></i> Book Now
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div class="no-sessions" id="no-coach-sessions" style="display: <?= empty($data['availableSlots']) ? 'block' : 'none' ?>;">
                <div class="no-sessions-content">
                    <i class="fas fa-calendar-times"></i>
                    <h3>No Coach Sessions Available</h3>
                    <p>Try adjusting your filters or check back later for new coaching sessions.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Coach Session Booking Modal -->


<script type="application/json" id="coachSessionData"><?php echo json_encode([
    'coaches' => $data['coaches'],
    'availableSlots' => $data['availableSlots'] ?? [],
    'sessionTypes' => $data['sessionTypes'],
    'canAccessAllCoaches' => $data['canAccessAllCoaches'],
    'planName' => $data['planName'],
    'urlRoot' => URLROOT,
], JSON_UNESCAPED_SLASHES); ?></script>
<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>
<script src="<?php echo URLROOT; ?>/js/player/dashboard.js"></script>
<script src="<?php echo URLROOT; ?>/js/player/coach-sessions.js"></script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>