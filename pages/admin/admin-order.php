<?php
require '../../_base.php';
$_title = 'Fix & Go | Admin Order List';
require '../../controller/order-controller.php';
require_once '../../component/msg.php';
include 'adminHeader.php';
$all_orders = getOrderHistoryAdmin();


// 2. Pagination Configuration
$limit = 5; // Number of items per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// 3. Calculate Total Pages
$total_records = count($all_orders);
$total_pages = ceil($total_records / $limit);

// Ensure page doesn't exceed total pages
if ($page > $total_pages && $total_pages > 0) {
    $page = $total_pages;
}

// 4. Slice the array to get only items for the current page
$offset = ($page - 1) * $limit;
$orders = array_slice($all_orders, $offset, $limit);
displayFlashMessage();
?>
<link rel="stylesheet" href="../../css/admin-order.css">
<link rel="stylesheet" href="../../css/msg.css">
<div class="admin-container">
    <div class="admin-header">
        <h1>Orders Management</h1>
        <p>View and manage all customer orders</p>
    </div>

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
                            <td data-label="Order ID"><strong><?= $order->order_id ?></strong></td>

                            <td data-label="Customer">
                                <a
                                    style="text-decoration:none; color:var(--color-text-primary);"
                                    href="<?= !empty($order->user_name) ? '../admin/admin-order-detail.php?id=' . $order->order_id : '#' ?>"
                                    <?= empty($order->user_name) ? 'onclick="return false;"' : '' ?>>
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
                                <?php $disabled = empty($order->user_name) ? 'disabled' : ''; ?>
                                <button class="btn-small btn-view" onclick="viewOrderDetails(<?= $order->order_id ?? 0 ?>)" <?= $disabled ?>>
                                    View Order
                                </button>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" style="text-align: center; padding: 3rem; color: var(--color-text-muted);">
                        <strong>No orders found</strong>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if ($total_pages > 1): ?>
        <div class="pagination-container">
            <div class="pagination-info">
                Showing <strong><?= $offset + 1 ?></strong> to <strong><?= min($offset + $limit, $total_records) ?></strong> of <strong><?= $total_records ?></strong> orders
            </div>

            <div class="pagination-controls">
                <a href="?page=<?= $page - 1 ?>" class="page-btn <?= ($page <= 1) ? 'disabled' : '' ?>">
                    &laquo; Prev
                </a>

                <?php
                $start = max(1, $page - 2);
                $end = min($total_pages, $page + 2);

                if ($start > 1) {
                    echo '<a href="?page=1" class="page-btn">1</a>';
                    if ($start > 2) echo '<span style="padding:0.5rem">...</span>';
                }

                for ($i = $start; $i <= $end; $i++): ?>
                    <a href="?page=<?= $i ?>" class="page-btn <?= ($i == $page) ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor;

                if ($end < $total_pages) {
                    if ($end < $total_pages - 1) echo '<span style="padding:0.5rem">...</span>';
                    echo '<a href="?page=' . $total_pages . '" class="page-btn">' . $total_pages . '</a>';
                }
                ?>

                <a href="?page=<?= $page + 1 ?>" class="page-btn <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                    Next &raquo;
                </a>
            </div>
        </div>
    <?php endif; ?>

</div>

<script>
    function viewOrderDetails(orderId) {
        if (orderId > 0) {
            window.location.href = '../admin/admin-order-detail.php?id=' + orderId;
        } else {
            alert('Invalid order ID');
        }
    }
</script>

<?php include 'adminFooter.php'; ?>