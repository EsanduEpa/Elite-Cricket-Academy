document.addEventListener('DOMContentLoaded', function () {
    function openModal(modal) {
        if (!modal) {
            return;
        }
        modal.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(modal) {
        if (!modal) {
            return;
        }
        modal.classList.remove('is-open');
        document.body.style.overflow = '';
    }

    document.querySelectorAll('.js-open-slot-modal').forEach(function (button) {
        button.addEventListener('click', function () {
            const id = (button.dataset.modalId || '').trim();
            if (!id) {
                return;
            }
            openModal(document.getElementById(id));
        });
    });

    document.querySelectorAll('.pay-modal-overlay').forEach(function (overlay) {
        overlay.addEventListener('click', function (event) {
            if (event.target === overlay) {
                closeModal(overlay);
            }
        });

        overlay.querySelectorAll('.js-close-slot-modal').forEach(function (closeBtn) {
            closeBtn.addEventListener('click', function () {
                closeModal(overlay);
            });
        });
    });

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') {
            return;
        }
        const openOverlay = document.querySelector('.pay-modal-overlay.is-open');
        if (openOverlay) {
            closeModal(openOverlay);
        }
    });
});
