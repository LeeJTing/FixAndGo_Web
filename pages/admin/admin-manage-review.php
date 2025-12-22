<?php
$_title = 'Fix & Go | Admin Order Detail';
require_once '../../_base.php';
require_once '../../dao/product_dao.php';
require_once '../../component/msg.php';
include_once 'adminHeader.php';
?>
<link rel="stylesheet" href="<?= $rootDir ?>/css/admin-review.css">
<script>
    window.adminReviewControllerUrl = '<?= $rootDir ?>/controller/admin-review-controller.php';
</script>
<script src="<?= $rootDir ?>/js/admin-review.js"></script>
<?php

$products = getProductListDao();

?>

<div class="product-list">
    <?php if ($products) :
        foreach ($products as $v) : ?>
            <div class="product-item">
                <img src="<?= $rootDir . '/' . htmlspecialchars($v->file_path) ?>" alt="<?= htmlspecialchars($v->alt_text ?? $v->product_name) ?>">
                <div class="product-title"><?= htmlspecialchars($v->product_name) ?></div>
                <div class="product-meta" data-price="<?= htmlspecialchars($v->unit_price) ?>">
                    ID: <?= htmlspecialchars($v->product_id) ?>
                </div>
                <div class="product-action-btn">
                    <button class="btn btn-view view-reviews-btn" data-product-id="<?= htmlspecialchars($v->product_id) ?>">Manage Reviews</button>
                </div>
            </div>
    <?php endforeach;
    endif ?>
</div>

<?php
include_once __DIR__ . "/adminFooter.php";
