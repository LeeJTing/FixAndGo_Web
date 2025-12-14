<?php
require '../../_base.php';
require '../../controller/order-controller.php';
include 'adminHeader.php';
$orders = getOrderHistoryAdmin();
?>
<link rel="stylesheet" href="../../css/admin-order.css">

<body>

    <div class="admin-container">
        <div class="admin-header">
            <h1>Orders Management</h1>
            <p>View and manage all customer orders</p>
        </div>

        <!-- Stats Bar -->
        <div class="stats-bar">
            <div class="stat-card">
                <h3><?= is_array($orders) ? count($orders) : 0 ?></h3>
                <p>Total Orders</p>
            </div>
            <div class="stat-card">
                <h3>
                    <?php
                    if (is_array($orders) && !empty($orders)) {
                        $totalRevenue = 0;
                        foreach ($orders as $order) {
                            if (is_object($order) && isset($order->total_price)) {
                                $totalRevenue += $order->total_price;
                            }
                        }
                        echo $totalRevenue;
                    } else {
                        echo '0';
                    }
                    ?>
                </h3>
                <p>Total Revenue (RM)</p>
            </div>
            <div class="stat-card">
                <h3>
                    <?php
                    if (is_array($orders) && !empty($orders)) {
                        $totalItems = 0;
                        foreach ($orders as $order) {
                            if (is_object($order) && isset($order->total_items)) {
                                $totalItems += $order->total_items;
                            }
                        }
                        echo $totalItems;
                    } else {
                        echo '0';
                    }
                    ?>
                </h3>
                <p>Total Items</p>
            </div>
        </div>

        <!-- Orders Table -->
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Items / Qty</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Points</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (is_array($orders) && !empty($orders)): ?>
                    <?php foreach ($orders as $order): ?>
                        <?php if (is_object($order) && isset($order->order_id)): ?>
                            <tr>
                                <td data-label="Order ID"><strong>#<?= $order->order_id ?></strong></td>

                                <td data-label="Customer">
                                    <a href="../admin/admin-order-detail.php?id=<?= $order->order_id ?>">
                                        <div><strong><?= htmlspecialchars($order->user_name ?? 'Unknown') ?></strong></div>
                                    </a>
                                </td>

                                <td data-label="Date">
                                    <?= isset($order->order_at) ? date('d M Y', strtotime($order->order_at)) : 'N/A' ?><br>
                                    <small style="color:#94a3b8;">
                                        <?= isset($order->order_at) ? date('h:i A', strtotime($order->order_at)) : '' ?>
                                    </small>
                                </td>

                                <td data-label="Items / Qty">
                                    <strong><?= $order->total_items ?? 0 ?></strong> item<?= ($order->total_items ?? 0) > 1 ? 's' : '' ?><br>
                                    <small style="color:#94a3b8;"><?= $order->total_quantity ?? 0 ?> total qty</small>
                                </td>

                                <td data-label="Total" class="total-price">
                                    RM <?= number_format($order->total_price ?? 0, 2) ?>
                                </td>

                                <td data-label="Payment">
                                    <span class="status <?= strtolower($order->payment_status ?? 'unknown') ?>">
                                        <?= ucfirst($order->payment_status ?? 'Unknown') ?>
                                    </span>
                                </td>

                                <td data-label="Status">
                                    <span class="status <?= strtolower($order->status ?? 'unknown') ?>">
                                        <?= ucfirst($order->status ?? 'Unknown') ?>
                                    </span>
                                </td>

                                <td data-label="Points">
                                    <?php if (isset($order->utilize_point) && $order->utilize_point): ?>
                                        <span style="color: #10b981; font-weight: 600;">True</span>
                                    <?php else: ?>
                                        <span style="color: #94a3b8;">-</span>
                                    <?php endif; ?>
                                </td>

                                <td data-label="Action">
                                    <button class="btn-small btn-view" onclick="viewOrderDetails(<?= $order->order_id ?? 0 ?>)">
                                        View Details
                                    </button>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 3rem; color: var(--color-text-muted);">
                            <strong>No orders found or data format error</strong>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        function viewOrderDetails(orderId) {
            if (orderId > 0) {
                window.location.href = 'order-details.php?id=' + orderId;
            } else {
                alert('Invalid order ID');
            }
        }
    </script>

</body>
<?php include 'adminFooter.php'; ?>