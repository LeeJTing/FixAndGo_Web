<?php

function getUserById($id){
    global $_db;
    $stm = $_db->prepare("SELECT * FROM USERS WHERE user_id=?;");
    $stm->execute([$id]);

    return $stm->fetch();
}

function getUserByEmail($email){
    global $_db;
    $stm = $_db->prepare("SELECT * FROM USERS WHERE email=?;");
    $stm->execute([$email]);

    return $stm->fetch();
}
