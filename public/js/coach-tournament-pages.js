// Coach tournament folder views

function updateCount() {
    const selectedCountElement = document.getElementById('selectedCount');
    if (!selectedCountElement) {
        return;
    }

    const selectedCheckboxes = document.querySelectorAll('input[name="selected[]"]:checked');
    selectedCountElement.textContent = selectedCheckboxes.length;

    document.querySelectorAll('input[name="selected[]"]').forEach((checkbox) => {
        const row = checkbox.closest('.player-row');
        if (!row) {
            return;
        }

        const roleSelect = row.querySelector('select');
        if (roleSelect) {
            roleSelect.style.opacity = checkbox.checked ? '1' : '0.4';
        }
    });
}

function bindConfirmButtons() {
    document.querySelectorAll('[data-confirm-message]').forEach((button) => {
        button.addEventListener('click', (event) => {
            const message = button.getAttribute('data-confirm-message') || '';
            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });
}

function bindSelectionCheckboxes() {
    document.querySelectorAll('input[name="selected[]"]').forEach((checkbox) => {
        checkbox.addEventListener('change', updateCount);
    });
}

function bindPerformanceToggles() {
    document.querySelectorAll('[data-performance-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const targetId = button.getAttribute('data-performance-toggle');
            const target = document.getElementById(targetId);
            if (!target) {
                return;
            }

            const isHidden = target.hasAttribute('hidden');
            if (isHidden) {
                target.removeAttribute('hidden');
                button.setAttribute('aria-expanded', 'true');
            } else {
                target.setAttribute('hidden', 'hidden');
                button.setAttribute('aria-expanded', 'false');
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    bindConfirmButtons();
    bindSelectionCheckboxes();
    bindPerformanceToggles();
    updateCount();
});

window.updateCount = updateCount;