<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/home.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/login.css">


   

        <!-- Main Content -->
        <main class="main-content">
            <!-- Left Side - Cricket Image -->
            <div class="image-section">
                <div class="cricket-image">
                    <img src="<?php echo URLROOT; ?>/img/hero1.jpg" alt="Cricket Player" />
                </div>
            </div>

            <!-- Right Side - Login Form -->
            <div class="form-section">
                <div class="form-container">
                    <h1 class="academy-title">Elite Cricket Academy</h1>
                    
                    <div class="login-box">
                        <h2>LOGIN</h2>
                        
                        <!-- Show flash messages if any -->
                        <?php flash('register_success'); ?>
                        <?php flash('login_message'); ?>
                        
                        <form method="POST" action="<?php echo URLROOT; ?>/login" class="login-form">
                            <div class="form-group">
                                <label for="email">Email or Username</label>
                                <input type="text" id="email" name="email" placeholder="Enter your email or username" value="<?php echo $data['email']; ?>" required>
                                <div class="error-message <?php echo (!empty($data['email_err'])) ? 'show' : ''; ?>">
                                    <?php echo $data['email_err']; ?>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" id="password" name="password" placeholder="Enter your password" required>
                                <div class="error-message <?php echo (!empty($data['password_err'])) ? 'show' : ''; ?>">
                                    <?php echo $data['password_err']; ?>
                                </div>
                            </div>
                            
                            <div class="forgot-password">
                                <a href="#" id="forgotPasswordLink">Forgot password?</a>
                            </div>
                            
                            <button type="submit" class="login-btn">Login</button>
                        </form>
                        
                        <div class="divider">
                            <span>OR</span>
                        </div>
                        
                        <div class="register-link">
                            <span>Don't have an account? </span>
                            <a href="<?php echo URLROOT; ?>/register">Register</a>
                        </div>

                        
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Forgot Password Modal -->
    <div class="modal" id="forgotPasswordModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-key"></i> Reset Password</h2>
                <button class="modal-close" id="closeForgotPasswordModal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <!-- Show flash messages if any -->
                <div id="forgotPasswordMessage" class="message-container" style="display: none;"></div>
                
                <form method="POST" action="<?php echo URLROOT; ?>/login/forgot_password" id="forgotPasswordForm">
                    <div class="form-group">
                        <label for="reset_username">Username *</label>
                        <input type="text" id="reset_username" name="username" placeholder="Enter your username" required>
                        <small class="form-text">Enter the username associated with your account</small>
                    </div>

                    <div class="form-group">
                        <label for="reset_email">Email *</label>
                        <input type="email" id="reset_email" name="email" placeholder="Enter your email" required>
                        <small class="form-text">Enter the email address registered with your account</small>
                    </div>

                    <div class="form-group">
                        <label for="new_password">New Password *</label>
                        <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required minlength="6">
                        <small class="form-text">Password must be at least 6 characters long</small>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm Password *</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required minlength="6">
                        <small class="form-text">Re-enter your new password</small>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-secondary" id="cancelForgotPassword">Cancel</button>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-check"></i>
                            Reset Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Forgot Password Modal
        document.addEventListener('DOMContentLoaded', function() {
            const forgotPasswordLink = document.getElementById('forgotPasswordLink');
            const forgotPasswordModal = document.getElementById('forgotPasswordModal');
            const closeForgotPasswordModal = document.getElementById('closeForgotPasswordModal');
            const cancelForgotPassword = document.getElementById('cancelForgotPassword');
            const forgotPasswordForm = document.getElementById('forgotPasswordForm');

            // Open modal
            if (forgotPasswordLink) {
                forgotPasswordLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    forgotPasswordModal.classList.add('active');
                });
            }

            // Close modal
            if (closeForgotPasswordModal) {
                closeForgotPasswordModal.addEventListener('click', function() {
                    forgotPasswordModal.classList.remove('active');
                });
            }

            if (cancelForgotPassword) {
                cancelForgotPassword.addEventListener('click', function() {
                    forgotPasswordModal.classList.remove('active');
                });
            }

            // Close modal on backdrop click
            forgotPasswordModal.addEventListener('click', function(e) {
                if (e.target === forgotPasswordModal) {
                    forgotPasswordModal.classList.remove('active');
                }
            });

            // Form validation
            if (forgotPasswordForm) {
                forgotPasswordForm.addEventListener('submit', function(e) {
                    const newPassword = document.getElementById('new_password').value;
                    const confirmPassword = document.getElementById('confirm_password').value;

                    if (newPassword !== confirmPassword) {
                        e.preventDefault();
                        showMessage('Passwords do not match!', 'error');
                        return false;
                    }

                    if (newPassword.length < 6) {
                        e.preventDefault();
                        showMessage('Password must be at least 6 characters long!', 'error');
                        return false;
                    }
                });
            }

            function showMessage(message, type) {
                const messageContainer = document.getElementById('forgotPasswordMessage');
                messageContainer.textContent = message;
                messageContainer.className = 'message-container ' + (type === 'error' ? 'error-message' : 'success-message');
                messageContainer.style.display = 'block';
                
                setTimeout(() => {
                    messageContainer.style.display = 'none';
                }, 5000);
            }
        });
    </script>
</body>
</html>


