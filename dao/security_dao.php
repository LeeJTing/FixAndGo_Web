<?php

function getUserById($id)
{
    global $_db;
    $stm = $_db->prepare("SELECT * FROM USERS WHERE user_id=?;");
    $stm->execute([$id]);

    return $stm->fetch();
}

function getUserByEmail($email)
{
    global $_db;
    $stm = $_db->prepare("SELECT * FROM USERS WHERE email=?;");
    $stm->execute([$email]);

    return $stm->fetch();
}

// Update user password (store hashed password)
function updateUserPassword($userId, $hashedPassword)
{
    global $_db;
    $stm = $_db->prepare("UPDATE users SET hash_password = ? WHERE user_id = ?");
    return $stm->execute([$hashedPassword, $userId]);
}
