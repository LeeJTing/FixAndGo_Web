<?php
require "../_base.php";
require "../DAO/cart_dao.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['remove_items'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

$item_ids = $_POST['item_ids'] ?? [];

if (!is_array($item_ids) || empty($item_ids)) {
    echo json_encode(['success' => false, 'error' => 'No items selected']);
    exit;
}

try {
    $deleted = removeFromCartItems($item_ids);
    echo json_encode(['success' => true, 'deleted' => (int)$deleted]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}
exit;
