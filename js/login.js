$(document).ready(function () {

    id = email = password = null;
    id_valid = email_valid = password_valid = false;

    $('#identify').on('input', function () {
        input = $(this).val();
        id_valid = email_valid = false;

        // check whether is email or User ID
        if (emailFormat(input)) {
            email = input;
            email_valid = true;
            $('#identify + .error').css('display', 'none');
        } else if (userIDFormat(input)) {
            id = input;
            id_valid = true;
            $('#identify + .error').css('display', 'none');
        } else {
            email = id = null;
            $('#identify + .error').css('display', 'inline');
        }
    });

    $('#password').on('input', function () {
        password = $(this).val();
        if (!passwordFormat(password)) {
            password_valid = true;
            $('#password + .error').css('display', 'inline');
        } else {
            password_valid = false;
            $('#password + .error').css('display', 'none');
        }
    });

    $('input').on('input', function () {
        if ((id_valid || email_valid) && password_valid) {
            $('#loginBtn').prop('disabled', true);
        }else{
            $('#loginBtn').prop('disabled', false);
        }
    });
});