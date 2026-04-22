/* global document, window */

(function () {
    'use strict';

    const MODAL_IDS = [
        'performanceModal',
        'achievementModal',
        'detailsModal',
        'achievementViewModal',
        'deletePerformanceModal',
        'deleteAchievementModal'
    ];

    function getModal(id) {
        return document.getElementById(id);
    }

    function syncBodyState() {
        const hasVisibleModal = MODAL_IDS.some((id) => {
            const modal = getModal(id);
            return modal && modal.classList.contains('app-modal--visible');
        });

        document.body.classList.toggle('modal-open', hasVisibleModal);
    }

    function openModal(id) {
        const modal = getModal(id);
        if (!modal) return;
        modal.classList.add('app-modal--visible');
        modal.setAttribute('aria-hidden', 'false');
        syncBodyState();
    }

    function closeModal(id) {
        const modal = getModal(id);
        if (!modal) return;
        modal.classList.remove('app-modal--visible');
        modal.setAttribute('aria-hidden', 'true');
        syncBodyState();
    }

    function formatDate(value, withWeekday) {
        if (!value) return '-';

        const parsed = new Date(value);
        if (Number.isNaN(parsed.getTime())) {
            return value;
        }

        return parsed.toLocaleDateString('en-US', withWeekday ? {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        } : {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
    }

    function statusLabel(value) {
        switch (String(value || '').toLowerCase()) {
        case 'verified':
            return 'Verified';
        case 'rejected':
            return 'Rejected';
        default:
            return 'Pending Review';
        }
    }

    function setText(id, value) {
        const el = document.getElementById(id);
        if (el) {
            el.textContent = value === null || value === undefined || value === '' ? '-' : String(value);
        }
    }

    function resetPerformanceForm() {
        const form = document.getElementById('performanceStatsForm');
        if (!form) return;

        form.reset();
        form.action = `${document.getElementById('performancePage')?.dataset?.urlroot || ''}/performance/addPerformanceStats`;
        const performanceId = document.getElementById('performanceId');
        if (performanceId) performanceId.value = '';

        const modalTitle = document.querySelector('#performanceModal .app-modal__title');
        const modalSubtitle = document.querySelector('#performanceModal .app-modal__subtitle');
        const submitText = document.getElementById('performanceSubmitText');

        if (modalTitle) modalTitle.textContent = 'Add Performance Statistics';
        if (modalSubtitle) modalSubtitle.textContent = 'Submit your latest batting, bowling, and fielding figures in the same register-style layout used across player forms.';
        if (submitText) submitText.textContent = 'Submit Performance Statistics';
    }

    function openAddPerformanceModal() {
        resetPerformanceForm();
        openModal('performanceModal');
    }

    function openEditPerformanceModal(trigger) {
        resetPerformanceForm();

        const form = document.getElementById('performanceStatsForm');
        const performanceId = document.getElementById('performanceId');
        const modalTitle = document.querySelector('#performanceModal .app-modal__title');
        const modalSubtitle = document.querySelector('#performanceModal .app-modal__subtitle');
        const submitText = document.getElementById('performanceSubmitText');

        if (form) {
            form.action = `${document.getElementById('performancePage')?.dataset?.urlroot || ''}/performance/editPerformanceStats`;
        }
        if (performanceId) performanceId.value = trigger.dataset.performanceId || '';

        const fieldMap = {
            matchSelect: 'matchId',
            runsScored: 'runsScored',
            ballsFaced: 'ballsFaced',
            wicketsTaken: 'wicketsTaken',
            oversBowled: 'oversBowled',
            runsConceded: 'runsConceded',
            catches: 'catches',
            stumpings: 'stumpings',
            performanceRating: 'rating'
        };

        Object.keys(fieldMap).forEach((elementId) => {
            const field = document.getElementById(elementId);
            if (field) {
                field.value = trigger.dataset[fieldMap[elementId]] || 0;
            }
        });

        if (modalTitle) modalTitle.textContent = 'Edit Performance Statistics';
        if (modalSubtitle) modalSubtitle.textContent = 'Update your pending match record and submit the corrected figures for review.';
        if (submitText) submitText.textContent = 'Update Performance Statistics';

        openModal('performanceModal');
    }

    function openPerformanceDetails(trigger) {
        setText('detailsDate', formatDate(trigger.dataset.date, true));
        setText('detailsTournament', trigger.dataset.tournamentName || '-');
        setText('detailsOpponent', trigger.dataset.opponentTeam || '-');
        setText('detailsVenue', trigger.dataset.venue || '-');
        setText('detailsResult', trigger.dataset.result || '-');
        setText('detailsRuns', trigger.dataset.runsScored || 0);
        setText('detailsBalls', trigger.dataset.ballsFaced || 0);
        setText('detailsWickets', trigger.dataset.wicketsTaken || 0);
        setText('detailsOvers', trigger.dataset.oversBowled || 0);
        setText('detailsConceded', trigger.dataset.runsConceded || 0);
        setText('detailsCatches', trigger.dataset.catches || 0);
        setText('detailsStumpings', trigger.dataset.stumpings || 0);

        const catches = Number(trigger.dataset.catches || 0);
        const stumpings = Number(trigger.dataset.stumpings || 0);
        setText('detailsFieldingTotal', catches + stumpings);
        setText('detailsRatingValue', trigger.dataset.rating || 0);
        setText('detailsStatus', statusLabel(trigger.dataset.verifiedStatus));

        const addedByRow = document.getElementById('detailsAddedByRow');
        const verifiedByRow = document.getElementById('detailsVerifiedByRow');

        setText('detailsAddedBy', trigger.dataset.addedByName || '-');
        setText('detailsVerifiedBy', trigger.dataset.verifiedByName || '-');

        if (addedByRow) {
            addedByRow.style.display = trigger.dataset.addedByName ? '' : 'none';
        }
        if (verifiedByRow) {
            verifiedByRow.style.display = trigger.dataset.verifiedByName ? '' : 'none';
        }

        openModal('detailsModal');
    }

    function resetAchievementForm() {
        const form = document.getElementById('achievementForm');
        if (!form) return;

        form.reset();
        form.action = `${document.getElementById('performancePage')?.dataset?.urlroot || ''}/performance/addAchievement`;

        const achievementId = document.getElementById('achievementId');
        const modalTitle = document.getElementById('modalTitle');
        const submitText = document.getElementById('submitText');
        const verificationStatus = document.getElementById('verificationStatus');
        const dateInput = document.getElementById('achievementDate');

        if (achievementId) achievementId.value = '';
        if (modalTitle) modalTitle.innerHTML = '<i class="fas fa-trophy label-icon-gold"></i> Add New Achievement';
        if (submitText) submitText.textContent = 'Save Achievement';
        if (verificationStatus) verificationStatus.style.display = 'none';
        if (dateInput && !dateInput.value) {
            dateInput.value = new Date().toISOString().split('T')[0];
        }
    }

    function openAddAchievementModal() {
        resetAchievementForm();
        openModal('achievementModal');
    }

    function openEditAchievementModal(trigger) {
        resetAchievementForm();

        const form = document.getElementById('achievementForm');
        const achievementId = document.getElementById('achievementId');
        const modalTitle = document.getElementById('modalTitle');
        const submitText = document.getElementById('submitText');

        if (form) {
            form.action = `${document.getElementById('performancePage')?.dataset?.urlroot || ''}/performance/editAchievement`;
        }
        if (achievementId) achievementId.value = trigger.dataset.achievementId || '';
        if (modalTitle) modalTitle.innerHTML = '<i class="fas fa-edit label-icon-blue"></i> Edit Achievement';
        if (submitText) submitText.textContent = 'Update Achievement';

        const fieldMap = {
            achievementDate: 'date',
            tournamentName: 'tournament',
            matchName: 'matchName',
            achievementText: 'achievement'
        };

        Object.keys(fieldMap).forEach((elementId) => {
            const field = document.getElementById(elementId);
            if (field) {
                field.value = trigger.dataset[fieldMap[elementId]] || '';
            }
        });

        openModal('achievementModal');
    }

    function openAchievementDetails(trigger) {
        setText('achievementViewDate', formatDate(trigger.dataset.date, true));
        setText('achievementViewTournament', trigger.dataset.tournament || '-');
        setText('achievementViewMatch', trigger.dataset.matchName || '-');
        setText('achievementViewText', trigger.dataset.achievement || '-');
        setText('achievementViewStatus', statusLabel(trigger.dataset.verifiedStatus));
        setText('achievementViewSubmitted', formatDate(trigger.dataset.createdAt, false));

        const banner = document.getElementById('achievementViewBanner');
        if (banner) {
            banner.textContent = '';
            if (trigger.dataset.verifiedStatus === 'verified') {
                banner.textContent = 'This achievement has been officially verified.';
            } else if (trigger.dataset.verifiedStatus === 'rejected') {
                banner.textContent = 'This achievement was rejected and can be deleted or resubmitted after corrections.';
            } else {
                banner.textContent = 'This achievement is currently pending review.';
            }
        }

        openModal('achievementViewModal');
    }

    function openDeletePerformanceModal(trigger) {
        const input = document.getElementById('deletePerformanceId');
        const text = document.getElementById('deletePerformanceText');

        if (input) input.value = trigger.dataset.performanceId || '';
        if (text) {
            const opponent = trigger.dataset.opponentTeam || 'this match';
            const date = formatDate(trigger.dataset.date, false);
            text.textContent = `Delete the pending performance record for ${opponent} on ${date}?`;
        }

        openModal('deletePerformanceModal');
    }

    function openDeleteAchievementModal(trigger) {
        const input = document.getElementById('deleteAchievementId');
        const text = document.getElementById('deleteAchievementText');

        if (input) input.value = trigger.dataset.achievementId || '';
        if (text) {
            const label = trigger.dataset.achievementLabel || 'this achievement';
            const tournament = trigger.dataset.tournament || 'the selected tournament';
            text.textContent = `Delete "${label}" from ${tournament}?`;
        }

        openModal('deleteAchievementModal');
    }

    document.addEventListener('click', function (event) {
        const trigger = event.target.closest('[data-performance-action]');
        if (!trigger) return;

        const action = trigger.dataset.performanceAction;

        switch (action) {
        case 'open-performance-modal':
            event.preventDefault();
            openAddPerformanceModal();
            break;
        case 'close-performance-modal':
            event.preventDefault();
            closeModal('performanceModal');
            break;
        case 'view-match-performance':
            event.preventDefault();
            openPerformanceDetails(trigger);
            break;
        case 'edit-match-performance':
            event.preventDefault();
            openEditPerformanceModal(trigger);
            break;
        case 'delete-match-performance':
            event.preventDefault();
            openDeletePerformanceModal(trigger);
            break;
        case 'close-delete-performance-modal':
            event.preventDefault();
            closeModal('deletePerformanceModal');
            break;
        case 'add-achievement':
            event.preventDefault();
            openAddAchievementModal();
            break;
        case 'edit-achievement':
            event.preventDefault();
            openEditAchievementModal(trigger);
            break;
        case 'view-achievement':
            event.preventDefault();
            openAchievementDetails(trigger);
            break;
        case 'delete-achievement':
            event.preventDefault();
            openDeleteAchievementModal(trigger);
            break;
        case 'close-achievement-modal':
            event.preventDefault();
            closeModal('achievementModal');
            break;
        case 'close-achievement-view-modal':
            event.preventDefault();
            closeModal('achievementViewModal');
            break;
        case 'close-details-modal':
            event.preventDefault();
            closeModal('detailsModal');
            break;
        case 'close-delete-achievement-modal':
            event.preventDefault();
            closeModal('deleteAchievementModal');
            break;
        default:
            break;
        }
    });

    window.addEventListener('click', function (event) {
        MODAL_IDS.forEach((id) => {
            const modal = getModal(id);
            if (modal && event.target === modal) {
                closeModal(id);
            }
        });
    });

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') return;

        MODAL_IDS.forEach((id) => {
            const modal = getModal(id);
            if (modal && modal.classList.contains('app-modal--visible')) {
                closeModal(id);
            }
        });
    });
})();
