<?php
require_once "../../_base.php";
include "../../_head.php";
require '../../controller/order-controller.php';
require '../../component/msg.php';
$user_id = temp('USER_ID');
$_title = "Fix & Go | Member - Order Detail";
$order_id = (int)get('id');
$order = getOrderById($order_id);
$items = getOrderItem($order_id);
$user = getUserOrder($order_id, $order->user_id);
$payment = getPayment($order_id);
$current_order_address = $order->address_id;
if (empty($items)) {
    echo "<p>No order found.</p>";
    exit;
}

// Order info (same for all rows)
$order = $items[0];

// Calculations
$total_items = count($items);
$total_quantity = array_sum(array_map(fn($i) => $i->qty, $items));
displayFlashMessage();
?>

<link rel="stylesheet" href="../../css/member-order-detail.css">
<link rel="stylesheet" href="../../css/msg.css">
<div class="container">

    <!-- Order Overview -->
    <div class="order-header">
        <div>
            <h2>Order: <?= htmlspecialchars($order->order_id) ?></h2>
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
                <div class="card-header-flex">
                    <h3>Shipping Information</h3>
                </div>

                <div id="shippingDisplay">
                    <p><span></span></p>
                    <p></p>
                    <?= htmlspecialchars($user->address_one) ?><br>

                    <?php if ($user->address_two): ?>
                        <?= htmlspecialchars($user->address_two) ?><br>
                    <?php endif; ?>

                    <?php if ($user->address_three): ?>
                        <?= htmlspecialchars($user->address_three) ?><br>
                    <?php endif; ?>

                    <?= htmlspecialchars($user->post_code) ?> <?= htmlspecialchars($user->state) ?><br>
                    <?= htmlspecialchars($user->country) ?>
                    <p>Shipping: <span>Standard Delivery</span></p>
                </div>
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
            <form method="post" id="cancelOrderForm">
                <input type="hidden" name="order_id" value="<?= $order_id ?>">
                <input type="hidden" name="user_id" value="<?= $user_id ?>">
                <div class="actions">
                    <button class="btn btn-outline">Download Invoice</button>
                    <?php if ($order->order_status === 'Processing'): ?>
                        <button type="submit" class="btn btn-danger">Cancel Order</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

</div>
<script src="../../js/confirmMsg.js"></script>
<script src="../../js/member-order-detail.js"></script>
<?php include '../../_foot.php'; ?>