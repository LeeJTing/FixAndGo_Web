<?php
require_once __DIR__ . "/../_base.php";
require_once __DIR__ . "/../dao/security_dao.php";
require_once __DIR__ . "/../dao/otp_dao.php";
require_once __DIR__ . "/../email/email.php";

if (get('check') == 'email' || get('otp') == 'resend') {
    $email = get('otp') == 'resend' ? flash('USER_EMAIL') : post('email');
    if (is_exists($email, 'USERS', 'email')) {
        $user = getUserByEmail($email);
        flash('USER_EMAIL', $user->email);
        flash('USER_ID', $user->user_id);
        $id = $user->user_id;
        $otp = generateNumericOTP();
        sendOTPToEmail($otp, $user->email, $user->user_name);
        $hash = hash_password($otp);
        $expired_at = date('Y-m-d H:i:s', time() + 300); //5minutes

        if (insertOPT($id, $hash, $expired_at)) {
            redirect($rootDir . '/pages/guest/validateOTP.php');
        }
    }else{
        redirect($rootDir . '/pages/guest/forgot_password.php?error=true');
    }
}

if(is_post() && get('validate') == 'otp'){
    $otp = post('otp');
    $id = flash('USER_ID');
    $url = $rootDir . "/pages/guest/validateOTP.php?error=true";

    $otpRow = getHashedOTPById($id);
    if(password_verify($otp, $otpRow->hashed_password)){
        $url = $rootDir . "/pages/guest/change_password.php";
    }

    flash('USER_ID', $id);
    redirect($url);

}

if(is_post() && get('change') == 'password'){
    $id = flash('USER_ID');
    $email = flash('USER_EMAIL');
    $new_password = post('new_password');
    $hashedPassword = hash_password($new_password);

    updateUserPassword($id, $hashedPassword);

    redirect(homePageURL());

}

function generateNumericOTP($length = 6)
{
    $otp = '';
    for ($i = 0; $i < $length; $i++) {
        $otp .= random_int(0, 9);
    }
    return $otp;
}

function sendOTPToEmail($otp, $toEmail, $toName)
{
    $subject = 'Fix & Go | Forget Password';
    $emailBody = "
        <h2>Password Reset Request</h2>
        <p>Your OTP code is:</p>
        <h1 style='letter-spacing:5px;'>$otp</h1>
        <p>This code will expire in <strong>5 minutes</strong>.</p>
        <p>If you did not request this, please ignore this email.</p>
    ";


    $m = get_mail();
    $m->isHTML(true);
    $m->Subject = $subject;
    $m->Body = $emailBody;
    $m->addAddress($toEmail, $toName);
    $m->send();
}
