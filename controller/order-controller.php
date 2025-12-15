<?php
require_once  __DIR__ . '/../_base.php';
require __DIR__ . '/../DAO/order-dao.php';

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
            deleteOrder($orderId);
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'text' => "Order #{$orderId} has been cancelled and deleted successfully."
            ];

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
        $date = post('estimated_delivery');

        $order = getOrderById($orderId);

        // $date comes from POST input, e.g., '2025-10-20'
        $newDate = new DateTime($date);

        // Assuming $order->order_at comes from DB as timestamp
        $orderDate = new DateTime($order->order_at);

        // Format to full datetime string for MySQL TIMESTAMP
        $updateDate = $newDate->format('Y-m-d H:i:s');

        // Compare dates ignoring time, if needed
        if ($newDate > $orderDate) {
            updateOrderDate($updateDate, $orderId); // Save in DB
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'text' => "Estimated delivery date updated to " . $newDate->format('M d, Y') . "."
            ];
        } else {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'text' => "Failed to update date. It must be after the order date."
            ];
        }


        // Redirect back to the orders list page
        header("Location: ../../../pages/admin/admin-order-detail.php?id={$orderId}");
        exit;
    }
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

function getMemberAddress($user_id)
{
    return getMemberAddressDao($user_id);
}
