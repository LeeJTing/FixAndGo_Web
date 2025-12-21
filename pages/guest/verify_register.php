<?php
require_once "../../_base.php";
require_once __DIR__ . '/../../dao/token-dao.php';

$token = get('token');
$action = strtolower(get('action') ?? '');

function renderMessage($title, $message){
    global $_title;
    $_title = 'Fix & Go | Verification';
    include_once __DIR__ . '/../../_head.php';
    echo "<main style='padding:40px; text-align:center;'>";
    echo "<h1>" . htmlspecialchars($title) . "</h1>\n";
    echo "<p>" . htmlspecialchars($message) . "</p>\n";
    echo "<p><a href='" . homePageURL() . "'>Return to Home</a></p>";
    echo "</main>";
    include_once __DIR__ . '/../../_foot.php';
    exit();
}

if (!$token || !$action) {
    renderMessage('Invalid Request', 'Missing token or action.');
}

$row = getIDBytoken($token, 'Register');

if (!$row) {
    renderMessage('Invalid or Expired', 'This verification link is invalid or has expired.');
}

$user_id = is_object($row) ? $row->user_id : ($row['user_id'] ?? null);

if (!$user_id) {
    renderMessage('Invalid Token', 'Unable to resolve user from token.');
}

try {
    if ($action === 'yes') {
        $stm = $_db->prepare("UPDATE users SET account_status = 'Unblock' WHERE user_id = ?");
        $stm->execute([$user_id]);

        $upd = $_db->prepare("UPDATE token SET expired_at = NOW() WHERE token = ?");
        $upd->execute([$token]);

        renderMessage('Email Verified', 'Thank you — your email has been verified. You may now login.');
    }

    if ($action === 'no') {
        $del = $_db->prepare("DELETE FROM users WHERE user_id = ?");
        $del->execute([$user_id]);

        $delCart = $_db->prepare("DELETE FROM cart WHERE user_id = ?");
        $delCart->execute([$user_id]);

        $delToken = $_db->prepare("DELETE FROM token WHERE token = ?");
        $delToken->execute([$token]);

        renderMessage('Registration Cancelled', 'Your registration has been cancelled as requested.');
    }

    renderMessage('Unknown Action', 'Action not recognised.');

} catch (Exception $e) {
    error_log('Verify error: ' . $e->getMessage());
    renderMessage('Error', 'An error occurred while processing your request.');
}

?>
