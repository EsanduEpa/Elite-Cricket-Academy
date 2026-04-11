window.addEventListener('DOMContentLoaded', function () {
    window.setTimeout(function () {
        var form = document.getElementById('payhere-form');
        if (form) {
            form.submit();
        }
    }, 600);
});