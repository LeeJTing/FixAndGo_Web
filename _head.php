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
    <header>
        <div class="container flex justify-between">
            <a href="<?= homePageURL() ?>" class="logo" data-link="home">
                <img src="<?= $rootDir ?>/images/logo.png" alt="Logo" style="width: 70px; height: 90px;" />
                Fix&Go
            </a>
            <nav class="desktop-nav">
                <ul class="flex">
                    <li><a href="<?= homePageURL() ?>">Home</a></li>
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
                        <li><a href="<?= $rootDir ?>#">Products</a></li>
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
                            <a href="<?= $rootDir ?>/pages/cart/cart.php"><button class="btn-primary checkout-btn">Checkout</button></a>
                        </div>
                    </div>
                </div>

                <!-- Overlay (dark background when cart is open) -->
                <div id="cartOverlay" class="cart-overlay"></div>
            </div>
    </header>
</body>
<script>
    (function($) {
        function recalcHeaderCart() {
            // Recalculate total only for items checked (is_check)
            let total = 0;
            let count = 0;
            $('#cartItems .cart-item').each(function() {
                const $it = $(this);
                const checked = $it.find('.is-check-checkbox').is(':checked');
                const qty = parseInt($it.find('.product-quantity-display').text()) || 0;
                const price = parseFloat($it.data('price')) || 0;
                if (checked) {
                    total += qty * price;
                    count += 1;
                }
            });
            $('#cartTotalPrice').text('RM ' + total.toFixed(2));
            $('#cartCount').text(count);
        }

        $(function() {
            // initialize count from server-rendered value
            $('#cartCount').text(<?= (int)$cart_count ?>);

            // Open/close handlers
            $('#cartIcon').on('click', function(e) {
                e.stopPropagation();
                $('#cartSidebar').addClass('open');
                $('#cartOverlay').show();
            });

            $('.close-cart, #cartOverlay').on('click', function() {
                $('#cartSidebar').removeClass('open');
                $('#cartOverlay').hide();
            });

            // Quantity buttons
            $(document).on('click', '#cartItems .qty-btn', function(e) {
                e.preventDefault();
                const $btn = $(this);
                const $item = $btn.closest('.cart-item');
                const itemId = $item.data('item-id');
                let qty = parseInt($item.find('.product-quantity-display').text()) || 0;
                if ($btn.hasClass('plus')) qty++;
                if ($btn.hasClass('minus')) qty = Math.max(1, qty - 1);

                $.post(ROOT_DIR + '/AJAX/update_quantity.php', {
                        update_quantity: true,
                        item_id: itemId,
                        quantity: qty
                    })
                    .done(function(resp) {
                        if (resp.success) {
                            $item.find('.product-quantity-display').text(qty);
                            const price = parseFloat($item.data('price')) || 0;
                            $item.find('.product-total').text((price * qty).toFixed(2));
                            recalcHeaderCart();
                        } else {
                            alert('Error: ' + (resp.error || 'Failed to update quantity'));
                        }
                    }).fail(function() {
                        alert('Network error updating quantity');
                    });
            });

            // Remove
            $(document).on('click', '#cartItems .delete-btn', function(e) {
                e.preventDefault();
                const $btn = $(this);
                const itemId = $btn.data('item-id');
                if (!confirm('Remove this item from cart?')) return;
                $.post(ROOT_DIR + '/AJAX/remove_item.php', {
                        remove_item: true,
                        item_id: itemId
                    })
                    .done(function(resp) {
                        if (resp.success) {
                            $btn.closest('.cart-item').slideUp(200, function() {
                                $(this).remove();
                                recalcHeaderCart();
                            });
                        } else {
                            alert('Error: ' + (resp.error || 'Failed to remove item'));
                        }
                    }).fail(function() {
                        alert('Network error removing item');
                    });
            });

            // is_check checkbox -> update DB (is_check) and also update is_take so both views stay in sync
            $(document).on('change', '#cartItems .is-check-checkbox', function() {
                const $cb = $(this);
                const itemId = $cb.data('item-id');
                const isCheck = $cb.is(':checked') ? 1 : 0;

                // First update the is_check flag
                $.post(ROOT_DIR + '/AJAX/update_cart_check.php', {
                        update_is_check: true,
                        item_id: itemId,
                        is_check: isCheck
                    })
                    .done(function(resp) {
                        if (!resp.success) {
                            alert('Error updating selection: ' + (resp.error || 'Unknown'));
                            $cb.prop('checked', !isCheck);
                            return;
                        }

                        // Also update is_take on server so full-cart and slide-cart remain consistent
                        $.post(ROOT_DIR + '/AJAX/update_cart_take.php', {
                                update_is_take: true,
                                item_id: itemId,
                                is_take: isCheck
                            })
                            .done(function(resp2) {
                                if (!resp2.success) {
                                    console.warn('Failed to sync is_take:', resp2.error);
                                }
                            }).fail(function() {
                                console.warn('Network error syncing is_take');
                            });

                        // Update any matching checkboxes on the main cart page (if present)
                        const $mainChk = $('.is-take-checkbox[data-item-id="' + itemId + '"]');
                        if ($mainChk.length) {
                            $mainChk.prop('checked', !!isCheck);
                            // update visual selection class on the cart item row
                            const $mainRow = $mainChk.closest('.cart-item');
                            if ($mainRow.length) {
                                if (isCheck) $mainRow.addClass('item-selected');
                                else $mainRow.removeClass('item-selected');
                            }
                        }

                        // Recalculate header totals and page totals if available
                        recalcHeaderCart();
                        if (typeof updateCartTotals === 'function') {
                            try {
                                updateCartTotals();
                            } catch (e) {
                                console.warn(e);
                            }
                        }
                    }).fail(function() {
                        alert('Network error updating selection');
                        $cb.prop('checked', !$cb.is(':checked'));
                    });
            });
        });

        $('.logout-btn').on('click', function(e) {
            e.preventDefault();

            logout = confirm("Click OK to confirm to exit!");
            if (logout) {
                $.post(ROOT_DIR + "/_logout.php", {
                    logout: true
                }).done(function() {
                    window.location.href = ROOT_DIR + "/pages/guest/login.php";
                    console.log("posted");
                }).fail(function() {
                    console.log("failed");
                })
            }
        });
    })(jQuery);
</script>