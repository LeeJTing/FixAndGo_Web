<?php
require_once __DIR__ . '/../_base.php';

function addToken($id, $token, $used_for, $expired)
{
    global $_db;

    $stmt = $_db->prepare("INSERT INTO Token (user_id, token, used_for, expired_at)
                           VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$id, $token, $used_for, $expired]);
}

function getIDBytoken($token, $used_for)
{
    global $_db;
    $stmt = $_db->prepare("SELECT user_id FROM Token 
                           WHERE token = ?
                           AND expired_at > NOW()
                           AND used_for = ?");
    $stmt->execute([$token, $used_for]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? $row['user_id'] : false;
}
