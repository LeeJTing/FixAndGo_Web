<?php
require "../_base.php";
require "../DAO/cart_dao.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quantity'])) {
    $item_id = $_POST['item_id'] ?? 0;
    $quantity = $_POST['quantity'] ?? 1;
    
    if ($item_id > 0 && $quantity > 0) {
        try {
            $res = updateCartItem($item_id, $quantity);
            if (is_array($res) && !empty($res['success'])) {
                echo json_encode([
                    'success' => true,
                    'quantity' => (int)($res['quantity'] ?? $quantity),
                    'stock' => (int)($res['stock'] ?? 0),
                    'capped' => !empty($res['capped']),
                ]);
            } else {
                $err = is_array($res) ? ($res['error'] ?? 'Database update failed') : 'Database update failed';
                echo json_encode([
                    'success' => false,
                    'error' => $err,
                    'quantity' => is_array($res) ? ($res['quantity'] ?? null) : null,
                    'stock' => is_array($res) ? ($res['stock'] ?? null) : null,
                ]);
            }
        } catch (Throwable $e) {
            error_log('update_quantity error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Server error']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid item ID or quantity']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
exit;
?>