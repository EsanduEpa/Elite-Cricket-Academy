<?php
// Ensure session helper is available
if (!function_exists('flash')) {
    require_once APPROOT . '/helpers/session_helper.php';
}
?>

<?php require_once APPROOT . '/views/inc/components/header.php'; ?>
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/home.css">
<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/register.css">

    <!-- Main Container -->
    <div class="main-container">
        <!-- Registration Form Section -->
        <div class="form-section">
            <div class="form-container">
                <h1 class="form-title">REGISTER</h1>
                
                <?php flash('register_success'); ?>
                
                <div class="success-message" id="successMessage">
                    Registration successful! Welcome to Elite Cricket Academy.
                </div>
                
                <form id="registrationForm" method="POST" action="">
                    <div class="form-group">
                        <label for="fullName">Full Name</label>
                        <input type="text" id="fullName" name="fullName" placeholder="Enter your full name" value="<?php echo $data['fullName']; ?>" required>
                        <div class="error-message <?php echo (!empty($data['fullName_err'])) ? 'show' : ''; ?>" id="fullNameError"><?php echo $data['fullName_err']; ?></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="dateOfBirth">Date of birth</label>
                        <input type="date" id="dateOfBirth" name="dateOfBirth" value="<?php echo $data['dateOfBirth']; ?>" max="<?php echo date('Y-m-d'); ?>" required>
                        <small class="form-hint">You must be at least 5 years old</small>
                        <div class="error-message <?php echo (!empty($data['dateOfBirth_err'])) ? 'show' : ''; ?>" id="dateOfBirthError"><?php echo $data['dateOfBirth_err']; ?></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" id="address" name="address" placeholder="Enter your address" value="<?php echo $data['address']; ?>" required>
                        <div class="error-message <?php echo (!empty($data['address_err'])) ? 'show' : ''; ?>" id="addressError"><?php echo $data['address_err']; ?></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email" value="<?php echo $data['email']; ?>" required>
                        <div class="error-message <?php echo (!empty($data['email_err'])) ? 'show' : ''; ?>" id="emailError"><?php echo $data['email_err']; ?></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="contactNumber">Contact Number</label>
                        <input type="tel" id="contactNumber" name="contactNumber" placeholder="e.g., 0771234567 or +94771234567" value="<?php echo $data['contactNumber']; ?>" pattern="[0-9+\-\s()]+" required>
                        <small class="form-hint">10-15 digits (may include +, -, spaces, or parentheses)</small>
                        <div class="error-message <?php echo (!empty($data['contactNumber_err'])) ? 'show' : ''; ?>" id="contactNumberError"><?php echo $data['contactNumber_err']; ?></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="school">School/Institution</label>
                        <input type="text" id="school" name="school" placeholder="Enter your school/institution" value="<?php echo $data['school']; ?>" required>
                        <div class="error-message <?php echo (!empty($data['school_err'])) ? 'show' : ''; ?>" id="schoolError"><?php echo $data['school_err']; ?></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" placeholder="Choose a username" value="<?php echo $data['username']; ?>" required>
                        <div class="error-message <?php echo (!empty($data['username_err'])) ? 'show' : ''; ?>" id="usernameError"><?php echo $data['username_err']; ?></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Choose a strong password" minlength="8" required>
                        <small class="form-hint">Must be at least 8 characters with uppercase, lowercase, number, and special character</small>
                        <div class="error-message <?php echo (!empty($data['password_err'])) ? 'show' : ''; ?>" id="passwordError"><?php echo $data['password_err']; ?></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirmPassword">Re-enter password</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Re-enter password" required>
                        <div class="error-message <?php echo (!empty($data['confirmPassword_err'])) ? 'show' : ''; ?>" id="confirmPasswordError"><?php echo $data['confirmPassword_err']; ?></div>
                    </div>
                    
                    <button type="submit" class="register-submit-btn">
                        <div class="loading" id="loadingSpinner"></div>
                        <span id="buttonText">Register</span>
                    </button>
                </form>
                
                <div class="login-link">
                    Already have an account? <a href="<?php echo URLROOT; ?>/login">Login</a>
                </div>
            </div>
        </div>
        
        <!-- Cricket Player Image Section -->
        <div class="image-section">
            <div class="cricket-player">
                <div class="image-overlay"></div>
            </div>
        </div>
    </div>

    <script src="<?php echo URLROOT; ?>/js/register.js"></script>
</body>
</html> 