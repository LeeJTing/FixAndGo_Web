<?php
require "../../_base.php";
// $_SESSION['USER_ID'] = "M001";
$_title = 'Fix & GO | Cart';
include "../../_head.php";
require_once "../../DAO/cart_dao.php";
require "../../DAO/product_dao.php";
require "../../DAO/profile_dao.php";

// Determine user ID - use logged in user or generate guest session ID
$user_id = temp('USER_ID') ?? null;
if (!$user_id) {
    // For guest users, use session-based identifier
    if (!isset($_SESSION['guest_session_id'])) {
        $_SESSION['guest_session_id'] = 'guest_' . session_id();
    }
    $user_id = $_SESSION['guest_session_id'];
}

$cart = getCartByUserId($user_id);

$cart_items = $cart ? getCartItems($cart->cart_id) : [];
$total = 0;
$selected_total = 0;

// Get customer addresses (only if logged in)
$addresses = temp('USER_ID') ? getAddressesByUserId(temp('USER_ID')) : [];
$address_count = is_array($addresses) ? count($addresses) : 0;
foreach ($cart_items as $item) {
    $item->item_total = $item->unit_price * $item->qty;
    $total += $item->item_total;

    // Use is_check to calculate selected total (is_check is the primary selection flag)
    $is_check = isset($item->is_check) ? (int)$item->is_check : (int)($item->is_take ?? 0);
    if ($is_check) {
        $selected_total += $item->item_total;
    }
}
?>

<link rel="stylesheet" href="<?= $rootDir ?>/css/cart.css" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    const ROOT_DIR = '<?= $rootDir ?>';
</script>
<script src="<?= $rootDir ?>/js/cart.js"></script>

<main class="cart-page">
    <div class="cartTitle flex align-center">
        <a href="<?= $homelink ?>" class="logo flex align-center">
            <img src="<?= $rootDir ?>/images/logo.png" alt="Logo" style="width:100px; height:120px; margin-right:10px;" />
            <span class="logo-text" style="padding:5px; font-size:2em; font-family:'Times New Roman', Times, serif; color:#FFA000;">Fix&Go</span>
        </a>
        <span style="padding:5px; font-size:2em; margin: 0 10px; color:#FFA000;">|</span>
        <span class="cart-label" style="padding:5px; font-size:3em; font-family:'Times New Roman', Times, serif; color:#FFA000;">Cart</span>
    </div>

    <div class="cart-items-container">
        <div class="cart-header flex align-center" style="background-color:black;">
            <span class="header-checkbox">
                All
                <input type="checkbox" id="select-all-checkbox" style="transform: scale(1.2); margin-right: 5px; width: 65px; " />
            </span>
            <span class="header-name">Product Name</span>
            <span class="header-price">Price</span>
            <span class="header-quantity">Quantity</span>
            <span class="header-total">Total</span>
            <span class="header-delete">Action</span>
        </div>

        <!-- Cart Items -->
        <?php if (empty($cart_items)): ?>
            <div class="empty-cart">
                <i class="fa-solid fa-cart-shopping" style="font-size: 4em; color: #ccc; margin-bottom: 20px;"></i>
                <p style="font-size: 1.3em; margin-bottom: 20px; color: #6c757d;">Your cart is empty</p>
                <a href="../product-list.php" class="btn" style="background: #FFA000; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; font-size: 1.1em; transition: all 0.3s ease;">
                    <i class="fa-solid fa-bag-shopping"></i> Continue Shopping
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($cart_items as $item):
                // Get full product details including description
                $product_details = getProductByIdDao($item->product_id);
                $description = $product_details ? ($product_details->description ?? 'No description available.') : 'No description available.';
                $short_description = $product_details ? ($product_details->short_desc ?? '') : '';
            ?>
                <?php
                // Use is_check as primary, fallback to is_take for backward compatibility
                $is_check = isset($item->is_check) ? (int)$item->is_check : (int)($item->is_take ?? 0);
                ?>
                <div class="cart-item flex align-center <?= $is_check ? 'item-selected' : '' ?>"
                    data-item-id="<?= $item->item_id ?>"
                    data-price="<?= $item->unit_price ?>">

                    <div class="item-checkbox-container">
                        <input type="checkbox"
                            class="item-checkbox is-take-checkbox"
                            data-item-id="<?= $item->item_id ?>"
                            data-is-check="<?= $is_check ?>"
                            <?= $is_check ? 'checked' : '' ?> />
                    </div>

                    <img src="<?= $rootDir ?>/<?= htmlspecialchars($item->file_path) ?>"
                        alt="<?= htmlspecialchars($item->product_name) ?>"
                        class="product-image clickable-image"
                        data-product-id="<?= $item->product_id ?>"
                        data-product-name="<?= htmlspecialchars($item->product_name) ?>"
                        data-product-price="RM <?= number_format($item->unit_price, 2) ?>"
                        data-product-description="<?= htmlspecialchars($description) ?>"
                        data-product-short-desc="<?= htmlspecialchars($short_description) ?>"
                        title="Click to view details" />

                    <span class="product-name"><?= htmlspecialchars($item->product_name) ?></span>
                    <span class="product-price">RM <?= number_format($item->unit_price, 2) ?></span>

                    <div class="quantity-control">
                        <!-- Minus button -->
                        <a href="javascript:void(0)" class="qty-btn minus <?= $item->qty <= 1 ? 'disabled' : '' ?>">-</a>

                        <span class="product-quantity-display"><?= $item->qty ?></span>

                        <!-- Plus button -->
                        <a href="javascript:void(0)" class="qty-btn plus <?= $item->stock_quantity >= 999 ? 'disabled' : '' ?>">+</a>
                    </div>

                    <span class="product-total">RM <?= number_format($item->item_total, 2) ?></span>

                    <a href="javascript:void(0)"
                        class="delete-btn"
                        data-item-id="<?= $item->item_id ?>">
                        Remove
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Cart Footer -->
    <?php if (!empty($cart_items)): ?>
        <?php if (!temp('USER_ID')): ?>
            <div class="guest-checkout-notice">
                <div class="notice-content">
                    <i class="fa-solid fa-info-circle"></i>
                    <div class="notice-text">
                        <h4>Continue as Guest or Login</h4>
                        <p>You can proceed to checkout as a guest, or <a href="<?= $rootDir ?>/pages/auth/login.php" class="login-link">login</a> to save your address and track orders.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Address Section - Only visible for logged-in users -->
        <?php if ($user_id): ?>
            <div class="cart-address-section">
                <h3 class="section-title">Delivery Address</h3>

                <!-- Select Addresses Section -->
                <?php if (!empty($addresses)): ?>
                    <div class="address-list">
                        <h4>Select Delivery Address:</h4>
                        <div class="address-options">
                            <?php foreach ($addresses as $address): ?>
                                <div class="address-option" data-address-id="<?= $address->address_id ?>">
                                    <input type="radio"
                                        class="address-radio"
                                        id="address-<?= $address->address_id ?>"
                                        name="selected_address"
                                        value="<?= $address->address_id ?>"
                                        data-address-id="<?= $address->address_id ?>">
                                    <label for="address-<?= $address->address_id ?>" class="address-label">
                                        <div class="address-details">
                                            <p class="address-line"><strong><?= htmlspecialchars($address->address_name ?? $address->address_one) ?></strong></p>
                                            <p class="address-line"><?= htmlspecialchars($address->address_one) ?></p>
                                            <?php if ($address->address_two): ?>
                                                <p class="address-line"><?= htmlspecialchars($address->address_two) ?></p>
                                            <?php endif; ?>
                                            <?php if ($address->address_three): ?>
                                                <p class="address-line"><?= htmlspecialchars($address->address_three) ?></p>
                                            <?php endif; ?>
                                            <p class="address-line"><?= htmlspecialchars($address->post_code) ?> <?= htmlspecialchars($address->state) ?>, <?= htmlspecialchars($address->country) ?></p>
                                        </div>
                                    </label>

                                    <div class="address-actions">
                                        <button type="button" class="btn-edit edit-address-btn"
                                            data-address-id="<?= $address->address_id ?>"
                                            data-address-name="<?= htmlspecialchars($address->address_name) ?>"
                                            data-address-one="<?= htmlspecialchars($address->address_one) ?>"
                                            data-address-two="<?= htmlspecialchars($address->address_two) ?>"
                                            data-address-three="<?= htmlspecialchars($address->address_three) ?>"
                                            data-state="<?= htmlspecialchars($address->state) ?>"
                                            data-post-code="<?= htmlspecialchars($address->post_code) ?>"
                                            data-country="<?= htmlspecialchars($address->country) ?>">
                                            Edit
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Add Address Section -->
                <div class="add-new-address">
                    <button class="btn-toggle-form" id="toggleAddressForm">
                        <i class="fa-solid fa-plus"></i> Add New Address
                    </button>

                    <form id="newAddressForm" class="address-form hidden">
                        <input type="hidden" id="address_id" name="address_id" value="">
                        <h4>Add New Delivery Address</h4>

                        <div class="form-group">
                            <label for="address_name">Label (optional)</label>
                            <input type="text" id="address_name" name="address_name" placeholder="Home, Office, etc.">
                            <label for="address_one">Address Line 1 *</label>
                            <input type="text"
                                id="address_one"
                                name="address_one"
                                placeholder="Street address"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="address_two">Address Line 2</label>
                            <input type="text"
                                id="address_two"
                                name="address_two"
                                placeholder="Apartment, unit, etc. (optional)">
                        </div>

                        <div class="form-group">
                            <label for="address_three">Address Line 3</label>
                            <input type="text"
                                id="address_three"
                                name="address_three"
                                placeholder="Additional address info (optional)">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="state">State *</label>
                                <select id="state" name="state" required>
                                    <option value="">Select State</option>
                                    <option value="Johor">Johor</option>
                                    <option value="Kedah">Kedah</option>
                                    <option value="Kelantan">Kelantan</option>
                                    <option value="Kuala Lumpur">Kuala Lumpur</option>
                                    <option value="Labuan">Labuan</option>
                                    <option value="Malacca">Malacca</option>
                                    <option value="Negeri Sembilan">Negeri Sembilan</option>
                                    <option value="Pahang">Pahang</option>
                                    <option value="Penang">Penang</option>
                                    <option value="Perak">Perak</option>
                                    <option value="Perlis">Perlis</option>
                                    <option value="Putrajaya">Putrajaya</option>
                                    <option value="Sabah">Sabah</option>
                                    <option value="Sarawak">Sarawak</option>
                                    <option value="Selangor">Selangor</option>
                                    <option value="Terengganu">Terengganu</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="post_code">Postal Code *</label>
                                <input type="text"
                                    id="post_code"
                                    name="post_code"
                                    placeholder="5-digit postal code"
                                    maxlength="5"
                                    required>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-save">Save Address</button>
                            <button type="button" class="btn-cancel" id="cancelAddressForm">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <!-- Payment Method Section -->
        <div class="payment-method-section">
            <h3>Payment Method</h3>
            <div class="payment-options">
                <label class="payment-option">
                    <input type="radio" name="payment_method" value="Cash" checked>
                    <i class="fa-solid fa-money-bill-wave"></i>
                    <span>Cash on Delivery</span>
                </label>
                <label class="payment-option">
                    <input type="radio" name="payment_method" value="Credit/Debit Card">
                    <i class="fa-solid fa-credit-card"></i>
                    <span>Credit/Debit Card</span>
                </label>
                <label class="payment-option">
                    <input type="radio" name="payment_method" value="Online Banking">
                    <i class="fa-solid fa-university"></i>
                    <span>Online Banking</span>
                </label>
            </div>
        </div>

        <div class="cart-footer">
            <div class="total-section">
                <span class="selected-total-label">Selected Total: RM <span id="selected-total"><?= number_format($selected_total, 2) ?></span></span>
            </div>
            <button id="btnCheckout" type="button" onclick="proceedToCheckout()">Proceed to Checkout</button>
        </div>
    <?php endif; ?>
</main>

<!-- Image Popup Modal -->
<div id="imagePopup" class="popup-modal">
    <div class="popup-content">
        <span class="popup-close">&times;</span>
        <div class="popup-image-container">
            <img id="popupImage" src="" alt="" class="popup-image" />
        </div>
        <div class="popup-details">
            <h3 id="popupProductName"></h3>
            <p id="popupProductPrice" class="popup-price"></p>
            <div id="popupShortDescription" class="popup-short-desc"></div>
            <div id="popupProductDescription" class="popup-description"></div>
            <a id="popupViewDetails" href="#" class="popup-view-btn">View Full Details</a>
        </div>
    </div>
</div>

<?php include "../../_foot.php"; ?>