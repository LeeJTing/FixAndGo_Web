<?php
require "../../_base.php";
require "../../DAO/cart_dao.php";
require "../../DAO/order-dao.php";

$user_id = temp('USER_ID');
if (!$user_id) {
    header("Location: {$rootDir}/pages/auth/login.php");
    exit;
}

$payment_method = $_POST['payment_method'] ?? 'Cash';   // "Cash" / "Credit/Debit Card" / "Online Banking"
$address_id = (int)($_POST['address_id'] ?? 0);
if ($address_id <= 0) die("Please select a delivery address.");

$cart = getCartByUserId($user_id);
if (!$cart) die("Cart not found.");

$items = getCheckedCartItems($cart->cart_id);
if (!$items || count($items) === 0) die("No selected items to checkout.");

try {
    $_db->beginTransaction();

    // Verify address 
    $stmt = $_db->prepare("SELECT address_id FROM address WHERE address_id=? AND user_id=? LIMIT 1");
    $stmt->execute([$address_id, $user_id]);
    if (!$stmt->fetchColumn()) {
        throw new Exception("Invalid address.");
    }

    // total calculation + stock check
    $total = 0;
    foreach ($items as $it) {
        $qty = (int)$it->qty;
        if ($qty <= 0) throw new Exception("Invalid quantity.");
        if ((int)$it->stock_quantity < $qty) {
            throw new Exception("Insufficient stock for product: {$it->product_name}");
        }
        $total += ((float)$it->unit_price * $qty);
    }

    // shipping fee
    $shipping = 10.00;
    $total += $shipping;

    $order_id = createOrderDao($user_id, $address_id, $total, 'Pending', 'Pending', 0);

    foreach ($items as $it) {
        addOrderItemDao($order_id, (int)$it->product_id, (int)$it->qty, (float)$it->unit_price);
    }

    if (!isset($_SESSION['order_payment_method'])) $_SESSION['order_payment_method'] = [];
    $_SESSION['order_payment_method'][$order_id] = $payment_method;

    // Clear checked items from cart
    clearCheckedCartItems($cart->cart_id);

    $_db->commit();

    header("Location: {$rootDir}/pages/order/order_detail.php?order_id={$order_id}");
    exit;
} catch (Exception $e) {
    $_db->rollBack();
    die("Checkout failed: " . htmlspecialchars($e->getMessage()));
}
