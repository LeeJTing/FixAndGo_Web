<?php
// 获取当前登录用户的信息
$currentUser = getCurrentUser();

// 如果没有登录用户，使用默认值
if (!$currentUser) {
    $currentUser = (object)[
        'user_id' => null,
        'user_name' => 'Guest User',
        'user_role' => 'Guest'
    ];
}

// 获取用户头像
$userProfilePic = $currentUser->user_id ? getUserProfilePicture($currentUser->user_id) : $pathPrefix . '/images/profile/default_profile_picture.webp';
?>

<div class="admin-sidebar">
    <h2 class="logo">ShopAdmin</h2>

    <ul class="menu">
        <li><a href="adminDashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'adminDashboard.php' ? 'active' : '' ?>">Dashboard</a></li>
        <li><a href="admin-order.php" class="<?= basename($_SERVER['PHP_SELF']) == 'admin-order.php' ? 'active' : '' ?>">Orders</a></li>
        <li><a href="admin-product.php" class="<?= basename($_SERVER['PHP_SELF']) == 'admin-product.php' ? 'active' : '' ?>">Products</a></li>
        <li><a href="adminCustomer.php" class="<?= basename($_SERVER['PHP_SELF']) == 'adminCustomer.php' ? 'active' : '' ?>">Customer</a></li>
        <li><a href="admin-category.php" class="<?= basename($_SERVER['PHP_SELF']) == 'admin-add-category.php' ? 'active' : '' ?>">Category</a></li>
        <li><a href="analytics.php" class="<?= basename($_SERVER['PHP_SELF']) == 'analytics.php' ? 'active' : '' ?>">Analytics</a></li>
    </ul>

    <div class="sidebar-footer">
        <img src="<?= htmlspecialchars($userProfilePic) ?>"
            class="avatar"
            alt="<?= htmlspecialchars($currentUser->user_name) ?>"
            width="45" height="45" style="width: 45px; height: 45px; object-fit: cover;"
            onerror="this.src='<?= $pathPrefix ?>/images/profile/default_profile_picture.webp'">
        <p class="admin-name">
            <?= htmlspecialchars($currentUser->user_name) ?><br>
            <span><?= htmlspecialchars($currentUser->user_role) ?></span>
        </p>
    </div>
</div>