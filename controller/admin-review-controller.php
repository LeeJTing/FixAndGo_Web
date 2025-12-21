<?php
require_once __DIR__ . "/../_base.php";
require_once __DIR__ . "/../dao/review_dao.php";
require_once __DIR__ . "/../dao/security_dao.php";

header('Content-Type: application/json; charset=utf-8');

$action = post('action') ?? get('action') ?? '';
try {
    switch ($action) {
        case 'fetch_product_reviews':
            $pid = $_REQUEST['product_id'] ?? null;
            if (!$pid) throw new Exception('Missing product_id');
            $rows = adminGetReviewsByProduct($pid);
            echo json_encode(['ok' => true, 'reviews' => $rows]);
            break;

        case 'admin_toggle_valid':
            $rid = $_POST['review_id'] ?? null;
            $val = isset($_POST['value']) ? intval($_POST['value']) : null;
            if (!$rid || $val === null) throw new Exception('Missing params');
            if ($val === 1) validReview($rid);
            else invalidReview($rid);
            echo json_encode(['ok' => true]);
            break;

        case 'admin_block_user':
            $uid = $_POST['user_id'] ?? null;
            if (!$uid) throw new Exception('Missing user_id');
            $ok = setUserAccountStatus($uid, 'Blocked');
            // invalidate all reviews by this user
            invalidReviewsByUser($uid);
            echo json_encode(['ok' => $ok]);
            break;

        case 'delete':
            $rid = $_POST['review_id'] ?? null;
            if (!$rid) throw new Exception('Missing review_id');
            deleteReviewId($rid);
            echo json_encode(['ok' => true]);
            break;

        default:
            echo json_encode(['ok' => false, 'message' => 'Unknown action']);
    }
} catch (Exception $e) {
    echo json_encode(['ok' => false, 'message' => $e->getMessage()]);
}
