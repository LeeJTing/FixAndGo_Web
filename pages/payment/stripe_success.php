<?php
require "../../_base.php";
require "../../vendor/autoload.php";
require "../../DAO/order-dao.php";
require "../../DAO/loyaltypoint_dao.php";
require "../../component/receipt_email.php";
require_once __DIR__ . '/../../config/stripe.php';

$user_id = temp('USER_ID');
if (!$user_id) {
    header("Location: {$rootDir}/pages/auth/login.php");
    exit;
}

$order_id = (int)($_GET['order_id'] ?? 0);
$session_id = (string)($_GET['session_id'] ?? '');

if ($order_id <= 0) {
    http_response_code(400);
    die('Invalid order.');
}

// Load order and ensure ownership.
$order = getOrderByIdAndUserDao($order_id, $user_id);
if (!$order) {
    http_response_code(404);
    die('Order not found or not yours.');
}

// If already paid, just redirect.
if (strtolower((string)$order->payment_status) === 'paid') {
    header("Location: {$rootDir}/pages/order/order_detail.php?order_id={$order_id}");
    exit;
}

if ($session_id === '') {
    echo "<h2>Payment confirmation pending</h2>";
    echo "<p>Missing Stripe session id. Please use the Pay Now button again from your order page.</p>";
    echo "<a href=\"{$rootDir}/pages/order/order_detail.php?order_id={$order_id}\">Back to Order</a>";
    exit;
}

$stripeCfg = stripe_config();
\Stripe\Stripe::setApiKey($stripeCfg['secret_key']);

try {
    $session = \Stripe\Checkout\Session::retrieve($session_id, []);

    // Confirm this session belongs to this order.
    $metaOrderId = isset($session->metadata->order_id) ? (int)$session->metadata->order_id : 0;
    if ($metaOrderId !== (int)$order_id) {
        http_response_code(400);
        die('Session does not match order.');
    }

    // Stripe Checkout uses session.payment_status = 'paid' when payment succeeded.
    if ((string)$session->payment_status !== 'paid') {
        echo "<h2>Payment not completed</h2>";
        echo "<p>Your payment is not marked as paid yet (status: " . htmlspecialchars((string)$session->payment_status) . ").</p>";
        echo "<a href=\"{$rootDir}/pages/order/order_detail.php?order_id={$order_id}\">Back to Order</a>";
        exit;
    }

    // Optional: sanity check amount
    $expectedAmount = (int)round(((float)$order->total_price) * 100);
    if (isset($session->amount_total) && (int)$session->amount_total !== $expectedAmount) {
        error_log("Stripe amount mismatch for order {$order_id}: expected {$expectedAmount}, got {$session->amount_total}");
    }

    $_db->beginTransaction();

    // Ensure payment row exists.
    if (!paymentExistsForOrderDao($order_id)) {
        // Default to card if unknown; the user selected method is stored in session elsewhere.
        createPaymentPendingDao($order_id, 'Credit/Debit Card');
    }

    // Persist Stripe IDs.
    if (!empty($session->id)) {
        saveStripeSessionIdDao($order_id, (string)$session->id);
    }
    if (!empty($session->payment_intent)) {
        saveStripePaymentIntentIdDao($order_id, (string)$session->payment_intent);
    }

    // Mark order/payment as paid.
    markOrderPaidDao($order_id, $user_id);
    markPaymentPaidDao($order_id);

    // Reward points: RM 1 spent => 1 point (only now that payment is confirmed).
    $reward_points = (int)floor((float)$order->total_price);
    if ($reward_points > 0) {
        addLoyaltyPointsDao($user_id, $reward_points);
    }

    $_db->commit();

    // Send receipt/invoice email (best-effort).
    try {
        $paymentRow = getPaymentRowByOrderIdDao($order_id);
        $paymentLabel = $paymentRow->payment_method ?? 'Credit/Debit Card';
        sendReceiptEmailForOrder((int)$order_id, [
            'payment_method' => $paymentLabel,
            'status' => 'Processing'
        ]);
    } catch (Throwable $mailEx) {
        error_log('Stripe receipt email failed for order ' . $order_id . ': ' . $mailEx->getMessage());
    }

    header("Location: {$rootDir}/pages/order/order_detail.php?order_id={$order_id}");
    exit;
} catch (Throwable $e) {
    if (isset($_db) && $_db->inTransaction()) {
        $_db->rollBack();
    }
    error_log('Stripe success verify failed for order ' . $order_id . ': ' . $e->getMessage());
    echo "<h2>We couldn't confirm your payment yet</h2>";
    echo "<p>Please wait a moment and refresh the order page. If it still shows Pending, contact support.</p>";
    echo "<a href=\"{$rootDir}/pages/order/order_detail.php?order_id={$order_id}\">Back to Order</a>";
    exit;
}
