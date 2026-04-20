<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/coach-dashboard.css">

<style>
.recommendations-page-title {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
}
.recommendations-subtitle {
    margin: 6px 0 0;
    color: #64748b;
    font-size: 14px;
}
.stats-row {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 14px;
    padding: 20px;
}
.stat-card {
    background: #fff;
    border-radius: 14px;
    padding: 18px;
    box-shadow: 0 1px 4px rgba(15, 23, 42, 0.08);
    border: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
}
.stat-content {
    min-width: 0;
}
.stat-number {
    font-size: 24px;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.1;
}
.stat-label {
    margin-top: 2px;
    color: #64748b;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}
.recommendations-container {
    padding: 0 20px 24px;
}
.recommendations-panel {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    box-shadow: 0 1px 4px rgba(15, 23, 42, 0.08);
    overflow: hidden;
}
.recommendations-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 980px;
}
.recommendations-table thead th {
    background: #f8fafc;
    color: #64748b;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    text-align: left;
    padding: 14px 16px;
    border-bottom: 1px solid #e2e8f0;
    white-space: nowrap;
}
.recommendations-table tbody td {
    padding: 14px 16px;
    border-bottom: 1px solid #f1f5f9;
    color: #334155;
    vertical-align: top;
    font-size: 13px;
}
.recommendations-table tbody tr:hover {
    background: #f8fafc;
}
.recommendation-player {
    font-weight: 700;
    color: #0f172a;
}
.recommendation-meta {
    color: #64748b;
    font-size: 12px;
    margin-top: 2px;
}
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
}
.status-pending { background: rgba(59, 130, 246, 0.12); color: #2563eb; }
.status-approved { background: rgba(16, 185, 129, 0.12); color: #059669; }
.status-rejected { background: rgba(239, 68, 68, 0.12); color: #dc2626; }
.status-confirmed { background: rgba(139, 92, 246, 0.12); color: #7c3aed; }
.empty-table-row td {
    text-align: center;
    color: #94a3b8;
    padding: 40px 16px;
}
.page-action-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 0.6rem 1rem;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.9);
    color: #4A90E2;
    font-size: 0.95rem;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}
.page-action-btn:hover {
    background: #fff;
    transform: translateY(-2px);
}
@media (max-width: 1200px) {
    .stats-row {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}
@media (max-width: 768px) {
    .stats-row,
    .recommendations-container {
        padding-left: 14px;
        padding-right: 14px;
    }
    .stats-row {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="coach-layout">
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
                    <a href="<?php echo URLROOT; ?>/staffslots/calendar" class="nav-link" data-tooltip="My Slot Sessions">
                        <i class="fas fa-calendar-check"></i>
                        <span>My Slot Sessions</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/players" class="nav-link" data-tooltip="Players">
                        <i class="fas fa-users"></i>
                        <span>Players</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/performance" class="nav-link" data-tooltip="Performance">
                        <i class="fas fa-chart-line"></i>
                        <span>Performance</span>
                    </a>
                </li>
                <li class="nav-item active">
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
                    <a href="<?php echo URLROOT; ?>/coach/communication" class="nav-link" data-tooltip="Communication">
                        <i class="fas fa-comments"></i>
                        <span>Communication</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo URLROOT; ?>/coach/requests" class="nav-link" data-tooltip="Requests">
                        <i class="fas fa-clipboard-list"></i>
                        <span>Requests</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <div class="main-content">
        <div class="dashboard-header">
            <div class="header-content recommendations-page-title">
                <div>
                    <h1>
                        <i class="fas fa-star"></i>
                        Tournament Recommendations
                    </h1>
                    <p class="recommendations-subtitle">View your submitted tournament recommendations</p>
                </div>
                <div style="display:flex;gap:8px;align-items:flex-start;">
                    <a href="<?php echo URLROOT; ?>/coach/tournaments" class="page-action-btn">
                        <i class="fas fa-arrow-left"></i>
                        Back to Tournaments
                    </a>
                </div>
            </div>
        </div>

        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-number"><?php echo (int)($data['stats']['pending'] ?? 0); ?></div>
                    <div class="stat-label">Pending</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-number"><?php echo (int)($data['stats']['approved'] ?? 0); ?></div>
                    <div class="stat-label">Approved</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-number"><?php echo (int)($data['stats']['rejected'] ?? 0); ?></div>
                    <div class="stat-label">Rejected</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-number"><?php echo (int)($data['stats']['confirmed'] ?? 0); ?></div>
                    <div class="stat-label">Confirmed</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-number"><?php echo (int)($data['stats']['total'] ?? 0); ?></div>
                    <div class="stat-label">Total</div>
                </div>
            </div>
        </div>

        <div class="recommendations-container">
            <div class="recommendations-panel">
                <table class="recommendations-table">
                    <thead>
                        <tr>
                            <th>Player</th>
                            <th>Tournament</th>
                            <th>Role</th>
                            <th>Captaincy</th>
                            <th>Wicket Keeper</th>
                            <th>Status</th>
                            <th>Reason</th>
                            <th>Comments</th>
                            <th>Recommended</th>
                            <th>Reviewed</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['recommendations'])): ?>
                            <?php foreach ($data['recommendations'] as $rec): ?>
                                <?php
                                    $status = strtolower((string)($rec->Status ?? 'pending'));
                                    $statusClass = in_array($status, ['pending', 'approved', 'rejected', 'confirmed'], true) ? $status : 'pending';
                                ?>
                                <tr>
                                    <td>
                                        <div class="recommendation-player"><?php echo htmlspecialchars($rec->PlayerName ?? 'N/A'); ?></div>
                                        <?php if (!empty($rec->BattingStyle) || !empty($rec->BowlingStyle)): ?>
                                            <div class="recommendation-meta">
                                                <?php echo htmlspecialchars(trim(($rec->BattingStyle ?? '') . (!empty($rec->BattingStyle) && !empty($rec->BowlingStyle) ? ' · ' : '') . ($rec->BowlingStyle ?? ''))); ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($rec->TournamentName ?? 'N/A'); ?></td>
                                    <?php
                                        $role = strtolower(trim((string)($rec->RecommendedRole ?? '')));
                                        $roleLabel = match ($role) {
                                            'batsman' => 'Batsman',
                                            'bowler' => 'Bowler',
                                            'allrounder' => 'All-rounder',
                                            default => ($role !== '' ? ucfirst(str_replace(['-', '_'], ' ', $role)) : '—'),
                                        };

                                        $captaincy = strtolower(trim((string)($rec->Captaincy ?? 'team member')));
                                        $captaincyLabel = match ($captaincy) {
                                            'captain' => 'Captain',
                                            'vice captain' => 'Vice Captain',
                                            default => 'Team Member',
                                        };

                                        $wk = strtolower(trim((string)($rec->WicketKeeper ?? 'no')));
                                        $wkLabel = $wk === 'yes' ? 'Yes' : 'No';
                                    ?>
                                    <td><?php echo htmlspecialchars($roleLabel); ?></td>
                                    <td><?php echo htmlspecialchars($captaincyLabel); ?></td>
                                    <td><?php echo htmlspecialchars($wkLabel); ?></td>
                                    <td><span class="status-badge status-<?php echo $statusClass; ?>"><?php echo htmlspecialchars(ucfirst($status)); ?></span></td>
                                    <td><?php echo htmlspecialchars($rec->Reason ?? '—'); ?></td>
                                    <td><?php echo htmlspecialchars($rec->Comments ?? '—'); ?></td>
                                    <td><?php echo !empty($rec->DateRecommended) ? date('M d, Y', strtotime($rec->DateRecommended)) : '—'; ?></td>
                                    <td><?php echo !empty($rec->DateReviewed) ? date('M d, Y', strtotime($rec->DateReviewed)) : '—'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="empty-table-row">
                                    No recommendations yet. Your submitted tournament recommendations will appear here.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo URLROOT; ?>/js/common/sidebar.js"></script>

<?php require_once APPROOT . '/views/inc/components/footer.php'; ?>
