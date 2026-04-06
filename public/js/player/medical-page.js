/* global document, window, fetch, FormData */

(function () {
    'use strict';

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function findPlanById(plans, planId) {
        if (!plans) return null;
        const id = String(planId);
        if (Array.isArray(plans)) {
            return plans.find(p => String(p?.PlanID) === id) || null;
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

    function validateMinWords(text, minWords) {
        const words = String(text || '').trim().split(/\s+/).filter(word => word.length > 0);
        return words.length >= minWords;
    }

    function validateRestDays(value) {
        if (!Number.isInteger(Number(value))) {
            return { valid: false, message: 'Rest days must be a whole number (no decimals)' };
        }

        const numValue = parseInt(value, 10);
        if (numValue < 0) return { valid: false, message: 'Rest days cannot be negative' };
        if (numValue > 1000) return { valid: false, message: 'Rest days cannot exceed 1000 days' };
        return { valid: true, message: '' };
    }

    function openAddMedicalModal() {
        const modal = document.getElementById('addMedicalModal');
        if (!modal) {
            console.error('Modal element not found');
            return;
        }
        modal.style.cssText = 'display:flex; align-items:center; justify-content:center; position:fixed; top:0; left:0; right:0; bottom:0; z-index:9999; background-color:rgba(0,0,0,0.55); overflow-y:auto;';
        const content = modal.querySelector('.modal-content');
        if (content) {
            content.style.animation = 'none';
            content.offsetHeight; // force reflow to restart animation
            content.style.animation = '';
        }
        document.body.style.overflow = 'hidden';

        const dateInput = document.getElementById('reported_date');
        if (dateInput && !dateInput.value) {
            dateInput.value = new Date().toISOString().split('T')[0];
        }

        const injuryError = document.getElementById('injury_details_error');
        const diagnosisError = document.getElementById('diagnosis_error');
        const restDaysError = document.getElementById('rest_days_error');
        [injuryError, diagnosisError, restDaysError].forEach(el => {
            if (el) el.style.display = 'none';
        });
        document.querySelectorAll('#addMedicalModal .form-control').forEach(el => (el.style.borderColor = ''));
    }

    function closeAddMedicalModal() {
        const modal = document.getElementById('addMedicalModal');
        if (!modal) return;
        modal.style.cssText = 'display:none;';
        document.body.style.overflow = '';

        const form = modal.querySelector('form');
        if (form) form.reset();

        const injuryError = document.getElementById('injury_details_error');
        const diagnosisError = document.getElementById('diagnosis_error');
        const restDaysError = document.getElementById('rest_days_error');
        [injuryError, diagnosisError, restDaysError].forEach(el => {
            if (el) el.style.display = 'none';
        });
        document.querySelectorAll('#addMedicalModal .form-control').forEach(el => (el.style.borderColor = ''));
    }

    function viewMedicalRecord(recordId) {
        window.alert('Viewing medical record #' + recordId);
    }

    function viewWorkoutPlan(planId) {
        hydrateMedicalData();

        const modal = document.getElementById('workoutPlanModal');
        const content = document.getElementById('workoutPlanContent');
        if (!modal || !content) return;

        const workoutPlans = window.medicalData?.workoutPlans || [];
        const plan = findPlanById(workoutPlans, planId);
        if (plan) {
            content.innerHTML = `
                <div class="plan-header">
                    <h4>${escapeHtml(plan.workoutname)}</h4>
                    <p><strong>Trainer:</strong> ${escapeHtml(plan.trainer_name)}</p>
                    <p><strong>Frequency:</strong> ${escapeHtml(plan.frequency)}</p>
                    <p><strong>Duration:</strong> ${escapeHtml(plan.Duration)} days</p>
                </div>
            `;
        } else {
            content.innerHTML = '<p>Workout plan not found.</p>';
        }

        modal.style.cssText = 'display:block; position:fixed; top:0; left:0; right:0; bottom:0; z-index:99999; background-color:rgba(0,0,0,0.65); overflow-y:auto;';
    }

    function viewNutritionPlan(planId) {
        hydrateMedicalData();

        const modal = document.getElementById('nutritionPlanModal');
        const content = document.getElementById('nutritionPlanContent');
        if (!modal || !content) return;

        const nutritionPlans = window.medicalData?.nutritionPlans || [];
        const plan = findPlanById(nutritionPlans, planId);
        if (plan) {
            const dietDetails = escapeHtml(plan.DietDetails).replace(/\n/g, '<br>');
            content.innerHTML = `
                <div class="plan-header">
                    <h4>${escapeHtml(plan.nutritionPlanName)}</h4>
                    <p><strong>Nutritionist:</strong> ${escapeHtml(plan.trainer_name)}</p>
                    <p><strong>Duration:</strong> ${escapeHtml(plan.Duration)} days</p>
                </div>
                <div class="plan-details">${dietDetails}</div>
            `;
        } else {
            content.innerHTML = '<p>Nutrition plan not found.</p>';
        }

        modal.style.cssText = 'display:block; position:fixed; top:0; left:0; right:0; bottom:0; z-index:99999; background-color:rgba(0,0,0,0.65); overflow-y:auto;';
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) modal.style.display = 'none';
    }

    function openUpdateStatusModal(recordId, currentStatus, verifyStatus, diagnosis, treatment, bodyArea, injuryDate, reportedDate, happenedAtAcademy, restDays) {
        if (verifyStatus === 'pending') {
            // Full edit modal
            const modal = document.getElementById('editFullRecordModal');
            if (!modal) return;
            document.getElementById('edit_record_id').value = recordId;
            document.getElementById('edit_injury_date').value = injuryDate;
            document.getElementById('edit_reported_date').value = reportedDate;
            document.getElementById('edit_body_area').value = bodyArea;
            document.getElementById('edit_diagnosis').value = diagnosis;
            document.getElementById('edit_treatment').value = treatment;
            document.getElementById('edit_rest_days').value = restDays;
            document.getElementById('edit_recovery_status').value = currentStatus;
            const academyYes = document.getElementById('edit_academy_yes');
            const academyNo  = document.getElementById('edit_academy_no');
            if (academyYes) academyYes.checked = (happenedAtAcademy === 'yes');
            if (academyNo)  academyNo.checked  = (happenedAtAcademy !== 'yes');
            modal.style.cssText = 'display:block; position:fixed; top:0; left:0; right:0; bottom:0; z-index:99999; background-color:rgba(0,0,0,0.65); overflow-y:auto;';
            document.body.style.overflow = 'hidden';
        } else {
            // Recovery status only (verified)
            const modal = document.getElementById('updateStatusModal');
            const recordIdInput = document.getElementById('update_record_id');
            const statusSelect  = document.getElementById('update_recovery_status');
            if (!modal || !recordIdInput || !statusSelect) return;
            recordIdInput.value = recordId;
            statusSelect.value  = currentStatus;
            modal.style.cssText = 'display:block; position:fixed; top:0; left:0; right:0; bottom:0; z-index:99999; background-color:rgba(0,0,0,0.65); overflow-y:auto;';
            document.body.style.overflow = 'hidden';
        }
    }

    function closeEditFullRecordModal() {
        const modal = document.getElementById('editFullRecordModal');
        if (!modal) return;
        modal.style.cssText = 'display:none;';
        document.body.style.overflow = '';
        const form = modal.querySelector('form');
        if (form) form.reset();
    }

    function closeUpdateStatusModal() {
        const modal = document.getElementById('updateStatusModal');
        if (!modal) return;

        modal.style.cssText = 'display:none;';
        document.body.style.overflow = '';

        const form = modal.querySelector('form');
        if (form) form.reset();
    }

    function confirmDeleteRecord(recordId) {
        const modal = document.getElementById('deleteRecordModal');
        const recordIdInput = document.getElementById('delete_record_id');
        if (!modal || !recordIdInput) return;

        recordIdInput.value = recordId;
        modal.style.cssText = 'display:block; position:fixed; top:0; left:0; right:0; bottom:0; z-index:99999; background-color:rgba(0,0,0,0.65); overflow-y:auto;';
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteRecordModal() {
        const modal = document.getElementById('deleteRecordModal');
        if (!modal) return;
        modal.style.cssText = 'display:none;';
        document.body.style.overflow = '';
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
                } else {
                    window.alert(data.message || 'Failed to delete medical record');
                }
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
        const form = document.querySelector('#addMedicalModal form');

        if (restDaysInput) {
            restDaysInput.addEventListener('input', function () {
                const errorElement = document.getElementById('rest_days_error');
                if (!errorElement) return;

                const validation = validateRestDays(this.value);
                if (!validation.valid) {
                    errorElement.textContent = validation.message;
                    errorElement.style.display = 'block';
                    this.style.borderColor = '#dc3545';
                } else {
                    errorElement.style.display = 'none';
                    this.style.borderColor = '#28a745';
                }
            });

            restDaysInput.addEventListener('keypress', function (e) {
                if (e.key === '.' || e.key === ',') {
                    e.preventDefault();
                }
            });
        }

        if (form) {
            form.addEventListener('submit', function (e) {
                let isValid = true;
                const errors = [];

                const restDays = document.getElementById('rest_days_needed')?.value;
                if (restDays) {
                    const validation = validateRestDays(restDays);
                    if (!validation.valid) {
                        isValid = false;
                        errors.push(validation.message);
                        const el = document.getElementById('rest_days_error');
                        if (el) {
                            el.textContent = validation.message;
                            el.style.display = 'block';
                        }
                        const field = document.getElementById('rest_days_needed');
                        if (field) field.style.borderColor = '#dc3545';
                    }
                }

                if (!isValid) {
                    e.preventDefault();
                    window.alert('Please fix the following errors:\n\n' + errors.join('\n'));
                }
            });
        }
    }

    // Expose functions used by onclick attributes in the view
    window.openAddMedicalModal = openAddMedicalModal;
    window.closeAddMedicalModal = closeAddMedicalModal;
    window.viewMedicalRecord = viewMedicalRecord;
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
    });

    window.addEventListener('click', function (event) {
        const addMedicalModal = document.getElementById('addMedicalModal');
        const workoutModal = document.getElementById('workoutPlanModal');
        const nutritionModal = document.getElementById('nutritionPlanModal');
        const updateStatusModal = document.getElementById('updateStatusModal');
        const editFullRecordModal = document.getElementById('editFullRecordModal');
        const deleteRecordModal = document.getElementById('deleteRecordModal');

        if (event.target === addMedicalModal) {
            closeAddMedicalModal();
        }
        if (event.target === workoutModal) {
            workoutModal.style.cssText = 'display:none;';
        }
        if (event.target === nutritionModal) {
            nutritionModal.style.cssText = 'display:none;';
        }
        if (event.target === updateStatusModal) {
            closeUpdateStatusModal();
        }
        if (event.target === editFullRecordModal) {
            closeEditFullRecordModal();
        }
        if (event.target === deleteRecordModal) {
            closeDeleteRecordModal();
        }
    });
})();
