<?php
require '../../_base.php';
$_title = 'Fix & Go | Product';
include '../../_head.php';
require '../../controller/product-controller.php';
$id = get('id') ?? 0;
$product = getProductById($id);

if (!$product) {
    die("<h1>Product not found!</h1>");
}
$mainImage = getProductImages($id);
?>
<link rel="stylesheet" href="../../css/product-detail.css">
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
        <!-- ==================== HARD-CODED REVIEWS SECTION (Paste below .detail-grid) ==================== -->
        <div class="reviews-section">
            <h2 class="section-title">Customer Reviews</h2>

            <!-- Summary -->
            <div class="reviews-summary">
                <div class="average-rating">
                    <span class="big-rating">4.8</span>
                    <div class="stars">
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star-half-alt"></i>
                    </div>
                    <p>Based on 127 reviews</p>
                </div>
            </div>

            <!-- Review List -->
            <div class="reviews-list">

                <div class="review-item">
                    <div class="review-header">
                        <div class="reviewer">
                            <strong>Ahmad Z.</strong>
                            <span class="verified">Verified Buyer</span>
                        </div>
                        <div class="review-rating">
                            <i class="fa-solid fa-star filled"></i>
                            <i class="fa-solid fa-star filled"></i>
                            <i class="fa-solid fa-star filled"></i>
                            <i class="fa-solid fa-star filled"></i>
                            <i class="fa-solid fa-star filled"></i>
                        </div>
                    </div>
                    <p class="review-date">15 Nov 2025</p>
                    <p class="review-text">Hammer berkualiti tinggi! Berat seimbang, mudah digenggam. Dah guna untuk pasang rak TV — tak goyang langsung. Recommended!</p>
                </div>

                <div class="review-item">
                    <div class="review-header">
                        <div class="reviewer">
                            <strong>Siti Nurhaliza</strong>
                            <span class="verified">Verified Buyer</span>
                        </div>
                        <div class="review-rating">
                            <i class="fa-solid fa-star filled"></i>
                            <i class="fa-solid fa-star filled"></i>
                            <i class="fa-solid fa-star filled"></i>
                            <i class="fa-solid fa-star filled"></i>
                            <i class="fa-regular fa-star"></i>
                        </div>
                    </div>
                    <p class="review-date">10 Nov 2025</p>
                    <p class="review-text">Cantik design dia, nampak premium. Cuma pemegang getah sikit licin bila berpeluh. Tapi overall sangat berpuas hati</p>
                </div>

                <div class="review-item">
                    <div class="review-header">
                        <div class="reviewer">
                            <strong>Rajesh Kumar</strong>
                            <span class="verified">Verified Buyer</span>
                        </div>
                        <div class="review-rating">
                            <i class="fa-solid fa-star filled"></i>
                            <i class="fa-solid fa-star filled"></i>
                            <i class="fa-solid fa-star filled"></i>
                            <i class="fa-solid fa-star filled"></i>
                            <i class="fa-solid fa-star filled"></i>
                        </div>
                    </div>
                    <p class="review-date">3 Nov 2025</p>
                    <p class="review-text">Best hammer I ever bought! Solid steel, no rust, very strong. Worth every sen. Will buy again for my brother.</p>
                </div>

                <div class="review-item">
                    <div class="review-header">
                        <div class="reviewer">
                            <strong>Lina Tan</strong>
                            <span class="verified">Verified Buyer</span>
                        </div>
                        <div class="review-rating">
                            <i class="fa-solid fa-star filled"></i>
                            <i class="fa-solid fa-star filled"></i>
                            <i class="fa-solid fa-star filled"></i>
                            <i class="fa-solid fa-star filled"></i>
                            <i class="fa-regular fa-star"></i>
                        </div>
                    </div>
                    <p class="review-date">28 Oct 2025</p>
                    <p class="review-text">Packaging cantik, sampai dengan selamat. Hammer berat yang sesuai, tak terlalu berat untuk wanita. Terima kasih FixAndGo!</p>
                </div>

            </div>

            <!-- Fake Review Form (for demo) -->
            <div class="add-review">
                <h3>Write a Review</h3>
                <div style="background:#f1f5f9; padding:2rem; border-radius:16px; text-align:center; color:#64748b;">
                    <i class="fa-regular fa-comment-dots" style="font-size:3rem; margin-bottom:1rem;"></i>
                    <p><strong>Login required to write a review</strong></p>
                    <a href="login.php" style="color:#f59e0b; font-weight:600;">Click here to login →</a>
                </div>
            </div>
        </div>
        <!-- ==================== END OF HARD-CODED REVIEWS ==================== -->
    </div>

    <?php include "../../_foot.php" ?>
</body>

</html>