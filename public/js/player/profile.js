// Profile Image Upload Functionality
document.addEventListener('DOMContentLoaded', function() {
    const profileImageInput = document.getElementById('profileImageInput');
    const profileImagePreview = document.getElementById('profileImagePreview');
    const profileImageWrapper = document.querySelector('.profile-image-wrapper');
    const deleteImageBtn = document.getElementById('deleteImageBtn');
    const uploadMessage = document.getElementById('uploadMessage');
    const validationInfo = document.getElementById('imageValidationInfo');

    // Function to show message
    function showMessage(message, type = 'success') {
        uploadMessage.textContent = message;
        uploadMessage.className = 'upload-message show ' + type;
        
        setTimeout(() => {
            uploadMessage.classList.remove('show');
        }, 5000);
    }

    // Function to show/hide validation info
    function showValidationInfo() {
        if (validationInfo) {
            validationInfo.style.display = 'block';
            setTimeout(() => {
                validationInfo.style.display = 'none';
            }, 5000);
        }
    }

    // Click on image wrapper to trigger file input
    if (profileImageWrapper) {
        profileImageWrapper.addEventListener('click', function() {
            profileImageInput.click();
        });
    }

    // Handle file selection
    if (profileImageInput) {
        profileImageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (!file) {
                return;
            }

            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                showMessage('Please select a JPG, JPEG, or PNG image', 'error');
                showValidationInfo();
                profileImageInput.value = '';
                return;
            }

            // Validate file size (2MB max)
            const maxSize = 2 * 1024 * 1024; // 2MB in bytes
            if (file.size > maxSize) {
                showMessage('File size must be less than 2MB', 'error');
                showValidationInfo();
                profileImageInput.value = '';
                return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = function(event) {
                profileImagePreview.src = event.target.result;
            };
            reader.readAsDataURL(file);

            // Upload the file
            uploadProfileImage(file);
        });
    }

    // Upload profile image function
    function uploadProfileImage(file) {
        const formData = new FormData();
        formData.append('profile_image', file);

        // Show loading message
        showMessage('Uploading image...', 'success');

        // Send AJAX request
        fetch(window.location.origin + '/Elite/player/uploadProfileImage', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage(data.message, 'success');
                profileImagePreview.src = data.image_url;
                
                // Reload page after 2 seconds to show delete button if it wasn't there
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                showMessage(data.message, 'error');
                // Reset preview to original image
                profileImagePreview.src = profileImagePreview.getAttribute('data-original-src') || profileImagePreview.src;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('An error occurred while uploading the image', 'error');
        });

        // Clear file input
        profileImageInput.value = '';
    }

    // Delete profile image
    if (deleteImageBtn) {
        deleteImageBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (!confirm('Are you sure you want to delete your profile image?')) {
                return;
            }

            // Show loading message
            showMessage('Deleting image...', 'success');

            // Send AJAX request
            fetch(window.location.origin + '/Elite/player/deleteProfileImage', {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage(data.message, 'success');
                    
                    // Set default avatar
                    profileImagePreview.src = window.location.origin + '/Elite/images/default-avatar.png';
                    
                    // Reload page after 2 seconds to hide delete button
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    showMessage(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('An error occurred while deleting the image', 'error');
            });
        });
    }

    // Form validation enhancement (if needed)
    const profileForm = document.getElementById('profileForm');
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            // Add any additional validation here if needed
        });
    }

    // Account deactivation modal controls (called by onclick attributes in the view)
    function confirmDeactivation() {
        const modal = document.getElementById('deactivationModal');
        if (modal) modal.style.display = 'block';
    }

    function closeDeactivationModal() {
        const modal = document.getElementById('deactivationModal');
        if (modal) modal.style.display = 'none';
    }

    window.confirmDeactivation = confirmDeactivation;
    window.closeDeactivationModal = closeDeactivationModal;

    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('deactivationModal');
        if (modal && event.target === modal) {
            closeDeactivationModal();
        }
    });
});
