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
        const monthDiff = today.getMonth() - birthDate.getMonth();
        
        // Adjust age if birthday hasn't occurred this year
        const adjustedAge = (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) 
            ? age - 1 : age;
        
        if (birthDate > today) {
            showError('dateOfBirth', 'Date of birth cannot be in the future');
            isValid = false;
        } else if (adjustedAge < 5) {
            showError('dateOfBirth', 'You must be at least 5 years old to register');
            isValid = false;
        } else if (adjustedAge > 100) {
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
    
    // Enhanced phone number validation
    const phoneDigitsOnly = formData.contactNumber.replace(/[^0-9]/g, '');
    if (phoneDigitsOnly.length < 10) {
        showError('contactNumber', 'Contact number must be at least 10 digits');
        isValid = false;
    } else if (!(/^[0-9+\-\s()]+$/.test(formData.contactNumber))) {
        showError('contactNumber', 'Please enter a valid phone number (digits, +, -, spaces, or parentheses only)');
        isValid = false;
    } else if (phoneDigitsOnly.length > 15) {
        showError('contactNumber', 'Contact number cannot exceed 15 digits');
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
    
    // Enhanced password validation
    if (formData.password.length < 8) {
        showError('password', 'Password must be at least 8 characters long');
        isValid = false;
    } else {
        const passwordErrors = [];
        
        if (!/[A-Z]/.test(formData.password)) {
            passwordErrors.push('one uppercase letter');
        }
        if (!/[a-z]/.test(formData.password)) {
            passwordErrors.push('one lowercase letter');
        }
        if (!/[0-9]/.test(formData.password)) {
            passwordErrors.push('one number');
        }
        if (!/[!@#$%^&*(),.?":{}|<>]/.test(formData.password)) {
            passwordErrors.push('one special character (!@#$%^&*(),.?":{}|<>)');
        }
        
        if (passwordErrors.length > 0) {
            showError('password', 'Password must contain at least ' + passwordErrors.join(', '));
            isValid = false;
        }
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

// Real-time password strength validation
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    
    if (password.length > 0 && password.length < 8) {
        showError('password', 'Password must be at least 8 characters long');
    } else if (password.length >= 8) {
        const passwordErrors = [];
        
        if (!/[A-Z]/.test(password)) {
            passwordErrors.push('one uppercase letter');
        }
        if (!/[a-z]/.test(password)) {
            passwordErrors.push('one lowercase letter');
        }
        if (!/[0-9]/.test(password)) {
            passwordErrors.push('one number');
        }
        if (!/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
            passwordErrors.push('one special character');
        }
        
        if (passwordErrors.length > 0) {
            showError('password', 'Password needs: ' + passwordErrors.join(', '));
        } else {
            // Password is strong
            document.getElementById('password').parentElement.classList.remove('error');
            const errorElement = document.querySelector('#password + .error-message');
            if (errorElement) {
                errorElement.style.display = 'none';
            }
        }
    }
});

// Real-time phone number validation
document.getElementById('contactNumber').addEventListener('input', function() {
    const phone = this.value;
    const phoneDigitsOnly = phone.replace(/[^0-9]/g, '');
    
    if (phone.length > 0) {
        if (phoneDigitsOnly.length < 10) {
            showError('contactNumber', 'Contact number must be at least 10 digits');
        } else if (!(/^[0-9+\-\s()]+$/.test(phone))) {
            showError('contactNumber', 'Only digits, +, -, spaces, or parentheses allowed');
        } else if (phoneDigitsOnly.length > 15) {
            showError('contactNumber', 'Contact number cannot exceed 15 digits');
        } else {
            // Valid phone number
            document.getElementById('contactNumber').parentElement.classList.remove('error');
            const errorElement = document.querySelector('#contactNumber + .error-message');
            if (errorElement) {
                errorElement.style.display = 'none';
            }
        }
    }
});

// Real-time date of birth validation
document.getElementById('dateOfBirth').addEventListener('change', function() {
    const birthDate = new Date(this.value);
    const today = new Date();
    const age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    
    const adjustedAge = (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) 
        ? age - 1 : age;
    
    if (birthDate > today) {
        showError('dateOfBirth', 'Date of birth cannot be in the future');
    } else if (adjustedAge < 5) {
        showError('dateOfBirth', 'You must be at least 5 years old to register');
    } else if (adjustedAge > 100) {
        showError('dateOfBirth', 'Please enter a valid date of birth');
    } else {
        // Valid date
        document.getElementById('dateOfBirth').parentElement.classList.remove('error');
        const errorElement = document.querySelector('#dateOfBirth + .error-message');
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