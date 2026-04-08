<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/trainer/supplements.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/common/modal.css">
<!-- Mobile-specific meta tags -->
<meta name="theme-color" content="#2c3e50">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="mobile-web-app-capable" content="yes">

    <!-- Trainer Layout -->
    <div class="player-layout supplement-page">
        <!-- Left Sidebar Panel -->
        <div class="trainer-sidebar" id="trainerSidebar">
            <div class="sidebar-header">
                <div class="trainer-logo">
                    <i class="fas fa-user-tie"></i>
                    <h3>Trainer Dashboard</h3>
                </div>
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <nav class="sidebar-nav">
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/trainer" class="nav-link">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/trainer/bookings" class="nav-link">
                            <i class="fas fa-calendar-check"></i>
                            <span>Schedule & Bookings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/staffslots/calendar" class="nav-link">
                            <i class="fas fa-calendar-check"></i>
                            <span>My Slot Sessions</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/trainer/workout" class="nav-link">
                            <i class="fas fa-dumbbell"></i>
                            <span>Workout Plans</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/trainer/nutrition" class="nav-link">
                            <i class="fas fa-apple-alt"></i>
                            <span>Nutrition Plans</span>
                        </a>
                    </li>
                    <li class="nav-item active">
                        <a href="<?php echo URLROOT; ?>/trainer/supplements" class="nav-link">
                            <i class="fas fa-capsules"></i>
                            <span>Supplements</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo URLROOT; ?>/trainer/injury_reports" class="nav-link">
                            <i class="fas fa-user-injured"></i>
                            <span>Injury Reports</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Profile Section -->
            <div class="trainer-profile">
                <div class="trainer-avatar">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="trainer-name"><?php echo $_SESSION['username'] ?? 'Trainer'; ?></div>
                <div class="trainer-role">Fitness Trainer</div>
                <div class="profile-actions">
                    <a href="<?php echo URLROOT; ?>/trainer/profile" class="profile-btn" title="Profile">
                        <i class="fas fa-user-cog"></i>
                    </a>
                    <a href="<?php echo URLROOT; ?>/login/logout" class="logout-btn" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="main-content" id="mainContent">
            <!-- Dashboard Header -->
            <div class="dashboard-header">
                <div class="header-content">
                    <div class="header-text">
                        <h1><i class="fas fa-capsules"></i> Supplement Plans Management</h1>
                        <p>Create and manage customized supplement plans for your trainees</p>
                    </div>
                    <div class="header-actions">
                        <button class="btn btn-training plan-cta-btn js-plan-cta" onclick="openAddSupplementPlanModal()">
                            <i class="fas fa-plus"></i>Add New Plan
                        </button>
                        <button class="btn btn-refresh" onclick="location.reload()">
                            <i class="fas fa-sync-alt"></i>
                            <div class="current-time"><?php echo date('H:i'); ?></div>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php flash('supplement_message'); ?>

            <!-- Supplement Plans Table Card -->
            <div class="schedule-card">
                <div class="card-header">
                    <div class="header-content">
                        <h2><i class="fas fa-capsules"></i> Your Supplement Plans</h2>
                        <div class="table-controls" style="display: flex; gap: 15px; align-items: center;">
                            <div style="position: relative;">
                                <i class="fas fa-search" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #666;"></i>
                                <input type="text" id="searchInput" placeholder="Search plans..." style="padding: 8px 12px 8px 35px; border: 1px solid rgba(255,255,255,0.3); border-radius: 20px; background: rgba(255,255,255,0.2); color: white; font-size: 13px;">
                            </div>
                            <select id="statusFilter" style="padding: 8px 15px; border: 1px solid rgba(255,255,255,0.3); border-radius: 20px; background: rgba(255,255,255,0.2); color: white; font-size: 13px;">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card-content">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Plan ID</th>
                                <th>Player Details</th>
                                <th>Supplement Info</th>
                                <th>Dosage & Duration</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($data['supplement_plans'])): ?>
                                <?php foreach($data['supplement_plans'] as $plan): ?>
                                    <tr data-plan-id="<?php echo $plan->PlanID; ?>">
                                        <td>
                                            <div class="table-cell-primary">#<?php echo str_pad($plan->PlanID, 4, '0', STR_PAD_LEFT); ?></div>
                                            <div class="table-cell-secondary"><?php echo date('M d, Y', strtotime($plan->CreatedDate)); ?></div>
                                        </td>
                                        <td>
                                            <div class="table-cell-title"><?php echo htmlspecialchars($plan->player_name ?? 'Unknown Player'); ?></div>
                                            <div class="table-cell-details">
                                                <i class="fas fa-user"></i>
                                                <?php echo htmlspecialchars($plan->player_email ?? 'No email'); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="table-cell-title">
                                                <?php 
                                                $details = htmlspecialchars($plan->SupplementDetails);
                                                echo strlen($details) > 50 ? substr($details, 0, 50) . '...' : $details; 
                                                ?>
                                            </div>
                                            <div class="table-badge <?php 
                                                if($plan->Status == 'active') echo 'status-active';
                                                elseif($plan->Status == 'inactive') echo 'status-upcoming';
                                                else echo '';
                                            ?>">
                                                <?php echo ucfirst($plan->Status); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="table-cell-primary"><?php echo htmlspecialchars($plan->Dosage); ?></div>
                                            <div class="table-cell-secondary"><?php echo $plan->Duration; ?> days</div>
                                        </td>
                                        <td>
                                            <div class="profile-actions">
                                                <button class="profile-btn" onclick="viewPlanDetails(<?php echo $plan->PlanID; ?>, 'supplement')" title="View Details" style="background: rgba(46, 213, 115, 0.1); color: #2ed573; border-color: rgba(46, 213, 115, 0.3);">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="profile-btn" onclick="editPlan(<?php echo $plan->PlanID; ?>, 'supplement')" title="Edit Plan" style="background: rgba(255, 159, 67, 0.1); color: #ff9f43; border-color: rgba(255, 159, 67, 0.3);">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="profile-btn" onclick="deletePlan(<?php echo $plan->PlanID; ?>, 'supplement')" title="Delete Plan" style="background: rgba(255, 107, 107, 0.1); color: #ff6b6b; border-color: rgba(255, 107, 107, 0.3);">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 40px 20px;">
                                        <div style="color: #666; display: flex; flex-direction: column; align-items: center; gap: 15px;">
                                            <i class="fas fa-capsules" style="font-size: 3rem; color: #4A90E2; margin-bottom: 15px;"></i>
                                            <h3 style="color: #4A90E2; margin-bottom: 8px;">No supplement plans found</h3>
                                            <p style="margin-bottom: 20px;">Start by creating your first supplement plan!</p>
                                            <button class="btn btn-training plan-cta-btn plan-cta-secondary js-plan-cta" onclick="openAddSupplementPlanModal()">
                                                <i class="fas fa-plus"></i>Add Supplement Plan
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Supplement Plan Modal -->
    <div id="addSupplementPlanModal" class="modal">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #4A90E2, #5BA0F2);">
                <h2><i class="fas fa-capsules"></i> Add New Supplement Plan</h2>
                <span class="close" onclick="closeAddSupplementPlanModal()">&times;</span>
            </div>
            <form method="POST" action="<?php echo URLROOT; ?>/trainer/addSupplementPlan">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="player_id" style="color: #333; font-weight: 600; margin-bottom: 8px; display: block;">
                            <i class="fas fa-user" style="color: #4A90E2; margin-right: 8px;"></i>
                            Select Player <span style="color: #ff6b6b;">*</span>
                        </label>
                        <select id="player_id" name="player_id" required 
                                style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; background: white;">
                            <option value="">Choose a player...</option>
                            <?php if (!empty($data['players'])): ?>
                                <?php foreach($data['players'] as $player): ?>
                                    <option value="<?php echo $player->UserID; ?>">
                                        <?php echo htmlspecialchars($player->name); ?> (<?php echo htmlspecialchars($player->email); ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="supplement_details" style="color: #333; font-weight: 600; margin-bottom: 8px; display: block;">
                            <i class="fas fa-capsules" style="color: #4A90E2; margin-right: 8px;"></i>
                            Supplement Details <span style="color: #ff6b6b;">*</span>
                        </label>
                        <textarea id="supplement_details" name="supplement_details" rows="4" required 
                                style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; resize: vertical;"
                                placeholder="Provide detailed supplement plan including supplement names, purposes, instructions, timing, precautions, etc..."></textarea>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label for="dosage" style="color: #333; font-weight: 600; margin-bottom: 8px; display: block;">
                                <i class="fas fa-pills" style="color: #4A90E2; margin-right: 8px;"></i>
                                Dosage <span style="color: #ff6b6b;">*</span>
                            </label>
                            <input type="text" id="dosage" name="dosage" required 
                                   style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;"
                                   placeholder="e.g., 2 tablets daily, 500mg twice daily">
                        </div>
                        <div class="form-group">
                            <label for="duration" style="color: #333; font-weight: 600; margin-bottom: 8px; display: block;">
                                <i class="fas fa-calendar-alt" style="color: #4A90E2; margin-right: 8px;"></i>
                                Duration (Days) <span style="color: #ff6b6b;">*</span>
                            </label>
                            <input type="number" id="duration" name="duration" required min="1" max="365" 
                                   style="width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;"
                                   placeholder="Enter duration in days">
                        </div>
                    </div>
                    
                    <div style="background: #e0f2fe; border: 1px solid #81d4fa; border-radius: 8px; padding: 15px; margin-top: 20px;">
                        <div style="display: flex; align-items: flex-start; gap: 10px;">
                            <i class="fas fa-info-circle" style="color: #0277bd; margin-top: 2px;"></i>
                            <div>
                                <strong style="color: #0277bd;">Supplement Plan Guidelines:</strong>
                                <ul style="margin: 8px 0 0 0; color: #0277bd; font-size: 13px;">
                                    <li>Include specific supplement names and brands</li>
                                    <li>Specify exact dosage and timing</li>
                                    <li>Consider player's dietary restrictions</li>
                                    <li>Include any precautions or side effects</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer" style="background: #f8fafc; padding: 20px 25px; display: flex; justify-content: flex-end; gap: 12px;">
                    <button type="button" onclick="closeAddSupplementPlanModal()" style="background: #e5e7eb; color: #374151; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 500;">
                        Cancel
                    </button>
                    <button type="submit" style="background: linear-gradient(135deg, #4A90E2, #5BA0F2); color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 500; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-save"></i>Create Plan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Plan Details Modal -->
    <div id="viewPlanModal" class="modal">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #4A90E2, #5BA0F2);">
                <h2><i class="fas fa-eye"></i> Supplement Plan Details</h2>
                <span class="close" onclick="closeViewPlanModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div id="viewPlanContent">
                    <!-- Content will be populated by JavaScript -->
                </div>
            </div>
            <div class="modal-footer" style="background: #f8fafc; padding: 20px 25px; display: flex; justify-content: flex-end;">
                <button type="button" onclick="closeViewPlanModal()" style="background: #4A90E2; color: white; border: none; padding: 12px 24px; border-radius: 8px; cursor: pointer; font-weight: 500;">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script>
        function setSupplementPlanButtonsBusy() {
            const buttons = document.querySelectorAll('.js-plan-cta');
            buttons.forEach((button) => {
                if (!button.dataset.originalHtml) {
                    button.dataset.originalHtml = button.innerHTML;
                }

                button.classList.add('is-opening');
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Opening...';
            });

            window.setTimeout(() => {
                buttons.forEach((button) => {
                    button.classList.remove('is-opening');
                    button.disabled = false;
                    if (button.dataset.originalHtml) {
                        button.innerHTML = button.dataset.originalHtml;
                    }
                });
            }, 420);
        }

        // Add Supplement Plan Modal Functions
        function openAddSupplementPlanModal() {
            setSupplementPlanButtonsBusy();
            const modal = document.getElementById('addSupplementPlanModal');
            modal.style.display = 'block';
            setTimeout(() => modal.classList.add('show'), 10);
        }

        function closeAddSupplementPlanModal() {
            const modal = document.getElementById('addSupplementPlanModal');
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = 'none';
                modal.querySelector('form').reset();
            }, 300);
        }

        // View plan details
        function viewPlanDetails(planId, type) {
            const modal = document.getElementById('viewPlanModal');
            const content = document.getElementById('viewPlanContent');
            
            content.innerHTML = `
                <div style="text-align: center; padding: 40px; color: #666;">
                    <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #4A90E2; margin-bottom: 15px;"></i>
                    <p>Loading plan details...</p>
                </div>
            `;
            
            modal.style.display = 'block';
            setTimeout(() => modal.classList.add('show'), 10);
            
            // In a real implementation, this would fetch details via AJAX
            setTimeout(() => {
                content.innerHTML = `
                    <div style="display: grid; gap: 20px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: #f8fafc; border-radius: 8px; border-left: 4px solid #4A90E2;">
                            <label style="font-weight: 600; color: #374151; margin: 0; display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-hashtag" style="color: #4A90E2;"></i>Plan ID:
                            </label>
                            <span style="font-family: 'Courier New', monospace; background: #e0e7ff; color: #3730a3; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                #${String(planId).padStart(4, '0')}
                            </span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: #f8fafc; border-radius: 8px; border-left: 4px solid #4A90E2;">
                            <label style="font-weight: 600; color: #374151; margin: 0; display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-capsules" style="color: #4A90E2;"></i>Plan Type:
                            </label>
                            <span style="font-weight: 600; color: #1f2937; font-size: 16px; text-transform: capitalize;">${type}</span>
                        </div>
                        <div style="padding: 15px; background: #f8fafc; border-radius: 8px; border-left: 4px solid #4A90E2;">
                            <p style="color: #666; margin: 0; font-style: italic;">Full plan details would be loaded here via AJAX in a real implementation...</p>
                        </div>
                    </div>
                `;
            }, 1000);
        }

        function closeViewPlanModal() {
            const modal = document.getElementById('viewPlanModal');
            modal.classList.remove('show');
            setTimeout(() => modal.style.display = 'none', 300);
        }

        // Edit plan
        function editPlan(planId, type) {
            alert(`Edit ${type} plan ${planId} - Feature coming soon!`);
        }

        // Delete plan
        function deletePlan(planId, type) {
            if (confirm(`Are you sure you want to delete this ${type} plan?`)) {
                alert(`Delete ${type} plan ${planId} - Feature coming soon!`);
            }
        }

        // Search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            
            if (searchInput) {
                searchInput.addEventListener('input', filterTable);
            }
            if (statusFilter) {
                statusFilter.addEventListener('change', filterTable);
            }
        });

        function filterTable() {
            const searchTerm = document.getElementById('searchInput')?.value.toLowerCase() || '';
            const statusFilter = document.getElementById('statusFilter')?.value || '';
            const tableRows = document.querySelectorAll('.dashboard-table tbody tr');
            
            tableRows.forEach(row => {
                if (row.querySelector('[colspan]')) return; // Skip no-data row
                
                const playerName = row.querySelector('.table-cell-title')?.textContent.toLowerCase() || '';
                const status = row.querySelector('.table-badge')?.textContent.toLowerCase() || '';
                
                const matchesSearch = playerName.includes(searchTerm);
                const matchesStatus = !statusFilter || status.includes(statusFilter);
                
                row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
            });
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const addModal = document.getElementById('addSupplementPlanModal');
            const viewModal = document.getElementById('viewPlanModal');
            
            if (event.target === addModal) {
                closeAddSupplementPlanModal();
            }
            if (event.target === viewModal) {
                closeViewPlanModal();
            }
        }
    </script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>