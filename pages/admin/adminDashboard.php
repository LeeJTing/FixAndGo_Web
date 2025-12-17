<?php 
require '../../_base.php';

// 获取当前登录用户信息
$currentUser = getCurrentUser();
if (!$currentUser) {
    $currentUser = (object)[
        'user_id' => null,
        'user_name' => 'Guest',
        'user_role' => 'Guest'
    ];
}

// 获取用户头像
$userProfilePic = $currentUser->user_id ? getUserProfilePicture($currentUser->user_id) : $pathPrefix . '/images/profile/default_profile_picture.webp';

include 'adminHeader.php';

?>
<link rel="stylesheet" href="../../css/adminDashboard.css">
<div class="admin-topbar">
    <input class="search-box" type="text" placeholder="Search orders, products, customers...">

    <div class="profile-area">
        <img src="<?= htmlspecialchars($userProfilePic) ?>" 
             class="avatar" 
             alt="<?= htmlspecialchars($currentUser->user_name) ?>"
             width="36" height="36" 
             style="width: 36px; height: 36px; object-fit: cover; border-radius: 50%;"
             onerror="this.src='<?= $pathPrefix ?>/images/profile/default_profile_picture.webp'">
        <span><?= htmlspecialchars($currentUser->user_name) ?></span>
    </div>
</div>

<!-- Page Content Starts Here -->
<div class="admin-content">

    <h1 class="page-title">Dashboard Overview</h1>

    <div class="cards-grid">

        <div class="card">
            <h3>Total Revenue</h3>
            <p class="value">$124,567</p>
            <span class="up">▲ 2.5% vs last month</span>
        </div>

        <div class="card">
            <h3>New Orders Today</h3>
            <p class="value">82</p>
            <span class="down">▼ 1.2% vs yesterday</span>
        </div>

        <div class="card">
            <h3>Total Customers</h3>
            <p class="value">1,450</p>
            <span class="up">▲ 0.8% vs last month</span>
        </div>

        <div class="card">
            <h3>Average Order Value</h3>
            <p class="value">$151.91</p>
            <span class="up">▲ 5.1% vs last month</span>
        </div>

    </div>

    <div class="dashboard-flex">
        
        <!-- Left Section -->
        <div class="panel chart-panel">
            <div class="panel-header">
                <div>
                    <h3>Sales Performance</h3>
                    <p class="subtitle">A summary of your sales over the last month.</p>
                </div>

                <div class="toggle-group">
                    <button class="toggle active">Month</button>
                    <button class="toggle">Week</button>
                    <button class="toggle">Day</button>
                </div>
            </div>

            <img class="chart-img" src="https://placehold.co/600x250/1e1e1e/3e82f7?text=Chart+Line+Placeholder">
        </div>

        <!-- Right Section -->
        <div class="quick-actions">
            <h3>Quick Actions</h3>
            <button class="qa-btn primary">Add New Product</button>
            <button class="qa-btn">Manage Inventory</button>
            <button class="qa-btn">Customer Tickets</button>
        </div>

    </div>


    <div class="panel">
        <h3>Recent Orders</h3>
        <table class="table">
            <tr><th>Order ID</th><th>Name</th><th>Date</th><th>Total</th><th>Status</th></tr>
            <tr><td>#12548</td><td>Emily Carter</td><td>2023-10-26</td><td>$250</td><td class="status shipped">Shipped</td></tr>
            <tr><td>#12547</td><td>Michael Chen</td><td>2023-10-26</td><td>$89.5</td><td class="status pending">Pending</td></tr>
            <tr><td>#12546</td><td>Sophia Rodriguez</td><td>2023-10-25</td><td>$412.75</td><td class="status canceled">Canceled</td></tr>
        </table>
    </div>

</div> <!-- END content -->

<?php include 'adminFooter.php'; ?>