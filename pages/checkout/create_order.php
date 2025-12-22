<?php
require "../../_base.php";
require "../../DAO/cart_dao.php";
require "../../DAO/order-dao.php";
require "../../DAO/loyaltypoint_dao.php";
require "../../component/receipt_email.php";

$user_id = temp('USER_ID');
if (!$user_id) {
    header("Location: {$rootDir}/pages/auth/login.php");
    exit;
}

$payment_method = $_POST['payment_method'] ?? 'Cash';
$use_loyalty_points = (int)($_POST['use_loyalty_points'] ?? 0) === 1;
$address_id = (int)($_POST['address_id'] ?? 0);
if ($address_id <= 0) die("Please select a delivery address.");

$cart = getCartByUserId($user_id);
if (!$cart) die("Cart not found.");

$items = getCheckedCartItems($cart->cart_id);
if (!$items || count($items) === 0) die("No selected items to checkout.");

try {
    $_db->beginTransaction();

    $stmt = $_db->prepare("SELECT address_name, address_one, address_two, address_three, state, post_code, country FROM address WHERE address_id=? AND user_id=? LIMIT 1");
    $stmt->execute([$address_id, $user_id]);
    $address = $stmt->fetch(PDO::FETCH_OBJ);
    if (!$address) {
        throw new Exception("Invalid address.");
    }

    $total = 0;
    foreach ($items as $it) {
        $qty = (int)$it->qty;
        if ($qty <= 0) throw new Exception("Invalid quantity.");
        if ((int)$it->stock_quantity < $qty) {
            throw new Exception("Insufficient stock for product: {$it->product_name}");
        }
        $total += ((float)$it->unit_price * $qty);
    }

    $shipping = 10.00;
    $total += $shipping;

    $used_points = 0;
    if ($use_loyalty_points) {
        $available_points = getAvailableLoyaltyPointsDao($user_id);
        $total_cents = (int)round(((float)$total) * 100);
        $point_value_cents = 10;
        $max_usable_points = (int)floor($total_cents / $point_value_cents);
        $used_points = min($available_points, $max_usable_points);
        if ($used_points > 0) {
            $discount_cents = $used_points * $point_value_cents;
            $total_cents -= $discount_cents;
            $total = $total_cents / 100;
        }
    }

    $is_fully_paid_by_points = ($use_loyalty_points && $used_points > 0 && (int)round(((float)$total) * 100) === 0);

    // Determine the effective payment method AFTER applying loyalty points.
    // If points fully cover the total, it is NOT Cash on Delivery.
    $effective_payment_method = $is_fully_paid_by_points ? 'Loyalty Points' : $payment_method;
    $is_cod = ($effective_payment_method === 'Cash');

    $order_payment_status = ($is_cod || $is_fully_paid_by_points) ? 'Paid' : 'Pending';
    $order_status = $is_cod ? 'Delivered' : (($is_fully_paid_by_points) ? 'Processing' : 'Pending');
    $utilize_point_flag = ($used_points > 0) ? 1 : 0;

    $order_id = createOrderDao($user_id, $address_id, $total, $order_payment_status, $order_status, $utilize_point_flag);

    if ($used_points > 0) {
        if (!spendLoyaltyPointsDao($user_id, $used_points)) {
            throw new Exception('Failed to apply loyalty points.');
        }
    }

    // Create payment record.
    if ($is_fully_paid_by_points) {
        if (!insertPaymentForOrderDao($order_id, 'Loyalty Points')) {
            throw new Exception('Failed to create payment record.');
        }
    } else {
        if (!createPaymentPendingDao($order_id, $effective_payment_method)) {
            throw new Exception('Failed to create payment record.');
        }

        if ($is_cod) {
            markPaymentPaidDao($order_id);
        }
    }

    // Reward points: RM 1 spent by customer => 1 point (only when marked Paid now).
    if ($order_payment_status === 'Paid') {
        $reward_points = (int)floor((float)$total);
        if ($reward_points > 0) {
            addLoyaltyPointsDao($user_id, $reward_points);
        }
    }

    foreach ($items as $it) {
        addOrderItemDao($order_id, (int)$it->product_id, (int)$it->qty, (float)$it->unit_price);
    }

    if (!isset($_SESSION['order_payment_method'])) $_SESSION['order_payment_method'] = [];
    $_SESSION['order_payment_method'][$order_id] = $effective_payment_method;

    // Clear checked items from cart
    clearCheckedCartItems($cart->cart_id);

    $_db->commit();

    if ($is_cod || $is_fully_paid_by_points) {
        try {
            $overrides = [];
            if ($is_cod) {
                $overrides = [
                    'payment_method' => 'Cash on Delivery',
                    'status' => 'Delivered'
                ];
            } elseif ($is_fully_paid_by_points) {
                $overrides = [
                    'payment_method' => 'Loyalty Points',
                    'status' => 'Processing'
                ];
            }
            sendReceiptEmailForOrder((int)$order_id, $overrides);
        } catch (Throwable $mailEx) {
            // Do not block checkout on email errors.
            error_log('Receipt email failed for order ' . $order_id . ': ' . $mailEx->getMessage());
        }
    }

    header("Location: {$rootDir}/pages/order/order_detail.php?order_id={$order_id}");
    exit;
} catch (Exception $e) {
    $_db->rollBack();
    die("Checkout failed: " . htmlspecialchars($e->getMessage()));
}
