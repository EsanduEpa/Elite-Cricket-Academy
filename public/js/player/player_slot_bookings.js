document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.js-cancel-booking-form').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!window.confirm('Cancel this session booking?')) {
                event.preventDefault();
            }
        });
    });
});