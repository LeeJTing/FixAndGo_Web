<?php
require_once  __DIR__ . '/../_base.php';
require __DIR__ . '/../DAO/order-dao.php';
require_once __DIR__ . '/../email/email.php';
require_once __DIR__ . '/../DAO/product_dao.php';
require_once __DIR__ . '/../DAO/security_dao.php';

$order_id = get('order_id');
$user_id = get('user_id');
$action = get('action') ?? null;

if ($action === 'getAddress') {
    $user_id = get('user_id');
    $order_id = get('order_id');

    $addresses = getMemberAddressDao($user_id, $order_id); // Returns array of objects
    $count = count($addresses);

    if ($count < 1) {
        $_SESSION['flash_message'] = [
            'type' => 'warning', // success, warning, error etc.
            'text' => 'Not enough addresses to select. Redirected to order detail.'
        ];
        header("Location: ../../../pages/admin/admin-order-detail.php?id={$order_id}");
        exit;
    }

    // Otherwise, return JSON response
    $response = [
        'count' => $count,
        'addresses' => $addresses
    ];

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
} else if (is_post()) {
    $action = post('action');
    if ($action == 'updateAddress') {
        $orderId = post('order_id');
        $address_id = post('address');

        // Update the order with the selected address
        updateOrderAddress($orderId, $address_id);

        // Set a flash message for success
        $_SESSION['flash_message'] = [
            'type' => 'success',
            'text' => 'Delivery address updated successfully.'
        ];

        // Redirect back to order detail page
        header("Location: ../../../pages/admin/admin-order-detail.php?id={$orderId}");
        exit;
    } else if ($action === 'updateStatus') {
        $orderId = post('order_id');
        $status = post('status');
        $statusMap = [
            'Pending'    => 'pending',
            'Processing' => 'processing',
            'Shipping'   => 'shipping',
            'Delivered'  => 'delivered',
            'Cancelled'  => 'cancelled'
        ];

        if ($status === 'Cancelled') {
            try {
                $deleted = cancelOrder((int)$orderId);
                $order_userId = getOrderByIdDao((int)$orderId);
                $user = getUserById($order_userId->user_id);
                $adminCancelOrder = sendOrderCancelEmail($orderId, $user->email, $user->username);
                if ($deleted && $adminCancelOrder) {
                    $_SESSION['flash_message'] = [
                        'type' => 'success',
                        'text' => "Order {$orderId} has been cancelled successfully."
                    ];
                } else {
                    $_SESSION['flash_message'] = [
                        'type' => 'error',
                        'text' => "Failed to delete Order {$orderId}. Please try again."
                    ];
                }
            } catch (Exception $e) {
                $_SESSION['flash_message'] = [
                    'type' => 'error',
                    'text' => "Error cancelling Order {$orderId}: " . $e->getMessage()
                ];
            }

            // Redirect back to the orders list page
            header("Location: ../../../pages/admin/admin-order.php");
            exit;
        } else if (in_array($statusMap[$status], ['shipping', 'processing', 'pending'])) {
            if (in_array($statusMap[$status], ['shipping', 'processing', 'pending'])) {
                updateOrderStatus($orderId, $statusMap[$status]);

                // Set flash message
                $_SESSION['flash_message'] = [
                    'type' => 'success',
                    'text' => "Order status updated to " . $statusMap[$status] . "."
                ];
            } else {
                // Optional: invalid status
                $_SESSION['flash_message'] = [
                    'type' => 'error',
                    'text' => "Invalid order status."
                ];
            }
            // Redirect back to order detail page
            header("Location: ../../../pages/admin/admin-order-detail.php?id={$orderId}");
            exit;
        } else if ($statusMap[$status] === 'delivered') {
            // Set flash message
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'text' => "Order status updated to " . $statusMap[$status] . "."
            ];

            // Redirect back to the orders list page
            header("Location: ../../../pages/admin/admin-order.php");
            exit;
        }
    } else if ($action === 'updateDate') {
        $orderId = post('order_id');
        $date    = post('estimated_delivery');

        $order = getOrderById($orderId);

        // Safety check
        if (!$order || empty($date)) {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'text' => 'Invalid order or delivery date.'
            ];
            header("Location: ../../../pages/admin/admin-order-detail.php?id={$orderId}");
            exit;
        }

        try {
            // DATE ONLY objects (immutable = no accidental modify)
            $newDate = new DateTime($date);       // Convert string to DateTime
            $newDateOnly = $newDate->format('Y-m-d');
            $orderDate = $order->order_at;

            if (!$newDate) {
                throw new Exception('Invalid date format.');
            }
            // Compare DATE only
            if ($newDate->format('Y-m-d') > $orderDate) {

                updateDeliveryDate($orderId, $newDate->format('Y-m-d'));

                $_SESSION['flash_message'] = [
                    'type' => 'success',
                    'text' => 'Estimated delivery date updated to ' . $newDate->format('M d, Y')
                ];
            } else {
                $_SESSION['flash_message'] = [
                    'type' => 'error',
                    'text' => 'Estimated delivery date must be after order date.'
                ];
            }
        } catch (Exception $e) {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'text' => 'Failed to update delivery date.'
            ];
        }
        // Redirect back to the orders list page
        header("Location: ../../../pages/admin/admin-order-detail.php?id={$orderId}");
        exit;
    } else if ($action === 'cancelOrder') {
        $orderId = (int)post('order_id');
        $userId = post('userId');
        $user = getUserById($userId);
        $result = cancelCustomerOrder($orderId, $userId);
        $sendSuccess = notifyCustomerCancellation($orderId, $user->email, $user->user_name);
        header('Content-Type: application/json');

        if ($result && $sendSuccess) {
            echo json_encode([
                'success' => true,
                'message' => 'Order cancelled successfully.'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Cannot cancel order. It may be already shipped or invalid.'
            ]);
        }
        exit;
    }
}
function getUserOrder($order, $user)
{
    return getOrderDetailsForUserDao(
        $order,
        $user
    );
}
function getPayment($order_id)
{
    return getSpecificPaymentDao($order_id);
}

function getOrderHistoryAdmin()
{
    return getAllOrdersAdmin();
}

function getOrderItem($id)
{
    return getOrderItemsDao($id);
}

function getOrderHistoryMember($id)
{
    return getMemberOrderHistory($id);
}

function getOrderById($order_id)
{
    return getOrderByIdDao($order_id);
}

function getPaymentRecord($order_id)
{
    return getPaymentRowByOrderIdDao($order_id);
}

function getMemberAddress($user_id, $order_id)
{
    return getMemberAddressDao($user_id, $order_id);
}
function getAllCategory()
{
    return getAllCategoryDao();
}
function getWithSelectedAddress($id)
{
    return getOrderWithSelectedAddressDao($id);
}

function notifyCustomerCancellation($order_id, $customer_email, $customer_name)
{
    $mail = get_mail();

    if (!$mail) {
        error_log("Mailer instance not available for cancellation email to {$customer_email}");
        return false;
    }

    try {
        $mail->addAddress($customer_email, $customer_name);
        $mail->isHTML(true);
        $mail->Subject = "Order {$order_id} - Cancellation Confirmation";

        // Professional, friendly HTML email body
        $mail->Body = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 30px auto; background: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
                .header { background: #ef4444; color: #ffffff; padding: 30px; text-align: center; }
                .header h1 { margin: 0; font-size: 24px; }
                .content { padding: 30px; }
                .content h2 { color: #dc2626; }
                .highlight { background: #fef2f2; border-left: 4px solid #ef4444; padding: 15px; margin: 20px 0; font-weight: bold; }
                .footer { background: #1e293b; color: #cbd5e1; padding: 25px; text-align: center; font-size: 14px; }
                .btn { display: inline-block; background: #f59e0b; color: #ffffff; padding: 12px 25px; text-decoration: none; border-radius: 6px; font-weight: bold; margin: 20px 0; }
                a { color: #f59e0b; text-decoration: none; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>Order Cancellation Confirmed</h1>
                </div>
                <div class="content">
                    <p>Dear <strong>' . htmlspecialchars($customer_name) . '</strong>,</p>

                    <p>We’re writing to confirm that your order has been successfully <strong>cancelled</strong> as requested.</p>

                    <div class="highlight">
                        Order Number: <strong>' . htmlspecialchars($order_id) . '</strong>
                    </div>

                    <h2>What happens next?</h2>
                    <ul>
                        <li>If you paid by card or online payment, a full refund has been initiated.</li>
                        <li>The amount will be credited back to your original payment method within <strong>3–7 business days</strong> (depending on your bank).</li>
                        <li>You will receive a separate refund confirmation email once processed.</li>
                    </ul>

                    <p>If you cancelled by mistake or have any questions, please don’t hesitate to reply to this email or contact our support team — we’re happy to help you place a new order!</p>

                    <p style="text-align: center;">
                        <a href="https://Fix&Go.com/orders" class="btn">View Your Orders</a>
                    </p>

                    <p>Thank you for shopping with us.<br>We hope to serve you again soon!</p>

                    <p>Best regards,<br><strong>The FixAndGo Team</strong></p>
                </div>
                <div class="footer">
                    &copy; ' . date('Y') . ' FixAndGo Hardware Store. All rights reserved.<br>
                    <a href="https://Fix&Go.com">www.Fix&Go.com</a> | 
                    <a href="mailto:support@Fix&Go.com">support@Fix&Go.com</a>
                </div>
            </div>
        </body>
        </html>';

        // Plain text fallback
        $mail->AltBody = "Dear {$customer_name},\n\n" .
            "Your order {$order_id} has been successfully cancelled.\n\n" .
            "A full refund has been initiated and will appear in your account within 3–7 business days.\n\n" .
            "If you have any questions, please contact our support team.\n\n" .
            "Thank you,\nThe FixAndGo Team";

        $mail->send();
        error_log("Cancellation confirmation email sent to {$customer_email} for order {$order_id}");
        return true;
    } catch (Exception $e) {
        error_log("Failed to send cancellation email to {$customer_email}: " . $e->getMessage());
        return false;
    }
}

function sendOrderCancelEmail($order_id, $customer_email, $customer_name, $reason = 'Customer request')
{
    try {
        $mail = get_mail();

        if (!$mail) {
            throw new Exception("Failed to initialize mailer");
        }

        // Recipient
        $mail->addAddress($customer_email, $customer_name);

        // Subject
        $mail->Subject = "Order #{$order_id} Cancellation Confirmation - FixAndGo";

        // HTML Body
        $mail->Body = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #2563eb; color: white; padding: 20px; text-align: center; }
                .content { background: #f9fafb; padding: 30px; }
                .alert { background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 5px; }
                .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>FixAndGo Store</h1>
                </div>
                <div class='content'>
                    <h2>Order Cancellation Notice</h2>
                    <div class='alert'>
                        <h3>Order #{$order_id} Has Been Cancelled</h3>
                        <p><strong>Reason:</strong> {$reason}</p>
                    </div>
                    <p>Dear {$customer_name},</p>
                    <p>Your order has been successfully cancelled. Any payments made will be refunded within 3-5 business days.</p>
                    <p>If you have any questions, please contact our support team.</p>
                    <div class='footer'>
                        <p>Thank you,<br>FixAndGo Team</p>
                    </div>
                </div>
            </div>
        </body>
        </html>";

        // Plain text alternative
        $mail->AltBody = "Dear {$customer_name},\n\nYour order #{$order_id} has been cancelled.\nReason: {$reason}\n\nRefund will be processed within 3-5 business days.\n\nThank you,\nFixAndGo Team";

        // Send email
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: " . $e->getMessage());
        return false;
    }
}
