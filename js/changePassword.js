$(document).ready(function () {
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

    $('input').on('input', function () {
        confirm_password = $('#confirm_password').val();
        new_password = $('#new_password').val();
        $('#update-btn').prop('disabled', !(confirm_password === new_password && passwordFormat(new_password)));

    })
})