<?php
require '../../_base.php';
require '../../controller/order-controller.php';
require_once '../../component/msg.php';
include 'adminHeader.php';
$id = get('id') ?? null;
$order = getOrderDetailsAdmin($id);
?>
<link rel="stylesheet" href="../../css/msg.css">
<link rel="stylesheet" href="../../css/admin-order-detail.css">

<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <div>
                    <span class="order-id">Order ID: <?= $order->order_id ?></span>
                    <span class="status status-<?= strtolower($order->status) ?>"><?= ucfirst($order->status) ?></span>
                </div>
                <div class="actions">
                    <button class="btn btn-secondary" onclick="window.print()">Invoice</button>
                    <button class="btn btn-primary">Track order</button>
                </div>
            </div>

            <h2 class="section-title">Items</h2>
            <div class="items">
                <?php if (!empty($orderItems)): ?>
                    <?php foreach ($orderItems as $item): ?>
                        <div class="item">
                            <img src="../../<?= htmlspecialchars($item->product_image ?? 'images/placeholder.jpg') ?>"
                                alt="<?= htmlspecialchars($item->product_name) ?>"
                                class="item-img">
                            <div class="item-details">
                                <div class="item-name"><?= htmlspecialchars($item->product_name) ?></div>
                                <div class="item-spec"><?= htmlspecialchars($item->spec ?? '') ?></div>
                            </div>
                            <div>
                                <div class="item-price">RM <?= number_format($item->unit_price, 2) ?></div>
                                <div style="text-align:right; color:var(--color-text-muted); font-size:14px;">Qty: <?= $item->qty ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-items">
                        <p>No items found for this order</p>
                    </div>
                <?php endif; ?>
            </div>

            <h2 class="section-title">Delivery</h2>
            <div class="delivery">
                <div class="address">
                    <div class="label">Address</div>
                    <div class="value">
                        <?php if (!empty($order->address_one)): ?>
                            <?= htmlspecialchars($order->address_one) ?><br>
                            <?php if (!empty($order->address_two)): ?>
                                <?= htmlspecialchars($order->address_two) ?><br>
                            <?php endif; ?>
                            <?php if (!empty($order->address_three)): ?>
                                <?= htmlspecialchars($order->address_three) ?><br>
                            <?php endif; ?>
                            <?= htmlspecialchars($order->state) ?>, <?= htmlspecialchars($order->post_code) ?><br>
                            <?= htmlspecialchars($order->country) ?><br>
                            <?php if (!empty($order->contact_num)): ?>
                                <?= htmlspecialchars($order->contact_num) ?>
                            <?php endif; ?>
                        <?php else: ?>
                            No address provided
                        <?php endif; ?>
                    </div>
                </div>
                <div class="method">
                    <div class="label">Delivery method</div>
                    <div class="value">
                        <?php
                        // You might need to add delivery method to your query
                        echo htmlspecialchars($order->delivery_method ?? 'Standard');
                        ?>
                    </div>
                    <div class="estimated">
                        <?php
                        // Calculate estimated delivery based on order date
                        $orderDate = new DateTime($order->order_at);
                        $estimatedDate = clone $orderDate;
                        $estimatedDate->modify('+5 days'); // Adjust as needed
                        echo 'Estimated delivery: ' . $estimatedDate->format('M d, Y');
                        ?>
                    </div>
                </div>
            </div>

            <h2 class="section-title">Order Summary</h2>
            <div class="summary">
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>RM <?= number_format($order->total_price, 2) ?></span>
                </div>

                <?php if ($order->utilize_point): ?>
                    <div class="summary-row">
                        <span>Points Discount</span>
                        <span class="discount">-RM <?= number_format($order->points_discount ?? 0, 2) ?></span>
                    </div>
                <?php endif; ?>

                <div class="summary-row">
                    <span>Delivery</span>
                    <span>RM <?= number_format($order->delivery_fee ?? 0, 2) ?></span>
                </div>

                <div class="summary-row">
                    <span>Tax</span>
                    <span>RM <?= number_format($order->tax_amount ?? 0, 2) ?></span>
                </div>

                <div class="summary-row total">
                    <span>Total</span>
                    <span>RM <?= number_format($order->total_price, 2) ?></span>
                </div>
            </div>

            <!-- Payment Information -->
            <h2 class="section-title">Payment Information</h2>
            <div class="payment-info">
                <div class="payment-row">
                    <span>Payment Method:</span>
                    <span><?= htmlspecialchars($order->payment_method ?? 'Not specified') ?></span>
                </div>
                <div class="payment-row">
                    <span>Payment Status:</span>
                    <span class="payment-status status-<?= strtolower($order->payment_status) ?>">
                        <?= ucfirst($order->payment_status) ?>
                    </span>
                </div>
                <?php if ($order->paid_at): ?>
                    <div class="payment-row">
                        <span>Paid At:</span>
                        <span><?= date('M d, Y h:i A', strtotime($order->paid_at)) ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

<?php include 'adminFooter.php'; ?>