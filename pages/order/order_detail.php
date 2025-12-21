<?php
require "../../_base.php";
require "../../DAO/order-dao.php";

$user_id = temp('USER_ID');
if (!$user_id) {
    header("Location: {$rootDir}/pages/auth/login.php");
    exit;
}

$order_id = (int)($_GET['order_id'] ?? 0);
if ($order_id <= 0) die("Invalid order.");

$order = getOrderWithAddressByIdAndUserDao($order_id, $user_id);
if (!$order) die("Order not found or not yours.");

$items = getOrderItemsDao($order_id);
$payRow = getPaymentRowByOrderIdDao($order_id);

$shipping_fee = 10.00;
$items_total = 0.0;
foreach ($items as $it) {
    $items_total += (float)$it->subtotal;
}
$pre_total_cents = (int)round((($items_total + $shipping_fee) * 100));
$final_total_cents = (int)round(((float)$order->total_price) * 100);
$discount_cents = max(0, $pre_total_cents - $final_total_cents);
$used_points = (int)round($discount_cents / 10);
$discount_rm = $discount_cents / 100;

if ($payRow) {
    if ($payRow->payment_method === 'Bank Transfer') $selectedMethod = 'Online Banking';
    else if ($payRow->payment_method === 'Credit Card' || $payRow->payment_method === 'Debit Card') $selectedMethod = 'Credit/Debit Card';
    else $selectedMethod = 'Cash on Delivery';
} else {
    $selectedMethod = $_SESSION['order_payment_method'][$order_id] ?? 'Cash on Delivery';
}

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt - Order #<?= (int)$order_id ?></title>
    <link rel="stylesheet" href="../../css/order.css">
</head>

<body>
    <div class="sheet">
        <div class="paper">

            <div class="top">
                <div>
                    <div class="brand">Fix & GO</div>
                    <div class="sub">Receipt / Invoice</div>
                </div>

                <div class="meta">
                    <div><b>Order #<?= (int)$order_id ?></b></div>
                    <div>Date: <?= htmlspecialchars(date("Y-m-d", strtotime($order->order_at ?? 'now'))) ?></div>
                    <?php
                    $ps = strtolower($order->payment_status ?? 'pending');
                    $badgeClass = ($ps === 'paid') ? 'paid' : (($ps === 'failed') ? 'failed' : 'pending');
                    ?>
                    <div><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($order->payment_status) ?></span></div>
                </div>
            </div>

            <div class="section">
                <h3>Customer & Delivery</h3>

                <div class="two-col">
                    <div class="box">
                        <div class="row">
                            <div class="k">Customer ID</div>
                            <div class="v"><?= htmlspecialchars($order->user_id) ?></div>
                        </div>
                        <div class="row">
                            <div class="k">Order Status</div>
                            <div class="v"><?= htmlspecialchars($order->status) ?></div>
                        </div>
                        <div class="row">
                            <div class="k">Payment Method</div>
                            <div class="v"><?= htmlspecialchars($selectedMethod) ?></div>
                        </div>
                    </div>

                    <div class="box">
                        <div class="v" style="margin-bottom:6px;"><?= htmlspecialchars($order->address_name ?? 'Home') ?></div>
                        <div style="font-size:14px; line-height:1.55;">
                            <?= htmlspecialchars($order->address_one) ?><br>
                            <?= !empty($order->address_two) ? htmlspecialchars($order->address_two) . "<br>" : "" ?>
                            <?= !empty($order->address_three) ? htmlspecialchars($order->address_three) . "<br>" : "" ?>
                            <?= htmlspecialchars($order->post_code) ?> <?= htmlspecialchars($order->state) ?>, <?= htmlspecialchars($order->country) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section">
                <h3>Items</h3>

                <div class="box" style="padding:0;">
                    <table>
                        <thead>
                            <tr>
                                <th style="padding-left:12px;">Item</th>
                                <th class="num">Qty</th>
                                <th class="num">Unit (RM)</th>
                                <th class="num" style="padding-right:12px;">Amount (RM)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $it): ?>
                                <tr>
                                    <td style="padding-left:12px;">
                                        <div style="font-weight:700;"><?= htmlspecialchars($it->product_name) ?></div>
                                    </td>
                                    <td class="num"><?= (int)$it->qty ?></td>
                                    <td class="num"><?= number_format((float)$it->unit_price, 2) ?></td>
                                    <td class="num" style="padding-right:12px;"><?= number_format((float)$it->subtotal, 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="totals">
                    <div class="box">
                        <div class="row">
                            <div class="k">Subtotal</div>
                            <div class="v">RM <?= number_format((float)$items_total, 2) ?></div>
                        </div>
                        <div class="row">
                            <div class="k">Shipping Fee</div>
                            <div class="v">RM <?= number_format((float)$shipping_fee, 2) ?></div>
                        </div>
                        <?php if ($used_points > 0 && $discount_cents > 0): ?>
                            <div class="row">
                                <div class="k">Loyalty Points Deducted</div>
                                <div class="v">-RM <?= number_format((float)$discount_rm, 2) ?> (<?= (int)$used_points ?> points)</div>
                            </div>
                        <?php endif; ?>
                        <div class="row">
                            <div class="k">Total</div>
                            <div class="v">RM <?= number_format((float)$order->total_price, 2) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="note">
                <?php if (strtolower($order->payment_status) !== 'paid'): ?>
                    <?php if ($selectedMethod === 'Cash on Delivery'): ?>
                        Payment will be collected upon delivery.
                    <?php elseif ($selectedMethod === 'Online Banking'): ?>
                        Online Banking will redirect to Stripe FPX to complete payment.
                    <?php else: ?>
                        Credit/Debit payment will redirect to Stripe Checkout to complete payment.
                    <?php endif; ?>
                <?php else: ?>
                    Payment received. Thank you for your purchase.
                <?php endif; ?>
            </div>

            <div class="actions">
                <a class="btn" href="<?= $rootDir ?>/pages/order/order_list.php">Back to Orders</a>

                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <?php if (strtolower($order->payment_status) !== 'paid' && $selectedMethod !== 'Cash on Delivery'): ?>
                        <a class="btn primary" target="_blank" href="<?= $rootDir ?>/pages/payment/stripe_create_session.php?order_id=<?= (int)$order_id ?>">Pay Now</a>
                    <?php endif; ?>

                    <button class="btn" onclick="window.print()">Print / Save PDF</button>
                </div>
            </div>

        </div>
    </div>
</body>

</html>