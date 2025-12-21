<?php
require_once __DIR__ . '/../../_base.php';
require_once __DIR__ . '/../../dao/product_dao.php';
require_once __DIR__ . '/../../dao/review_dao.php';
require_once __DIR__ . '/../../dao/security_dao.php';
include_once 'adminHeader.php';

$productId = get('id') ?: null;
if (!$productId) {
    echo '<p>Product id missing.</p>';
    include_once __DIR__ . '/adminFooter.php';
    exit;
}

$product = getProductByIdAdminDao($productId);
$product_filePath = (array) getProductImagesDao($productId);
$file_path = null;
foreach ($product_filePath as $v) {
    if ($v->position == 1) {
        $file_path = $v->file_path;
    }
}
$reviews = adminGetReviewsByProduct($productId);

?>
<link rel="stylesheet" href="<?= $rootDir ?>/css/admin-review.css">
<div class="admin-review-page">
    <h1>Manage Reviews for: <?= htmlspecialchars($product->product_name ?? 'Unknown') ?></h1>
    <div class="product-summary">
        <img src="<?= $rootDir . '/' . htmlspecialchars($file_path) ?>" alt="<?= htmlspecialchars($product->product_name) ?>" style="width:120px;height:80px;object-fit:cover;float:left;margin-right:12px;">
        <div>
            <div><strong><?= htmlspecialchars($product->product_name) ?></strong></div>
            <div>Price: RM <?= htmlspecialchars($product->unit_price) ?></div>
            <div>Stock: <?= htmlspecialchars($product->stock_quantity) ?></div>
        </div>
        <div style="clear:both"></div>
    </div>

    <div class="admin-review-list">
        <?php if (!empty($reviews)): foreach ($reviews as $r): 
            $isBlocked = (($r['account_status'] ?? '') === 'Blocked');
            ?>
                <div class="review-row">
                    <div class="avatar">
                        <img src="<?= $rootDir . '/' . getUserProfilePicture($r['user_id'])?>" alt="profile picture">
                    </div>
                    <div class="review-body">
                        <div class="review-meta">
                            <strong><?= htmlspecialchars($r['user_name'] ?? 'Unknown') ?></strong>
                            &nbsp;·&nbsp; <span><?= htmlspecialchars($r['reviewed_at'] ?? '') ?></span>
                            &nbsp;·&nbsp; <small><?= htmlspecialchars($r['rating']) ?> / 5</small>
                        </div>
                        <div class="review-comment"><?= nl2br(htmlspecialchars($r['comment'])) ?></div>
                    </div>
                    <div class="review-actions">
                        <?php if ($isBlocked): ?>
                            <button class="btn btn-valid-toggle btn-invalid" data-review-id="<?= $r['review_id'] ?>" data-is-valid="0" disabled>Invalid</button>
                            <button class="btn btn-blocked" disabled>Blocked</button>
                        <?php else: ?>
                            <button class="btn btn-valid-toggle <?= $r['is_valid'] ? 'btn-valid' : 'btn-invalid' ?>" data-review-id="<?= $r['review_id'] ?>" data-is-valid="<?= $r['is_valid'] ?>"><?= $r['is_valid'] ? 'Valid' : 'Invalid' ?></button>
                            <button class="btn btn-block-user" data-user-id="<?= $r['user_id'] ?>"><?= ($r['account_status'] ?? '') === 'Blocked' ? 'Blocked' : 'Block User' ?></button>
                        <?php endif; ?>
                        <button class="btn btn-delete-review" data-review-id="<?= $r['review_id'] ?>">Delete</button>
                    </div>
                </div>
            <?php endforeach;
        else: ?>
            <p>No reviews for this product.</p>
        <?php endif; ?>
    </div>

</div>

<script>
    window.adminReviewControllerUrl = '<?= $rootDir ?>/controller/admin-review-controller.php';
</script>
<script src="<?= $rootDir ?>/js/admin-review.js"></script>

<?php include_once __DIR__ . '/adminFooter.php'; ?>