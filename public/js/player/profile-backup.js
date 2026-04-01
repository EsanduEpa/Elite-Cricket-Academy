/* global document, window */

(function () {
    'use strict';

    function confirmDeactivation() {
        const modal = document.getElementById('deactivationModal');
        if (modal) modal.style.display = 'block';
    }

    function closeDeactivationModal() {
        const modal = document.getElementById('deactivationModal');
        if (modal) modal.style.display = 'none';
    }

    window.confirmDeactivation = confirmDeactivation;
    window.closeDeactivationModal = closeDeactivationModal;

    window.addEventListener('click', function (event) {
        const modal = document.getElementById('deactivationModal');
        if (modal && event.target === modal) {
            closeDeactivationModal();
        }
    });
})();
