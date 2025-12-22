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
        <li><a href="admin-category.php" class="<?= basename($_SERVER['PHP_SELF']) == 'admin-category.php' ? 'active' : '' ?>">Category</a></li>
        <li><a href="admin-manage-review.php" class="<?= basename($_SERVER['PHP_SELF']) == 'admin-manage-review.php' ? 'active' : '' ?>">Product Review</a></li>
    </ul>

    <div class="sidebar-footer">
        <div class="footer-content">
            <img src="<?= htmlspecialchars($userProfilePic) ?>"
                class="avatar"
                alt="<?= htmlspecialchars($currentUser->user_name) ?>"
                width="45" height="45" style="width: 45px; height: 45px; object-fit: cover;"
                onerror="this.src='<?= $pathPrefix ?>/images/profile/default_profile_picture.webp'">
            <p class="admin-name">
                <?= htmlspecialchars($currentUser->user_name) ?><br>
                <span><?= htmlspecialchars($currentUser->user_role) ?></span>
            </p>

            <!-- Theme Toggle Button -->
            <button id="themeToggle" class="theme-toggle" aria-label="Toggle theme">
                <svg class="sun-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="5"></circle>
                    <line x1="12" y1="1" x2="12" y2="3"></line>
                    <line x1="12" y1="21" x2="12" y2="23"></line>
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                    <line x1="1" y1="12" x2="3" y2="12"></line>
                    <line x1="21" y1="12" x2="23" y2="12"></line>
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                </svg>
                <svg class="moon-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                </svg>
            </button>
        </div>
    </div>
</div>

<script>
    // Theme toggle functionality
    const themeToggle = document.getElementById('themeToggle');
    const body = document.body;

<<<<<<< HEAD
    // Check for saved theme preference or default to 'dark'
    const currentTheme = localStorage.getItem('theme') || 'dark';
    body.setAttribute('data-theme', currentTheme);

    themeToggle.addEventListener('click', () => {
        const currentTheme = body.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

        body.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
    });
=======
// Check for saved theme preference or default to 'dark'
const currentTheme = localStorage.getItem('adminTheme') || 'dark';
body.setAttribute('data-theme', currentTheme);

themeToggle.addEventListener('click', () => {
    const currentTheme = body.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    
    body.setAttribute('data-theme', newTheme);
    localStorage.setItem('adminTheme', newTheme);
});
>>>>>>> df3affb8b1a94085391db39f0927ce7f283b9902
</script>