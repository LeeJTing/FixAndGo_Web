<?php
require_once "../../_base.php";
$_title = "Fix & Go | Order History";
include "../../_head.php";
require '../../controller/order-controller.php';

$order_id = get('id');
$items = getOrderItem($order_id);

if (empty($items)) {
    echo "<p>No order found.</p>";
    include '../../_foot.php';
    exit;
}

// Order info (same for all rows)
$order = $items[0];

// Calculations
$total_items = count($items);
$total_quantity = array_sum(array_map(fn($i) => $i->qty, $items));
?>

<link rel="stylesheet" href="../../css/member-order-history.css">
<link rel="stylesheet" href="../../css/msg.css">

<div class="order-history-container">

    <div class="order-card">

        <!-- HEADER -->
        <div class="order-header">
            <div>
                <p class="order-id">Order ID: <?= htmlspecialchars($order->order_id) ?></p>
                <p class="order-date">
                    <?= date('d M Y', strtotime($order->order_at)) ?><br>
                    <small><?= date('h:i A', strtotime($order->order_at)) ?></small>
                </p>
            </div>

            <div class="order-meta">
                <span class="order-status <?= strtolower($order->order_status) ?>">
                    <?= htmlspecialchars($order->order_status) ?>
                </span>
                <span class="order-total">
                    RM <?= number_format($order->total_price, 2) ?>
                </span>
            </div>
        </div>

        <!-- BODY -->
        <div class="order-body">

            <div class="order-item-summary">
                <?= $total_items ?> items (<?= $total_quantity ?> qty)
            </div>

            <?php foreach ($items as $item): ?>
                <div class="order-item">

                    <img src="../../<?= $item->product_image ?? 'images/no-image.png' ?>" alt="Product">

                    <div>
                        <div class="item-name">
                            <?= htmlspecialchars($item->product_name) ?>
                        </div>
                        <div class="item-qty">
                            Qty: <?= (int)$item->qty ?> × RM <?= number_format($item->unit_price, 2) ?>
                        </div>
                    </div>

                    <div class="item-price">
                        RM <?= number_format($item->subtotal, 2) ?>
                    </div>

                </div>
            <?php endforeach; ?>

        </div>

        <!-- FOOTER -->
        <div class="order-footer">
            <a href="member-order-history.php" class="btn-view">
                Back to Order History
            </a>
        </div>

    </div>

</div>

<?php include '../../_foot.php'; ?>