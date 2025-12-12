<?php

$error = false;

$userID = null;
$userName = null;
$email = null;
$password = null;

if(is_post()){
    $userID = post('userID');
    $userName = post('userName');
    $email = post('email');
    $password = post('password');

    // check unique id and email
    $valid_id = is_unique($userID, 'Users', 'user_id');
    $valid_email = is_unique($email, 'Users', 'email');

    if($valid_id){
        $repeatIDMsg = "";
    }
    else{
        $repeatIDMsg = "The ID is repeated, please change it.";
        $error = true;
    }

    if($valid_email){
        $repeatEmailMsg = "";
    }
    else{
        $repeatEmailMsg = "The email is repeated, please change it.";
        $error = true;
    }

    if(!$error){
        $hashed_password = hash_password($password);
        $stm = $_db->prepare("INSERT INTO USERS (user_id, user_name, user_role, email, hash_password, account_status)
                             VALUES (?, ?, ?, ?, ?, ?)");
        $stm->execute([$userID, $userName, 'Member', $email, $hashed_password, 'Unverified']);
        $stmt = $_db->prepare("INSERT INTO CART (user_id)
                              VALUES (?)");
        $stmt->execute([$userID]);

        temp('USER_ID', $userID);
        temp('USER_ROLE', 'Member');
        temp('USER_NAME', $userName);
        temp('USER_EMAIL', $email);

        redirect(homePageURL());
    }
}