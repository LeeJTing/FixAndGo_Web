<?php
require "../_base.php";
require "../DAO/cart_dao.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quantity'])) {
    $item_id = $_POST['item_id'] ?? 0;
    $quantity = $_POST['quantity'] ?? 1;
    
    if ($item_id > 0 && $quantity > 0) {
        try {
            $success = updateCartItem($item_id, $quantity);
            
            if ($success) {
                echo json_encode(['success' => true, 'quantity' => $quantity]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Database update failed']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid item ID or quantity']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
exit;
?>