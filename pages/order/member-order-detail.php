<?php
$_title = 'Fix & Go | Member Order Detail';
require_once "../../_base.php";
include "../../_head.php";
require '../../controller/order-controller.php';
require '../../component/msg.php';

$user_id = temp('USER_ID');
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

$shipping_fee = 10.00;
$items_total = 0.0;
foreach ($items as $i) {
    $items_total += (float)$i->subtotal;
}
$pre_total_cents = (int)round((($items_total + $shipping_fee) * 100));
$final_total_cents = (int)round(((float)$order->total_price) * 100);
$discount_cents = max(0, $pre_total_cents - $final_total_cents);
$used_points = (int)round($discount_cents / 10);
$discount_rm = $discount_cents / 100;
displayFlashMessage();
?>

<link rel="stylesheet" href="../../css/member-order-detail.css">
<link rel="stylesheet" href="../../css/msg.css">
<div class="container">

    <!-- Order Overview -->
    <div class="order-header">
        <div>
            <a href="member-order-history.php" class="btn-back">
                ← Back to Order History
            </a>

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
                <p>Method: <span><?= htmlspecialchars($payment->payment_method ?? 'Cash') ?></span></p>
                <p>Status: <span><?= htmlspecialchars($order->payment_status) ?></span></p>
                <p>Transaction ID: <span><?= htmlspecialchars($payment->payment_id ?? '-') ?></span></p>
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

            <div class="summary-row">
                <span>Shipping Fee</span>
                <span>RM <?= number_format((float)$shipping_fee, 2) ?></span>
            </div>

            <?php if ($used_points > 0 && $discount_cents > 0): ?>
                <div class="summary-row">
                    <span>Loyalty Points Deducted</span>
                    <span>-RM <?= number_format((float)$discount_rm, 2) ?> (<?= (int)$used_points ?> points)</span>
                </div>
            <?php endif; ?>

            <div class="summary-total">
                Total: RM <?= number_format($order->total_price, 2) ?>
            </div>
            <form method="post" id="cancelOrderForm">
                <input type="hidden" name="order_id" value="<?= $order_id ?>">
                <input type="hidden" name="user_id" value="<?= $user_id ?>">
                <div class="actions">
                    <?php if (in_array($order->order_status, ['Processing', 'Pending']) && ($order->payment_status === 'Paid')): ?>
                        <button type="submit" class="btn btn-danger">Cancel Order</button>
                    <?php endif; ?>


                    <?php
                    $paymentStatus = strtolower((string)($order->payment_status ?? ''));
                    $paymentMethod = strtolower((string)($payment->payment_method ?? 'cash'));
                    $canPayNow = ($paymentStatus === 'pending')
                        && ($paymentMethod !== 'cash')
                        && ($paymentMethod !== 'loyalty points');
                    ?>

                    <?php if ($order->order_status === 'Pending' && $canPayNow): ?>
                        <a class="btn btn-primary" target="_blank"
                            href="<?= $rootDir ?>/pages/payment/stripe_create_session.php?order_id=<?= (int)$order_id ?>">
                            Pay Now
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

</div>
<script src="../../js/confirmMsg.js"></script>
<script src="../../js/member-order-detail.js"></script>
<?php include '../../_foot.php'; ?>