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
    <script src="/js/script.js"></script>
    <script src="/js/header-cart.js"></script>
    <script>
        const ROOT_DIR = '<?= $rootDir ?>';
    </script>

    <?php if (temp('USER_ID')): ?>
        <script src="<?= $rootDir ?>/js/member.js"></script>
        <script>
            console.log('<?= temp('USER_ID') ?>')
        </script>
    <?php else: ?>
        <script src="<?= $rootDir ?>/js/guest.js"></script>
    <?php endif; ?>
</head>

<body>
    <div id="message-container"></div>
    <header>
        <div class="container flex justify-between">
            <a href="<?= homePageURL() ?>" class="logo" data-link="home">
                <img src="<?= $rootDir ?>/images/logo.png" alt="Logo" style="width: 70px; height: 90px;" />
                Fix&Go
            </a>
            <nav class="desktop-nav">
                <ul class="flex">
                    <li><a href="<?= homePageURL() ?>">Home</a></li>
                    <li><a href="<?= $rootDir ?>/pages/product/product-list.php">Products</a></li>
                    <li><a href="<?= $rootDir ?>/pages/guest/aboutUs.php">About</a></li>
                    <li><a href="<?= $rootDir ?>/pages/guest/contactUs.php">Contact</a></li>
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
                        <a href="<?= $rootDir ?>/pages/order/member-order-history.php">
                            <i class="fa-regular fa-clipboard"></i> My Orders
                        </a>
                        <hr>
                        <a href="javascript:void(0)" class="logout-btn">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
                        </a>
                    </div>
                <?php else: ?>
                    <div class="profile-icon guest" id="profileIcon">
                        <i class="fa-regular fa-circle-user"></i>
                        <span>Guest</span>
                    </div>
                    <div class="profile-dropdown" id="profileDropdown">
                        <a href="<?= $rootDir ?>/pages/guest/login.php">
                            <i class="fa-solid fa-arrow-right-to-bracket"></i> Login
                        </a>
                        <a href="<?= $rootDir ?>/pages/guest/register.php">
                            <i class="fa-solid fa-user-plus"></i> Register
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Cart Icon (click to open sidebar) - Hidden on cart page -->
                <?php
                $current_page = basename($_SERVER['PHP_SELF']);
                $is_cart_page = ($current_page === 'cart.php');
                ?>
                <?php if (!$is_cart_page): ?>
                    <div class="cart-icon <?= $userRole ?>" id="cartIcon">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span class="cart-count" id="cartCount" data-count="<?= (int)$cart_count ?>">0</span>
                    </div>
                <?php endif; ?>


                <div class="mobile-dropdown" id="mobileDropdown">
                    <ul>
                        <li><a href="<?= homePageURL() ?>">Home</a></li>
                        <li><a href="<?= $rootDir ?>/pages/product/product-list.php">Products</a></li>
                        <li><a href="<?= $rootDir ?>/pages/guest/aboutUs.php">About</a></li>
                        <li><a href="<?= $rootDir ?>">Contact</a></li>
                    </ul>
                </div>

                <!-- Sliding Cart Sidebar -->
                <?php
                require_once __DIR__ . '/DAO/cart_dao.php';
                $user_id = temp("USER_ID") ?? null;
                if (!$user_id) {
                    if (!isset($_SESSION['guest_session_id'])) {
                        $_SESSION['guest_session_id'] = 'guest_' . session_id();
                    }
                    $user_id = $_SESSION['guest_session_id'];
                }

                $cart = getCartByUserId($user_id);
                $cart_items = $cart ? getCartItems($cart->cart_id) : [];
                $cart_total = 0;
                $cart_count = is_array($cart_items) ? count($cart_items) : 0;
                foreach ($cart_items as $ci) {
                    $qty = isset($ci->qty) ? (int)$ci->qty : (int)($ci['qty'] ?? 1);
                    $price = isset($ci->unit_price) ? (float)$ci->unit_price : (float)($ci['unit_price'] ?? 0);
                    // Sum only checked (is_check) items for the checkout total
                    $is_check_flag = isset($ci->is_check) ? (int)$ci->is_check : (int)($ci['is_check'] ?? 1);
                    if ($is_check_flag) {
                        $cart_total += $qty * $price;
                    }
                }
                ?>
                <div id="cartSidebar" class="cart-sidebar">
                    <div class="cart-header">
                        <h3>Your Cart</h3>
                        <button class="close-cart">&times;</button>
                    </div>
                    <div class="cart-items" id="cartItems">
                        <?php if (empty($cart_items)): ?>
                            <p class="empty-cart">Your cart is empty</p>
                        <?php else: ?>
                            <?php foreach ($cart_items as $item):
                                $item_id = $item->item_id ?? $item['item_id'];
                                $product_name = htmlspecialchars($item->product_name ?? ($item['product_name'] ?? ''));
                                $unit_price = number_format($item->unit_price ?? $item['unit_price'] ?? 0, 2);
                                $qty = (int)($item->qty ?? $item['qty'] ?? 1);
                                $item_total = number_format(($item->unit_price ?? $item['unit_price'] ?? 0) * $qty, 2);
                                $file_path = $item->file_path ?? $item['file_path'] ?? '';
                                $is_check = isset($item->is_check) ? (int)$item->is_check : (int)($item['is_check'] ?? 1);
                            ?>
                                <div class="cart-item flex align-center" data-item-id="<?= $item_id ?>" data-price="<?= $item->unit_price ?>">
                                    <div class="item-checkbox-container">
                                        <input type="checkbox" class="item-checkbox is-check-checkbox" data-item-id="<?= $item_id ?>" <?= $is_check ? 'checked' : '' ?> />
                                    </div>
                                    <img src="<?= $rootDir ?>/<?= htmlspecialchars($file_path) ?>" alt="<?= $product_name ?>" class="product-image" style="width:50px;height:50px;object-fit:cover;" />
                                    <div class="cart-item-info">
                                        <div class="product-name-small"><?= $product_name ?></div>
                                        <div class="product-price-small">RM <?= $unit_price ?></div>
                                    </div>
                                    <div class="quantity-control-small">
                                        <a href="javascript:void(0)" class="qty-btn minus <?= $qty <= 1 ? 'disabled' : '' ?>">-</a>
                                        <span class="product-quantity-display"><?= $qty ?></span>
                                        <a href="javascript:void(0)" class="qty-btn plus">+</a>
                                    </div>
                                    <div class="product-total-small">RM <span class="product-total"><?= $item_total ?></span></div>
                                    <a href="javascript:void(0)" class="delete-btn" data-item-id="<?= $item_id ?>">Remove</a>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="cart-footer">
                        <div class="cart-total">
                            <strong>Total: <span id="cartTotalPrice">RM <?= number_format($cart_total, 2) ?></span></strong>
                        </div>
                        <div class="cart-actions">
                            <button class="btn-primary checkout-btn" type="button" onclick="window.location.href='<?= $rootDir ?>/pages/cart/cart.php'">Checkout</button>
                        </div>
                    </div>
                </div>

                <!-- Overlay (dark background when cart is open) -->
                <div id="cartOverlay" class="cart-overlay"></div>
            </div>
    </header>
    <script src="<?= $rootDir ?>/js/logout.js"></script>
</body>