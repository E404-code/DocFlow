/**
 * Profile Page JavaScript
 * Handles password field toggling, form validation, and language updates
 */

function togglePasswordFields() {
    const passwordFields = document.getElementById('passwordFields');
    const editButton = event.target;
    
    if (passwordFields.style.display === 'none') {
        passwordFields.style.display = 'block';
        editButton.textContent = 'Cancel';
    } else {
        passwordFields.style.display = 'none';
        editButton.textContent = 'Edit Password';
        clearPasswordFields();
    }
}

function clearPasswordFields() {
    document.getElementById('currentPassword').value = '';
    document.getElementById('newPassword').value = '';
    document.getElementById('confirmPassword').value = '';
}

function resetForm() {
    document.getElementById('profileForm').reset();
    document.getElementById('passwordFields').style.display = 'none';
    document.querySelector('[onclick="togglePasswordFields()"]').textContent = 'Edit Password';
    hideSuccessMessage();
}

function showSuccessMessage(type, mess) {
    const message = document.getElementById('successMessage');
    message.style.display = 'block';
    if(type === 'error') {
         message.style.backgroundColor = 'var(--error-base)';
         message.textContent = mess;
    } else if(type === 'success') {
         message.style.backgroundColor = 'var(--success-base)';
         message.textContent = mess;
    } else {
         message.style.backgroundColor = 'var(--success-base)';
         message.textContent = 'Profile updated successfully!';
    }
    setTimeout(() => {
        message.style.display = 'none';
    }, 3000);
}

// Regex patterns from auth.js and register.js (global scope)
const emRgx = /^(?!.*[._-]{2})[a-zA-Z](([a-zA-Z0-9._-]+)?[a-zA-Z0-9]+)?@(?=[a-zA-Z]+)([\w-._]+)?[a-zA-Z]+\.[a-zA-Z]+$/;
const passRgx = /^(?!.*[\s`|/\\'"]{1})(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,35}$/;

function hideSuccessMessage() {
    document.getElementById('successMessage').style.display = 'none';
}

function validatePasswordForm() {
    const passwordFields = document.getElementById('passwordFields');
    if (passwordFields.style.display === 'block') {
        const currentPassword = document.getElementById('currentPassword').value;
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;
        
        if (!currentPassword || !newPassword || !confirmPassword) {
            showSuccessMessage('error', 'Please fill in all password fields');
            return false;
        }
        
        if (newPassword !== confirmPassword) {
            showSuccessMessage('error', 'New password and confirmation do not match');
            return false;
        }
        
        // Use App.validateFormInput for password validation with regex from auth.js
        const passwordField = document.getElementById('newPassword');
        if (!window.App.validateFormInput(passwordField, 'password', passRgx)) {
            return false;
        }
    }
    return true;
}

function updateButtonTexts() {
    const isArabic = document.documentElement.lang === 'ar';
    const editPasswordBtn = document.querySelector('[onclick="togglePasswordFields()"]');
    
    if (isArabic) {
        if (document.getElementById('passwordFields').style.display === 'none') {
            editPasswordBtn.textContent = 'تعديل كلمة المرور';
        } else {
            editPasswordBtn.textContent = 'إلغاء';
        }
    } else {
        if (document.getElementById('passwordFields').style.display === 'none') {
            editPasswordBtn.textContent = 'Edit Password';
        } else {
            editPasswordBtn.textContent = 'Cancel';
        }
    }
}

// Initialize profile page functionality
document.addEventListener('DOMContentLoaded', function() {
    const isEditMode = window.location.search.includes('edit=true');
    getUserInfo(document.getElementById('email').value.trim());
    
    // Handle form submission
    const profileForm = document.getElementById('profileForm');
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (validateForm()) {
                updateProfile();
            }
        });
    }
    
    // Add real-time validation using regex from auth.js and register.js
    const nameField = document.getElementById('name');
    const emailField = document.getElementById('email');
    const newPasswordField = document.getElementById('newPassword');
    const confirmPasswordField = document.getElementById('confirmPassword');
    
    if (nameField && isEditMode) {
        nameField.addEventListener('blur', function() {
            window.App.validateFormInput(this, 'name');
        });
    }
    
    if (emailField && isEditMode) {
        emailField.addEventListener('blur', function() {
            window.App.validateFormInput(this, 'email', emRgx);
        });
    }
    
    if (newPasswordField && isEditMode) {
        newPasswordField.addEventListener('blur', function() {
            window.App.validateFormInput(this, 'password', passRgx);
        });
    }
    
    if (confirmPasswordField && isEditMode) {
        confirmPasswordField.addEventListener('blur', function() {
            const newPassword = document.getElementById('newPassword').value;
            if (this.value && this.value !== newPassword) {
                window.App.showFieldError(this, 'Passwords do not match');
            } else {
                window.App.clearFieldError(this);
            }
        });
    }
    
    // Listen for language changes
    const originalToggleLang = window.toggleLang;
    window.toggleLang = function() {
        if (originalToggleLang) originalToggleLang();
        setTimeout(updateButtonTexts, 100);
    };
    
    // Initialize button text based on current language
    updateButtonTexts();

    const regenBtn = document.getElementById('regenerateTokenBtn');
    if (regenBtn) {
        regenBtn.addEventListener('click', function() {
            fetch('/doc/api/reset-token.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({})
            })
                .then(response => response.json())
                .then(info => {
                    if (info && info.status === 'success') {
                        showSuccessMessage('success', info.message);
                    } else {
                        showSuccessMessage('error', (info && info.message) ? info.message : 'Failed to regenerate token');
                    }
                })
                .catch(() => showSuccessMessage('error', 'Failed to regenerate token'));
        });
    }

    // Get user info on page load
    function getUserInfo(email) {
        fetch('/doc/api/user-info.php', {method: 'POST', body: JSON.stringify({email: email})})
            .then(response => response.json())
            .then(info => {
                if(info) {
                    if(info.status === 'success') {
                        document.querySelector('.profile-avatar').textContent = info.content.name[0];
                        document.querySelector('.profile-info h2').textContent = info.content.name;
                        document.querySelector('.profile-info span').textContent = info.content.role;
                        document.getElementById('name').value = info.content.name;
                        document.getElementById('email').value = info.content.email;
                        document.getElementById('role').value = info.content.role;
                        document.getElementById('joined').value = info.content.created_at;
                    }

                    if(info.status === 'error') {
                        showSuccessMessage('error', info.message);
                    }
                }
            })
            .catch(error => console.error('Error fetching user info:', error));
    }
    
    function validateForm() {
        const isEditMode = window.location.search.includes('edit=true');
        if (!isEditMode) return true;
        
        let isValid = true;
        
        // Validate name
        const nameField = document.getElementById('name');
        if (!window.App.validateFormInput(nameField, 'name')) {
            isValid = false;
        }
        
        // Validate email
        const emailField = document.getElementById('email');
        if (!window.App.validateFormInput(emailField, 'email', emRgx)) {
            isValid = false;
        }
        
        // Validate password if fields are visible
        if (!validatePasswordForm()) {
            isValid = false;
        }
        
        return isValid;
    }
    
    function updateProfile() {
        const formData = {
            name: document.getElementById('name').value,
            email: document.getElementById('email').value
        };
        
        // Add password fields if they're being changed
        const passwordFields = document.getElementById('passwordFields');
        if (passwordFields.style.display === 'block') {
            const currentPassword = document.getElementById('currentPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            
            if (currentPassword && newPassword) {
                formData.currentPassword = currentPassword;
                formData.newPassword = newPassword;
            }
        }
        
        fetch('/doc/api/update-profile.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showSuccessMessage('success', data.message);
                
                // Clear password fields after successful update
                const passwordFields = document.getElementById('passwordFields');
                if (passwordFields.style.display === 'block') {
                    clearPasswordFields();
                    passwordFields.style.display = 'none';
                    document.querySelector('[onclick="togglePasswordFields()"]').textContent = 'Edit Password';
                }
                
                // Reload page to show updated session data if email changed
                if (formData.email !== document.getElementById('email').value) {
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                }
            } else {
                showSuccessMessage('error', data.message);
            }
        })
        .catch(error => {
            console.error('Error updating profile:', error);
            showSuccessMessage('error', 'An error occurred while updating profile');
        });
    }
});
