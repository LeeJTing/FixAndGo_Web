<?php 
/**
 * adminDashboard.php
 * 位置: pages/admin/adminDashboard.php
 * 管理员仪表板主页面
 */

require '../../_base.php';
require_once '../../controller/DashboardController.php';

// 获取当前登录用户信息
$currentUser = getCurrentUser();
if (!$currentUser) {
    redirect($pathPrefix . '/index.php');
}

// 检查是否是管理员
if ($currentUser->user_role !== 'Admin') {
    redirect($pathPrefix . '/index.php');
}

// 获取用户头像
$userProfilePic = getUserProfilePicture($currentUser->user_id);

// 初始化控制器
$dashboardController = new DashboardController($_db);

// 获取仪表板数据
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
            <a href="<?= $pathPrefix ?>/pages/member/profile.php">
                <i class="fa-regular fa-user"></i> My Profile
            </a>
            <hr>
            <a href="javascript:void(0)" class="logout-link" onclick="handleAdminLogout()">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
            </a>
        </div>
    </div>
</div>

<!-- Page Content Starts Here -->
<div class="admin-content">

    <h1 class="page-title">Dashboard Overview</h1>

    <!-- 统计卡片 -->
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
            <button class="qa-btn primary" onclick="window.location.href='<?= $pathPrefix ?>/pages/admin/products.php'">
                <i class="fa-solid fa-plus"></i> Add New Product
            </button>
            <button class="qa-btn" onclick="window.location.href='<?= $pathPrefix ?>/pages/admin/inventory.php'">
                <i class="fa-solid fa-boxes"></i> Manage Inventory
            </button>
            <button class="qa-btn" onclick="window.location.href='<?= $pathPrefix ?>/pages/admin/tickets.php'">
                <i class="fa-solid fa-ticket"></i> Customer Tickets
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
// Admin Profile Dropdown Toggle
document.addEventListener('DOMContentLoaded', function() {
    const profileIcon = document.getElementById('adminProfileIcon');
    const profileDropdown = document.getElementById('adminProfileDropdown');
    const dropdownArrow = profileIcon?.querySelector('.dropdown-arrow');
    
    if (profileIcon && profileDropdown) {
        profileIcon.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('active');
            dropdownArrow?.classList.toggle('rotated');
        });
        
        document.addEventListener('click', function(e) {
            if (!profileIcon.contains(e.target) && !profileDropdown.contains(e.target)) {
                profileDropdown.classList.remove('active');
                dropdownArrow?.classList.remove('rotated');
            }
        });
    }
    
    // Initialize chart
    initSalesChart('month');
    
    // Toggle buttons for chart
    document.querySelectorAll('.toggle').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.toggle').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            initSalesChart(this.dataset.period);
        });
    });
    
    // Search functionality
    const searchBox = document.getElementById('searchBox');
    searchBox.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('#ordersTable tbody .order-row');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
});

// Logout Handler
function handleAdminLogout() {
    if (confirm('Are you sure you want to logout?')) {
        window.location.href = '<?= $pathPrefix ?>/logout.php';
    }
}

// Sales Chart
let salesChart = null;

function initSalesChart(period) {
    const pathPrefix = '<?= $pathPrefix ?>';
    
    fetch(`${pathPrefix}/pages/admin/getDashboardData.php?period=${period}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            const ctx = document.getElementById('salesChart').getContext('2d');
            
            if (salesChart) {
                salesChart.destroy();
            }
            
            salesChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Sales (RM)',
                        data: data.values,
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 2,
                        borderRadius: 8,
                        hoverBackgroundColor: 'rgba(59, 130, 246, 1)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            callbacks: {
                                label: function(context) {
                                    return 'RM ' + context.parsed.y.toFixed(2);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            },
                            ticks: {
                                color: '#94a3b8',
                                callback: function(value) {
                                    return 'RM ' + value.toFixed(0);
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#94a3b8'
                            }
                        }
                    }
                }
            });
        })
        .catch(error => {
            console.error('Error loading chart data:', error);
            alert('Failed to load chart data. Please check the console for details.');
        });
}
</script>

<?php include 'adminFooter.php'; ?>