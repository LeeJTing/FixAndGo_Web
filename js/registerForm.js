$(document).ready(function() {

    let name_valid = false, id_valid = false, password_valid = false, confirmPassword_valid = false,  email_valid = false; 

    $('#userID').on('input', function() {
        userId = $(this).val();
        id_valid = userIDFormat(userId);
        if(id_valid){
            $('#userID ~ .error').css('display', 'none');
        }
        else{
            $('#userID ~ .error').css('display', 'inline');
        }
    });

    $('#userName').on('input', function() {
        userName = $(this).val();
        name_valid = userNameFormat(userName)
        if(name_valid){
            $('#userName ~ .error').css('display', 'none');
        }
        else{
            $('#userName ~ .error').css('display', 'inline');
        }
    });

    $('#email').on('input', function() {
        email = $(this).val();
        email_valid = emailFormat(email);
        if(email_valid){
            $('#email ~ .error').css('display', 'none');
        }
        else{
            $('#email ~ .error').css('display', 'inline');
        }
    });

    $('#password').on('input', e => {
        password = $(e.target).val();
        password_valid = passwordFormat(password);
        checkConfirm();
        if(password_valid){
            $('#password ~ .error').css('display', 'none');
        }else{
            $('#password ~ .error').css('display', 'inline');
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

    $('#confirmPassword').on('input', function() {
        checkConfirm();
    });

    function checkConfirm(){
        password = $('#password').val();
        confirmPassword = $('#confirmPassword').val();
        confirmPassword_valid = password === confirmPassword && confirmPassword.length > 0;

        if(confirmPassword_valid){
            $('#passwordMatch').css('display', 'inline');
            $('#confirmPassword ~ .error').css('display', 'none');
        }
        else{
            $('#passwordMatch').css('display', 'none');
            $('#confirmPassword ~ .error').css('display', 'inline');
        }
    }

    $('input').on('input', function() {
        if(id_valid && name_valid && password_valid && confirmPassword_valid && email_valid){ 
            $('button').prop('disabled', false);
        }else{
            $('button').prop('disabled', true);
        }
    });

})