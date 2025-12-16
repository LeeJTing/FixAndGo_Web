<?php
require '../../_base.php';
require '../../controller/order-controller.php';
require_once '../../component/msg.php';
include 'adminHeader.php';

$id = get('id') ?? null;

$orderItems = getAllProductByOrderId($id);
$address = getOrderWithSelectedAddress($id);
$order = getOrderById($id);
$payment = getPaymentRecord($id);

// --- 1. LOGIC: CHECK IF ORDER IS LOCKED ---
$current_status = strtolower($order->status ?? '');
// Define statuses that prevent editing
$locked_statuses = ['shipping', 'delivered', 'completed', 'cancelled'];
// Boolean: True if order is editable
$is_editable = !in_array($current_status, $locked_statuses);
displayFlashMessage();
?>
<link rel="stylesheet" href="../../css/msg.css">
<link rel="stylesheet" href="../../css/admin-order-detail.css">

<body>
    <div class="container">

        <div class="action-bar">
            <a href="admin-order.php" class="btn btn-back">
                &larr; Back to List
            </a>
        </div>

        <div class="card">
            <div class="header">
                <div id="header-row">
                    <div class="header-left">
                        <span class="order-id">Order ID: <?= $order->order_id ?>&nbsp;&nbsp;</span>
                    </div>
                    <?php
                    // Define all possible statuses for the dropdown
                    $all_statuses = ['Pending', 'Processing', 'Shipping', 'Delivered', 'Cancelled'];
                    ?>

                    <?php if ($is_editable): ?>
                        <form action="../../controller/order-controller.php" method="POST" style="display:inline-block;">
                            <input type="hidden" name="action" value="updateStatus">
                            <input type="hidden" name="order_id" value="<?= $order->order_id ?>">

                            <select name="status" class="status-select status-<?= strtolower($order->status) ?>" onchange="confirmStatusChange(this)">
                                <?php foreach ($all_statuses as $s): ?>
                                    <option value="<?= $s ?>" <?= $order->status == $s ? 'selected' : '' ?>>
                                        <?= ucfirst($s) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>

                    <?php else: ?>
                        <span class="status status-<?= strtolower($order->status) ?>">
                            <?= ucfirst($order->status) ?>
                            <i class="fas fa-lock" style="font-size: 12px; margin-left: 5px; opacity: 0.7;"></i>
                        </span>
                    <?php endif; ?>
                    <div class="estimated">
                        <?php
                        $orderDate = new DateTime($order->order_at);
                        echo "Order date: " . $orderDate->format('M d, Y');
                        ?>
                    </div>
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
                    <div class="address-header">
                        <div class="label">Address Home</div>

                        <?php if ($is_editable): ?>
                            <button type="button"
                                class="btn-text-edit"
                                data-user-id="<?= $order->user_id ?>"
                                data-order-id="<?= $order->order_id ?>">
                                Edit <i class="fas fa-pen"></i>
                            </button>
                        <?php endif; ?>

                    </div>

                    <div class="value">
                        <?php if (!empty($address->address_one)): ?>
                            <?= htmlspecialchars($address->address_one) ?><br>

                            <?php if ($address->address_two): ?>
                                <?= htmlspecialchars($address->address_two) ?><br>
                            <?php endif; ?>

                            <?php if ($address->address_three): ?>
                                <?= htmlspecialchars($address->address_three) ?><br>
                            <?php endif; ?>

                            <?= htmlspecialchars($address->post_code) ?>
                            <?= htmlspecialchars($address->state) ?><br>
                            <?= htmlspecialchars($address->country) ?>
                        <?php else: ?>
                            <span style="color: #999;">No address provided</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="method">
                    <div class="label">Delivery Date</div>
                    <div class="estimated">
                        <?php
                        if ($order->deliver_at) {
                            $deliverDate = new DateTime($order->deliver_at);
                            echo "Delivery date: " . $deliverDate->format('M d, Y');
                        } else {
                            echo "Delivery date: Not scheduled";
                        }
                        ?>
                    </div>

                    <?php if ($is_editable): ?>
                        <form action="../../controller/order-controller.php" method="POST">
                            <input type="hidden" name="action" value="updateDate">
                            <input type="hidden" name="order_id" value="<?= $order->order_id ?>">
                            <input type="date" name="estimated_delivery"
                                value="<?= $order->deliver_at ? (new DateTime($order->deliver_at))->format('Y-m-d') : '' ?>">
                            <button type="submit" class="btn-save">Update</button>
                        </form>
                    <?php endif; ?>


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
                        <span class="discount">-RM <?= number_format($order->total_price ?? 0, 2) ?></span>
                    </div>
                <?php endif; ?>

                <div class="summary-row total">
                    <span>Total</span>
                    <span>RM <?= number_format($order->total_price, 2) ?></span>
                </div>
            </div>

            <h2 class="section-title">Payment Information</h2>
            <div class="payment-info">
                <div class="payment-row">
                    <span>Payment Method:</span>
                    <span><?= htmlspecialchars($payment->payment_method ?? 'Not specified') ?></span>
                </div>
                <div class="payment-row">
                    <span>Payment Status:</span>
                    <span id="text-black" class="payment-status status-<?= strtolower($order->payment_status) ?>">
                        <?= ucfirst($order->payment_status) ?>
                    </span>
                </div>
                <?php if ($order->utilize_point): ?>
                    <div class="payment-row">
                        <span>Points Used:</span>
                        <span><?= round($order->total_price ?? 0) ?> points</span>
                    </div>
                <?php endif; ?>
                <?php if ($payment->paid_at): ?>
                    <div class="payment-row">
                        <span>Paid At:</span>
                        <span><?= date('M d, Y h:i A', strtotime($payment->paid_at)) ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div id="address-form-overlay" style="display: none;">
        <div class="centered-form-box">
            <h3>Select Delivery Address</h3>

            <form action="../../controller/order-controller.php" method="POST">
                <input type="hidden" name="action" value="updateAddress">
                <input type="hidden" name="order_id" id="form_order_id" value="<?= $order->order_id ?>">

                <div id="address-list-container">
                    <p class="loading-text">Loading addresses...</p>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="hideAddressForm()">Cancel</button>
                    <button type="submit" class="btn-save">Update Address</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function confirmStatusChange(selectElement) {
            const newStatus = selectElement.value;

            showConfirm(
                'Are you sure you want to change status to ' + newStatus + "?",
                function(result) {
                    if (result) {
                        selectElement.form.submit();
                    }
                }
            );
        }
    </script>
    <script src="../../js/confirmMsg.js"></script>
    <script src="../../js/admin-order-detail.js"></script>
</body>

<?php include 'adminFooter.php'; ?>