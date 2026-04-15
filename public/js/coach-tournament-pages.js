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

document.addEventListener('DOMContentLoaded', () => {
    bindConfirmButtons();
    bindSelectionCheckboxes();
    updateCount();
});

window.updateCount = updateCount;