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
</head>

<header>
    <div class="container flex justify-between">
        <a href="#" class="logo" data-link="home">
            <img src="<?= $rootDir ?>/images/logo.png" alt="Logo" style="width: 70px; height: 90px;" />
            Fix&Go
        </a>
        <nav class="desktop-nav">
            <ul class="flex">
                <li><a href="<?= $rootDir ?>#">Home</a></li>
                <li><a href="<?= $rootDir ?>#">Products</a></li>
                <li><a href="<?= $rootDir ?>#">About</a></li>
                <li><a href="<?= $rootDir ?>#">Contact</a></li>
            </ul>
        </nav>

        <!-- Menu Toggle Button (appears on all sizes) -->
        <button class="mobile-menu-toggle" id="mobileMenuToggle">
            <i class="fa-solid fa-bars"></i>
        </button>
        <!-- Cart Icon (click to open sidebar) -->
        <div class="cart-icon" id="cartIcon">
            <i class="fa-solid fa-cart-shopping"></i>
            <span class="cart-count" id="cartCount">0</span>
        </div>

        <div class="mobile-dropdown" id="mobileDropdown">
            <ul>
                <li><a href="<?= $rootDir ?>#">Home</a></li>
                <li><a href="<?= $rootDir ?>#">Products</a></li>
                <li><a href="<?= $rootDir ?>#">About</a></li>
                <li><a href="<?= $rootDir ?>#">Contact</a></li>
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