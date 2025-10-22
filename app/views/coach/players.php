<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-dashboard.css">

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
                
                <li class="nav-item active">
                    <a href="<?php echo URLROOT; ?>/coach/players" class="nav-link" data-tooltip="Players">
                        <i class="fas fa-users"></i>
                        <span>Players</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/tournaments" class="nav-link" data-tooltip="Tournaments">
                        <i class="fas fa-trophy"></i>
                        <span>Tournaments</span>
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
                        <i class="fas fa-users"></i>
                        Player Management
                    </h1>
                    <p style="margin: 0; opacity: 0.9; font-size: 14px;">View and manage your players</p>
                </div>
                <div class="header-actions">
                    <button class="btn-primary">
                        <i class="fas fa-file-export"></i>
                        Export List
                    </button>
                    <button class="btn-secondary">
                        <i class="fas fa-filter"></i>
                        Filter
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div class="stat-card" style="background: linear-gradient(135deg, #4A90E2, #357ABD); color: white; padding: 24px; border-radius: 16px; box-shadow: 0 4px 15px rgba(74, 144, 226, 0.3);">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div>
                        <h3 style="margin: 0; font-size: 32px; font-weight: 700;">24</h3>
                        <p style="margin: 8px 0 0 0; opacity: 0.9; font-size: 14px;">Total Players</p>
                    </div>
                    <div style="width: 50px; height: 50px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-users" style="font-size: 24px;"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card" style="background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 24px; border-radius: 16px; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div>
                        <h3 style="margin: 0; font-size: 32px; font-weight: 700;">18</h3>
                        <p style="margin: 8px 0 0 0; opacity: 0.9; font-size: 14px;">Active Players</p>
                    </div>
                    <div style="width: 50px; height: 50px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-user-check" style="font-size: 24px;"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white; padding: 24px; border-radius: 16px; box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div>
                        <h3 style="margin: 0; font-size: 32px; font-weight: 700;">6</h3>
                        <p style="margin: 8px 0 0 0; opacity: 0.9; font-size: 14px;">On Leave</p>
                    </div>
                    <div style="width: 50px; height: 50px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-user-clock" style="font-size: 24px;"></i>
                    </div>
                </div>
            </div>

            <div class="stat-card" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; padding: 24px; border-radius: 16px; box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div>
                        <h3 style="margin: 0; font-size: 32px; font-weight: 700;">85%</h3>
                        <p style="margin: 8px 0 0 0; opacity: 0.9; font-size: 14px;">Avg Attendance</p>
                    </div>
                    <div style="width: 50px; height: 50px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-chart-line" style="font-size: 24px;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Players Table -->
        <div class="table-container" style="background: white; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); overflow: hidden;">
            <div style="padding: 24px; border-bottom: 1px solid rgba(74, 144, 226, 0.2);">
                <h2 style="margin: 0; font-size: 20px; font-weight: 700; color: #333;">
                    <i class="fas fa-users" style="color: #4A90E2; margin-right: 10px;"></i>
                    Player List
                </h2>
            </div>
            
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead style="background: rgba(74, 144, 226, 0.1);">
                        <tr>
                            <th style="padding: 16px; text-align: left; font-weight: 600; color: #333; border-bottom: 2px solid rgba(74, 144, 226, 0.2);">Player ID</th>
                            <th style="padding: 16px; text-align: left; font-weight: 600; color: #333; border-bottom: 2px solid rgba(74, 144, 226, 0.2);">Name</th>
                            <th style="padding: 16px; text-align: left; font-weight: 600; color: #333; border-bottom: 2px solid rgba(74, 144, 226, 0.2);">Position</th>
                            <th style="padding: 16px; text-align: left; font-weight: 600; color: #333; border-bottom: 2px solid rgba(74, 144, 226, 0.2);">Age</th>
                            <th style="padding: 16px; text-align: left; font-weight: 600; color: #333; border-bottom: 2px solid rgba(74, 144, 226, 0.2);">Contact</th>
                            <th style="padding: 16px; text-align: left; font-weight: 600; color: #333; border-bottom: 2px solid rgba(74, 144, 226, 0.2);">Status</th>
                            <th style="padding: 16px; text-align: left; font-weight: 600; color: #333; border-bottom: 2px solid rgba(74, 144, 226, 0.2);">Attendance</th>
                            <th style="padding: 16px; text-align: center; font-weight: 600; color: #333; border-bottom: 2px solid rgba(74, 144, 226, 0.2);">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Player 1 -->
                        <tr style="border-bottom: 1px solid rgba(74, 144, 226, 0.1); transition: background-color 0.3s;" onmouseover="this.style.backgroundColor='rgba(74,144,226,0.05)'" onmouseout="this.style.backgroundColor='transparent'">
                            <td style="padding: 16px; color: #666;">PLR001</td>
                            <td style="padding: 16px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #4A90E2, #357ABD); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">SA</div>
                                    <div>
                                        <div style="font-weight: 600; color: #333;">Sandun Akalanka</div>
                                        <div style="font-size: 12px; color: #999;">sandun@email.com</div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 16px;">
                                <span style="background: rgba(74, 144, 226, 0.15); color: #4A90E2; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">Batsman</span>
                            </td>
                            <td style="padding: 16px; color: #666;">22</td>
                            <td style="padding: 16px; color: #666;">+94 77 123 4567</td>
                            <td style="padding: 16px;">
                                <span style="background: rgba(16, 185, 129, 0.15); color: #10b981; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                                    <i class="fas fa-circle" style="font-size: 6px; margin-right: 6px;"></i>Active
                                </span>
                            </td>
                            <td style="padding: 16px; color: #666;">92%</td>
                            <td style="padding: 16px; text-align: center;">
                                <button style="background: rgba(74, 144, 226, 0.1); border: none; color: #4A90E2; padding: 8px 12px; border-radius: 6px; cursor: pointer; margin: 0 4px;" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button style="background: rgba(16, 185, 129, 0.1); border: none; color: #10b981; padding: 8px 12px; border-radius: 6px; cursor: pointer; margin: 0 4px;" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Player 2 -->
                        <tr style="border-bottom: 1px solid rgba(74, 144, 226, 0.1); transition: background-color 0.3s;" onmouseover="this.style.backgroundColor='rgba(74,144,226,0.05)'" onmouseout="this.style.backgroundColor='transparent'">
                            <td style="padding: 16px; color: #666;">PLR002</td>
                            <td style="padding: 16px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">KP</div>
                                    <div>
                                        <div style="font-weight: 600; color: #333;">Kavindu Perera</div>
                                        <div style="font-size: 12px; color: #999;">kavindu@email.com</div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 16px;">
                                <span style="background: rgba(16, 185, 129, 0.15); color: #10b981; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">Bowler</span>
                            </td>
                            <td style="padding: 16px; color: #666;">24</td>
                            <td style="padding: 16px; color: #666;">+94 71 234 5678</td>
                            <td style="padding: 16px;">
                                <span style="background: rgba(16, 185, 129, 0.15); color: #10b981; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                                    <i class="fas fa-circle" style="font-size: 6px; margin-right: 6px;"></i>Active
                                </span>
                            </td>
                            <td style="padding: 16px; color: #666;">88%</td>
                            <td style="padding: 16px; text-align: center;">
                                <button style="background: rgba(74, 144, 226, 0.1); border: none; color: #4A90E2; padding: 8px 12px; border-radius: 6px; cursor: pointer; margin: 0 4px;" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button style="background: rgba(16, 185, 129, 0.1); border: none; color: #10b981; padding: 8px 12px; border-radius: 6px; cursor: pointer; margin: 0 4px;" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Player 3 -->
                        <tr style="border-bottom: 1px solid rgba(74, 144, 226, 0.1); transition: background-color 0.3s;" onmouseover="this.style.backgroundColor='rgba(74,144,226,0.05)'" onmouseout="this.style.backgroundColor='transparent'">
                            <td style="padding: 16px; color: #666;">PLR003</td>
                            <td style="padding: 16px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #f59e0b, #d97706); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">NF</div>
                                    <div>
                                        <div style="font-weight: 600; color: #333;">Nimal Fernando</div>
                                        <div style="font-size: 12px; color: #999;">nimal@email.com</div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 16px;">
                                <span style="background: rgba(139, 92, 246, 0.15); color: #8b5cf6; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">All-rounder</span>
                            </td>
                            <td style="padding: 16px; color: #666;">26</td>
                            <td style="padding: 16px; color: #666;">+94 76 345 6789</td>
                            <td style="padding: 16px;">
                                <span style="background: rgba(245, 158, 11, 0.15); color: #f59e0b; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                                    <i class="fas fa-circle" style="font-size: 6px; margin-right: 6px;"></i>On Leave
                                </span>
                            </td>
                            <td style="padding: 16px; color: #666;">76%</td>
                            <td style="padding: 16px; text-align: center;">
                                <button style="background: rgba(74, 144, 226, 0.1); border: none; color: #4A90E2; padding: 8px 12px; border-radius: 6px; cursor: pointer; margin: 0 4px;" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button style="background: rgba(16, 185, 129, 0.1); border: none; color: #10b981; padding: 8px 12px; border-radius: 6px; cursor: pointer; margin: 0 4px;" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Player 4 -->
                        <tr style="border-bottom: 1px solid rgba(74, 144, 226, 0.1); transition: background-color 0.3s;" onmouseover="this.style.backgroundColor='rgba(74,144,226,0.05)'" onmouseout="this.style.backgroundColor='transparent'">
                            <td style="padding: 16px; color: #666;">PLR004</td>
                            <td style="padding: 16px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #8b5cf6, #7c3aed); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">RS</div>
                                    <div>
                                        <div style="font-weight: 600; color: #333;">Ravindu Silva</div>
                                        <div style="font-size: 12px; color: #999;">ravindu@email.com</div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 16px;">
                                <span style="background: rgba(74, 144, 226, 0.15); color: #4A90E2; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">Batsman</span>
                            </td>
                            <td style="padding: 16px; color: #666;">21</td>
                            <td style="padding: 16px; color: #666;">+94 77 456 7890</td>
                            <td style="padding: 16px;">
                                <span style="background: rgba(16, 185, 129, 0.15); color: #10b981; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                                    <i class="fas fa-circle" style="font-size: 6px; margin-right: 6px;"></i>Active
                                </span>
                            </td>
                            <td style="padding: 16px; color: #666;">95%</td>
                            <td style="padding: 16px; text-align: center;">
                                <button style="background: rgba(74, 144, 226, 0.1); border: none; color: #4A90E2; padding: 8px 12px; border-radius: 6px; cursor: pointer; margin: 0 4px;" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button style="background: rgba(16, 185, 129, 0.1); border: none; color: #10b981; padding: 8px 12px; border-radius: 6px; cursor: pointer; margin: 0 4px;" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Player 5 -->
                        <tr style="border-bottom: 1px solid rgba(74, 144, 226, 0.1); transition: background-color 0.3s;" onmouseover="this.style.backgroundColor='rgba(74,144,226,0.05)'" onmouseout="this.style.backgroundColor='transparent'">
                            <td style="padding: 16px; color: #666;">PLR005</td>
                            <td style="padding: 16px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #ef4444, #dc2626); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">TW</div>
                                    <div>
                                        <div style="font-weight: 600; color: #333;">Tharaka Wickramasinghe</div>
                                        <div style="font-size: 12px; color: #999;">tharaka@email.com</div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 16px;">
                                <span style="background: rgba(239, 68, 68, 0.15); color: #ef4444; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">Wicket Keeper</span>
                            </td>
                            <td style="padding: 16px; color: #666;">23</td>
                            <td style="padding: 16px; color: #666;">+94 70 567 8901</td>
                            <td style="padding: 16px;">
                                <span style="background: rgba(16, 185, 129, 0.15); color: #10b981; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                                    <i class="fas fa-circle" style="font-size: 6px; margin-right: 6px;"></i>Active
                                </span>
                            </td>
                            <td style="padding: 16px; color: #666;">84%</td>
                            <td style="padding: 16px; text-align: center;">
                                <button style="background: rgba(74, 144, 226, 0.1); border: none; color: #4A90E2; padding: 8px 12px; border-radius: 6px; cursor: pointer; margin: 0 4px;" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button style="background: rgba(16, 185, 129, 0.1); border: none; color: #10b981; padding: 8px 12px; border-radius: 6px; cursor: pointer; margin: 0 4px;" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Player 6 -->
                        <tr style="border-bottom: 1px solid rgba(74, 144, 226, 0.1); transition: background-color 0.3s;" onmouseover="this.style.backgroundColor='rgba(74,144,226,0.05)'" onmouseout="this.style.backgroundColor='transparent'">
                            <td style="padding: 16px; color: #666;">PLR006</td>
                            <td style="padding: 16px;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #06b6d4, #0891b2); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">DN</div>
                                    <div>
                                        <div style="font-weight: 600; color: #333;">Dilshan Nanayakkara</div>
                                        <div style="font-size: 12px; color: #999;">dilshan@email.com</div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 16px;">
                                <span style="background: rgba(16, 185, 129, 0.15); color: #10b981; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">Bowler</span>
                            </td>
                            <td style="padding: 16px; color: #666;">25</td>
                            <td style="padding: 16px; color: #666;">+94 75 678 9012</td>
                            <td style="padding: 16px;">
                                <span style="background: rgba(16, 185, 129, 0.15); color: #10b981; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;">
                                    <i class="fas fa-circle" style="font-size: 6px; margin-right: 6px;"></i>Active
                                </span>
                            </td>
                            <td style="padding: 16px; color: #666;">90%</td>
                            <td style="padding: 16px; text-align: center;">
                                <button style="background: rgba(74, 144, 226, 0.1); border: none; color: #4A90E2; padding: 8px 12px; border-radius: 6px; cursor: pointer; margin: 0 4px;" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button style="background: rgba(16, 185, 129, 0.1); border: none; color: #10b981; padding: 8px 12px; border-radius: 6px; cursor: pointer; margin: 0 4px;" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div style="padding: 20px; border-top: 1px solid rgba(74, 144, 226, 0.2); display: flex; justify-content: space-between; align-items: center;">
                <div style="color: #666; font-size: 14px;">
                    Showing 1 to 6 of 24 players
                </div>
                <div style="display: flex; gap: 8px;">
                    <button style="background: rgba(74, 144, 226, 0.1); border: none; color: #4A90E2; padding: 8px 16px; border-radius: 8px; cursor: pointer; font-weight: 600;">
                        <i class="fas fa-chevron-left" style="margin-right: 6px;"></i>Previous
                    </button>
                    <button style="background: linear-gradient(135deg, #4A90E2, #357ABD); border: none; color: white; padding: 8px 16px; border-radius: 8px; cursor: pointer; font-weight: 600;">1</button>
                    <button style="background: rgba(74, 144, 226, 0.1); border: none; color: #4A90E2; padding: 8px 16px; border-radius: 8px; cursor: pointer; font-weight: 600;">2</button>
                    <button style="background: rgba(74, 144, 226, 0.1); border: none; color: #4A90E2; padding: 8px 16px; border-radius: 8px; cursor: pointer; font-weight: 600;">3</button>
                    <button style="background: rgba(74, 144, 226, 0.1); border: none; color: #4A90E2; padding: 8px 16px; border-radius: 8px; cursor: pointer; font-weight: 600;">
                        Next<i class="fas fa-chevron-right" style="margin-left: 6px;"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Sidebar toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('coachSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mainContent = document.querySelector('.main-content');
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            
            // Update toggle icon
            const icon = this.querySelector('i');
            if (sidebar.classList.contains('collapsed')) {
                icon.classList.remove('fa-angle-left');
                icon.classList.add('fa-angle-right');
                if (mainContent) {
                    mainContent.style.marginLeft = '80px';
                }
            } else {
                icon.classList.remove('fa-angle-right');
                icon.classList.add('fa-angle-left');
                if (mainContent) {
                    mainContent.style.marginLeft = '280px';
                }
            }
        });
    }
});
</script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
