<?php
require '../../_base.php';
require '../../_head.php';
// Sample data (replace with real query later)
$orders = [
    (object)[
        'order_id' => 1001,
        'customer_name' => 'Ahmad Bin Ali',
        'email' => 'ahmad@gmail.com',
        'total_amount' => 259.90,
        'status' => 'pending',
        'order_date' => '2025-04-10 14:30:22',
        'items' => 3
    ],
    (object)[
        'order_id' => 1002,
        'customer_name' => 'Siti Nurhaliza',
        'email' => 'siti@live.com',
        'total_amount' => 899.00,
        'status' => 'processing',
        'order_date' => '2025-04-09 09:15:11',
        'items' => 1
    ],
    (object)[
        'order_id' => 1003,
        'customer_name' => 'Rajesh Kumar',
        'email' => 'rajesh@company.com',
        'total_amount' => 159.50,
        'status' => 'shipped',
        'order_date' => '2025-04-08 18:22:05',
        'items' => 5
    ],
    (object)[
        'order_id' => 1004,
        'customer_name' => 'Lina Tan',
        'email' => 'lina88@yahoo.com',
        'total_amount' => 399.00,
        'status' => 'delivered',
        'order_date' => '2025-04-07 11:45:33',
        'items' => 2
    ],
    (object)[
        'order_id' => 1005,
        'customer_name' => 'Muhammad Zulkifli',
        'email' => 'zul@gmail.com',
        'total_amount' => 79.90,
        'status' => 'cancelled',
        'order_date' => '2025-04-06 16:10:20',
        'items' => 1
    ],
];
?>
<link rel="stylesheet" href="../../css/order-page.css">

<body>

    <div class="admin-container">
        <div class="admin-header">
            <h1>Orders Management</h1>
            <p>View and manage all customer orders</p>
        </div>

        <!-- Stats -->
        <div class="stats-bar">
            <div class="stat-card">
                <h3><?= count($orders) ?></h3>
                <p>Total Orders</p>
            </div>
            <div class="stat-card">
                <h3>RM <?= number_format(array_sum(array_column($orders, 'total_amount')), 2) ?></h3>
                <p>Total Revenue</p>
            </div>
            <div class="stat-card">
                <h3><?= count(array_filter($orders, fn($o) => $o->status === 'pending')) ?></h3>
                <p>Pending</p>
            </div>
            <div class="stat-card">
                <h3><?= count(array_filter($orders, fn($o) => $o->status === 'delivered')) ?></h3>
                <p>Delivered</p>
            </div>
        </div>

        <!-- Orders Table -->
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td data-label="Order ID"><strong>#<?= $order->order_id ?></strong></td>
                        <td data-label="Customer">
                            <div><strong><?= htmlspecialchars($order->customer_name) ?></strong></div>
                            <small style="color:#94a3b8;"><?= htmlspecialchars($order->email) ?></small>
                        </td>
                        <td data-label="Date"><?= date('d M Y', strtotime($order->order_date)) ?><br>
                            <small style="color:#94a3b8;"><?= date('h:i A', strtotime($order->order_date)) ?></small>
                        </td>
                        <td data-label="Items"><?= $order->items ?> item<?= $order->items > 1 ? 's' : '' ?></td>
                        <td data-label="Total" class="total-price">RM <?= number_format($order->total_amount, 2) ?></td>
                        <td data-label="Status"><span class="status <?= $order->status ?>"><?= ucfirst($order->status) ?></span></td>
                        <td data-label="Action">
                            <button class="btn-small btn-view">
                                View Details
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
<?php include '../../_foot.php'; ?>