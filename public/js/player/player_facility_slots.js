document.addEventListener('DOMContentLoaded', function () {
    const payModal = document.getElementById('payModal');
    const payForm = document.getElementById('payForm');
    const payConfirmButton = document.getElementById('payConfirmButton');
    const payNote = document.getElementById('payNote');
    const closeButton = document.getElementById('payModalClose');

    if (!payModal || !payForm) {
        return;
    }

    function formatAmount(amount) {
        return 'LKR ' + amount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    function openPayModal(button) {
        document.getElementById('pOccId').value = button.dataset.occId || '';
        document.getElementById('pAmount').value = button.dataset.amount || '';
        document.getElementById('pFacility').textContent = button.dataset.facility || '';
        document.getElementById('pDate').textContent = button.dataset.date || '';
        document.getElementById('pTime').textContent = button.dataset.time || '';
        document.getElementById('pTotal').textContent = formatAmount(parseFloat(button.dataset.amount || '0'));

        const amount = parseFloat(button.dataset.amount || '0');
        payForm.action = payModal.dataset.payUrl;
        if (payConfirmButton) {
            payConfirmButton.innerHTML = '<i class="fas fa-lock"></i> Pay &amp; Confirm Booking';
            payConfirmButton.disabled = amount <= 0;
        }
        if (payNote) {
            payNote.innerHTML = amount > 0
                ? '<i class="fas fa-shield-alt"></i> Review the slot details, then continue to the payment portal.'
                : '<i class="fas fa-exclamation-triangle"></i> This facility slot has no configured payment amount. Booking is unavailable until a payable price is set.';
        }

        payModal.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }

    function closePayModal() {
        payModal.classList.remove('is-open');
        document.body.style.overflow = '';
        if (payConfirmButton) {
            payConfirmButton.disabled = false;
        }
    }

    document.querySelectorAll('.js-open-facility-details').forEach(function (button) {
        button.addEventListener('click', function () {
            openPayModal(button);
        });
    });

    if (closeButton) {
        closeButton.addEventListener('click', closePayModal);
    }

    payModal.addEventListener('click', function (event) {
        if (event.target === payModal) {
            closePayModal();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && payModal.classList.contains('is-open')) {
            closePayModal();
        }
    });

    payForm.addEventListener('submit', function (event) {
        if (!payForm.action || (payConfirmButton && payConfirmButton.disabled)) {
            event.preventDefault();
        }
    });
});
