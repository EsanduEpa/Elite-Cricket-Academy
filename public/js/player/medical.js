/* global document, window, fetch, FormData */

(function () {
    'use strict';

    const MODAL_IDS = [
        'addMedicalModal',
        'workoutPlanModal',
        'nutritionPlanModal',
        'editFullRecordModal',
        'updateStatusModal',
        'deleteRecordModal'
    ];

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function hasContent(value) {
        return value !== null && typeof value !== 'undefined' && String(value).trim() !== '';
    }

    function formatLabel(value) {
        if (!hasContent(value)) {
            return 'Not provided';
        }

        return String(value)
            .replace(/_/g, ' ')
            .replace(/\b\w/g, char => char.toUpperCase());
    }

    function formatDate(value) {
        if (!hasContent(value)) {
            return 'Not provided';
        }

        const parsedDate = new Date(value);
        if (Number.isNaN(parsedDate.getTime())) {
            return escapeHtml(value);
        }

        return escapeHtml(parsedDate.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        }));
    }

    function formatMultilineText(value, emptyText = 'Not provided') {
        if (!hasContent(value)) {
            return `<p class="plan-empty-text">${escapeHtml(emptyText)}</p>`;
        }

        return String(value)
            .split(/\n{2,}/)
            .map(block => block.trim())
            .filter(Boolean)
            .map(block => `<p>${escapeHtml(block).replace(/\n/g, '<br>')}</p>`)
            .join('');
    }

    function renderPlanMeta(items) {
        return `
            <div class="plan-meta-grid">
                ${items.map(item => `
                    <div class="plan-meta-item">
                        <span class="plan-meta-label">${escapeHtml(item.label)}</span>
                        <span class="plan-meta-value">${item.value}</span>
                    </div>
                `).join('')}
            </div>
        `;
    }

    function renderPlanSection(title, content) {
        return `
            <section class="plan-section">
                <h5>${escapeHtml(title)}</h5>
                <div class="plan-rich-text">${content}</div>
            </section>
        `;
    }

    function findPlanById(plans, planId) {
        if (!plans) return null;
        const id = String(planId);
        if (Array.isArray(plans)) {
            return plans.find(plan => String(plan?.PlanID) === id) || null;
        }
        if (typeof plans === 'object') {
            return plans[planId] || plans[id] || null;
        }
        return null;
    }

    function getMedicalUrlRoot() {
        const page = document.getElementById('medicalPage');
        return (page && page.dataset && page.dataset.urlroot) ? page.dataset.urlroot : '';
    }

    function hydrateMedicalData() {
        if (window.medicalData) return;
        const el = document.getElementById('medicalData');
        if (!el) {
            window.medicalData = {};
            return;
        }

        try {
            window.medicalData = JSON.parse(el.textContent || '{}');
        } catch (_err) {
            window.medicalData = {};
        }
    }

    function hasVisibleModal() {
        return Boolean(document.querySelector('.medical-modal.app-modal--visible'));
    }

    function syncBodyModalState() {
        document.body.classList.toggle('modal-open', hasVisibleModal());
    }

    function getModal(modalId) {
        return document.getElementById(modalId);
    }

    function clearValidationState(modal) {
        if (!modal) return;
        modal.querySelectorAll('.form-control').forEach(field => {
            field.classList.remove('is-valid', 'is-invalid');
        });
        modal.querySelectorAll('.medical-error-text').forEach(errorText => {
            errorText.textContent = '';
            errorText.classList.remove('is-visible');
        });
    }

    function resetModal(modalId) {
        const modal = getModal(modalId);
        if (!modal) return;

        if (modalId === 'workoutPlanModal' || modalId === 'nutritionPlanModal') {
            return;
        }

        const form = modal.querySelector('form');
        if (form) {
            form.reset();
        }

        if (modalId === 'deleteRecordModal') {
            const deleteInput = document.getElementById('delete_record_id');
            if (deleteInput) {
                deleteInput.value = '';
            }
        }

        clearValidationState(modal);
    }

    function openModal(modalId) {
        const modal = getModal(modalId);
        if (!modal) return;

        modal.classList.add('app-modal--visible');
        modal.setAttribute('aria-hidden', 'false');
        syncBodyModalState();
    }

    function closeModal(modalId) {
        const modal = getModal(modalId);
        if (!modal) return;

        modal.classList.remove('app-modal--visible');
        modal.setAttribute('aria-hidden', 'true');
        resetModal(modalId);
        syncBodyModalState();
    }

    function validateRestDays(value) {
        if (value === '' || value === null || typeof value === 'undefined') {
            return { valid: true, message: '' };
        }

        if (!Number.isInteger(Number(value))) {
            return { valid: false, message: 'Rest days must be a whole number (no decimals)' };
        }

        const numValue = parseInt(value, 10);
        if (numValue < 0) return { valid: false, message: 'Rest days cannot be negative' };
        if (numValue > 1000) return { valid: false, message: 'Rest days cannot exceed 1000 days' };
        return { valid: true, message: '' };
    }

    function updateRestDaysValidation(input, errorElement) {
        if (!input || !errorElement) return true;

        const validation = validateRestDays(input.value);
        input.classList.remove('is-valid', 'is-invalid');

        if (!validation.valid) {
            input.classList.add('is-invalid');
            errorElement.textContent = validation.message;
            errorElement.classList.add('is-visible');
            return false;
        }

        if (input.value !== '') {
            input.classList.add('is-valid');
        }
        errorElement.textContent = '';
        errorElement.classList.remove('is-visible');
        return true;
    }

    function openAddMedicalModal() {
        const dateInput = document.getElementById('reported_date');
        if (dateInput && !dateInput.value) {
            dateInput.value = new Date().toISOString().split('T')[0];
        }
        openModal('addMedicalModal');
    }

    function closeAddMedicalModal() {
        closeModal('addMedicalModal');
    }

    function renderPlanModal(plan, config) {
        const content = document.getElementById(config.contentId);
        if (!content) return;

        if (!plan) {
            content.innerHTML = '<p>Plan not found.</p>';
            openModal(config.modalId);
            return;
        }

        content.innerHTML = config.render(plan);
        openModal(config.modalId);
    }

    function viewWorkoutPlan(planId) {
        hydrateMedicalData();
        renderPlanModal(findPlanById(window.medicalData?.workoutPlans || [], planId), {
            modalId: 'workoutPlanModal',
            contentId: 'workoutPlanContent',
            render: plan => `
                <div class="plan-header">
                    <h4>${escapeHtml(plan.workoutname || 'Workout plan')}</h4>
                    <p><strong>Trainer:</strong> ${escapeHtml(plan.trainer_name || 'Not assigned')}</p>
                    <p><strong>Frequency:</strong> ${escapeHtml(plan.frequency || 'Not provided')}</p>
                    <p><strong>Duration:</strong> ${escapeHtml(plan.Duration || 'Not provided')} days</p>
                </div>

                ${renderPlanMeta([
                    { label: 'Intensity', value: escapeHtml(formatLabel(plan.Intensity)) },
                    { label: 'Status', value: escapeHtml(formatLabel(plan.Status || plan.assignment_status || 'active')) },
                    { label: 'Assigned Date', value: formatDate(plan.AssignedDate) },
                    { label: 'End Date', value: formatDate(plan.EndDate) },
                    { label: 'Assigned By', value: escapeHtml(plan.assigned_by_name || 'Not provided') },
                    { label: 'Not Suitable For', value: escapeHtml(formatLabel(plan.NotSuitableFor)) }
                ])}

                ${renderPlanSection('Benefits', formatMultilineText(plan.Benefits, 'No benefits have been added for this workout yet.'))}

                ${renderPlanSection(
                    'Video Demonstration',
                    hasContent(plan.VideoLink)
                        ? `<p><a class="plan-link" href="${escapeHtml(plan.VideoLink)}" target="_blank" rel="noopener noreferrer">Open workout video</a></p>`
                        : '<p class="plan-empty-text">No video link has been provided for this workout.</p>'
                )}
            `
        });
    }

    function viewNutritionPlan(planId) {
        hydrateMedicalData();
        renderPlanModal(findPlanById(window.medicalData?.nutritionPlans || [], planId), {
            modalId: 'nutritionPlanModal',
            contentId: 'nutritionPlanContent',
            render: plan => `
                <div class="plan-header">
                    <h4>${escapeHtml(plan.nutritionPlanName || 'Nutrition plan')}</h4>
                    <p><strong>Nutritionist:</strong> ${escapeHtml(plan.trainer_name || 'Not assigned')}</p>
                    <p><strong>Duration:</strong> ${escapeHtml(plan.Duration || 'Not provided')} days</p>
                </div>

                ${renderPlanMeta([
                    { label: 'Status', value: escapeHtml(formatLabel(plan.Status || 'active')) },
                    { label: 'Created', value: formatDate(plan.CreatedDate) },
                    { label: 'Plan ID', value: escapeHtml(plan.PlanID || 'Not provided') }
                ])}

                ${renderPlanSection('Diet Details', formatMultilineText(plan.DietDetails, 'No diet details are available for this plan.'))}

                ${renderPlanSection('Trainer Notes', formatMultilineText(plan.Notes, 'No custom notes were added to this nutrition plan.'))}
            `
        });
    }

    function openUpdateStatusModal(recordId, currentStatus, verifyStatus, diagnosis, treatment, bodyArea, injuryDate, reportedDate, happenedAtAcademy, restDays) {
        if (verifyStatus === 'pending') {
            document.getElementById('edit_record_id').value = recordId;
            document.getElementById('edit_injury_date').value = injuryDate;
            document.getElementById('edit_reported_date').value = reportedDate;
            document.getElementById('edit_body_area').value = bodyArea;
            document.getElementById('edit_diagnosis').value = diagnosis;
            document.getElementById('edit_treatment').value = treatment;
            document.getElementById('edit_rest_days').value = restDays;
            document.getElementById('edit_recovery_status').value = currentStatus;

            const academyYes = document.getElementById('edit_academy_yes');
            const academyNo = document.getElementById('edit_academy_no');
            if (academyYes) academyYes.checked = happenedAtAcademy === 'yes';
            if (academyNo) academyNo.checked = happenedAtAcademy !== 'yes';

            openModal('editFullRecordModal');
            return;
        }

        const recordIdInput = document.getElementById('update_record_id');
        const statusSelect = document.getElementById('update_recovery_status');
        if (!recordIdInput || !statusSelect) return;

        recordIdInput.value = recordId;
        statusSelect.value = currentStatus;
        openModal('updateStatusModal');
    }

    function closeEditFullRecordModal() {
        closeModal('editFullRecordModal');
    }

    function closeUpdateStatusModal() {
        closeModal('updateStatusModal');
    }

    function confirmDeleteRecord(recordId) {
        const recordIdInput = document.getElementById('delete_record_id');
        if (!recordIdInput) return;

        recordIdInput.value = recordId;
        openModal('deleteRecordModal');
    }

    function closeDeleteRecordModal() {
        closeModal('deleteRecordModal');
    }

    function deleteMedicalRecord() {
        const recordId = document.getElementById('delete_record_id')?.value;
        if (!recordId) {
            window.alert('Error: No record ID found');
            return;
        }

        const formData = new FormData();
        formData.append('record_id', recordId);

        fetch(getMedicalUrlRoot() + '/player/deleteMedicalRecord', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.alert('Medical record deleted successfully');
                    window.location.reload();
                    return;
                }

                window.alert(data.message || 'Failed to delete medical record');
            })
            .catch(error => {
                console.error('Error:', error);
                window.alert('An error occurred while deleting the record');
            })
            .finally(() => {
                closeDeleteRecordModal();
            });
    }

    function initRealtimeValidation() {
        const restDaysInput = document.getElementById('rest_days_needed');
        const restDaysError = document.getElementById('rest_days_error');
        const addForm = document.querySelector('#addMedicalModal form');

        if (restDaysInput && restDaysError) {
            restDaysInput.addEventListener('input', function () {
                updateRestDaysValidation(restDaysInput, restDaysError);
            });

            restDaysInput.addEventListener('keypress', function (event) {
                if (event.key === '.' || event.key === ',') {
                    event.preventDefault();
                }
            });
        }

        if (addForm) {
            addForm.addEventListener('submit', function (event) {
                if (!updateRestDaysValidation(restDaysInput, restDaysError)) {
                    event.preventDefault();
                    window.alert('Please fix the rest days value before submitting.');
                }
            });
        }
    }

    function handleActionClick(trigger) {
        const action = trigger.dataset.medicalAction;
        if (!action) return;

        switch (action) {
        case 'open-add-modal':
            openAddMedicalModal();
            break;
        case 'view-workout':
            viewWorkoutPlan(trigger.dataset.planId);
            break;
        case 'view-nutrition':
            viewNutritionPlan(trigger.dataset.planId);
            break;
        case 'open-update-modal':
            openUpdateStatusModal(
                trigger.dataset.recordId,
                trigger.dataset.recoveryStatus,
                trigger.dataset.verifyStatus,
                trigger.dataset.diagnosis,
                trigger.dataset.treatment,
                trigger.dataset.bodyArea,
                trigger.dataset.injuryDate,
                trigger.dataset.reportedDate,
                trigger.dataset.happenedAtAcademy,
                trigger.dataset.restDays
            );
            break;
        case 'confirm-delete':
            confirmDeleteRecord(trigger.dataset.recordId);
            break;
        case 'delete-record':
            deleteMedicalRecord();
            break;
        default:
            break;
        }
    }

    function initDelegatedInteractions() {
        document.addEventListener('click', function (event) {
            const actionTrigger = event.target.closest('[data-medical-action]');
            if (actionTrigger) {
                event.preventDefault();
                handleActionClick(actionTrigger);
                return;
            }

            const closeTrigger = event.target.closest('[data-medical-close]');
            if (closeTrigger) {
                event.preventDefault();
                closeModal(closeTrigger.dataset.medicalClose);
                return;
            }

            const backdrop = event.target.closest('.medical-modal');
            if (backdrop && event.target === backdrop) {
                closeModal(backdrop.id);
            }
        });
    }

    window.openAddMedicalModal = openAddMedicalModal;
    window.closeAddMedicalModal = closeAddMedicalModal;
    window.viewWorkoutPlan = viewWorkoutPlan;
    window.viewNutritionPlan = viewNutritionPlan;
    window.closeModal = closeModal;
    window.openUpdateStatusModal = openUpdateStatusModal;
    window.closeUpdateStatusModal = closeUpdateStatusModal;
    window.closeEditFullRecordModal = closeEditFullRecordModal;
    window.confirmDeleteRecord = confirmDeleteRecord;
    window.closeDeleteRecordModal = closeDeleteRecordModal;
    window.deleteMedicalRecord = deleteMedicalRecord;

    document.addEventListener('DOMContentLoaded', function () {
        hydrateMedicalData();
        initRealtimeValidation();
        initDelegatedInteractions();
    });

    window.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') return;

        for (const modalId of MODAL_IDS) {
            const modal = getModal(modalId);
            if (modal && modal.classList.contains('app-modal--visible')) {
                closeModal(modalId);
                break;
            }
        }
    });
})();