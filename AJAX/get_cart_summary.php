<?php
require "../_base.php";
require_once "../DAO/cart_dao.php";
require_once "../DAO/loyaltypoint_dao.php";

header('Content-Type: application/json');

try {
    // Determine user ID 
    $user_id = temp('USER_ID') ?? null;
    if (!$user_id) {
        if (!isset($_SESSION['guest_session_id'])) {
            $_SESSION['guest_session_id'] = 'guest_' . session_id();
        }
        $user_id = $_SESSION['guest_session_id'];
    }

    $use_loyalty_points = (int)($_GET['use_loyalty_points'] ?? 0) === 1;

    $cart = getCartByUserId($user_id);
    if (!$cart) {
        echo json_encode([
            'success' => true,
            'selected_total' => 0.0,
            'shipping_fee' => 10.0,
            'discount' => 0.0,
            'final_total' => 0.0,
            'loyalty_points' => 0,
            'loyalty_points_rm' => 0.0,
        ]);
        exit;
    }

    $items = getCheckedCartItems($cart->cart_id);
    $selected_total = 0.0;
    foreach ($items as $it) {
        $selected_total += ((float)$it->unit_price * (int)$it->qty);
    }

    $shipping_fee = 10.0;
    $subtotal_plus_shipping = $selected_total + $shipping_fee;

    $available_points = 0;
    if (temp('USER_ID')) {
        $available_points = getAvailableLoyaltyPointsDao(temp('USER_ID'));
    }

    $discount = 0.0;
    $used_points = 0;
    if ($use_loyalty_points && $available_points > 0 && $subtotal_plus_shipping > 0) {
        $total_cents = (int)round($subtotal_plus_shipping * 100);
        $point_value_cents = 10;
        $max_usable_points = (int)floor($total_cents / $point_value_cents);
        $used_points = min($available_points, $max_usable_points);
        $discount_cents = $used_points * $point_value_cents;
        $discount = $discount_cents / 100;
    }

    $final_total = max(0.0, $subtotal_plus_shipping - $discount);

    echo json_encode([
        'success' => true,
        'selected_total' => round($selected_total, 2),
        'shipping_fee' => round($shipping_fee, 2),
        'discount' => round($discount, 2),
        'final_total' => round($final_total, 2),
        'loyalty_points' => (int)$available_points,
        'loyalty_points_rm' => round(((int)$available_points) * 0.10, 2),
        'used_points' => (int)$used_points,
        'used_points_rm' => round(((int)$used_points) * 0.10, 2),
        'remaining_points' => (int)max(0, (int)$available_points - (int)$used_points),
    ]);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
