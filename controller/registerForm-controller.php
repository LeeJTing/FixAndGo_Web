<?php
require_once __DIR__ . "/../_base.php";
require_once __DIR__ . "/../dao/token-dao.php";
require_once __DIR__ . "/../dao/security_dao.php";

$error = false;

$userID = null;
$userName = null;
$email = null;
$password = null;
$expiredAt = null;

if (is_post()) {
    $userID = post('userID');
    $userName = post('userName');
    $email = post('email');
    $password = post('password');

    // check unique id and email
    $valid_id = is_unique($userID, 'Users', 'user_id');
    $valid_email = is_unique($email, 'Users', 'email');

    if ($valid_id) {
        $repeatIDMsg = "";
    } else {
        $repeatIDMsg = "The ID is repeated, please change it.";
        $error = true;
    }

    if ($valid_email) {
        $repeatEmailMsg = "";
    } else {
        $repeatEmailMsg = "The email is repeated, please change it.";
        $error = true;
    }

    if (!$error) {
        // insert user with unverified status, create cart, persist token in DB, then send verification email
        $hashed_password = hash_password($password);
        $stmIns = $_db->prepare("INSERT INTO USERS (user_id, user_name, user_role, email, hash_password, account_status)
                             VALUES (?, ?, ?, ?, ?, ?)");
        $stmIns->execute([$userID, $userName, 'Member', $email, $hashed_password, 'Unverify']);

        $stmtCart = $_db->prepare("INSERT INTO CART (user_id) VALUES (?)");
        $stmtCart->execute([$userID]);

        // generate token and store in token table (valid 10 minutes)
        $token = bin2hex(random_bytes(16));
        $expiredAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));
        addToken($userID, $token, 'Register', $expiredAt);

        require_once __DIR__ . "/../email/email.php";
        try {
            $mail = get_mail();
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Fix & Go - Email verification';

            global $rootDir;
            $yesLink = $rootDir . '/controller/registerForm-controller.php?token=' . urlencode($token) . '&action=yes';
            $noLink = $rootDir . '/controller/registerForm-controller.php?token=' . urlencode($token) . '&action=no';

            $mail->Body = "<p>Hi " . htmlspecialchars($userName) . ",</p>"
                . "<p>Please confirm your account by clicking one of the options below. This link is valid for 10 minutes.</p>"
                . "<p><a href=\"$yesLink\">Yes, I am</a> &nbsp;|&nbsp; <a href=\"$noLink\">No</a></p>"
                . "<p>If you didn't initiate this registration, you may ignore this email.</p>";

            $mail->send();
        } catch (Exception $e) {
            error_log('Verification email error: ' . $e->getMessage());
        }

        // tell user to check their email
        redirect($rootDir . '/pages/guest/register_sent.php');
    }
}

// GET handler: verify token via DB when user clicks the emailed link
if (is_get() && get('token')) {
    $getToken = get('token');
    $action = get('action');
    $url = $rootDir . "/pages/guest/register.php";

    global $_db;

    // resolve user id from token (token must be valid and not expired)
    $userId = getIDBytoken($getToken, 'Register')->user_id;
    if (!$userId) {
        // invalid or expired token
        $url = $rootDir . "/pages/guest/register.php";
    }

    if ($action === 'yes') {
        // activate the user account
        $stmt = $_db->prepare("UPDATE users SET account_status = ? WHERE user_id = ?");
        $stmt->execute(['Unblock', $userId]);

        // remove the used token
        $del = $_db->prepare("DELETE FROM token WHERE token = ? AND used_for = ?");
        $del->execute([$getToken, 'Register']);

        $user = getUserById($userId);
        temp('USER_ID', $user->user_id);
        temp('USER_NAME', $user->user_name);
        temp('USER_ROLE', $user->user_role);
        temp('EMAIL', $user->email);
        temp('ACCOUNT_STATUS', $user->account_status);

        $url = homePageURL();
    }

    if ($action === 'no') {
        // user declined: remove user, cart and tokens
        $delCart = $_db->prepare("DELETE FROM cart WHERE user_id = ?");
        $delCart->execute([$userId]);
        
        $delToken = $_db->prepare("DELETE FROM token WHERE user_id = ? AND used_for = ?");
        $delToken->execute([$userId, 'Register']);

        $delUser = $_db->prepare("DELETE FROM users WHERE user_id = ?");
        $delUser->execute([$userId]);

        $url = $rootDir . "/pages/guest/register.php";
    }

    // unknown action -> redirect to register
    redirect($url);
}
