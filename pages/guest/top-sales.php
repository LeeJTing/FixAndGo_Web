<?php
require_once "../../_base.php";
require_once "../../controller/product-controller.php";
$_title = 'Fix & GO | About Us';
include_once "../../_head.php";

$topProducts = getTopSellingProducts(5);
?>

<link rel="stylesheet" href="../../css/top-sales.css">

<section class="top-sales">
    <div class="top-sales-header">
        <h2>Top 5 Best Sellers</h2>
        <p>Check out our most popular products!</p>
    </div>

    <div class="top-sales-grid">
        <?php foreach ($topProducts as $p): ?>
            <div class="top-product-card">
                <div class="badge-top">Hot</div>

                <div class="top-product-image">
                    <img src="../../<?= htmlspecialchars($p->file_path ?? 'images/placeholder.jpg') ?>"
                        alt="<?= htmlspecialchars($p->alt ?? $p->product_name) ?>">
                </div>

                <div class="top-product-info">
                    <span class="top-category"><?= htmlspecialchars($p->category_name) ?></span>
                    <h3 class="top-product-title"><?= htmlspecialchars($p->product_name) ?></h3>
                    <div class="top-product-price">RM <?= number_format((float)$p->unit_price, 2) ?></div>

                    <div class="top-product-actions">
                        <a href="../product/product-detail.php?id=<?= $p->product_id ?>" class="btn-view">View</a>
                        <button class="btn-cart"
                            data-id="<?= $p->product_id ?>"
                            data-name="<?= htmlspecialchars($p->product_name) ?>"
                            data-price="<?= $p->unit_price ?>"
                            data-image="../../<?= htmlspecialchars($p->file_path ?? '') ?>">
                            <i class="fa-solid fa-cart-shopping"></i>
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="back-home-container">
        <a href="../../pages/product/product-list.php" class="btn-back-home">← Back to Product Lists</a>
    </div>
</section>

<?php
include_once "../../_foot.php";
