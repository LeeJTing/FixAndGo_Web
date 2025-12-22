<?php
require "../../_base.php";
require "../../vendor/autoload.php";
require "../../DAO/order-dao.php";
require "../../DAO/loyaltypoint_dao.php";
require "../../component/receipt_email.php";
require_once __DIR__ . '/../../config/stripe.php';

$payload = @file_get_contents('php://input');
$sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

$webhookSecret = stripe_webhook_secret();
if ($webhookSecret === '') {
    http_response_code(500);
    echo 'Webhook secret not configured.';
    exit;
}

try {
    $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
} catch (Throwable $e) {
    http_response_code(400);
    echo 'Invalid signature.';
    exit;
}

if ($event->type === 'checkout.session.completed') {
    $session = $event->data->object;

    $order_id = isset($session->metadata->order_id) ? (int)$session->metadata->order_id : 0;
    if ($order_id > 0 && (string)$session->payment_status === 'paid') {
        try {
            $order = getOrderByIdDao($order_id);
            if ($order && strtolower((string)$order->payment_status) !== 'paid') {
                $shouldSendReceipt = false;
                $_db->beginTransaction();

                if (!paymentExistsForOrderDao($order_id)) {
                    createPaymentPendingDao($order_id, 'Credit/Debit Card');
                }

                if (!empty($session->id)) {
                    saveStripeSessionIdDao($order_id, (string)$session->id);
                }
                if (!empty($session->payment_intent)) {
                    saveStripePaymentIntentIdDao($order_id, (string)$session->payment_intent);
                }

                markOrderPaidByOrderIdDao($order_id);
                markPaymentPaidDao($order_id);

                $shouldSendReceipt = true;

                // Reward points: RM 1 spent => 1 point
                $reward_points = (int)floor((float)$order->total_price);
                if ($reward_points > 0) {
                    addLoyaltyPointsDao((string)$order->user_id, $reward_points);
                }

                $_db->commit();

                // Send receipt/invoice email (best-effort).
                if ($shouldSendReceipt) {
                    try {
                        $paymentRow = getPaymentRowByOrderIdDao($order_id);
                        $paymentLabel = $paymentRow->payment_method ?? 'Credit/Debit Card';
                        sendReceiptEmailForOrder((int)$order_id, [
                            'payment_method' => $paymentLabel,
                            'status' => 'Processing'
                        ]);
                    } catch (Throwable $mailEx) {
                        error_log('Stripe webhook receipt email failed for order ' . $order_id . ': ' . $mailEx->getMessage());
                    }
                }
            }
        } catch (Throwable $e) {
            if (isset($_db) && $_db->inTransaction()) {
                $_db->rollBack();
            }
            error_log('Stripe webhook processing failed for order ' . $order_id . ': ' . $e->getMessage());
        }
    }
}

http_response_code(200);
echo 'ok';
