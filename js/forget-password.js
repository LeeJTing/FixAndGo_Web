$(document).ready(function(){
    $('#email').on('input', function(){
        email = $(this).val();
        if(emailFormat(email)){
            $('#emailError').hide();
        }else{
            $('#emailError').show();
        }
    });
})