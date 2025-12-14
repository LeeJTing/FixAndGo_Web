<?php
global $rootDir;

$identify = $password = null;
$success = $is_blocked = false;

if (is_post()) {
    $identify = post("identify");
    $password = post("password");

    $hash_password = hash_password($password);

    if (is_exists($identify, 'Users', 'email')) {
        $user = getUserByEmail($identify);
        if($user->account_status == 'Blocked'){
            $is_blocked = true;
        }
        else if ($user->hash_password == $hash_password) {
            $success = true;
            temp('USER_ID', $user->user_id);
            temp('USER_NAME', $user->name);
            temp('USER_ROLE', $user->user_role);
            temp('EMAIL', $user->email);
            temp('ACCOUNT_STATUS', $user->account_status);

            redirect(homePageURL());
        }
    }
    else if (is_exists($identify, 'Users', 'user_id')) {
        $user = getUserById($identify);

        if($user->account_status == 'Blocked'){
            $is_blocked = true;
        }
        else if ($user->hash_password == $hash_password) {
            echo 'here';
            $success = true;
            temp('USER_ID', $user->user_id);
            temp('USER_NAME', $user->name);
            temp('USER_ROLE', $user->user_role);
            temp('EMAIL', $user->email);
            temp('ACCOUNT_STATUS', $user->account_status);
            
            redirect(homePageURL());
        }
    }
}
