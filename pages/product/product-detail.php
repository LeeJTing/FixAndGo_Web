<?php
$_title = 'Fix & Go | Product';
require '../../_base.php';
include '../../_head.php';
require '../../controller/product-controller.php';
$user_id = temp('USER_ID');
$id = get('id') ?? 0;
$product = getProductById($id);

if (!$product) {
    die("<h1>Product not found!</h1>");
}
$mainImage = getProductImages($id);

$isWishlisted = false;
if ($user_id && $user_id !== 'Guest') {
    try {
        require_once '../../DAO/wishlist_dao.php';
        $isWishlisted = isProductInWishlistDao((string)$user_id, (int)$id);
    } catch (Exception $e) {
        $isWishlisted = false;
    }
}

?>
<link rel="stylesheet" href="../../css/product-detail.css">
<!-- <script>
    const ROOT_DIR = '<?= $rootDir ?>';
</script> -->
<script src="../../js/product-detail.js"></script>

<body>
    <div class="detail-container">
        <div class="back-to-products">
            <a href="/pages/product/product-list.php" class="btn-back">
                <i class="fa-solid fa-arrow-left"></i> Back to Products
            </a>
        </div>
        <div class="detail-grid">
            <!-- Gallery -->
            <div class="gallery-container">
                <!-- Thumbnails on the left (vertical) -->
                <?php if (count($mainImage) > 1): ?>
                    <div class="gallery-thumbs-vertical">
                        <?php foreach ($mainImage as $img): ?>
                            <div class="thumb-wrapper <?= $img === $mainImage ? 'active' : '' ?>">
                                <img src="../../<?= htmlspecialchars($img->file_path) ?>"
                                    alt="<?= htmlspecialchars($img->alt ?? $product->product_name) ?>"
                                    onclick="changeMainImage(this.src, this.parentElement)"
                                    class="thumb-vertical">
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Main Image -->
                <div class="gallery-main">
                    <button class="favorite-btn" type="button" data-product-id="<?= (int)$product->product_id ?>" aria-label="Add to Wishlist">
                        <i class="fa-regular fa-heart <?= $isWishlisted ? 'changeColor' : '' ?>"></i>
                    </button>
                    <img src="../../<?= htmlspecialchars($mainImage[0]->file_path ?? 'placeholder') ?>"
                        alt="<?= htmlspecialchars($mainImage->alt ?? $product->product_name) ?>"
                        id="mainImg"
                        class="main-product-image">
                </div>
            </div>

            <!-- Product Info -->
            <div class="product-info-detail">
                <h1 class="title"><?= htmlspecialchars($product->product_name) ?></h1>

                <div class="price-big">
                    RM <?= number_format($product->unit_price, 2) ?>
                    <?php if ($product->product_point): ?>
                        <span class="points">+<?= $product->product_point ?> pts</span>
                    <?php endif; ?>
                </div>

                <div class="stock-status <?= $product->stock_quantity > 0 ? 'in-stock' : 'out-stock' ?>">
                    <?php if ($product->stock_quantity > 0): ?>
                        In Stock — <?= $product->stock_quantity ?> left
                    <?php else: ?>
                        Out of Stock
                    <?php endif; ?>
                </div>

                <!-- Description (smaller & cleaner) -->
                <?php if ($product->description): ?>
                    <div class="desc">
                        <?= nl2br(htmlspecialchars($product->description)) ?>
                    </div>
                <?php endif; ?>

                <!-- Key Features (optional, smaller) -->
                <?php if ($product->short_desc): ?>
                    <div class="features">
                        <strong>Key Features</strong><br>
                        <small><?= $product->description ?></small>
                    </div>
                <?php endif; ?>

                <!-- Action Buttons -->
                <div class="actions-big">
                    <div class="quantity-selector">
                        <button type="button" class="qty-btn minus" onclick="changeQty(-1)">−</button>
                        <input type="number" id="quantity" name="quantity" value="1" min="1"
                            max="<?= $product->stock_quantity ?>" readonly class="qty-input">
                        <button type="button" class="qty-btn plus" onclick="changeQty(1)">+</button>
                    </div>

                    <!-- Add to Cart Button -->
                    <button class="btn-big btn-add-cart add-to-cart"
                        data-id="<?= $product->product_id ?>"
                        data-name="<?= htmlspecialchars($product->product_name) ?>"
                        data-price="<?= $product->unit_price ?>"
                        <?= $product->stock_quantity <= 0 ? 'disabled' : '' ?>>
                        <i class="fa-solid fa-cart-shopping"></i>
                        <?= $product->stock_quantity > 0 ? 'Add to Cart' : 'Sold Out' ?>
                    </button>
                </div>

                <!-- Small footer info -->
                <div class="meta-info">
                    Product ID: <?= $product->product_id ?>
                    • Sold <?= $product->sold_number ?? 0 ?> times
                </div>
            </div>
        </div>
        <!-- Reviews Section -->
        <?php include_once __DIR__ . "/../../component/reviewSection.php" ?>
    </div>

    <?php include "../../_foot.php" ?>
</body>

</html>