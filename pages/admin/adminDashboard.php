<?php 
/**
 * adminDashboard.php
 * location: pages/admin/adminDashboard.php
 * The main page of the administrator dashboard
 */

require '../../_base.php';
require_once '../../controller/DashboardController.php';

// Obtain the information of the currently logged-in user
$currentUser = getCurrentUser();
if (!$currentUser) {
    redirect($pathPrefix . '/index.php');
}

// Check if it is an administrator
// if ($currentUser->user_role !== 'Admin') {
//     redirect($pathPrefix . '/index.php');
// }

// Obtain the user's avatar
$userProfilePic = getUserProfilePicture($currentUser->user_id);

// Initialize the controller
$dashboardController = new DashboardController($_db);

// Obtain dashboard data
$stats = $dashboardController->getDashboardStats();
$recentOrders = $dashboardController->getRecentOrders(10);

include 'adminHeader.php';
?>
<link rel="stylesheet" href="../../css/adminDashboard.css">

<div class="admin-topbar">
    <input class="search-box" type="text" id="searchBox" placeholder="Search orders, products, customers...">

    <div class="profile-wrapper">
        <div class="profile-area" id="adminProfileIcon">
            <img src="<?= htmlspecialchars($userProfilePic) ?>" 
                 class="avatar" 
                 alt="<?= htmlspecialchars($currentUser->user_name) ?>"
                 width="36" height="36" 
                 style="width: 36px; height: 36px; object-fit: cover; border-radius: 50%;"
                 onerror="this.src='<?= $pathPrefix ?>/images/profile/default_profile_picture.webp'">
            <span class="username"><?= htmlspecialchars($currentUser->user_name) ?></span>
            <i class="fa-solid fa-chevron-down dropdown-arrow"></i>
        </div>

        <div class="admin-profile-dropdown" id="adminProfileDropdown">
            <a href="<?= $pathPrefix ?>/pages/admin/adminProfile.php">
                <i class="fa-regular fa-user"></i> My Profile
            </a>
            <hr>
            <a href="javascript:void(0)" class="logout-btn">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
            </a>
        </div>
    </div>
</div>

<!-- Page Content Starts Here -->
<div class="admin-content">

    <h1 class="page-title">Dashboard Overview</h1>

    <div class="toggle-group chart-type">
        <button class="toggle active" data-type="sales">Sales</button>
        <button class="toggle" data-type="orders">Orders</button>
        <button class="toggle" data-type="status">Status</button>
    </div>

    <!-- Statistical card -->
    <div class="cards-grid">
        <div class="card">
            <div class="card-icon">
                <i class="fa-solid fa-dollar-sign"></i>
            </div>
            <div class="card-content">
                <h3>Total Revenue</h3>
                <p class="value">RM <?= number_format($stats['revenue'], 2) ?></p>
                <span class="<?= $stats['revenueChange'] >= 0 ? 'up' : 'down' ?>">
                    <?= $stats['revenueChange'] >= 0 ? '▲' : '▼' ?> 
                    <?= abs($stats['revenueChange']) ?>% vs last month
                </span>
            </div>
        </div>

        <div class="card">
            <div class="card-icon">
                <i class="fa-solid fa-shopping-cart"></i>
            </div>
            <div class="card-content">
                <h3>New Orders Today</h3>
                <p class="value"><?= $stats['orders'] ?></p>
                <span class="<?= $stats['ordersChange'] >= 0 ? 'up' : 'down' ?>">
                    <?= $stats['ordersChange'] >= 0 ? '▲' : '▼' ?> 
                    <?= abs($stats['ordersChange']) ?>% vs yesterday
                </span>
            </div>
        </div>

        <div class="card">
            <div class="card-icon">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="card-content">
                <h3>Total Customers</h3>
                <p class="value"><?= number_format($stats['customers']) ?></p>
                <span class="<?= $stats['customersChange'] >= 0 ? 'up' : 'down' ?>">
                    <?= $stats['customersChange'] >= 0 ? '▲' : '▼' ?> 
                    <?= abs($stats['customersChange']) ?>% vs last month
                </span>
            </div>
        </div>

        <div class="card">
            <div class="card-icon">
                <i class="fa-solid fa-chart-line"></i>
            </div>
            <div class="card-content">
                <h3>Average Order Value</h3>
                <p class="value">RM <?= number_format($stats['avgOrder'], 2) ?></p>
                <span class="<?= $stats['avgOrderChange'] >= 0 ? 'up' : 'down' ?>">
                    <?= $stats['avgOrderChange'] >= 0 ? '▲' : '▼' ?> 
                    <?= abs($stats['avgOrderChange']) ?>% vs last month
                </span>
            </div>
        </div>
    </div>

    <div class="dashboard-flex">
        
        <!-- Left Section - Chart -->
        <div class="panel chart-panel">
            <div class="panel-header">
                <div>
                    <h3>Sales Performance</h3>
                    <p class="subtitle">A summary of your sales over the selected period.</p>
                </div>

                <div class="toggle-group">
                    <button class="toggle active" data-period="month">Month</button>
                    <button class="toggle" data-period="week">Week</button>
                    <button class="toggle" data-period="day">Day</button>
                </div>
            </div>

            <div class="chart-wrapper">
                <canvas id="salesChart"></canvas>
            </div>

        </div>

        <!-- Right Section - Quick Actions -->
        <div class="quick-actions">
            <h3>Quick Actions</h3>
            <button class="qa-btn primary"
                onclick="window.location.href='admin-product.php'">
                <i class="fa-solid fa-plus"></i> Add New Product
            </button>

            <button class="qa-btn"
                onclick="window.location.href='adminCustomer.php'">
                <i class="fa-solid fa-user-gear"></i> Manage Users
            </button>

            <button class="qa-btn" 
                onclick="window.location.href='admin-manage-review.php'">
                <i class="fa-solid fa-star"></i> Product Reviews
            </button>
        </div>

    </div>

    <!-- Recent Orders Table -->
    <div class="panel">
        <div class="panel-header-flex">
            <h3>Recent Orders</h3>
            <button class="btn-view-all" onclick="window.location.href='<?= $pathPrefix ?>admin-order.php'">
                View All Orders <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>
        
        <div class="table-responsive">
            <table class="table" id="ordersTable">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer Name</th>
                        <th>Order Date</th>
                        <th>Delivery Date</th>
                        <th>Total</th>
                        <th>Payment Status</th>
                        <th>Order Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentOrders)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: #94a3b8;">
                                <i class="fa-solid fa-inbox" style="font-size: 48px; margin-bottom: 16px; display: block;"></i>
                                <p style="font-size: 16px;">No orders found in the database.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentOrders as $order): ?>
                            <tr class="order-row">
                                <td><strong>#<?= htmlspecialchars($order->order_id) ?></strong></td>
                                <td><?= htmlspecialchars($order->user_name ?? 'Unknown') ?></td>
                                <td><?= date('M d, Y', strtotime($order->order_at)) ?></td>
                                <td><?= $order->deliver_at ? date('M d, Y', strtotime($order->deliver_at)) : '<span style="color: #94a3b8;">Not set</span>' ?></td>
                                <td><strong>RM <?= number_format($order->total_price, 2) ?></strong></td>
                                <td>
                                    <span class="status payment-<?= strtolower($order->payment_status) ?>">
                                        <?= htmlspecialchars($order->payment_status) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status order-<?= strtolower($order->status) ?>">
                                        <?= htmlspecialchars($order->status) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div> <!-- END content -->

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
let salesChart = null;
let currentType = 'sales';
let currentPeriod = 'month';

document.addEventListener('DOMContentLoaded', function () {

    // 初始化
    initSalesChart();

    /* ===========================
       Chart TYPE toggle
       =========================== */
    document.querySelectorAll('.chart-type .toggle').forEach(btn => {
        btn.addEventListener('click', function () {

            document
                .querySelectorAll('.chart-type .toggle')
                .forEach(b => b.classList.remove('active'));

            this.classList.add('active');
            currentType = this.dataset.type;

            initSalesChart();
        });
    });

    /* ===========================
       PERIOD toggle
       =========================== */
    document.querySelectorAll('.panel-header .toggle-group .toggle').forEach(btn => {
        btn.addEventListener('click', function () {

            document
                .querySelectorAll('.panel-header .toggle-group .toggle')
                .forEach(b => b.classList.remove('active'));

            this.classList.add('active');
            currentPeriod = this.dataset.period;

            initSalesChart();
        });
    });

});

/* ===========================
   Chart Loader
   =========================== */
function initSalesChart() {
    const pathPrefix = '<?= $pathPrefix ?>';

    const title = document.querySelector('.panel-header h3');

    if (currentType === 'sales') {
        title.textContent = 'Sales Performance';
    } else if (currentType === 'orders') {
        title.textContent = 'Order Volume';
    } else if (currentType === 'status') {
        title.textContent = 'Order Status Distribution';
    }

    if (currentType === 'status') {
        document.querySelectorAll('.panel-header .toggle')
            .forEach(b => b.style.display = 'none');
    } else {
        document.querySelectorAll('.panel-header .toggle')
            .forEach(b => b.style.display = 'inline-flex');
    }

    fetch(`${pathPrefix}/pages/admin/getDashboardData.php?period=${currentPeriod}&type=${currentType}`)
        .then(res => res.json())
        .then(data => {

            const ctx = document.getElementById('salesChart').getContext('2d');
            if (salesChart) salesChart.destroy();

            const chartType = currentType === 'status' ? 'pie' : 'bar';

            const colors = chartType === 'pie'
                ? ['#3b82f6', '#22c55e', '#f97316', '#ef4444']
                : 'rgba(59, 130, 246, 0.8)';

            salesChart = new Chart(ctx, {
                type: chartType,
                data: {
                    labels: data.labels,
                    datasets: [{
                        label:
                            currentType === 'sales' ? 'Sales (RM)' :
                            currentType === 'orders' ? 'Orders' :
                            'Order Status',
                        data: data.values,
                        backgroundColor: colors,
                        borderRadius: chartType === 'bar' ? 8 : 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,

                    // 🔥 关键：Orders 横向
                    indexAxis: currentType === 'orders' ? 'y' : 'x',

                    plugins: {
                        legend: {
                            display: chartType === 'pie'
                        }
                    },

                    scales: chartType === 'pie' ? {} : {
                        x: {
                            beginAtZero: true
                        },
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

        })
        .catch(err => console.error(err));
}
</script>
<?php include 'adminFooter.php'; ?>