
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
                        
                        <form id="loginForm" class="login-form">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" id="username" name="username" placeholder="Enter your username" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" id="password" name="password" placeholder="Enter your password" required>
                            </div>
                            
                            <div class="forgot-password">
                                <a href="#forgot">Forgot password?</a>
                            </div>
                            
                            <button type="submit" class="login-btn">Login</button>
                        </form>
                        
                        <div class="divider">
                            <span>OR</span>
                        </div>
                        
                        <div class="register-link">
                            <span>Don't have an account? </span>
                            <a href="<?php echo URLROOT; ?>/register"">Register</a>
                        </div>

                        
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="<?php echo URLROOT; ?>/js/login.js"></script>
</body>
</html>


