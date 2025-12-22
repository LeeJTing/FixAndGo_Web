<?php
require_once "../../_base.php";
require_once "../../controller/product-controller.php";
$_title = 'Fix & GO | About Us';
include_once "../../_head.php";

$products = getNewArrivalProduct(5);
?>
<link rel="stylesheet" href="../../css/new_arrival.css">
<section class="new-arrivals">
    <div class="new-arrivals-header">
        <h2>New Arrivals</h2>
        <p>Check out the latest products just added to Fix & Go</p>
    </div>
    <div class="new-arrivals-grid">
        <?php foreach ($products as $p): ?>
            <div class="new-product-card">
                <div class="badge-new">New</div>

                <div class="new-product-image">
                    <img src="../../<?= htmlspecialchars($p->file_path ?? 'images/placeholder.jpg') ?>"
                        alt="<?= htmlspecialchars($p->alt ?? $p->product_name) ?>">
                </div>

                <div class="new-product-info">
                    <span class="new-category"><?= htmlspecialchars($p->category_name) ?></span>
                    <h3 class="new-product-title"><?= htmlspecialchars($p->product_name) ?></h3>
                    <div class="new-product-price">RM <?= number_format((float)$p->unit_price, 2) ?></div>

                    <div class="new-product-actions">
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
