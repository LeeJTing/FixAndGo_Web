<?php
global $rootDir;

$error = null;
$identify = null;
$password = null;
$is_blocked = false;

if (is_post()) {

    $identify = post("identify");
    $password = post("password");

    // ===== 1. Find User（Email Or User ID）=====
    if (is_exists($identify, 'Users', 'email')) {
        $user = getUserByEmail($identify);
    }
    else if (is_exists($identify, 'Users', 'user_id')) {
        $user = getUserById($identify);
    }
    else {
        $error = "Account not found!";
        return;
    }

    // ===== 2. Blocked Check =====
    if ($user->account_status === 'Blocked') {
        $error = "Your account has been blocked!";
        $is_blocked = true;
        return;
    }

    // ===== 3. password authentification（bcrypt + 旧 SHA1）=====
    $stored = $user->hash_password;

    if (password_verify($password, $stored)) {
        // ✅ newuser（bcrypt）
        login_success($user);
    }
    else if (strlen($stored) === 40 && sha1($password) === $stored) {
        // ⚠️ olduser（SHA1）→ auto update
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        updateUserPassword($user->user_id, $newHash);

        login_success($user);
    }
    else {
        $error = "Invalid user ID or password.";
    }
}

function login_success($user)
{
    temp('USER_ID', $user->user_id);
    temp('USER_NAME', $user->user_name);
    temp('USER_ROLE', $user->user_role);
    temp('EMAIL', $user->email);
    temp('ACCOUNT_STATUS', $user->account_status);

    redirect(homePageURL());
}

function updateUserPassword($userId, $newHash)
{
    global $_db;
    $stmt = $_db->prepare("UPDATE users SET hash_password = ? WHERE user_id = ?");
    $stmt->execute([$newHash, $userId]);
}

