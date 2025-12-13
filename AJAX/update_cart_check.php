<?php
require "../_base.php";
require "../DAO/cart_dao.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_is_check'])) {
    $item_id = $_POST['item_id'] ?? 0;
    $is_check = isset($_POST['is_check']) ? (int)$_POST['is_check'] : 0;

    if ($item_id > 0) {
        try {
            $success = updateCartItemCheck($item_id, $is_check);

            if ($success) {
                echo json_encode(['success' => true, 'is_check' => $is_check]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Database update failed']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid item ID']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
exit;
