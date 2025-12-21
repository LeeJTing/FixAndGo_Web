<?php
require_once __DIR__ . "/../_base.php";


function insertOPT($id, $hash_password, $expired_at){
    global $_db;
    $stmt = $_db->prepare("INSERT INTO OTP(user_id, hashed_password, expired_at)
                           VALUES (?, ?, ?)");
    $stmt->execute([$id, $hash_password, $expired_at]);

    return true;
}

function getHashedOTPById($id){
    global $_db;
    $stmt = $_db->prepare("SELECT user_id, hashed_password FROM OTP
                           WHERE user_id = ?
                           AND expired_at > NOW()
                           ORDER BY start_at DESC
                           LIMIT 1");
    $stmt->execute([$id]);
    return $stmt->fetch();
}