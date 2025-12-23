<?php
require "../../_base.php";
require "../../vendor/autoload.php";
require "../../DAO/order-dao.php";
require_once __DIR__ . '/../../config/stripe.php';

$user_id = temp('USER_ID');
if (!$user_id) {
  header("Location: {$rootDir}/pages/auth/login.php");
  exit;
}

$order_id = (int)($_GET['order_id'] ?? 0);
if ($order_id <= 0) die("Invalid order.");

$order = getOrderByIdAndUserDao($order_id, $user_id);
if (!$order) die("Order not found or not yours.");
if ($order->payment_status === 'Paid') {
  header("Location: {$rootDir}/pages/order/order_detail.php?order_id={$order_id}");
  exit;
}

// Determine selected method.
// Prefer session , otherwise fall back to DB payment record.
$selectedMethod = $_SESSION['order_payment_method'][$order_id] ?? '';
if ($selectedMethod === '') {
  try {
    $payRow = getPaymentRowByOrderIdDao($order_id);
    $pmDb = isset($payRow->payment_method) ? (string)$payRow->payment_method : '';
    // payment.payment_method enum values: 'Credit Card','Debit Card','PayPal','Bank Transfer','Cash','Loyalty Points'
    if ($pmDb === 'Bank Transfer') {
      $selectedMethod = 'Online Banking';
    } elseif ($pmDb === 'Cash') {
      $selectedMethod = 'Cash';
    } elseif ($pmDb === 'Loyalty Points') {
      $selectedMethod = 'Loyalty Points';
    } elseif ($pmDb === 'Debit Card' || $pmDb === 'Credit Card' || $pmDb === 'PayPal') {
      $selectedMethod = 'Credit/Debit Card';
    }
  } catch (Throwable $e) {
    error_log('Stripe create session: failed to load payment method for order ' . $order_id . ': ' . $e->getMessage());
  }
}

if ($selectedMethod === '' || $selectedMethod === 'Cash') {
  die("This order is Cash on Delivery.");
}
if ($selectedMethod === 'Loyalty Points') {
  die("This order was paid by loyalty points.");
}

$stripeCfg = stripe_config();
\Stripe\Stripe::setApiKey($stripeCfg['secret_key']);

$payment_method_types = ($selectedMethod === 'Online Banking') ? ['fpx'] : ['card'];

$session = \Stripe\Checkout\Session::create([
  'mode' => 'payment',
  'payment_method_types' => $payment_method_types,
  'line_items' => [[
    'price_data' => [
      'currency' => 'myr',
      'product_data' => ['name' => "Order #{$order_id}"],
      'unit_amount' => (int) round(((float)$order->total_price) * 100),
    ],
    'quantity' => 1,
  ]],
  'metadata' => [
    'order_id' => (string)$order_id,
  ],
  'success_url' => $rootDir . "/pages/payment/stripe_success.php?order_id={$order_id}&session_id={CHECKOUT_SESSION_ID}",
  'cancel_url'  => $rootDir . "/pages/order/order_detail.php?order_id={$order_id}",
]);

try {
  if (!paymentExistsForOrderDao($order_id)) {
    createPaymentPendingDao($order_id, $selectedMethod);
  }

  if (!empty($session->id)) {
    saveStripeSessionIdDao($order_id, (string)$session->id);
  }
  if (!empty($session->payment_intent)) {
    saveStripePaymentIntentIdDao($order_id, (string)$session->payment_intent);
  }
} catch (Throwable $e) {
  error_log('Stripe session persist failed for order ' . $order_id . ': ' . $e->getMessage());
}

header("Location: " . $session->url);
exit;
