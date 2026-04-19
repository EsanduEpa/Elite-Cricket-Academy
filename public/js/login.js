// Login page behavior.
// This file keeps JavaScript out of v_login.php so the PHP view focuses on HTML/form data.
document.addEventListener('DOMContentLoaded', function () {
    const forgotPasswordLink = document.getElementById('forgotPasswordLink');
    const forgotPasswordModal = document.getElementById('forgotPasswordModal');
    const closeForgotPasswordModal = document.getElementById('closeForgotPasswordModal');
    const cancelForgotPassword = document.getElementById('cancelForgotPassword');
    const forgotPasswordForm = document.getElementById('forgotPasswordForm');

    function closeModal() {
        if (forgotPasswordModal) {
            forgotPasswordModal.classList.remove('active');
        }
    }

    // Open the reset-password modal without leaving the login page.
    if (forgotPasswordLink && forgotPasswordModal) {
        forgotPasswordLink.addEventListener('click', function (event) {
            event.preventDefault();
            forgotPasswordModal.classList.add('active');
        });
    }

    if (closeForgotPasswordModal) {
        closeForgotPasswordModal.addEventListener('click', closeModal);
    }

    if (cancelForgotPassword) {
        cancelForgotPassword.addEventListener('click', closeModal);
    }

    // Clicking the dark overlay closes the modal; clicking inside the form does not.
    if (forgotPasswordModal) {
        forgotPasswordModal.addEventListener('click', function (event) {
            if (event.target === forgotPasswordModal) {
                closeModal();
            }
        });
    }

    // Client-side validation helps the user, but the Login controller still validates on the server.
    if (forgotPasswordForm) {
        forgotPasswordForm.addEventListener('submit', function (event) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (newPassword !== confirmPassword) {
                event.preventDefault();
                showForgotPasswordMessage('Passwords do not match!', 'error');
                return;
            }

            if (newPassword.length < 6) {
                event.preventDefault();
                showForgotPasswordMessage('Password must be at least 6 characters long!', 'error');
            }
        });
    }
});

function showForgotPasswordMessage(message, type) {
    const messageContainer = document.getElementById('forgotPasswordMessage');
    if (!messageContainer) {
        return;
    }

    messageContainer.textContent = message;
    messageContainer.className = 'message-container ' + (type === 'error' ? 'error-message' : 'success-message');
    messageContainer.style.display = 'block';

    setTimeout(function () {
        messageContainer.style.display = 'none';
    }, 5000);
}
