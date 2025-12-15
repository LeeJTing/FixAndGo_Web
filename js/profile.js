$(document).ready(function () {
    // Initialize variables
    let originalName = $('#editUserName').val();
    let originalEmail = $('#editEmail').val();
    let originalContact = $('#editContactNumber').val();
    let orginalDOB = $('#editDob').val();

    let profileImageChanged = false;
    let originalImage = $('#currentProfilePicture').attr('src');
    let isValid = true;

    // Open Edit Profile Modal
    $('.edit-profile-btn').on('click', function () {
        openEditProfileModal();
        resetForm();
    });

    // Open modal function
    function openEditProfileModal() {
        // Show modal
        $('#editProfileModal').show();
        $('main').css('overflow', 'hidden');
    }

    // Close modal
    function closeEditProfileModal() {
        $('#editProfileModal').hide();
        $('main').css('overflow', 'auto');
        resetForm();
    }

    $('.close-btn, .cancel-btn').on('click', function () {
        closeEditProfileModal();
    });

    // Close modal when clicking outside
    $(window).on('click', function (e) {
        if ($(e.target).hasClass('modal')) {
            closeEditProfileModal();
        }
    });

    // Profile picture upload trigger
    $('#uploadPictureBtn').on('click', function () {
        $('#profileImage').click();
    });

    // Handle profile picture upload
    $('#profileImage').on('change', function (e) {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            const maxSize = 5 * 1024 * 1024; // 5MB

            // Validate file type
            if (!validTypes.includes(file.type)) {
                alert('Please select a valid image file (JPEG, PNG, GIF, or WebP)');
                this.value = '';
                return;
            }

            // Validate file size
            if (file.size > maxSize) {
                alert('Image size should be less than 5MB');
                this.value = '';
                return;
            }

            // Preview image
            const reader = new FileReader();
            reader.onload = function (e) {
                $('#currentProfilePicture').attr('src', e.target.result);
                profileImageChanged = true;
            }
            reader.readAsDataURL(file);
        }
    });

    $('#resetPictureBtn').on('click', function () {
        if ($('#currentProfilePicture').attr('src') == originalImage || confirm('Want to Reset?')) {
            $('#currentProfilePicture').attr('src', originalImage);
            $('#profileImage').val('');
        }
    });

    $('input').on('input', function(){
            $('#saveProfileBtn').prop('disabled', !validateForm());
    });

    // Form validation
    function validateForm() {
        $('.error-message').hide();
        let isValid = true;

        // Name validation
        userName = $('#editUserName').val().trim();
        if (!userNameFormat(userName)) {
            $('#nameError').text('User name must be between 4 and 50 characters and can not contain any numbers.').show();
            isValid = false;
        }

        // Email validation
        email = $('#editEmail').val().trim();
        if (!emailFormat(email)) {
            $('#emailError').text('Please enter a valid email address').show();
            isValid = false;
        }

        // Phone validation
        phone = $('#editContactNumber').val().trim();
        if (phone) {
            if (!contactNumberFormat(phone)) {
                $('#phoneError').text('Please enter a valid phone number').show();
                isValid = false;
            }
        }

        // DOB validation
        dob = $('#editDob').val();
        if(dob) {
            if(!dobFormat(dob)){
                $('#dobError').text('You must be an adult (more than 18 years old)').show();
                isValid = false;
            }
        }

        return isValid;
    }

    // Reset form
    function resetForm() {
        profileImageChanged = false;
        $('#editUserName').val(originalName);
        $('#editEmail').val(originalEmail);
        $('#currentProfilePicture').attr('src', originalImage);
        $('#editContactNumber').val(originalContact);
        $('#editDob').val(orginalDOB);
        $('#profileImage').val('');
        $('.error-message').hide();
    }

    // Show notification
    function showNotification(message, type) {
        if (type === 'success') {
            $('#notificationMessage').text(message);
            $('#successNotification').fadeIn();
        } else {
            $('#errorMessage').text(message);
            $('#errorNotification').fadeIn();
        }

        setTimeout(function () {
            hideNotification();
        }, 5000);
    }

    // Hide notification
    function hideNotification() {
        $('#successNotification').fadeOut();
        $('#errorNotification').fadeOut();
    }   
});