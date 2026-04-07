<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-dashboard.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-tournaments.css">

    <!-- Coach Dashboard Layout -->
    <div class="coach-layout">
        <!-- Left Sidebar Panel -->
        <div class="coach-sidebar" id="coachSidebar">
            <div class="sidebar-header">
                <div class="coach-logo">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <h3>Coach Panel</h3>
                </div>
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-angle-left"></i>
                </button>
            </div>
            
            <nav class="sidebar-nav">
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/dashboard" class="nav-link" data-tooltip="Dashboard">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/sessions" class="nav-link" data-tooltip="Sessions">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Sessions</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/players" class="nav-link" data-tooltip="Players">
                            <i class="fas fa-users"></i>
                            <span>Players</span>
                        </a>
                    </li>
                    
                    <li class="nav-item active">
                        <a href="<?php echo URLROOT; ?>/coach/tournaments" class="nav-link" data-tooltip="Tournaments">
                            <i class="fas fa-trophy"></i>
                            <span>Tournaments</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/tournament-recommendations" class="nav-link" data-tooltip="Recommendations">
                            <i class="fas fa-star"></i>
                            <span>Recommendations</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/health" class="nav-link" data-tooltip="Health & Injury">
                            <i class="fas fa-heartbeat"></i>
                            <span>Health & Injury</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/notifications" class="nav-link" data-tooltip="Notifications">
                            <i class="fas fa-bell"></i>
                            <span>Notifications</span>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/coach/events" class="nav-link" data-tooltip="Events">
                            <i class="fas fa-calendar"></i>
                            <span>Events</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <!-- Main Content Area -->
        <div class="main-content">
            <!-- Page Header -->
            <div class="dashboard-header">
                <div class="header-content">
                    <div class="header-left">
                        <h1>
                            <i class="fas fa-trophy"></i>
                            Tournaments & Team Recommendations
                        </h1>
                        <p style="margin: 0; opacity: 0.9; font-size: 14px;">Manage tournament participation and player recommendations</p>
                    </div>
                    <div class="header-actions">
                    </div>
                </div>
            </div>

            <!-- Tournament Content -->
            <div class="tournament-content">
                <!-- Upcoming Events -->
                <div style="margin-bottom: 30px;">
                    <h3 style="margin: 0 0 16px 0; color: #333; font-size: 18px;">
                        <i class="fas fa-calendar-alt" style="color: #4A90E2; margin-right: 8px;"></i>Upcoming Events
                    </h3>
                    <?php if (!empty($data['upcoming_events'])): ?>
                    <div class="tournaments-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px;">
                        <?php foreach ($data['upcoming_events'] as $event): 
                            $statusColors = ['upcoming' => '#4A90E2', 'ongoing' => '#10b981', 'completed' => '#6b7280'];
                            $eColor = $statusColors[$event['Status']] ?? '#4A90E2';
                        ?>
                        <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); border-top: 4px solid <?php echo $eColor; ?>;">
                            <div style="padding: 20px;">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                                    <h4 style="margin: 0; color: #333; font-size: 16px;"><?php echo htmlspecialchars($event['title']); ?></h4>
                                    <span style="background: <?php echo $eColor; ?>15; color: <?php echo $eColor; ?>; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase;"><?php echo $event['Status']; ?></span>
                                </div>
                                <?php if (!empty($event['description'])): ?>
                                <p style="margin: 0 0 12px 0; color: #666; font-size: 13px; line-height: 1.5;"><?php echo htmlspecialchars(substr($event['description'], 0, 120)); ?><?php echo strlen($event['description']) > 120 ? '...' : ''; ?></p>
                                <?php endif; ?>
                                <div style="display: flex; flex-wrap: wrap; gap: 12px; font-size: 12px; color: #888; margin-bottom: 15px;">
                                    <span><i class="fas fa-calendar" style="margin-right: 4px;"></i><?php echo date('M d, Y', strtotime($event['event_date'])); ?></span>
                                    <?php if (!empty($event['location'])): ?>
                                    <span><i class="fas fa-map-marker-alt" style="margin-right: 4px;"></i><?php echo htmlspecialchars($event['location']); ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($event['event_type'])): ?>
                                    <span><i class="fas fa-tag" style="margin-right: 4px;"></i><?php echo htmlspecialchars(ucfirst($event['event_type'])); ?></span>
                                    <?php endif; ?>
                                </div>
                                <!-- Action Buttons -->
                                <div style="display: flex; gap: 8px;">
                                    <button class="recommend-btn" data-tournament-id="<?php echo htmlspecialchars($event['id'] ?? $event['EventID'] ?? ''); ?>" data-tournament-name="<?php echo htmlspecialchars($event['title']); ?>" style="flex: 1; padding: 8px 12px; background: #4A90E2; color: white; border: none; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.2s;">
                                        <i class="fas fa-star"></i> Recommend Players
                                    </button>
                                    <a href="<?php echo URLROOT; ?>/coach/tournament-recommendations" style="flex: 1; padding: 8px 12px; background: #f0f0f0; color: #333; border: none; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 6px; transition: all 0.2s;">
                                        <i class="fas fa-list"></i> View All
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div style="text-align: center; padding: 40px; background: white; border-radius: 12px; color: #999;">
                        <i class="fas fa-calendar-times" style="font-size: 36px; margin-bottom: 12px; opacity: 0.3;"></i>
                        <p style="margin: 0;">No upcoming events scheduled</p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Past Events -->
                <div>
                    <h3 style="margin: 0 0 16px 0; color: #333; font-size: 18px;">
                        <i class="fas fa-history" style="color: #6b7280; margin-right: 8px;"></i>Past Events
                    </h3>
                    <?php if (!empty($data['past_events'])): ?>
                    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                    <th style="padding: 14px 16px; text-align: left; font-size: 13px; color: #64748b;">Event</th>
                                    <th style="padding: 14px 16px; text-align: left; font-size: 13px; color: #64748b;">Date</th>
                                    <th style="padding: 14px 16px; text-align: left; font-size: 13px; color: #64748b;">Location</th>
                                    <th style="padding: 14px 16px; text-align: left; font-size: 13px; color: #64748b;">Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['past_events'] as $event): ?>
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 14px 16px; font-weight: 500; color: #333;"><?php echo htmlspecialchars($event['title']); ?></td>
                                    <td style="padding: 14px 16px; color: #666;"><?php echo date('M d, Y', strtotime($event['event_date'])); ?></td>
                                    <td style="padding: 14px 16px; color: #666;"><?php echo htmlspecialchars($event['location'] ?? 'N/A'); ?></td>
                                    <td style="padding: 14px 16px; color: #666;"><?php echo htmlspecialchars(ucfirst($event['event_type'] ?? 'N/A')); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div style="text-align: center; padding: 40px; background: white; border-radius: 12px; color: #999;">
                        <i class="fas fa-archive" style="font-size: 36px; margin-bottom: 12px; opacity: 0.3;"></i>
                        <p style="margin: 0;">No past events found</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Recommendation Modal -->
    <div class="modal" id="quickRecommendationModal">
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <h2>Recommend Players</h2>
                <button class="modal-close" id="quickModalClose">&times;</button>
            </div>
            <div class="modal-body">
                <p id="tournamentInfo" style="margin: 0 0 20px 0; color: #666; font-size: 14px;"></p>
                <form id="quickRecommendationForm">
                    <input type="hidden" id="quickTournamentId" name="tournamentId">
                    
                    <div class="form-group" style="margin-bottom: 16px;">
                        <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Player <span style="color: #e74c3c;">*</span></label>
                        <select id="quickPlayerSelect" name="playerId" style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;" required>
                            <option value="">Select a player...</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 16px;">
                        <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Role <span style="color: #e74c3c;">*</span></label>
                        <select id="quickRoleSelect" name="recommendedRole" style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;" required>
                            <option value="">Select a role...</option>
                            <option value="batsman">Batsman - Primary batting focus</option>
                            <option value="bowler">Bowler - Primary bowling focus</option>
                            <option value="all-rounder">All-rounder - Both batting and bowling</option>
                            <option value="wicket-keeper">Wicket-keeper - Wicket-keeping specialist</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #333;">Reason</label>
                        <textarea id="quickReasonInput" name="reason" style="width: 100%; padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; font-family: inherit;" rows="3" placeholder="Why do you recommend this player?"></textarea>
                    </div>

                    <div style="display: flex; gap: 10px; justify-content: flex-end;">
                        <button type="button" class="btn btn-outline" id="quickModalCancelBtn" style="padding: 8px 20px; background: #f0f0f0; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Cancel</button>
                        <button type="submit" style="padding: 8px 20px; background: #4A90E2; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                            <i class="fas fa-check"></i> Recommend
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Overlay -->
    <div class="modal-overlay" id="quickModalOverlay"></div>

<script>
// Quick Recommendation Modal Handler
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('quickRecommendationModal');
    const modalOverlay = document.getElementById('quickModalOverlay');
    const closeBtn = document.getElementById('quickModalClose');
    const cancelBtn = document.getElementById('quickModalCancelBtn');
    const form = document.getElementById('quickRecommendationForm');
    const recommendBtns = document.querySelectorAll('.recommend-btn');

    function openModal(tournamentId, tournamentName) {
        document.getElementById('quickTournamentId').value = tournamentId;
        document.getElementById('tournamentInfo').textContent = `You are recommending players for: ${tournamentName}`;
        modal.style.display = 'flex';
        modalOverlay.style.display = 'block';
        
        // Load assigned players
        fetch('<?php echo URLROOT; ?>/coach/assigned-players')
            .then(r => r.json())
            .then(data => {
                const playerSelect = document.getElementById('quickPlayerSelect');
                playerSelect.innerHTML = '<option value="">Select a player...</option>';
                if (data.success && data.players) {
                    data.players.forEach(player => {
                        const option = document.createElement('option');
                        option.value = player.PlayerID;
                        option.textContent = player.Name || `Player ${player.PlayerID}`;
                        playerSelect.appendChild(option);
                    });
                }
            });
    }

    function closeModal() {
        modal.style.display = 'none';
        modalOverlay.style.display = 'none';
        form.reset();
    }

    recommendBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const tournamentId = this.getAttribute('data-tournament-id');
            const tournamentName = this.getAttribute('data-tournament-name');
            openModal(tournamentId, tournamentName);
        });
    });

    closeBtn?.addEventListener('click', closeModal);
    cancelBtn?.addEventListener('click', closeModal);
    modalOverlay?.addEventListener('click', closeModal);

    form?.addEventListener('submit', function(e) {
        e.preventDefault();
        const tournamentId = document.getElementById('quickTournamentId').value;
        const playerId = document.getElementById('quickPlayerSelect').value;
        const role = document.getElementById('quickRoleSelect').value;
        const reason = document.getElementById('quickReasonInput').value;

        fetch('<?php echo URLROOT; ?>/coach/save-recommendation', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                tournamentId: parseInt(tournamentId),
                playerId: parseInt(playerId),
                recommendedRole: role,
                reason: reason
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert('Recommendation saved successfully!');
                closeModal();
                window.location.href = '<?php echo URLROOT; ?>/coach/tournament-recommendations';
            } else {
                alert('Error: ' + (data.message || 'Failed to save recommendation'));
            }
        })
        .catch(err => alert('Error: ' + err.message));
    });
});
</script>

<script>
// Sidebar Toggle
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('coachSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mainContent = document.querySelector('.main-content');
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            const icon = this.querySelector('i');
            if (sidebar.classList.contains('collapsed')) {
                icon.classList.remove('fa-angle-left');
                icon.classList.add('fa-angle-right');
                mainContent.style.marginLeft = '80px';
            } else {
                icon.classList.remove('fa-angle-right');
                icon.classList.add('fa-angle-left');
                mainContent.style.marginLeft = '280px';
            }
        });
    }
});
</script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
