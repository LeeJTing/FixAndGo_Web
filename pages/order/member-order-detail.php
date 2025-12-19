<?php
require_once "../../_base.php";
$_title = "Fix & Go | Order History";
include "../../_head.php";
require '../../controller/order-controller.php';

$order_id = (int)get('id');
$order = getOrderById($order_id);
$items = getOrderItem($order_id);
$user = getUserOrder($order_id, $order->user_id);
$payment = getPayment($order_id);

if (empty($items)) {
    echo "<p>No order found.</p>";
    exit;
}

// Order info (same for all rows)
$order = $items[0];

// Calculations
$total_items = count($items);
$total_quantity = array_sum(array_map(fn($i) => $i->qty, $items));
?>

<link rel="stylesheet" href="../../css/member-order-detail.css">
<link rel="stylesheet" href="../../css/msg.css">
<div class="container">

    <!-- Order Overview -->
    <div class="order-header">
        <div>
            <h2>Order #<?= htmlspecialchars($order->order_id) ?></h2>
            <p class="text-muted">
                Placed on <?= date('d M Y, H:i', strtotime($order->order_at)) ?>
            </p>
        </div>

        <span class="status <?= strtolower($order->order_status) ?>">
            <?= htmlspecialchars($order->order_status) ?>
        </span>

    </div>

    <div class="grid">

        <!-- Left Column -->
        <div>

            <div class="card info">
                <h3>Shipping Information</h3>
                <p><span></span></p>
                <p></p>
                <?= htmlspecialchars($user->address_one) ?><br>

                <?php if ($user->address_two): ?>
                    <?= htmlspecialchars($user->address_two) ?><br>
                <?php endif; ?>

                <?php if ($user->address_three): ?>
                    <?= htmlspecialchars($user->address_three) ?><br>
                <?php endif; ?>

                <?= htmlspecialchars($user->post_code) ?>
                <?= htmlspecialchars($user->state) ?><br>
                <?= htmlspecialchars($user->country) ?>
                <p>Shipping: <span>Standard Delivery</span></p>
            </div>

            <div class="card info">
                <h3>Payment Information</h3>
                <p>Method: <span><?= $payment->payment_id ?></span></p>
                <p>Status: <span><?= htmlspecialchars($order->payment_status) ?></span></p>
                <p>Transaction ID: <span><?= $payment->payment_id ?></span></p>
            </div>

            <div class="card">
                <h3>Ordered Items (<?= $total_items ?> items)</h3>

                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Unit Price</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td>
                                    <div class="product">
                                        <img src="../../<?= htmlspecialchars($item->product_image ?? 'default.png') ?>" alt="">
                                        <?= htmlspecialchars($item->product_name) ?>
                                    </div>
                                </td>

                                <td>RM <?= number_format($item->unit_price, 2) ?></td>
                                <td><?= $item->qty ?></td>
                                <td>RM <?= number_format($item->subtotal, 2) ?></td>
                            </tr>
                        <?php endforeach; ?>

                    </tbody>
                </table>
            </div>

        </div>

        <!-- Right Column -->
        <div class="card summary">
            <h3>Order Summary</h3>

            <div class="summary-row">
                <span>Total Items</span>
                <span><?= $total_items ?></span>
            </div>

            <div class="summary-row">
                <span>Total Quantity</span>
                <span><?= $total_quantity ?></span>
            </div>

            <div class="summary-row">
                <span>Payment Status</span>
                <span><?= ucfirst($order->payment_status) ?></span>
            </div>

            <div class="summary-total">
                Total: RM <?= number_format($order->total_price, 2) ?>
            </div>

            <div class="actions">
                <button class="btn btn-primary">Track Order</button>
                <button class="btn btn-outline">Download Invoice</button>

                <?php if ($order->order_status === 'processing'): ?>
                    <button class="btn btn-danger">Cancel Order</button>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<?php include '../../_foot.php'; ?>