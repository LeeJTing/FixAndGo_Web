<?php
require "../_base.php";
require "../DAO/cart_dao.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_item'])) {
    $item_id = $_POST['item_id'] ?? 0;
    
    if ($item_id > 0) {
        try {
            $success = removeFromCart($item_id);
            
            if ($success) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Database delete failed']);
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
?>