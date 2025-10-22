// Staff Management JavaScript - Elite Cricket Academy
console.log('✅ staff-management.js loaded successfully!');

// Sample staff data (no database connection)
let staffMembers = [
    {
        id: 1,
        firstName: 'Michael',
        lastName: 'Johnson',
        email: 'michael.johnson@elite.com',
        phone: '+1-555-0101',
        role: 'head_coach',
        specialization: 'Batting Coach',
        joinDate: '2023-01-15',
        status: 'active',
        address: '123 Cricket Lane, Sports City'
    },
    {
        id: 2,
        firstName: 'Sarah',
        lastName: 'Williams',
        email: 'sarah.williams@elite.com',
        phone: '+1-555-0102',
        role: 'coach',
        specialization: 'Bowling Coach',
        joinDate: '2023-03-20',
        status: 'active',
        address: '456 Bowl Street, Sports City'
    },
    {
        id: 3,
        firstName: 'David',
        lastName: 'Brown',
        email: 'david.brown@elite.com',
        phone: '+1-555-0103',
        role: 'trainer',
        specialization: 'Fitness Trainer',
        joinDate: '2023-02-10',
        status: 'active',
        address: '789 Fitness Ave, Sports City'
    },
    {
        id: 4,
        firstName: 'Emma',
        lastName: 'Davis',
        email: 'emma.davis@elite.com',
        phone: '+1-555-0104',
        role: 'coach',
        specialization: 'Fielding Coach',
        joinDate: '2023-04-05',
        status: 'active',
        address: '321 Field Road, Sports City'
    },
    {
        id: 5,
        firstName: 'James',
        lastName: 'Miller',
        email: 'james.miller@elite.com',
        phone: '+1-555-0105',
        role: 'admin',
        specialization: 'Operations Manager',
        joinDate: '2022-11-01',
        status: 'active',
        address: '654 Admin Plaza, Sports City'
    },
    {
        id: 6,
        firstName: 'Lisa',
        lastName: 'Anderson',
        email: 'lisa.anderson@elite.com',
        phone: '+1-555-0106',
        role: 'trainer',
        specialization: 'Strength & Conditioning',
        joinDate: '2023-05-12',
        status: 'active',
        address: '987 Gym Street, Sports City'
    },
    {
        id: 7,
        firstName: 'Robert',
        lastName: 'Taylor',
        email: 'robert.taylor@elite.com',
        phone: '+1-555-0107',
        role: 'shopkeeper',
        specialization: 'Equipment Manager',
        joinDate: '2023-01-30',
        status: 'active',
        address: '147 Shop Lane, Sports City'
    },
    {
        id: 8,
        firstName: 'Jennifer',
        lastName: 'Thomas',
        email: 'jennifer.thomas@elite.com',
        phone: '+1-555-0108',
        role: 'coach',
        specialization: 'Wicket-keeping Coach',
        joinDate: '2023-06-01',
        status: 'active',
        address: '258 Keeper Road, Sports City'
    },
    {
        id: 9,
        firstName: 'Christopher',
        lastName: 'Jackson',
        email: 'christopher.jackson@elite.com',
        phone: '+1-555-0109',
        role: 'coach',
        specialization: 'Spin Bowling',
        joinDate: '2023-02-28',
        status: 'inactive',
        address: '369 Spin Avenue, Sports City'
    },
    {
        id: 10,
        firstName: 'Amanda',
        lastName: 'White',
        email: 'amanda.white@elite.com',
        phone: '+1-555-0110',
        role: 'trainer',
        specialization: 'Mental Coach',
        joinDate: '2023-07-15',
        status: 'active',
        address: '741 Mind Street, Sports City'
    },
    {
        id: 11,
        firstName: 'Daniel',
        lastName: 'Harris',
        email: 'daniel.harris@elite.com',
        phone: '+1-555-0111',
        role: 'coach',
        specialization: 'Fast Bowling',
        joinDate: '2023-03-10',
        status: 'active',
        address: '852 Speed Lane, Sports City'
    },
    {
        id: 12,
        firstName: 'Michelle',
        lastName: 'Martin',
        email: 'michelle.martin@elite.com',
        phone: '+1-555-0112',
        role: 'admin',
        specialization: 'HR Manager',
        joinDate: '2023-01-05',
        status: 'active',
        address: '963 HR Plaza, Sports City'
    },
    {
        id: 13,
        firstName: 'Kevin',
        lastName: 'Garcia',
        email: 'kevin.garcia@elite.com',
        phone: '+1-555-0113',
        role: 'trainer',
        specialization: 'Physiotherapist',
        joinDate: '2023-04-20',
        status: 'active',
        address: '159 Health Road, Sports City'
    },
    {
        id: 14,
        firstName: 'Rebecca',
        lastName: 'Martinez',
        email: 'rebecca.martinez@elite.com',
        phone: '+1-555-0114',
        role: 'coach',
        specialization: 'Junior Development',
        joinDate: '2023-05-05',
        status: 'active',
        address: '357 Junior Street, Sports City'
    },
    {
        id: 15,
        firstName: 'Brian',
        lastName: 'Robinson',
        email: 'brian.robinson@elite.com',
        phone: '+1-555-0115',
        role: 'shopkeeper',
        specialization: 'Inventory Manager',
        joinDate: '2023-02-15',
        status: 'active',
        address: '486 Stock Avenue, Sports City'
    },
    {
        id: 16,
        firstName: 'Nicole',
        lastName: 'Clark',
        email: 'nicole.clark@elite.com',
        phone: '+1-555-0116',
        role: 'coach',
        specialization: 'Power Hitting',
        joinDate: '2023-06-10',
        status: 'active',
        address: '753 Power Road, Sports City'
    },
    {
        id: 17,
        firstName: 'Andrew',
        lastName: 'Rodriguez',
        email: 'andrew.rodriguez@elite.com',
        phone: '+1-555-0117',
        role: 'coach',
        specialization: 'All-rounder Coach',
        joinDate: '2023-03-25',
        status: 'active',
        address: '951 Allround Lane, Sports City'
    },
    {
        id: 18,
        firstName: 'Stephanie',
        lastName: 'Lewis',
        email: 'stephanie.lewis@elite.com',
        phone: '+1-555-0118',
        role: 'trainer',
        specialization: 'Nutrition Coach',
        joinDate: '2023-07-01',
        status: 'active',
        address: '357 Health Plaza, Sports City'
    },
    {
        id: 19,
        firstName: 'Matthew',
        lastName: 'Lee',
        email: 'matthew.lee@elite.com',
        phone: '+1-555-0119',
        role: 'coach',
        specialization: 'Strategy Coach',
        joinDate: '2023-04-15',
        status: 'active',
        address: '159 Strategy Avenue, Sports City'
    },
    {
        id: 20,
        firstName: 'Jessica',
        lastName: 'Walker',
        email: 'jessica.walker@elite.com',
        phone: '+1-555-0120',
        role: 'admin',
        specialization: 'Finance Manager',
        joinDate: '2023-01-20',
        status: 'active',
        address: '753 Finance Street, Sports City'
    },
    {
        id: 21,
        firstName: 'Ryan',
        lastName: 'Hall',
        email: 'ryan.hall@elite.com',
        phone: '+1-555-0121',
        role: 'coach',
        specialization: 'Technical Coach',
        joinDate: '2023-05-20',
        status: 'active',
        address: '951 Tech Road, Sports City'
    },
    {
        id: 22,
        firstName: 'Angela',
        lastName: 'Young',
        email: 'angela.young@elite.com',
        phone: '+1-555-0122',
        role: 'coach',
        specialization: 'Youth Coach',
        joinDate: '2023-06-25',
        status: 'inactive',
        address: '147 Youth Lane, Sports City'
    }
];

// Role Requests Data (from user registrations)
let roleRequests = [
    {
        id: 1,
        firstName: 'Rajesh',
        lastName: 'Kumar',
        email: 'rajesh.kumar@email.com',
        phone: '+94 77 123 4567',
        requestedRole: 'coach',
        specialization: 'Spin Bowling Coach',
        experience: '8 years',
        qualifications: 'Level 3 Cricket Coaching Certificate, Former Provincial Player',
        requestDate: new Date('2025-10-15T10:30:00'),
        status: 'pending'
    },
    {
        id: 2,
        firstName: 'Samantha',
        lastName: 'Silva',
        email: 'samantha.silva@email.com',
        phone: '+94 71 987 6543',
        requestedRole: 'trainer',
        specialization: 'Sports Physiotherapist',
        experience: '5 years',
        qualifications: 'MSc in Sports Medicine, Certified Physiotherapist',
        requestDate: new Date('2025-10-17T14:20:00'),
        status: 'pending'
    },
    {
        id: 3,
        firstName: 'Kasun',
        lastName: 'Perera',
        email: 'kasun.perera@email.com',
        phone: '+94 76 456 7890',
        requestedRole: 'coach',
        specialization: 'Batting Technique Coach',
        experience: '10 years',
        qualifications: 'Level 4 Coaching, Ex-First Class Cricketer',
        requestDate: new Date('2025-10-18T09:15:00'),
        status: 'pending'
    }
];

let filteredStaff = [...staffMembers];
let currentPage = 1;
const itemsPerPage = 10;

// Render Role Requests
function renderRoleRequests() {
    const pendingRequests = roleRequests.filter(r => r.status === 'pending');
    const requestsSection = document.getElementById('roleRequestsSection');
    const requestsTableBody = document.getElementById('roleRequestsTableBody');
    const requestCount = document.getElementById('requestCount');
    
    if (!requestsSection || !requestsTableBody) return;
    
    if (pendingRequests.length === 0) {
        requestsSection.style.display = 'none';
        return;
    }
    
    requestsSection.style.display = 'block';
    requestCount.textContent = pendingRequests.length;
    
    requestsTableBody.innerHTML = pendingRequests.map(request => {
        return `
            <tr data-request-id="${request.id}">
                <td class="name-cell">${request.firstName} ${request.lastName}</td>
                <td class="email-cell">${request.email}</td>
                <td>
                    <span class="request-role ${request.requestedRole}">${getRoleDisplayName(request.requestedRole)}</span>
                </td>
                <td class="description-cell">
                    <strong>${request.specialization}</strong><br>
                    <small style="color: #666;">Experience: ${request.experience}</small><br>
                    <small style="color: #666;"><i class="fas fa-phone"></i> ${request.phone}</small>
                </td>
                <td>
                    <div class="request-actions">
                        <button class="btn-approve" onclick="approveRoleRequest(${request.id})">
                            <i class="fas fa-check"></i> Accept
                        </button>
                        <button class="btn-decline" onclick="declineRoleRequest(${request.id})">
                            <i class="fas fa-times"></i> Decline
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

// Helper function to get role display name
function getRoleDisplayName(role) {
    const roleNames = {
        'coach': 'Coach',
        'head_coach': 'Head Coach',
        'trainer': 'Trainer',
        'admin': 'Administrator',
        'shopkeeper': 'Shop Staff'
    };
    return roleNames[role] || role;
}

// Helper function to get time ago
function getTimeAgo(date) {
    const now = new Date();
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMs / 3600000);
    const diffDays = Math.floor(diffMs / 86400000);
    
    if (diffMins < 60) return `${diffMins} minute${diffMins !== 1 ? 's' : ''} ago`;
    if (diffHours < 24) return `${diffHours} hour${diffHours !== 1 ? 's' : ''} ago`;
    return `${diffDays} day${diffDays !== 1 ? 's' : ''} ago`;
}

// Approve Role Request
function approveRoleRequest(requestId) {
    const request = roleRequests.find(r => r.id === requestId);
    if (!request) return;
    
    // Show confirmation
    if (!confirm(`Approve ${request.firstName} ${request.lastName} as ${getRoleDisplayName(request.requestedRole)}?`)) {
        return;
    }
    
    // Add to staff members
    const newStaff = {
        id: staffMembers.length + 1,
        firstName: request.firstName,
        lastName: request.lastName,
        email: request.email,
        phone: request.phone,
        role: request.requestedRole,
        specialization: request.specialization,
        joinDate: new Date().toISOString().split('T')[0],
        status: 'active',
        address: 'To be updated',
        experience: request.experience,
        qualifications: request.qualifications
    };
    
    staffMembers.push(newStaff);
    filteredStaff = [...staffMembers];
    
    // Remove from requests
    request.status = 'approved';
    
    // Re-render
    renderRoleRequests();
    renderStaffTable();
    updateStats();
    
    showNotification(`${request.firstName} ${request.lastName} has been approved and added to staff!`, 'success');
}

// Decline Role Request
function declineRoleRequest(requestId) {
    const request = roleRequests.find(r => r.id === requestId);
    if (!request) return;
    
    // Show confirmation
    if (!confirm(`Decline role request from ${request.firstName} ${request.lastName}?`)) {
        return;
    }
    
    // Remove from requests
    request.status = 'declined';
    
    // Re-render
    renderRoleRequests();
    
    showNotification(`Role request from ${request.firstName} ${request.lastName} has been declined.`, 'info');
}

// Initialize on DOM load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing staff management...');
    
    // Render role requests
    renderRoleRequests();
    
    // Update time
    updateCurrentTime();
    setInterval(updateCurrentTime, 1000);
    
    // Initialize modals
    initializeModals();
    
    // Initialize search and filters
    initializeSearchAndFilters();
    
    // Render initial staff table
    renderStaffTable();
    
    // Initialize pagination
    initializePagination();
});

// Update current time
function updateCurrentTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
    const timeElement = document.getElementById('currentTime');
    if (timeElement) {
        timeElement.textContent = timeString;
    }
}

// Initialize Modals
function initializeModals() {
    console.log('🔧 Initializing modals...');
    
    // Add Staff Modal with Wizard
    const addStaffBtn = document.getElementById('addStaffBtn');
    const addStaffModal = document.getElementById('addStaffModal');
    const modalOverlay = document.getElementById('modalOverlay');
    const cancelBtn = document.getElementById('cancelBtn');
    
    console.log('Add Staff Button:', addStaffBtn);
    console.log('Add Staff Modal:', addStaffModal);
    
    if (addStaffBtn) {
        console.log('✅ Add Staff button found, adding click listener');
        addStaffBtn.addEventListener('click', () => {
            console.log('🎯 Add Staff button clicked!');
            addStaffModal.classList.add('active');
            const form = document.getElementById('addStaffForm');
            if (form) {
                form.reset();
            }
            // Set today's date as default
            const joinDateInput = document.getElementById('joinDate');
            if (joinDateInput) {
                joinDateInput.valueAsDate = new Date();
            }
            // Reset wizard to step 1
            goToWizardStep(1);
        });
    } else {
        console.error('❌ Add Staff button NOT found!');
    }
    
    if (modalOverlay) {
        modalOverlay.addEventListener('click', () => {
            console.log('Closing modal via overlay');
            addStaffModal.classList.remove('active');
            goToWizardStep(1); // Reset on close
        });
    }
    
    if (cancelBtn) {
        cancelBtn.addEventListener('click', () => {
            console.log('Cancel button clicked - redirecting to staff page');
            // Redirect to staff management page
            window.location.href = URLROOT + '/admin/staff';
        });
    }
    
    // Wizard Navigation
    const wizardNextBtn = document.getElementById('wizardNextBtn');
    const wizardPrevBtn = document.getElementById('wizardPrevBtn');
    
    console.log('Wizard Next Button:', wizardNextBtn);
    console.log('Wizard Prev Button:', wizardPrevBtn);
    
    if (wizardNextBtn) {
        wizardNextBtn.addEventListener('click', () => {
            console.log('Next button clicked');
            const currentStep = getCurrentWizardStep();
            if (validateWizardStep(currentStep)) {
                if (currentStep < 4) {
                    goToWizardStep(currentStep + 1);
                }
            }
        });
    }
    
    if (wizardPrevBtn) {
        wizardPrevBtn.addEventListener('click', () => {
            console.log('Previous button clicked');
            const currentStep = getCurrentWizardStep();
            if (currentStep > 1) {
                goToWizardStep(currentStep - 1);
            }
        });
    }
    
    // Edit Staff Modal
    const editStaffModal = document.getElementById('editStaffModal');
    const closeEditModal = document.getElementById('closeEditModal');
    const editModalOverlay = document.getElementById('editModalOverlay');
    const cancelEditBtn = document.getElementById('cancelEditBtn');
    
    if (closeEditModal) {
        closeEditModal.addEventListener('click', () => {
            editStaffModal.classList.remove('active');
        });
    }
    
    if (editModalOverlay) {
        editModalOverlay.addEventListener('click', () => {
            editStaffModal.classList.remove('active');
        });
    }
    
    if (cancelEditBtn) {
        cancelEditBtn.addEventListener('click', () => {
            editStaffModal.classList.remove('active');
        });
    }
    
    // Delete Staff Modal
    const deleteStaffModal = document.getElementById('deleteStaffModal');
    const closeDeleteModal = document.getElementById('closeDeleteModal');
    const deleteModalOverlay = document.getElementById('deleteModalOverlay');
    const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
    
    if (closeDeleteModal) {
        closeDeleteModal.addEventListener('click', () => {
            deleteStaffModal.classList.remove('active');
        });
    }
    
    if (deleteModalOverlay) {
        deleteModalOverlay.addEventListener('click', () => {
            deleteStaffModal.classList.remove('active');
        });
    }
    
    if (cancelDeleteBtn) {
        cancelDeleteBtn.addEventListener('click', () => {
            deleteStaffModal.classList.remove('active');
        });
    }
    
    // Form Submissions
    const addStaffForm = document.getElementById('addStaffForm');
    if (addStaffForm) {
        addStaffForm.addEventListener('submit', handleAddStaff);
    }
    
    const editStaffForm = document.getElementById('editStaffForm');
    if (editStaffForm) {
        editStaffForm.addEventListener('submit', handleEditStaff);
    }
    
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', handleDeleteStaff);
    }
}

// Wizard Functions
function getCurrentWizardStep() {
    const activeStep = document.querySelector('.wizard-step-content.active');
    const currentStep = activeStep ? parseInt(activeStep.dataset.step) : 1;
    console.log('Current wizard step:', currentStep);
    return currentStep;
}

function goToWizardStep(step) {
    console.log(`🔄 Going to wizard step ${step}`);
    
    // Update step contents
    document.querySelectorAll('.wizard-step-content').forEach(content => {
        content.classList.remove('active');
    });
    const targetContent = document.querySelector(`.wizard-step-content[data-step="${step}"]`);
    if (targetContent) {
        targetContent.classList.add('active');
        console.log(`✅ Step ${step} content activated`);
    } else {
        console.error(`❌ Step ${step} content not found!`);
    }
    
    // Update progress indicators
    document.querySelectorAll('.wizard-step').forEach(wizardStep => {
        const stepNum = parseInt(wizardStep.dataset.step);
        wizardStep.classList.remove('active', 'completed');
        
        if (stepNum === step) {
            wizardStep.classList.add('active');
        } else if (stepNum < step) {
            wizardStep.classList.add('completed');
        }
    });
    
    // Update button visibility
    const prevBtn = document.getElementById('wizardPrevBtn');
    const nextBtn = document.getElementById('wizardNextBtn');
    const submitBtn = document.getElementById('wizardSubmitBtn');
    
    if (prevBtn) {
        prevBtn.style.display = step === 1 ? 'none' : 'inline-flex';
    }
    
    if (nextBtn && submitBtn) {
        if (step === 2) {
            nextBtn.style.display = 'none';
            submitBtn.style.display = 'inline-flex';
            // Update review section
            updateReviewSection();
        } else {
            nextBtn.style.display = 'inline-flex';
            submitBtn.style.display = 'none';
        }
    }
}

function validateWizardStep(step) {
    let isValid = true;
    let requiredFields = [];
    
    switch(step) {
        case 1:
            requiredFields = ['firstName', 'lastName', 'email', 'phone', 'role', 'joinDate'];
            break;
    }
    
    requiredFields.forEach(fieldName => {
        const field = document.getElementById(fieldName);
        if (field && !field.value.trim()) {
            isValid = false;
            field.style.borderColor = '#ef4444';
            setTimeout(() => {
                field.style.borderColor = '';
            }, 2000);
        }
    });
    
    if (!isValid) {
        showNotification('Please fill in all required fields', 'error');
    }
    
    return isValid;
}

function updateReviewSection() {
    // Personal Information
    const fullName = document.getElementById('fullName').value;
    const dob = document.getElementById('dateOfBirth').value;
    const school = document.getElementById('school').value;
    
    document.getElementById('reviewFullName').textContent = fullName || '-';
    document.getElementById('reviewDOB').textContent = dob ? new Date(dob).toLocaleDateString() : '-';
    document.getElementById('reviewSchool').textContent = school || '-';
    
    // Contact Information
    document.getElementById('reviewEmail').textContent = document.getElementById('email').value || '-';
    document.getElementById('reviewPhone').textContent = document.getElementById('phone').value || '-';
    document.getElementById('reviewAddress').textContent = document.getElementById('address').value || '-';
    
    // Login Information
    document.getElementById('reviewUsername').textContent = document.getElementById('username').value || '-';
    
    // Role & Notes
    const roleSelect = document.getElementById('role');
    const roleText = roleSelect.options[roleSelect.selectedIndex]?.text || '-';
    
    document.getElementById('reviewRole').textContent = roleText;
    document.getElementById('reviewNotes').textContent = document.getElementById('notes').value || '-';
}

// Handle Add Staff
function handleAddStaff(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    
    // Debug: Log form data
    console.log('=== Submitting Staff Data ===');
    for (let [key, value] of formData.entries()) {
        console.log(`  ${key}: ${value}`);
    }
    
    // Construct URL - using URLROOT from config
    const url = '/Elite/admin/add_staff';
    console.log('Request URL:', url);
    
    // Show loading state
    const submitButton = e.target.querySelector('button[type="submit"]');
    const originalButtonText = submitButton ? submitButton.textContent : '';
    if (submitButton) {
        submitButton.disabled = true;
        submitButton.textContent = 'Adding Staff...';
    }
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('=== Response Received ===');
        console.log('  Status:', response.status, response.statusText);
        console.log('  Content-Type:', response.headers.get("content-type"));
        
        // Check response status
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        // Check content type
        const contentType = response.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
            // Response is not JSON - log it and show error
            return response.text().then(text => {
                console.error('=== Non-JSON Response ===');
                console.error('Raw response (first 1000 chars):', text.substring(0, 1000));
                console.error('Full response logged above ^');
                throw new Error('Server returned HTML instead of JSON. Check PHP errors.');
            });
        }
        
        // Parse JSON
        return response.json();
    })
    .then(data => {
        console.log('=== Parsed JSON Response ===');
        console.log(data);
        
        // Re-enable button
        if (submitButton) {
            submitButton.disabled = false;
            submitButton.textContent = originalButtonText;
        }
        
        // Check for success
        if (data.success || data.status === 'success') {
            console.log('✅ Staff member added successfully!');
            
            // Add to local array for immediate display
            const newStaff = {
                id: data.userId || data.data?.id,
                fullName: formData.get('fullName'),
                email: formData.get('email'),
                phone: formData.get('phone'),
                role: formData.get('role'),
                username: formData.get('username'),
                password: 'staff123456', // Default password
                status: 'active',
                address: formData.get('address') || 'Not provided',
                dateOfBirth: formData.get('dateOfBirth') || null,
                school: formData.get('school') || 'Not specified',
                notes: formData.get('notes') || null
            };
            
            staffMembers.push(newStaff);
            filteredStaff = [...staffMembers];
            
            // Show success message
            showNotification(data.message || 'Staff member added successfully!', 'success');
            
            // Close modal and refresh table
            document.getElementById('addStaffModal').classList.remove('active');
            goToWizardStep(1); // Reset wizard
            e.target.reset(); // Clear form
            renderStaffTable();
            updateStats();
        } else {
            // Show error message from server
            console.warn('⚠️ Server returned error:', data.message);
            showNotification(data.message || 'Failed to add staff member', 'error');
        }
    })
    .catch(error => {
        console.error('=== Error Adding Staff ===');
        console.error('Error type:', error.name);
        console.error('Error message:', error.message);
        console.error('Full error:', error);
        
        // Re-enable button
        if (submitButton) {
            submitButton.disabled = false;
            submitButton.textContent = originalButtonText;
        }
        
        // Show user-friendly error
        const errorMessage = error.message.includes('JSON') 
            ? 'Server error: Please check the console for details.'
            : error.message;
        showNotification(`Error: ${errorMessage}`, 'error');
    });
}

// Handle Edit Staff
function handleEditStaff(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const staffId = parseInt(formData.get('staffId'));
    
    const staffIndex = staffMembers.findIndex(s => s.id === staffId);
    if (staffIndex !== -1) {
        staffMembers[staffIndex] = {
            ...staffMembers[staffIndex],
            firstName: formData.get('firstName'),
            lastName: formData.get('lastName'),
            email: formData.get('email'),
            phone: formData.get('phone'),
            role: formData.get('role'),
            specialization: formData.get('specialization') || 'General',
            status: formData.get('status'),
            address: formData.get('address') || 'Not provided'
        };
        
        filteredStaff = [...staffMembers];
        
        showNotification('Staff member updated successfully!', 'success');
        document.getElementById('editStaffModal').classList.remove('active');
        renderStaffTable();
        updateStats();
    }
}

// Handle Delete Staff
function handleDeleteStaff() {
    const staffId = parseInt(document.getElementById('deleteStaffModal').dataset.staffId);
    
    staffMembers = staffMembers.filter(s => s.id !== staffId);
    filteredStaff = [...staffMembers];
    
    showNotification('Staff member removed successfully!', 'success');
    document.getElementById('deleteStaffModal').classList.remove('active');
    renderStaffTable();
    updateStats();
}

// Show Notification
function showNotification(message, type = 'success', emailSent = false) {
    const icons = {
        'success': 'check-circle',
        'error': 'exclamation-circle',
        'info': 'info-circle'
    };
    
    const colors = {
        'success': '#10b981',
        'error': '#ef4444',
        'info': '#4A90E2'
    };
    
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <i class="fas fa-${icons[type] || 'info-circle'}"></i>
        <span>${message}</span>
        ${emailSent ? '<br><small>Account credentials have been sent to the staff member\'s email.</small>' : ''}
    `;
    
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${colors[type] || '#4A90E2'};
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        z-index: 10000;
        animation: slideIn 0.3s ease;
        max-width: 400px;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Initialize Search and Filters
function initializeSearchAndFilters() {
    const searchInput = document.getElementById('staffSearch');
    const roleFilter = document.getElementById('roleFilter');
    const statusFilter = document.getElementById('statusFilter');
    
    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
    }
    
    if (roleFilter) {
        roleFilter.addEventListener('change', applyFilters);
    }
    
    if (statusFilter) {
        statusFilter.addEventListener('change', applyFilters);
    }
    
    // Export button
    const exportBtn = document.getElementById('exportBtn');
    if (exportBtn) {
        exportBtn.addEventListener('click', exportStaffData);
    }
}

// Apply Filters
function applyFilters() {
    const searchTerm = document.getElementById('staffSearch').value.toLowerCase();
    const roleFilter = document.getElementById('roleFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    
    filteredStaff = staffMembers.filter(staff => {
        const matchesSearch = 
            staff.firstName.toLowerCase().includes(searchTerm) ||
            staff.lastName.toLowerCase().includes(searchTerm) ||
            staff.email.toLowerCase().includes(searchTerm) ||
            staff.role.toLowerCase().includes(searchTerm);
        
        const matchesRole = roleFilter === 'all' || staff.role === roleFilter;
        const matchesStatus = statusFilter === 'all' || staff.status === statusFilter;
        
        return matchesSearch && matchesRole && matchesStatus;
    });
    
    currentPage = 1;
    renderStaffTable();
}

// Render Staff Table
function renderStaffTable() {
    const tbody = document.getElementById('staffTableBody');
    if (!tbody) return;
    
    const start = (currentPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    const pageStaff = filteredStaff.slice(start, end);
    
    if (pageStaff.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8">
                    <div class="empty-state">
                        <i class="fas fa-users-slash"></i>
                        <h3>No Staff Members Found</h3>
                        <p>Try adjusting your search or filters</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = pageStaff.map(staff => `
        <tr>
            <td>
                <input type="checkbox" class="staff-checkbox" data-id="${staff.id}">
            </td>
            <td>
                <div class="staff-member-cell">
                    <img src="https://ui-avatars.com/api/?name=${staff.firstName}+${staff.lastName}&background=4A90E2&color=fff" 
                         alt="${staff.firstName} ${staff.lastName}" 
                         class="staff-avatar">
                    <div class="staff-info">
                        <h4>${staff.firstName} ${staff.lastName}</h4>
                        <p>${staff.specialization}</p>
                    </div>
                </div>
            </td>
            <td>
                <span class="role-badge ${staff.role}">
                    ${formatRole(staff.role)}
                </span>
            </td>
            <td>${staff.email}</td>
            <td>${staff.phone}</td>
            <td>${formatDate(staff.joinDate)}</td>
            <td>
                <span class="status-badge ${staff.status}">
                    ${staff.status}
                </span>
            </td>
            <td>
                <div class="action-buttons">
                    <button class="action-btn view" onclick="viewStaff(${staff.id})" title="View Details">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="action-btn edit" onclick="editStaff(${staff.id})" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="action-btn delete" onclick="deleteStaff(${staff.id})" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
    
    updatePaginationInfo();
}

// View Staff Details
function viewStaff(id) {
    const staff = staffMembers.find(s => s.id === id);
    if (staff) {
        alert(`Staff Details:\n\nName: ${staff.firstName} ${staff.lastName}\nRole: ${formatRole(staff.role)}\nEmail: ${staff.email}\nPhone: ${staff.phone}\nSpecialization: ${staff.specialization}\nJoin Date: ${formatDate(staff.joinDate)}\nStatus: ${staff.status}\nAddress: ${staff.address}`);
    }
}

// Edit Staff
function editStaff(id) {
    const staff = staffMembers.find(s => s.id === id);
    if (staff) {
        document.getElementById('editStaffId').value = staff.id;
        document.getElementById('editFirstName').value = staff.firstName;
        document.getElementById('editLastName').value = staff.lastName;
        document.getElementById('editEmail').value = staff.email;
        document.getElementById('editPhone').value = staff.phone;
        document.getElementById('editRole').value = staff.role;
        document.getElementById('editStatus').value = staff.status;
        document.getElementById('editSpecialization').value = staff.specialization;
        document.getElementById('editAddress').value = staff.address;
        
        document.getElementById('editStaffModal').classList.add('active');
    }
}

// Delete Staff
function deleteStaff(id) {
    const staff = staffMembers.find(s => s.id === id);
    if (staff) {
        document.getElementById('deleteStaffName').textContent = `${staff.firstName} ${staff.lastName}`;
        document.getElementById('deleteStaffModal').dataset.staffId = id;
        document.getElementById('deleteStaffModal').classList.add('active');
    }
}

// Format Role
function formatRole(role) {
    const roleMap = {
        'coach': 'Coach',
        'head_coach': 'Head Coach',
        'trainer': 'Trainer',
        'admin': 'Administrator',
        'shopkeeper': 'Shop Staff'
    };
    return roleMap[role] || role;
}

// Format Date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

// Initialize Pagination
function initializePagination() {
    const prevBtn = document.getElementById('prevPage');
    const nextBtn = document.getElementById('nextPage');
    
    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                renderStaffTable();
            }
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            const maxPages = Math.ceil(filteredStaff.length / itemsPerPage);
            if (currentPage < maxPages) {
                currentPage++;
                renderStaffTable();
            }
        });
    }
}

// Update Pagination Info
function updatePaginationInfo() {
    const start = (currentPage - 1) * itemsPerPage + 1;
    const end = Math.min(currentPage * itemsPerPage, filteredStaff.length);
    
    document.getElementById('showingStart').textContent = start;
    document.getElementById('showingEnd').textContent = end;
    document.getElementById('totalStaff').textContent = filteredStaff.length;
}

// Update Stats
function updateStats() {
    const coaches = staffMembers.filter(s => s.role === 'coach' && s.status === 'active').length;
    const headCoaches = staffMembers.filter(s => s.role === 'head_coach' && s.status === 'active').length;
    const trainers = staffMembers.filter(s => s.role === 'trainer' && s.status === 'active').length;
    const admins = staffMembers.filter(s => s.role === 'admin' && s.status === 'active').length;
    const shopkeepers = staffMembers.filter(s => s.role === 'shopkeeper' && s.status === 'active').length;
    
    // Update stat cards if needed
    console.log('Stats:', { coaches: coaches + headCoaches, trainers, admins, shopkeepers });
}

// Export Staff Data
function exportStaffData() {
    const csvContent = generateCSV();
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `staff_members_${new Date().toISOString().split('T')[0]}.csv`;
    a.click();
    window.URL.revokeObjectURL(url);
    
    showNotification('Staff data exported successfully!', 'success');
}

// Generate CSV
function generateCSV() {
    const headers = ['ID', 'First Name', 'Last Name', 'Email', 'Phone', 'Role', 'Specialization', 'Join Date', 'Status', 'Address'];
    const rows = filteredStaff.map(staff => [
        staff.id,
        staff.firstName,
        staff.lastName,
        staff.email,
        staff.phone,
        formatRole(staff.role),
        staff.specialization,
        staff.joinDate,
        staff.status,
        staff.address
    ]);
    
    const csvContent = [
        headers.join(','),
        ...rows.map(row => row.map(cell => `"${cell}"`).join(','))
    ].join('\n');
    
    return csvContent;
}

// Select All Checkbox
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.staff-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    }
});

// Make functions globally accessible
window.viewStaff = viewStaff;
window.editStaff = editStaff;
window.deleteStaff = deleteStaff;

// Add animation styles
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(100px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes slideOut {
        from {
            opacity: 1;
            transform: translateX(0);
        }
        to {
            opacity: 0;
            transform: translateX(100px);
        }
    }
`;
document.head.appendChild(style);
