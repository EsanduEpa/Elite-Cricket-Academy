// Form validation and submission
document.getElementById('registrationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Clear previous errors
    clearErrors();
    
    // Get form data
    const formData = {
        fullName: document.getElementById('fullName').value.trim(),
        dateOfBirth: document.getElementById('dateOfBirth').value,
        address: document.getElementById('address').value.trim(),
        email: document.getElementById('email').value.trim(),
        contactNumber: document.getElementById('contactNumber').value.trim(),
        school: document.getElementById('school').value.trim(),
        username: document.getElementById('username').value.trim(),
        password: document.getElementById('password').value,
        confirmPassword: document.getElementById('confirmPassword').value
    };
    
    let isValid = true;
    
    // Validation
    if (formData.fullName.length < 2) {
        showError('fullName', 'Please enter your full name (at least 2 characters)');
        isValid = false;
    }
    
    if (!formData.dateOfBirth) {
        showError('dateOfBirth', 'Please enter your date of birth');
        isValid = false;
    } else {
        const birthDate = new Date(formData.dateOfBirth);
        const today = new Date();
        const age = today.getFullYear() - birthDate.getFullYear();
        if (age < 5 || age > 100) {
            showError('dateOfBirth', 'Please enter a valid date of birth');
            isValid = false;
        }
    }
    
    if (formData.address.length < 10) {
        showError('address', 'Please enter a complete address');
        isValid = false;
    }
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(formData.email)) {
        showError('email', 'Please enter a valid email address');
        isValid = false;
    }
    
    const phoneRegex = /^[+]?[\d\s\-\(\)]{10,}$/;
    if (!phoneRegex.test(formData.contactNumber)) {
        showError('contactNumber', 'Please enter a valid contact number');
        isValid = false;
    }
    
    if (formData.school.length < 2) {
        showError('school', 'Please enter your school/institution');
        isValid = false;
    }
    
    if (formData.username.length < 4) {
        showError('username', 'Username must be at least 4 characters long');
        isValid = false;
    }
    
    if (formData.password.length < 8) {
        showError('password', 'Password must be at least 8 characters long');
        isValid = false;
    }
    
    if (formData.password !== formData.confirmPassword) {
        showError('confirmPassword', 'Passwords do not match');
        isValid = false;
    }
    
    if (isValid) {
        // Show loading
        showLoading(true);
        
        // Submit the form
        this.submit();
    }
});

function showError(fieldName, message) {
    const field = document.getElementById(fieldName);
    const errorElement = document.querySelector(`#${fieldName} + .error-message`);
    
    if (field && errorElement) {
        field.parentElement.classList.add('error');
        errorElement.textContent = message;
        errorElement.style.display = 'block';
    }
}

function clearErrors() {
    const errorElements = document.querySelectorAll('.error-message');
    const formGroups = document.querySelectorAll('.form-group');
    
    errorElements.forEach(element => {
        element.style.display = 'none';
    });
    
    formGroups.forEach(group => {
        group.classList.remove('error');
    });
}

function showLoading(show) {
    const spinner = document.getElementById('loadingSpinner');
    const buttonText = document.getElementById('buttonText');
    const button = document.querySelector('.register-submit-btn');
    
    if (show) {
        spinner.style.display = 'inline-block';
        buttonText.textContent = 'Registering...';
        button.disabled = true;
    } else {
        spinner.style.display = 'none';
        buttonText.textContent = 'Register';
        button.disabled = false;
    }
}

// Real-time validation
document.getElementById('confirmPassword').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    if (password && confirmPassword && password !== confirmPassword) {
        showError('confirmPassword', 'Passwords do not match');
    } else if (password === confirmPassword && confirmPassword.length >= 8) {
        document.getElementById('confirmPassword').parentElement.classList.remove('error');
        const errorElement = document.querySelector('#confirmPassword + .error-message');
        if (errorElement) {
            errorElement.style.display = 'none';
        }
    }
});

// Username availability check (simulated)
document.getElementById('username').addEventListener('blur', function() {
    const username = this.value.trim();
    if (username.length >= 4) {
        // Simulate username check
        setTimeout(() => {
            const unavailableUsernames = ['admin', 'test', 'user123', 'cricket'];
            if (unavailableUsernames.includes(username.toLowerCase())) {
                showError('username', 'Username already taken');
            }
        }, 500);
    }
});

// Navigation
document.querySelector('.login-btn').addEventListener('click', function(e) {
    e.preventDefault();
    alert('Login page would be displayed here.');
});

// Home navigation
const homeLink = document.querySelector('.nav-menu a[href="#home"]');
if (homeLink) {
    homeLink.addEventListener('click', function(e) {
        e.preventDefault();
        window.location.href = '/'; // Navigate back to home page
    });
} 