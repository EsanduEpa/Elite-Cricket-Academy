document.addEventListener('DOMContentLoaded', function () {
    const payModal = document.getElementById('payModal');
    const payForm = document.getElementById('payForm');
    const cardNumber = document.getElementById('cardNum');
    const cardExpiry = document.getElementById('cardExp');
    const cardCvv = document.getElementById('cardCvv');
    const cardBrand = document.getElementById('cardBrand');
    const cardName = document.getElementById('cardName');
    const closeButton = document.getElementById('payModalClose');

    if (!payModal || !payForm) {
        return;
    }

    function formatAmount(amount) {
        return 'LKR ' + amount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    function clearPaymentInputs() {
        payForm.querySelectorAll('input[type="text"]').forEach(function (input) {
            input.value = '';
        });
        if (cardBrand) {
            cardBrand.textContent = '';
        }
    }

    function openPayModal(button) {
        document.getElementById('pOccId').value = button.dataset.occId || '';
        document.getElementById('pAmount').value = button.dataset.amount || '';
        document.getElementById('pFacility').textContent = button.dataset.facility || '';
        document.getElementById('pDate').textContent = button.dataset.date || '';
        document.getElementById('pTime').textContent = button.dataset.time || '';
        document.getElementById('pTotal').textContent = formatAmount(parseFloat(button.dataset.amount || '0'));

        clearPaymentInputs();
        payModal.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }

    function closePayModal() {
        payModal.classList.remove('is-open');
        document.body.style.overflow = '';
    }

    function validatePayForm() {
        const num = (cardNumber?.value || '').replace(/\s/g, '');
        const exp = (cardExpiry?.value || '').replace(/\s/g, '');
        const cvv = cardCvv?.value || '';
        const name = (cardName?.value || '').trim();

        if (!name) {
            alert('Please enter the cardholder name.');
            return false;
        }
        if (num.length < 13) {
            alert('Please enter a valid card number.');
            return false;
        }
        if (!/^\d{2}\/\d{2}$/.test(exp)) {
            alert('Please enter a valid expiry date (MM / YY).');
            return false;
        }
        if (cvv.length < 3) {
            alert('Please enter a valid CVV.');
            return false;
        }
        return true;
    }

    document.querySelectorAll('.js-open-pay-modal').forEach(function (button) {
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

    if (cardNumber) {
        cardNumber.addEventListener('input', function () {
            const raw = this.value.replace(/\D/g, '').substring(0, 16);
            this.value = raw.replace(/(.{4})/g, '$1  ').trim();

            if (!cardBrand) {
                return;
            }

            if (/^4/.test(raw)) {
                cardBrand.innerHTML = '<i class="fab fa-cc-visa"></i> Visa';
            } else if (/^5[1-5]/.test(raw)) {
                cardBrand.innerHTML = '<i class="fab fa-cc-mastercard"></i> Mastercard';
            } else if (/^3[47]/.test(raw)) {
                cardBrand.innerHTML = '<i class="fab fa-cc-amex"></i> Amex';
            } else {
                cardBrand.textContent = '';
            }
        });
    }

    if (cardExpiry) {
        cardExpiry.addEventListener('input', function () {
            const raw = this.value.replace(/\D/g, '').substring(0, 4);
            this.value = raw.length >= 3 ? raw.substring(0, 2) + ' / ' + raw.substring(2) : raw;
        });
    }

    if (cardCvv) {
        cardCvv.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').substring(0, 4);
        });
    }

    payForm.addEventListener('submit', function (event) {
        if (!validatePayForm()) {
            event.preventDefault();
        }
    });
});
