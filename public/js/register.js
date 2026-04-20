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
    const submitter = e.submitter;

    if (submitter && submitter.id === 'paymentPortalBtn') {
        clearErrors();

        const membershipPlan = getField('membershipPlan') ? getField('membershipPlan').value.trim() : '';
        if (!membershipPlan) {
            e.preventDefault();
            showError('membershipPlan', 'Please select a membership plan before opening the payment portal');
        }
        return;
    }

    e.preventDefault();

    // Clear previous errors
    clearErrors();

    // Get form data
    const formData = {
        firstName: getField('firstName').value.trim(),
        lastName: getField('lastName').value.trim(),
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
    if (formData.firstName.length < 2) {
        showError('firstName', 'Please enter your first name (at least 2 characters)');
        isValid = false;
    }

    if (formData.lastName.length < 2) {
        showError('lastName', 'Please enter your last name (at least 2 characters)');
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
    
        if (formData.address.length > 0 && formData.address.length < 10) {
        showError('address', 'Please enter a complete address');
        isValid = false;
    }
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(formData.email)) {
        showError('email', 'Please enter a valid email address');
        isValid = false;
    }
    
    // Phone number validation: exactly 10 digits and must start with 0
    const phoneDigitsOnly = formData.contactNumber.replace(/[^0-9]/g, '');
    if (!/^0[0-9]{9}$/.test(phoneDigitsOnly)) {
        showError('contactNumber', 'Contact number must be exactly 10 digits and start with 0');
        isValid = false;
    } else {
        getField('contactNumber').value = phoneDigitsOnly;
    }
    
    if (formData.school.length > 0 && formData.school.length < 2) {
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
        if (!/[!@#$%^&*(),.?":{}|<>\/]/.test(formData.password)) {
            passwordErrors.push('one special character (!@#$%^&*(),.?":{}|<>/)');
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
    const spinner = document.getElementById('registerSubmitSpinner');
    const buttonText = document.getElementById('registerSubmitText');
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

const membershipPlanField = getField('membershipPlan');
const paymentPortalButton = document.getElementById('paymentPortalBtn');
const paymentPortalButtonText = document.getElementById('paymentPortalBtnText');
const selectedPlanFeeHint = document.getElementById('selectedPlanFeeHint');

function updatePaymentPortalCopy() {
    if (!membershipPlanField || !paymentPortalButtonText || !selectedPlanFeeHint || !paymentPortalButton) {
        return;
    }

    const selectedOption = membershipPlanField.options[membershipPlanField.selectedIndex];
    const fee = selectedOption ? selectedOption.getAttribute('data-fee') : '';
    const planName = selectedOption ? selectedOption.getAttribute('data-plan-name') : '';
    const usesRecurringBilling = selectedOption ? selectedOption.getAttribute('data-recurring-billing') === '1' : false;

    if (membershipPlanField.value && usesRecurringBilling && fee) {
        paymentPortalButton.disabled = false;
        paymentPortalButtonText.textContent = `PayNow Rs. ${fee}`;
        selectedPlanFeeHint.textContent = `${planName} plan selected. You will be redirected with a payment amount of Rs. ${fee}.`;
    } else if (membershipPlanField.value && !usesRecurringBilling) {
        paymentPortalButton.disabled = true;
        paymentPortalButtonText.textContent = 'PayNow Unavailable';
        selectedPlanFeeHint.textContent = `${planName} does not use monthly billing. Pay per facility booking after account creation.`;
    } else {
        paymentPortalButton.disabled = true;
        paymentPortalButtonText.textContent = 'PayNow';
        selectedPlanFeeHint.textContent = 'Choose a membership plan, then continue to the payment portal with that monthly fee.';
    }
}

if (membershipPlanField) {
    membershipPlanField.addEventListener('change', function() {
        clearFieldError('membershipPlan');
        updatePaymentPortalCopy();
    });

    updatePaymentPortalCopy();
}

if (paymentPortalButton) {
    paymentPortalButton.addEventListener('click', function(e) {
        if (paymentPortalButton.disabled) {
            e.preventDefault();
            return;
        }

        if (!membershipPlanField || !membershipPlanField.value) {
            e.preventDefault();
            showError('membershipPlan', 'Please select a membership plan before opening the payment portal');
        }
    });
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
        if (!/[!@#$%^&*(),.?":{}|<>\/]/.test(password)) {
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
contactNumberField.addEventListener('keydown', function(e) {
    const allowedKeys = ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab', 'Home', 'End'];
    if (allowedKeys.includes(e.key) || (e.ctrlKey || e.metaKey)) {
        return;
    }

    if (!/^[0-9]$/.test(e.key)) {
        e.preventDefault();
    }
});

contactNumberField.addEventListener('input', function() {
    const phoneDigitsOnly = this.value.replace(/[^0-9]/g, '').slice(0, 10);
    this.value = phoneDigitsOnly;
    
    if (phoneDigitsOnly.length > 0) {
        if (!/^0/.test(phoneDigitsOnly)) {
            showError('contactNumber', 'Contact number must start with 0');
        } else if (phoneDigitsOnly.length !== 10) {
            showError('contactNumber', 'Contact number must be exactly 10 digits');
        } else {
            clearFieldError('contactNumber');
        }
    }
});

contactNumberField.addEventListener('blur', function() {
    const phoneDigitsOnly = this.value.replace(/[^0-9]/g, '');

    if (phoneDigitsOnly.length === 0) {
        return;
    }

    if (!/^0/.test(phoneDigitsOnly)) {
        showError('contactNumber', 'Contact number must start with 0');
    } else if (phoneDigitsOnly.length !== 10) {
        showError('contactNumber', 'Contact number must be exactly 10 digits');
    } else {
        clearFieldError('contactNumber');
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

// Membership plan modal.
// The PHP view renders the plans from the database; this JavaScript only handles selection UI.
(function initPlanDetailsModal() {
    const modal = document.getElementById('planDetailsModal');
    const openBtn = document.getElementById('seePlanDetailsBtn');
    const closeBtn = document.getElementById('closePlanModal');
    const dropdown = document.getElementById('membershipPlan');

    if (!modal || !openBtn || !closeBtn || !dropdown) {
        return;
    }

    function closeModal() {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    function highlightSelected() {
        document.querySelectorAll('.plan-card').forEach(function (card) {
            card.classList.toggle('plan-card-selected', card.getAttribute('data-plan-id') === dropdown.value);
        });
    }

    openBtn.addEventListener('click', function () {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    });

    closeBtn.addEventListener('click', closeModal);

    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeModal();
        }
    });

    document.querySelectorAll('.plan-select-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            dropdown.value = this.getAttribute('data-plan-id');
            highlightSelected();
            closeModal();
        });
    });

    dropdown.addEventListener('change', highlightSelected);
    highlightSelected();
}());
