$(document).ready(function () {

    let id = null, email = null, password = null;
    let id_valid = false, email_valid = false, password_valid = false;

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

    $('#rememberMe').on('change', function () {
    if ($(this).is(':checked')) {
        $(this).val('true');
    }else{
        $(this).val('false');
    }
});

    // listen on the password input (the input itself has id="password")
    $('#password').on('input', function () {
        password = $(this).val();
        if (!passwordFormat(password)) {
            password_valid = false;
            $('#password + .error').css('display', 'inline');
        } else {
            password_valid = true;
            $('#password + .error').css('display', 'none');
        }
    });

    $('input').on('input', function () {
        if ((id_valid || email_valid) && password_valid) {
            $('#loginBtn').prop('disabled', false);
        } else {
            $('#loginBtn').prop('disabled', true);
        }
    });
});