const registrationForm = document.getElementById('registrationForm');

function getField(fieldName) {
    return document.getElementById(fieldName);
}

function getErrorElement(fieldName) {
    return document.getElementById(`${fieldName}Error`);
}

function clearFieldError(fieldName) {
    const field = getField(fieldName);
    const errorElement = getErrorElement(fieldName);

    if (field) {
        const formGroup = field.closest('.form-group');
        if (formGroup) {
            formGroup.classList.remove('error');
        }
    }

    if (errorElement) {
        errorElement.style.display = 'none';
        errorElement.textContent = '';
    }
}

// Form validation and submission
if (registrationForm) {
registrationForm.addEventListener('submit', function(e) {
    e.preventDefault();

    // Clear previous errors
    clearErrors();

    // Get form data
    const formData = {
        fullName: getField('fullName').value.trim(),
        dateOfBirth: getField('dateOfBirth').value,
        address: getField('address').value.trim(),
        email: getField('email').value.trim(),
        contactNumber: getField('contactNumber').value.trim(),
        school: getField('school').value.trim(),
        username: getField('username').value.trim(),
        password: getField('password').value,
        confirmPassword: getField('confirmPassword').value,
        membershipPlan: getField('membershipPlan') ? getField('membershipPlan').value.trim() : ''
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
        const howage = today.getFullYear() - birthDate.getFullYear();
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

    if (!formData.membershipPlan) {
        showError('membershipPlan', 'Please select a membership plan');
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
}

function showError(fieldName, message) {
    const field = getField(fieldName);
    const errorElement = getErrorElement(fieldName);
    
    if (field && errorElement) {
        const formGroup = field.closest('.form-group');
        if (formGroup) {
            formGroup.classList.add('error');
        }
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

    if (!spinner || !buttonText || !button) {
        return;
    }
    
    if (show) {
        spinner.style.display = 'inline-block';
        buttonText.textContent = 'Registering...';
        button.disabled = true;
    } else {
        spinner.style.display = 'none';
        buttonText.textContent = 'Create Account';
        button.disabled = false;
    }
}

// Real-time validation
const confirmPasswordField = getField('confirmPassword');
if (confirmPasswordField) {
confirmPasswordField.addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    if (password && confirmPassword && password !== confirmPassword) {
        showError('confirmPassword', 'Passwords do not match');
    } else if (password === confirmPassword && confirmPassword.length >= 8) {
        clearFieldError('confirmPassword');
    }
});
}

// Real-time password strength validation
const passwordField = getField('password');
if (passwordField) {
passwordField.addEventListener('input', function() {
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
            clearFieldError('password');
        }
    }
});
}

// Real-time phone number validation
const contactNumberField = getField('contactNumber');
if (contactNumberField) {
contactNumberField.addEventListener('input', function() {
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
            clearFieldError('contactNumber');
        }
    }
});
}

// Real-time date of birth validation
const dateOfBirthField = getField('dateOfBirth');
if (dateOfBirthField) {
dateOfBirthField.addEventListener('change', function() {
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
        clearFieldError('dateOfBirth');
    }
});
}

// Username availability check (simulated)
const usernameField = getField('username');
if (usernameField) {
usernameField.addEventListener('blur', function() {
    const username = this.value.trim();
    if (username.length >= 4) {
        // Simulate username check
        setTimeout(() => {
            const unavailableUsernames = ['admin', 'test', 'user123', 'cricket'];
            if (unavailableUsernames.includes(username.toLowerCase())) {
                showError('username', 'Username already taken');
            } else {
                clearFieldError('username');
            }
        }, 500);
    }
});
}

// Navigation
const loginButton = document.querySelector('.login-btn');
if (loginButton) {
    loginButton.addEventListener('click', function(e) {
        e.preventDefault();
        alert('Login page would be displayed here.');
    });
}

// Home navigation
const homeLink = document.querySelector('.nav-menu a[href="#home"]');
if (homeLink) {
    homeLink.addEventListener('click', function(e) {
        e.preventDefault();
        window.location.href = '/'; // Navigate back to home page
    });
} 