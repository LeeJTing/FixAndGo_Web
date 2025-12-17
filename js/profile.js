$(document).ready(function () {
    // Initialize variables
    // upddate profile
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

    $('#editProfileModal .close-btn, #editProfileModal .cancel-btn').on('click', function () {
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

    $('input').on('input', function () {
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
        if (dob) {
            if (!dobFormat(dob)) {
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
});

$(document).ready(function () {
    let param = '?update=address';

    // Variables
    let isSubmitting = false;

    // Open modal when Add Address button is clicked
    $('.add-address-btn').on('click', function (e) {
        e.preventDefault();
        param = '?update=addaddress'
        $('.address-modal-title').text('Add New Address');
        $('#addressId').val('');
        openAddressModal();
    });

    // Open modal function
    function openAddressModal(skipReset) {
        $('#addAddressModal').css('display', 'flex');
        $('body').css('overflow', 'hidden');

        // Reset form and errors unless caller wants to keep values (edit)
        if (!skipReset) resetAddressForm();

        // Focus on first input after a small delay
        setTimeout(function () {
            $('#addressName').focus();
        }, 100);
    }

    // Close modal function
    function closeAddressModal() {
        $('#addAddressModal').css('display', 'none');
        $('body').css('overflow', '');
    }

    // Reset form function
    function resetAddressForm() {
        $('#addAddressForm')[0].reset();

        // Clear error messages
        $('.address-form-error').text('').hide();

        // Remove error classes
        $('.address-form-input').removeClass('error');

        // Reset loading state
        $('#addressLoading').removeClass('active');
        $('#saveAddressBtn')
            .prop('disabled', false)
            .text('Save Address');

        isSubmitting = false;
    }

    // Close modal events
    $('#closeAddressModal, #cancelAddressBtn').on('click', function (e) {
        e.preventDefault();
        closeAddressModal();
    });

    // Close modal when clicking outside
    $('#addAddressModal').on('click', function (e) {
        if ($(e.target).is('#addAddressModal')) {
            closeAddressModal();
        }
    });

    // Close modal with Escape key
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape' && $('#addAddressModal').css('display') === 'flex') {
            closeAddressModal();
        }
    });

    $('#addressName').on('blur', function () {
        addressName = $('#addressName').val().trim();
        $('#addressNameError').hide();
        if (!addressName) {
            showError('#addressNameError', 'Address name is required');
        } else if (addressName.length < 2) {
            showError('#addressNameError', 'Address name must be at least 2 characters');
        }
    })

    $('#addressLine1').on('blur', function () {
        $('#addressLine1Error').hide();
        addressLine1 = $('#addressLine1').val().trim();
        if (!addressLine1) {
            showError('#addressLine1Error', 'Address line 1 is required');
        }
    })

    $('#postCode').on('blur', function () {
        $('#postCodeError').hide();
        postCode = $('#postCode').val().trim();
        if (!postCode) {
            showError('#postCodeError', 'Post code is required');
        } else if (!/^\d{5}$/.test(postCode)) {
            showError('#postCodeError', 'Post code must be 5 digits');
        }
    })

    $('#state').on('blur', function () {
        $('#stateError').hide();
        state = $('#state').val();
        if (!state) {
            showError('#stateError', 'State is required');
        }
    })

    $('#country').on('blur', function () {
        $('#countryError').hide();
        country = $('#country').val();
        if (!country) {
            showError('#countryError', 'Country is required');
        }
    })

    function showError(fieldId, message) {
        $(fieldId).text(message).show();
        $(fieldId.replace('Error', '')).addClass('error');
        return false;
    }

    // Form validation
    function validateAddressForm() {
        let isValid = true;

        // Clear previous errors
        $('.address-form-error').text('').hide();
        $('.address-form-input').removeClass('error');

        // Validate Address Name
        addressName = $('#addressName').val().trim();
        if (!addressName) {
            isValid = showError('#addressNameError', 'Address name is required');
        } else if (addressName.length < 2) {
            isValid = showError('#addressNameError', 'Address name must be at least 2 characters');
        }

        // Validate Address Line 1
        addressLine1 = $('#addressLine1').val().trim();
        if (!addressLine1) {
            isValid = showError('#addressLine1Error', 'Address line 1 is required');
        }

        // Validate Post Code
        postCode = $('#postCode').val().trim();
        if (!postCode) {
            isValid = showError('#postCodeError', 'Post code is required');
        } else if (!/^\d{5}$/.test(postCode)) {
            isValid = showError('#postCodeError', 'Post code must be 5 digits');
        }

        // Validate State
        state = $('#state').val();
        if (!state) {
            isValid = showError('#stateError', 'State is required');
        }

        // Validate Country
        country = $('#country').val();
        if (!country) {
            isValid = showError('#countryError', 'Country is required');
        }

        return isValid;
    }

    // Format phone number (if needed)
    $('#postCode').on('input', function () {
        let value = $(this).val().replace(/\D/g, '');
        if (value.length > 5) {
            value = value.substr(0, 5);
        }
        $(this).val(value);
    });

    // Auto-capitalize first letter of address name
    $('#addressName').on('blur', function () {
        const value = $(this).val();
        if (value) {
            $(this).val(value.charAt(0).toUpperCase() + value.slice(1));
        }
    });

    // Real-time validation
    $('.address-form-input').on('blur', function () {
        const $field = $(this);
        const $error = $('#' + $field.attr('id') + 'Error');

        if ($field.is(':required') && !$field.val().trim()) {
            $error.text('This field is required').show();
            $field.addClass('error');
        } else {
            $error.hide();
            $field.removeClass('error');
        }
    });

    // Clear error on input
    $('.address-form-input').on('input', function () {
        const $field = $(this);
        const $error = $('#' + $field.attr('id') + 'Error');

        if ($field.val().trim()) {
            $error.hide();
            $field.removeClass('error');
        }
    });

    // Save address function
    function saveAddress() {
        if (isSubmitting) return;

        if (!validateAddressForm()) {
            alert("Invalid input! Please try agian!");
            return;
        }

        isSubmitting = true;

        // Prepare
        $.post(location.origin + location.pathname + param, {
            address_id: $('#addressId').val() || '',
            address_name: $('#addressName').val().trim(),
            address_one: $('#addressLine1').val().trim(),
            address_two: $('#addressLine2').val().trim(),
            address_three: $('#addressLine3').val().trim(),
            post_code: $('#postCode').val().trim(),
            state: $('#state').val(),
            country: $('#country').val()
        }).done(function () {
            window.location.href = location.href;
            alert("Address saved successfully!");
        }).fail(function () {
            alert("Failed to save address.");
        })

    }

    // Save button click event
    $('#saveAddressBtn').on('click', function (e) {
        e.preventDefault();
        saveAddress();
    });

    // Form submit on Enter key
    $('#addAddressForm').on('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            saveAddress();
        }
    });

    // Also handle Enter key specifically in the default address checkbox's label
    $('#defaultAddress').on('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            $(this).prop('checked', !$(this).prop('checked'));
        }
    });

    // Initialize modal with default country as Malaysia
    $('#country').val('Malaysia');

    // Edit address - prefills the add/edit modal
    $(document).on('click', '.action-btn.edit, .edit-address-btn', function (e) {
        e.preventDefault();
        param = '?update=updateaddress';
        const $btn = $(this);
        const id = $btn.data('address-id');
        // fill fields
        $('#addressId').val(id || '');
        $('#addressName').val($btn.data('address-name') || '');
        $('#addressLine1').val($btn.data('address-one') || '');
        $('#addressLine2').val($btn.data('address-two') || '');
        $('#addressLine3').val($btn.data('address-three') || '');
        $('#postCode').val($btn.data('post-code') || '');
        $('#state').val($btn.data('state') || '');
        $('#country').val($btn.data('country') || 'Malaysia');
        $('.address-modal-title').text('Edit Address');
        openAddressModal(true);
    });

    // Delete address - delegated handler
    $(document).on('click', '.delete-address-btn', function (e) {
        e.preventDefault();
        const id = $(this).data('address-id');
        if (!id) return;
        if (confirm("Confirm to delete this address?")) {
            window.location.href = location.origin + location.pathname + '?deleteAddress=' + id;
        }
    });

    // Change Password modal: open
    $(document).on('click', '#openChangePassword', function (e) {
        e.preventDefault();
        $('#changePasswordModal').show();
        $('body').css('overflow', 'hidden');
    });

    // Close change password modal (only inside that modal)
    $(document).on('click', '#changePasswordModal .close-btn, #changePasswordModal .cancel-btn', function (e) {
        e.preventDefault();
        $('#changePasswordModal').hide();
        $('body').css('overflow', '');
        $('#changePasswordForm')[0].reset();
        $('.error-message').hide();
    });

    // Validate change password before submit
    $('#changePasswordForm').on('submit', function (e) {
        const cur = $(this).find('input[name="current_password"]').val().trim();
        const nw = $(this).find('input[name="new_password"]').val().trim();
        const cf = $(this).find('input[name="confirm_password"]').val().trim();
        $('.error-message').hide();
        let ok = true;
        if (!cur) { ok = false; alert('Please enter current password'); }
        if (nw.length < 8) { ok = false; alert('New password must be at least 8 characters'); }
        if (nw !== cf) { ok = false; alert('New password and confirmation do not match'); }
        if (!ok) e.preventDefault();
    });
});

$(document).ready(function () {
    let password_valid = false;
    $('#confirmPasswordError').text('').hide();
    $('#confirmPasswordCorrect').text('').hide();
    $('#changePasswordBtn').attr('disabled', true);
    $('#openChangePassword').on('click', function () {
        $('#changePasswordModal').fadeIn();
    });

    $('.close-btn, .cancel-btn').on('click', function () {
        $('#changePasswordModal').fadeOut();
        $('#confirmPasswordError').text('').hide();
        $('#confirmPasswordCorrect').text('').hide();
    });

    $('#openChangePassword').on('click', function () {
        $('#newPasswordError').text('').hide();
        const strengthBar = $('#passwordStrengthBar');

        // Reset strength bar
        strengthBar.css({
            'width': '0%',
            'background-color': '#eee'
        });
    });

    function correctForm() {
        let old_pass = $('#old_password').val();
        let confirm_pass = $('#confirm_password').val();
        let new_pass = $('#new_password').val();
        return confirm_pass === new_pass && old_pass && new_pass && confirm_pass;
    }

    $('#changePasswordForm input').on('input', function () {
        $('#changePasswordBtn').attr('disabled', !correctForm());
    });

    $('#new_password, #confirm_password').on('input', function () {
        confirm_password = $('#confirm_password').val();
        new_password = $('#new_password').val();

        if (confirm_password === new_password && new_password && confirm_password) {
            $('#confirmPasswordCorrect').css('color', 'green');
            $('#confirmPasswordCorrect').text("Password were Match!!").show();
            $('#confirmPasswordError').text('').hide();
        } else if (confirm_password !== new_password && new_password && confirm_password) {
            $('#confirmPasswordCorrect').text('').hide();
            $('#confirmPasswordError').text('Not Match!!').show();
        } else {
            $('#confirmPasswordError').text('').hide();
            $('#confirmPasswordCorrect').text('').hide();
        }

    });

    $('#new_password').on('input', e => {
        password = $(e.target).val();
        password_valid = passwordFormat(password);
        if (password_valid) {
            $('#newPasswordError').text('').hide();
        } else {
            $('#newPasswordError').text('Password must be 8-12 characters with at least one letter and one number.').show();
        }
        const strengthBar = $('#passwordStrengthBar');

        // Reset strength bar
        strengthBar.css({
            'width': '0%',
            'background-color': '#eee'
        });

        if (password.length > 0) {
            let strength = 0;

            // Length check (8-12 characters)
            if (password.length >= 8 && password.length <= 12) {
                strength += 25;
            } else if (password.length > 12) {
                // Penalty for exceeding 12 characters
                strength -= 10;
            }

            // Contains letters
            if (/[a-zA-Z]/.test(password)) strength += 25;

            // Contains numbers
            if (/[0-9]/.test(password)) strength += 25;

            // Contains special characters
            if (/[^a-zA-Z0-9]/.test(password)) strength += 25;

            // Ensure strength is between 0 and 100
            strength = Math.max(0, Math.min(100, strength));

            // Update strength bar
            strengthBar.css('width', strength + '%');

            // Change color based on strength
            if (strength <= 25) {
                strengthBar.css('background-color', '#e74c3c'); // Red
            } else if (strength <= 50) {
                strengthBar.css('background-color', '#f39c12'); // Orange
            } else if (strength <= 75) {
                strengthBar.css('background-color', '#f1c40f'); // Yellow
            } else {
                strengthBar.css('background-color', '#2ecc71'); // Green
            }
        }
    });
})