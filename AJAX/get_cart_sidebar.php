<?php
require '../_base.php';
require '../DAO/cart_dao.php';

header('Content-Type: application/json; charset=utf-8');

if (!is_post() || !isset($_POST['get_cart_sidebar'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

// Same user/guest logic as the server-rendered header cart
$user_id = temp('USER_ID') ?? null;
if (!$user_id) {
    if (!isset($_SESSION['guest_session_id'])) {
        $_SESSION['guest_session_id'] = 'guest_' . session_id();
    }
    $user_id = $_SESSION['guest_session_id'];
}

try {
    $cart = getCartByUserId($user_id);
    $cart_items = $cart ? getCartItems($cart->cart_id) : [];

    $items = [];
    $totalQty = 0;
    $totalChecked = 0.0;

    foreach ($cart_items as $ci) {
        $item_id = (int) ($ci->item_id ?? ($ci['item_id'] ?? 0));
        $product_name = (string) ($ci->product_name ?? ($ci['product_name'] ?? ''));
        $unit_price = (float) ($ci->unit_price ?? ($ci['unit_price'] ?? 0));
        $qty = (int) ($ci->qty ?? ($ci['qty'] ?? 1));
        $file_path = (string) ($ci->file_path ?? ($ci['file_path'] ?? ''));
        $is_check = (int) ($ci->is_check ?? ($ci['is_check'] ?? 1));

        $totalQty += $qty;
        if ($is_check) {
            $totalChecked += $unit_price * $qty;
        }

        $items[] = [
            'item_id' => $item_id,
            'product_name' => $product_name,
            'unit_price' => $unit_price,
            'qty' => $qty,
            'file_path' => $file_path,
            'is_check' => $is_check,
        ];
    }

    echo json_encode([
        'success' => true,
        'items' => $items,
        'cartCount' => $totalQty,
        'cartTotal' => $totalChecked,
    ]);
} catch (Throwable $e) {
    error_log('get_cart_sidebar error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Server error']);
}
