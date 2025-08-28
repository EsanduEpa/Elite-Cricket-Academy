// login.js
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const navLinks = document.querySelector('.nav-links');
    
    // Mobile menu toggle
    mobileMenuToggle.addEventListener('click', function() {
        navLinks.style.display = navLinks.style.display === 'flex' ? 'none' : 'flex';
        
        // Animate hamburger menu
        this.classList.toggle('active');
        
        if (this.classList.contains('active')) {
            navLinks.style.display = 'flex';
            navLinks.style.flexDirection = 'column';
            navLinks.style.position = 'absolute';
            navLinks.style.top = '100%';
            navLinks.style.left = '0';
            navLinks.style.right = '0';
            navLinks.style.background = 'white';
            navLinks.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
            navLinks.style.padding = '1rem';
            navLinks.style.gap = '1rem';
        } else {
            navLinks.style.display = 'none';
        }
    });
    
    // Form validation and submission
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value;
        const submitBtn = document.querySelector('.login-btn');
        
        // Remove any existing messages
        const existingMessage = document.querySelector('.message');
        if (existingMessage) {
            existingMessage.remove();
        }
        
        // Basic validation
        if (!username || !password) {
            showMessage('Please fill in all fields.', 'error');
            return;
        }
        
        if (username.length < 3) {
            showMessage('Username must be at least 3 characters long.', 'error');
            return;
        }
        
        if (password.length < 6) {
            showMessage('Password must be at least 6 characters long.', 'error');
            return;
        }
        
        // Show loading state
        submitBtn.classList.add('loading');
        submitBtn.textContent = 'Logging in...';
        
        // Simulate API call
        setTimeout(() => {
            // Reset button state
            submitBtn.classList.remove('loading');
            submitBtn.textContent = 'Login';
            
            // Demo: Accept any valid credentials
            if (username === 'demo' && password === 'password') {
                showMessage('Login successful! Redirecting...', 'success');
                setTimeout(() => {
                    window.location.href = '#dashboard';
                }, 1500);
            } else {
                showMessage('Invalid credentials. Try username: "demo" and password: "password"', 'error');
            }
        }, 2000);
    });
    
    // Input animations
    const inputs = document.querySelectorAll('input');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            if (!this.value) {
                this.parentElement.classList.remove('focused');
            }
        });
        
        // Real-time validation feedback
        input.addEventListener('input', function() {
            validateInput(this);
        });
    });
    
    // Navigation smooth scrolling
    const navLinksElements = document.querySelectorAll('.nav-links a[href^="#"]');
    navLinksElements.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            console.log('Navigation clicked:', targetId);
            // Implement smooth scrolling to sections
        });
    });
    
    // Forgot password handler
    const forgotPasswordLink = document.querySelector('.forgot-password a');
    forgotPasswordLink.addEventListener('click', function(e) {
        e.preventDefault();
        showMessage('Password reset functionality would be implemented here.', 'success');
    });
    
    // Register link handler
    const registerLink = document.querySelector('.register-link a');
    registerLink.addEventListener('click', function(e) {
        e.preventDefault();
        console.log('Register clicked - would redirect to registration page');
        showMessage('Registration page would open here.', 'success');
    });
    
    function showMessage(text, type) {
        const message = document.createElement('div');
        message.className = `message ${type}`;
        message.textContent = text;
        
        const loginBox = document.querySelector('.login-box');
        loginBox.insertBefore(message, loginBox.firstChild);
        
        // Auto-remove success messages
        if (type === 'success') {
            setTimeout(() => {
                message.remove();
            }, 3000);
        }
    }
    
    function validateInput(input) {
        const value = input.value.trim();
        const inputGroup = input.parentElement;
        
        // Remove existing validation classes
        inputGroup.classList.remove('valid', 'invalid');
        
        if (input.type === 'text' && value.length >= 3) {
            inputGroup.classList.add('valid');
        } else if (input.type === 'password' && value.length >= 6) {
            inputGroup.classList.add('valid');
        } else if (value.length > 0) {
            inputGroup.classList.add('invalid');
        }
    }
    
    // Keyboard accessibility
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && e.target.tagName !== 'BUTTON') {
            const submitBtn = document.querySelector('.login-btn');
            submitBtn.click();
        }
    });
    
    // Form persistence (using memory instead of localStorage)
    let formData = {};
    
    inputs.forEach(input => {
        // Load saved data
        if (formData[input.name]) {
            input.value = formData[input.name];
        }
        
        // Save data on input
        input.addEventListener('input', function() {
            formData[this.name] = this.value;
        });
    });
    
    // Add visual feedback for form interactions
    const loginBox = document.querySelector('.login-box');
    
    loginBox.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-5px)';
        this.style.boxShadow = '0 25px 50px rgba(0, 0, 0, 0.15)';
    });
    
    loginBox.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
        this.style.boxShadow = '0 15px 35px rgba(0, 0, 0, 0.1)';
    });
});

// Additional CSS styles for validation states
const additionalCSS = `
.form-group.focused label {
    color: #3498db;
}

.form-group.valid input {
    border-left: 4px solid #27ae60;
}

.form-group.invalid input {
    border-left: 4px solid #e74c3c;
}

.mobile-menu-toggle.active span:nth-child(1) {
    transform: rotate(45deg) translate(5px, 5px);
}

.mobile-menu-toggle.active span:nth-child(2) {
    opacity: 0;
}

.mobile-menu-toggle.active span:nth-child(3) {
    transform: rotate(-45deg) translate(7px, -6px);
}
`;

// Inject additional CSS
const style = document.createElement('style');
style.textContent = additionalCSS;
document.head.appendChild(style);
