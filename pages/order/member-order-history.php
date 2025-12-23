<?php
$_title = "Fix & Go | Member Order History";
require "../../_base.php";
include  "../../_head.php";
require '../../controller/order-controller.php';
$user_id = temp('USER_ID');
$orders = getOrderHistoryMember($user_id);
?>
<link rel="stylesheet" href="../../css/msg.css">
<link rel="stylesheet" href="../../css/member-order-history.css">

<main class="main-container member-orders">
    <div class="page-header">
        <h1 class="page-title">My Order History</h1>
        <p class="page-subtitle">View and track all your past orders</p>
    </div>

    <div class="table-container">
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Payment Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($orders) && is_array($orders)): ?>
                    <?php foreach ($orders as $order): ?>
                        <tr class="order-row">
                            <td class="order-id">
                                <?= htmlspecialchars($order->order_id) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($order->order_date) ?><br>
                                <small><?= htmlspecialchars($order->order_time) ?></small>
                            </td>

                            <td>
                                <?= (int)$order->total_items ?> items<br>
                                <small><?= (int)$order->total_quantity ?> qty</small>
                            </td>

                            <td class="price-total">
                                RM <?= number_format($order->total_price, 2) ?>
                            </td>

                            <td>
                                <span class="status <?= strtolower($order->status) ?>">
                                    <?= htmlspecialchars($order->status) ?>
                                </span>
                            </td>
                            <td>
                                <span class="status <?= strtolower($order->payment_status) ?>">
                                    <?= htmlspecialchars($order->payment_status) ?>
                                </span>
                            </td>

                            <td class="actions">
                                <a href="member-order-detail.php?id=<?= $order->order_id ?>"
                                    class="btn-view">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="empty-state">
                            No orders found.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<?php include '../../_foot.php'; ?>