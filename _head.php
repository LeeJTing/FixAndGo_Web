<?php
// temp('USER_ID', "M001");
$homelink = homePageURL();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $_title ?? 'Untitled' ?></title>
    <link rel="shortcut icon" href="<?= $rootDir ?>/images/icon.png">
    <link rel="stylesheet" href="<?= $rootDir ?>/css/style.css">
    <link rel="stylesheet" href="<?= $rootDir ?>/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="<?= $rootDir ?>/js/script.js"></script>

    <?php if (temp('USER_ID')): ?>
        <script src="<?= $rootDir ?>/js/member.js"></script>
    <?php else: ?>
        <script src="<?= $rootDir ?>/js/guest.js"></script>
    <?php endif; ?>
</head>

<header>
    <div class="container flex justify-between">
        <a href="<?= $homelink ?>" class="logo" data-link="home">
            <img src="<?= $rootDir ?>/images/logo.png" alt="Logo" style="width: 70px; height: 90px;" />
            Fix&Go
        </a>
        <nav class="desktop-nav">
            <ul class="flex">
                <li><a href="<?= $homelink ?>">Home</a></li>
                <li><a href="<?= $rootDir ?>#">Products</a></li>
                <li><a href="<?= $rootDir ?>/pages/guest/aboutUs.php">About</a></li>
                <li><a href="<?= $rootDir ?>">Contact</a></li>
            </ul>
        </nav>

        <!-- Menu Toggle Button (appears on all sizes) -->
        <button class="mobile-menu-toggle" id="mobileMenuToggle">
            <i class="fa-solid fa-bars"></i>
        </button>
        <div class="profile-wrapper">
            <?php if (temp('USER_ID')): ?>
                <!-- LOGGED IN USER -->
                <div class="profile-icon logged-in" id="profileIcon">
                    <img src="<?= $rootDir . getUserProfilePicture(temp('USER_ID')) ?>"
                        alt="Profile"
                        class="profile-picture" />
                    <span class="username"><?= htmlspecialchars(temp('USER_NAME') ?? 'User') ?></span>
                    <i class="fa-solid fa-chevron-down dropdown-arrow"></i>
                </div>

                <div class="profile-dropdown" id="profileDropdown">
                    <a href="<?= $rootDir ?>/pages/member/profile.php">
                        <i class="fa-regular fa-user"></i> My Profile
                    </a>
                    <a href="<?= $rootDir ?>/pages/member/orders.php">
                        <i class="fa-regular fa-clipboard"></i> My Orders
                    </a>
                    <a href="<?= $rootDir ?>/pages/member/wishlist.php">
                        <i class="fa-regular fa-heart"></i> Wishlist
                    </a>
                    <hr>
                    <a href="<?= $rootDir ?>/pages/auth/logout.php" class="logout-btn">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
                    </a>
                </div>
            <?php else: ?>
                <div class="profile-icon guest" id="profileIcon">
                    <i class="fa-regular fa-circle-user"></i>
                    <span>Guest</span>
                </div>
                <div class="profile-dropdown" id="profileDropdown">
                    <a href="<?= $rootDir ?>/pages/auth/login.php">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i> Login
                    </a>
                    <a href="<?= $rootDir ?>/pages/guest/register.php">
                        <i class="fa-solid fa-user-plus"></i> Register
                    </a>
                </div>
            <?php endif; ?>
            <!-- Cart Icon (click to open sidebar) -->
            <div class="cart-icon <?= $userRole ?>" id="cartIcon">
                <i class="fa-solid fa-cart-shopping"></i>
                <span class="cart-count" id="cartCount">0</span>
            </div>
        </div>


        <div class="mobile-dropdown" id="mobileDropdown">
            <ul>
                <li><a href="<?= $homelink ?>">Home</a></li>
                <li><a href="<?= $rootDir ?>#">Products</a></li>
                <li><a href="<?= $rootDir ?>/pages/guest/aboutUs.php">About</a></li>
                <li><a href="<?= $rootDir ?>">Contact</a></li>
            </ul>
        </div>

        <!-- Sliding Cart Sidebar -->
        <div id="cartSidebar" class="cart-sidebar">
            <div class="cart-header">
                <h3>Your Cart</h3>
                <button class="close-cart">&times;</button>
            </div>
            <div class="cart-items" id="cartItems">
                <p class="empty-cart">Your cart is empty</p>
            </div>
            <div class="cart-footer">
                <div class="cart-total">
                    <strong>Total: <span id="cartTotalPrice">$0.00</span></strong>
                </div>
                <button class="btn-primary checkout-btn">Checkout</button>
            </div>
        </div>

        <!-- Overlay (dark background when cart is open) -->
        <div id="cartOverlay" class="cart-overlay"></div>
    </div>
</header>