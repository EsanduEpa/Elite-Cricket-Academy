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
                        <input type="text" id="fullName" name="fullName" placeholder="Enter your full name" required>
                        <div class="error-message" id="fullNameError">Please enter your full name</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="dateOfBirth">Date of birth</label>
                        <input type="date" id="dateOfBirth" name="dateOfBirth" placeholder="DD - MM - YY" required>
                        <div class="error-message" id="dateOfBirthError">Please enter a valid date of birth</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" id="address" name="address" placeholder="Enter your address" required>
                        <div class="error-message" id="addressError">Please enter your address</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email" required>
                        <div class="error-message" id="emailError">Please enter a valid email address</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="contactNumber">Contact Number</label>
                        <input type="tel" id="contactNumber" name="contactNumber" placeholder="Enter your contact number" required>
                        <div class="error-message" id="contactNumberError">Please enter a valid contact number</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="school">School/Institution</label>
                        <input type="text" id="school" name="school" placeholder="Enter your school/institution" required>
                        <div class="error-message" id="schoolError">Please enter your school/institution</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" placeholder="Choose a username" required>
                        <div class="error-message" id="usernameError">Please choose a username (min 4 characters)</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Choose a password" required>
                        <div class="error-message" id="passwordError">Password must be at least 8 characters long</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirmPassword">Re-enter password</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Re-enter password" required>
                        <div class="error-message" id="confirmPasswordError">Passwords do not match</div>
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