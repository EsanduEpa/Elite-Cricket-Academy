<?php
// Ensure session helper is available
if (!function_exists('flash')) {
    require_once APPROOT . '/helpers/session_helper.php';
}
?>

<link rel="stylesheet" href="<?php echo URLROOT; ?>/css/register.css">

<!-- Main Container -->
<div class="main-container">
    <!-- Registration Form Section -->
    <div class="form-section">
        <div class="form-container">
            <h1 class="form-title">REGISTER</h1>
            
            <?php flash('register_success'); ?>
            
            <form id="registrationForm" action="<?php echo URLROOT; ?>/register" method="post">
                <div class="form-group <?php echo (!empty($data['fullName_err'])) ? 'error' : ''; ?>">
                    <label for="fullName">Full Name</label>
                    <input type="text" id="fullName" name="fullName" placeholder="Enter your full name" value="<?php echo $data['fullName']; ?>" required>
                    <div class="error-message"><?php echo $data['fullName_err']; ?></div>
                </div>

                <div class="form-group <?php echo (!empty($data['dateOfBirth_err'])) ? 'error' : ''; ?>">
                    <label for="dateOfBirth">Date of birth</label>
                    <input type="date" id="dateOfBirth" name="dateOfBirth" placeholder="DD - MM - YY" value="<?php echo $data['dateOfBirth']; ?>" required>
                    <div class="error-message"><?php echo $data['dateOfBirth_err']; ?></div>
                </div>

                <div class="form-group <?php echo (!empty($data['address_err'])) ? 'error' : ''; ?>">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" placeholder="Enter your address" value="<?php echo $data['address']; ?>" required>
                    <div class="error-message"><?php echo $data['address_err']; ?></div>
                </div>

                <div class="form-group <?php echo (!empty($data['email_err'])) ? 'error' : ''; ?>">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" value="<?php echo $data['email']; ?>" required>
                    <div class="error-message"><?php echo $data['email_err']; ?></div>
                </div>

                <div class="form-group <?php echo (!empty($data['contactNumber_err'])) ? 'error' : ''; ?>">
                    <label for="contactNumber">Contact Number</label>
                    <input type="tel" id="contactNumber" name="contactNumber" placeholder="Enter your contact number" value="<?php echo $data['contactNumber']; ?>" required>
                    <div class="error-message"><?php echo $data['contactNumber_err']; ?></div>
                </div>

                <div class="form-group <?php echo (!empty($data['school_err'])) ? 'error' : ''; ?>">
                    <label for="school">School/Institution</label>
                    <input type="text" id="school" name="school" placeholder="Enter your school/institution" value="<?php echo $data['school']; ?>" required>
                    <div class="error-message"><?php echo $data['school_err']; ?></div>
                </div>

                <div class="form-group <?php echo (!empty($data['username_err'])) ? 'error' : ''; ?>">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Choose a username" value="<?php echo $data['username']; ?>" required>
                    <div class="error-message"><?php echo $data['username_err']; ?></div>
                </div>

                <div class="form-group <?php echo (!empty($data['password_err'])) ? 'error' : ''; ?>">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Choose a password" required>
                    <div class="error-message"><?php echo $data['password_err']; ?></div>
                </div>

                <div class="form-group <?php echo (!empty($data['confirmPassword_err'])) ? 'error' : ''; ?>">
                    <label for="confirmPassword">Re-enter password</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Re-enter password" required>
                    <div class="error-message"><?php echo $data['confirmPassword_err']; ?></div>
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