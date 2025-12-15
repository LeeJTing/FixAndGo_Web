<?php
require "../../_base.php";
require "../../vendor/autoload.php";
require "../../DAO/order-dao.php";

$user_id = temp('USER_ID');
if (!$user_id) { header("Location: {$rootDir}/pages/auth/login.php"); exit; }

$order_id = (int)($_GET['order_id'] ?? 0);
if ($order_id <= 0) die("Invalid order.");

$order = getOrderByIdAndUserDao($order_id, $user_id);
if (!$order) die("Order not found or not yours.");
if ($order->payment_status === 'Paid') {
  header("Location: {$rootDir}/pages/order/order_detail.php?order_id={$order_id}");
  exit;
}

$selectedMethod = $_SESSION['order_payment_method'][$order_id] ?? 'Cash';
if ($selectedMethod === 'Cash') die("This order is Cash on Delivery.");

\Stripe\Stripe::setApiKey('sk_test_51SeFjZJ0NvBCcCJetZZw02Tf4Hjz1ZUMyT9S1hMPiENlEBR5ZBT7F3b9l6ylskvJ2lED5qpp9nPVxXVeqOwwhZvV00IxMEImWN'); // put your Stripe Secret Key here

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
  'success_url' => $rootDir . "/pages/payment/stripe_success.php?order_id={$order_id}",
  'cancel_url'  => $rootDir . "/pages/order/order_detail.php?order_id={$order_id}",
]); // :contentReference[oaicite:5]{index=5}

header("Location: " . $session->url);
exit;
