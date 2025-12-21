<?php
global $rootDir;

$error = null;
$identify = null;
$password = null;
$is_blocked = false;
$remember = null;

if (is_post()) {

    $identify = post("identify");
    $password = post("password");

    // ===== 1. Find User（Email Or User ID）=====
    if (is_exists($identify, 'Users', 'email')) {
        $user = getUserByEmail($identify);
    } else if (is_exists($identify, 'Users', 'user_id')) {
        $user = getUserById($identify);
    } else {
        $error = "Account not found!";
        return;
    }

    // ===== 2. Blocked Check =====
    if ($user->account_status === 'Blocked') {
        $error = "Your account has been blocked!";
        $is_blocked = true;
        return;
    }

    // ===== 3. password authentification bcrypt =====
    $stored = $user->hash_password;

    if (password_verify($password, $stored)) {
        // password verify and login
        if (
            password_verify($password, $user->hash_password)
        ) {
            if ($user->account_status === 'Blocked') {
                $error = "Your account has been blocked!";
                $is_blocked = true;
                return;
            } elseif ($user->account_status === 'Unverify') {
                // resend verification token and instruct user to check email
                require_once __DIR__ . "/../dao/token-dao.php";
                require_once __DIR__ . "/../email/email.php";

                // generate a new token
                $token = bin2hex(random_bytes(16));
                $expiredAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));
                addToken($user->user_id, $token, 'Register', $expiredAt);

                try {
                    $mail = get_mail();
                    $mail->addAddress($user->email);
                    $mail->isHTML(true);
                    $mail->Subject = 'Fix & Go - Email verification';

                    global $rootDir;
                    $yesLink = $rootDir . '/controller/registerForm-controller.php?token=' . urlencode($token) . '&action=yes';
                    $noLink = $rootDir . '/controller/registerForm-controller.php?token=' . urlencode($token) . '&action=no';

                    $mail->Body = "<p>Hi " . htmlspecialchars($user->user_name) . ",</p>"
                        . "<p>Your account is not yet verified. Please confirm by clicking one of the options below. This link is valid for 10 minutes.</p>"
                        . "<p><a href=\"$yesLink\">Yes, I am</a> &nbsp;|&nbsp; <a href=\"$noLink\">No</a></p>";

                    $mail->send();
                } catch (Exception $e) {
                    error_log('Verification email error (login resend): ' . $e->getMessage());
                }

                redirect($rootDir . '/pages/guest/register_sent.php');
            } else {
                // proceed login
                $remember = post('rememberMe');
                login_success($user, $remember == 'true');
            }
        } else {
            $error = "Invalid user ID or password.";
        }
    } else {
        $error = "Invalid user ID or password.";
    }
}

function login_success($user, $remember = false)
{
    // set session temps
    temp('USER_ID', $user->user_id);
    temp('USER_NAME', $user->user_name);
    temp('USER_ROLE', $user->user_role);
    temp('EMAIL', $user->email);
    temp('ACCOUNT_STATUS', $user->account_status);

    if ($remember) {
        // generate persistent remember token (30 days)
        require_once __DIR__ . "/../dao/token-dao.php";

        $token = bin2hex(random_bytes(32));
        $expiredAt = date('Y-m-d H:i:s', strtotime('+30 days'));
        addToken($user->user_id, $token, 'Remember', $expiredAt);

        // set cookie (HTTP only)
        setcookie('remember_token', $token, strtotime('+10 days'), '/');
    }

    redirect(homePageURL());
}
