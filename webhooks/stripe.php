<?php
require "../_base.php";
require "../vendor/autoload.php";
require "../DAO/order-dao.php";

\Stripe\Stripe::setApiKey('sk_test_51SeFjZJ0NvBCcCJetZZw02Tf4Hjz1ZUMyT9S1hMPiENlEBR5ZBT7F3b9l6ylskvJ2lED5qpp9nPVxXVeqOwwhZvV00IxMEImWN'); // same secret key
$endpoint_secret = 'whsec_xxx';           // Stripe webhook signing secret

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

try {
    $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
} catch (Exception $e) {
    http_response_code(400);
    exit;
}

if ($event->type === 'checkout.session.completed') {
    $session = $event->data->object;
    $order_id = (int)($session->metadata->order_id ?? 0);

    if ($order_id > 0) {
        // Mark order paid
        markOrderPaidByOrderIdDao($order_id);

        // Insert payment ONLY now (paid_at auto fills -> correct)
        // Determine method: if fpx used => Bank Transfer, else Credit Card
        $method = 'Credit Card';
        if (!empty($session->payment_method_types) && in_array('fpx', $session->payment_method_types)) {
            $method = 'Bank Transfer';
        }

        // Prevent duplicate inserts
        $existing = getPaymentRowByOrderIdDao($order_id);
        if (!$existing) {
            insertPaymentForOrderDao($order_id, $method);
        }
    }
}

http_response_code(200);
