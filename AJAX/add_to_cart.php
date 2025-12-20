<?php
require '../_base.php';
require '../DAO/cart_dao.php';

header('Content-Type: application/json; charset=utf-8');

if (!is_post() || !isset($_POST['add_to_cart'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

$product_id = (int) post('product_id');
$quantity = (int) post('quantity', 1);
if ($quantity < 1) $quantity = 1;

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid product']);
    exit;
}

// Allow both logged-in users and guests (same approach used by header cart)
$user_id = temp('USER_ID') ?? null;
if (!$user_id) {
    if (!isset($_SESSION['guest_session_id'])) {
        $_SESSION['guest_session_id'] = 'guest_' . session_id();
    }
    $user_id = $_SESSION['guest_session_id'];
}

try {
    $cart = getCartByUserId($user_id);
    if (!$cart) {
        $stmt = $_db->prepare('INSERT INTO cart (user_id) VALUES (?)');
        $stmt->execute([$user_id]);
        $cart_id = (int) $_db->lastInsertId();
    } else {
        $cart_id = (int) ($cart->cart_id ?? $cart['cart_id']);
    }

    $ok = addToCart($cart_id, $product_id, $quantity);
    if (!$ok) {
        echo json_encode(['success' => false, 'error' => 'Failed to add item to cart']);
        exit;
    }

    $cartItems = getCartItems($cart_id);
    $totalQty = 0;
    foreach ($cartItems as $item) {
        $qty = isset($item->qty) ? (int) $item->qty : (int) ($item['qty'] ?? 0);
        $totalQty += $qty;
    }

    echo json_encode([
        'success' => true,
        'cartCount' => $totalQty,
    ]);
} catch (Throwable $e) {
    // Don't leak details to client; log server-side.
    error_log('add_to_cart error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Server error']);
}
