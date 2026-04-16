document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('coachSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mainContent = document.querySelector('.main-content');
    const playerSearch = document.getElementById('playerSearch');
    const playerStatusFilter = document.getElementById('playerStatusFilter');
    const playerAgeGroupFilter = document.getElementById('playerAgeGroupFilter');
    const resetPlayerFiltersBtn = document.getElementById('resetPlayerFiltersBtn');
    const playersTable = document.getElementById('playersTable');
    const playerCountLabel = document.getElementById('playerCountLabel');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');

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

    const applyPlayerFilters = function() {
        if (!playersTable) {
            return;
        }

        const searchValue = (playerSearch ? playerSearch.value : '').trim().toLowerCase();
        const statusValue = playerStatusFilter ? playerStatusFilter.value : 'all';
        const ageGroupValue = playerAgeGroupFilter ? playerAgeGroupFilter.value : 'all';
        const rows = playersTable.querySelectorAll('tbody tr[data-player-search]');
        let visibleCount = 0;

        rows.forEach(function(row) {
            const rowSearch = (row.getAttribute('data-player-search') || '').toLowerCase();
            const rowStatus = (row.getAttribute('data-player-status') || '').toLowerCase();
            const rowAgeGroups = (row.getAttribute('data-player-age-groups') || '').toLowerCase().split(',').map(function(item) {
                return item.trim();
            }).filter(Boolean);
            const matchesSearch = searchValue === '' || rowSearch.indexOf(searchValue) !== -1;
            const matchesStatus = statusValue === 'all' || rowStatus === statusValue;
            const matchesAgeGroup = ageGroupValue === 'all' || rowAgeGroups.indexOf(ageGroupValue) !== -1;
            const isVisible = matchesSearch && matchesStatus && matchesAgeGroup;

            row.style.display = isVisible ? '' : 'none';
            if (isVisible) {
                visibleCount++;
            }
        });

        if (playerCountLabel) {
            playerCountLabel.textContent = 'Showing ' + visibleCount + ' player(s) assigned to you';
        }
    };

    if (playerSearch) {
        playerSearch.addEventListener('input', applyPlayerFilters);
    }

    if (playerStatusFilter) {
        playerStatusFilter.addEventListener('change', applyPlayerFilters);
    }

    if (playerAgeGroupFilter) {
        playerAgeGroupFilter.addEventListener('change', applyPlayerFilters);
    }

    if (resetPlayerFiltersBtn) {
        resetPlayerFiltersBtn.addEventListener('click', function() {
            if (playerSearch) {
                playerSearch.value = '';
            }
            if (playerStatusFilter) {
                playerStatusFilter.value = 'all';
            }
            if (playerAgeGroupFilter) {
                playerAgeGroupFilter.value = 'all';
            }
            applyPlayerFilters();
        });
    }

    document.querySelectorAll('[data-action="export-players"]').forEach(function(button) {
        button.addEventListener('click', function() {
            window.exportPlayers();
        });
    });

    document.querySelectorAll('[data-action="open-achievement"]').forEach(function(button) {
        button.addEventListener('click', function() {
            const achievementId = Number(this.getAttribute('data-achievement-id'));
            window.viewAchievementDetails(achievementId);
        });
    });

    document.querySelectorAll('[data-action="close-achievement"]').forEach(function(button) {
        button.addEventListener('click', function() {
            window.closeAchievementModal();
        });
    });
});

window.exportPlayers = function exportPlayers() {
    const table = document.getElementById('playersTable');
    if (!table) {
        return;
    }

    const rows = Array.from(table.querySelectorAll('tbody tr[data-player-search]')).filter(function(row) {
        return row.style.display !== 'none';
    });

    const headers = Array.from(table.querySelectorAll('thead th')).map(function(th) {
        return th.textContent.trim();
    });
    const csvLines = [headers.join(',')];

    rows.forEach(function(row) {
        const cells = Array.from(row.querySelectorAll('td')).map(function(cell) {
            return '"' + cell.textContent.replace(/"/g, '""').replace(/\s+/g, ' ').trim() + '"';
        });
        csvLines.push(cells.join(','));
    });

    const blob = new Blob([csvLines.join('\n')], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'coach-players.csv';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
};

window.viewAchievementDetails = function viewAchievementDetails(id) {
    const achievements = window.__COACH_PLAYERS_DATA || [];
    const achievement = achievements.find(function(item) {
        return item.AchievementID === id;
    });

    if (!achievement) {
        return;
    }

    const modalBody = document.getElementById('achievementModalBody');
    if (!modalBody) {
        return;
    }

    let statusClass = '';
    let statusIcon = '';
    switch ((achievement.VerifiedStatus || 'pending').toLowerCase()) {
        case 'verified':
            statusClass = 'background: rgba(16, 185, 129, 0.15); color: #10b981;';
            statusIcon = 'fa-check-circle';
            break;
        case 'rejected':
            statusClass = 'background: rgba(239, 68, 68, 0.15); color: #ef4444;';
            statusIcon = 'fa-times-circle';
            break;
        default:
            statusClass = 'background: rgba(245, 158, 11, 0.15); color: #f59e0b;';
            statusIcon = 'fa-clock';
    }

    modalBody.innerHTML = `
        <div class="achievement-details">
            <div style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid rgba(74, 144, 226, 0.2);">
                <div style="display: flex; align-items: center; gap: 16px;">
                    <div>
                        <h3 style="margin: 0; color: #333; font-size: 20px;">${achievement.PlayerName || 'Unknown Player'}</h3>
                        <p style="margin: 4px 0 0 0; color: #666;">${achievement.PlayerEmail || ''}</p>
                        <p style="margin: 4px 0 0 0; color: #666;">${achievement.PlayerContact || ''}</p>
                    </div>
                </div>
            </div>

            <div style="display: grid; gap: 16px;">
                <div style="padding: 16px; background: rgba(74, 144, 226, 0.05); border-radius: 8px;">
                    <strong style="color: #4A90E2; display: block; margin-bottom: 8px;">Achievement</strong>
                    <span style="color: #333; font-size: 18px; font-weight: 600;">${achievement.Achievement || 'Not specified'}</span>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <strong style="color: #666; display: block; margin-bottom: 4px;">Match Name</strong>
                        <span style="color: #333;">${achievement.MatchName || 'N/A'}</span>
                    </div>
                    <div>
                        <strong style="color: #666; display: block; margin-bottom: 4px;">Tournament</strong>
                        <span style="color: #333;">${achievement.Tournament || 'N/A'}</span>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <strong style="color: #666; display: block; margin-bottom: 4px;">Date</strong>
                        <span style="color: #333;">${new Date(achievement.Date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</span>
                    </div>
                    <div>
                        <strong style="color: #666; display: block; margin-bottom: 4px;">Verification Status</strong>
                        <span style="${statusClass} padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; display: inline-block;">
                            <i class="fas ${statusIcon}" style="margin-right: 6px;"></i>${(achievement.VerifiedStatus || 'Pending').charAt(0).toUpperCase() + (achievement.VerifiedStatus || 'Pending').slice(1)}
                        </span>
                    </div>
                </div>

                ${achievement.Description ? `
                    <div style="padding: 16px; background: rgba(74, 144, 226, 0.05); border-radius: 8px;">
                        <strong style="color: #4A90E2; display: block; margin-bottom: 8px;">Description</strong>
                        <p style="margin: 0; color: #666; line-height: 1.6;">${achievement.Description}</p>
                    </div>
                ` : ''}

                <div style="padding: 12px; background: rgba(139, 92, 246, 0.05); border-radius: 8px; border-left: 4px solid #8b5cf6;">
                    <div style="font-size: 12px; color: #8b5cf6; margin-bottom: 4px;">
                        <i class="fas fa-clock" style="margin-right: 6px;"></i>Created
                    </div>
                    <div style="color: #666; font-size: 13px;">
                        ${new Date(achievement.CreatedAt).toLocaleString('en-US', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' })}
                    </div>
                </div>
            </div>
        </div>
    `;

    const modal = document.getElementById('achievementModal');
    if (modal) {
        modal.style.display = 'flex';
    }
};

window.closeAchievementModal = function closeAchievementModal() {
    const modal = document.getElementById('achievementModal');
    if (modal) {
        modal.style.display = 'none';
    }
};

document.getElementById('achievementModal')?.addEventListener('click', function(event) {
    if (event.target === this) {
        window.closeAchievementModal();
    }
});