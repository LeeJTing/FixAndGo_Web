<?php
$_title = 'Fix & Go | Wishlist';
require '../../_base.php';
require_once '../../component/msg.php';
require_once '../../DAO/wishlist_dao.php';

$user_id = temp('USER_ID');
if (!$user_id || $user_id === 'Guest' || temp('USER_ROLE') !== 'Member') {
    redirect('/pages/guest/login.php');
}

if (is_post() && isset($_POST['remove_product_id'])) {
    $pid = (int)post('remove_product_id');
    if ($pid > 0) {
        removeFromWishlistDao((string)$user_id, $pid);
        $_SESSION['flash_message'] = ['type' => 'success', 'text' => 'Removed from wishlist.'];
    }
    redirect('/pages/member/wishlist.php');
}

$items = getWishlistItemsByUserIdDao((string)$user_id);
?>
<?php include '../../_head.php'; ?>
<link rel="stylesheet" href="../../css/msg.css">
<link rel="stylesheet" href="../../css/member-order-history.css">
<link rel="stylesheet" href="../../css/wishlist.css">

<main class="main-container member-orders wishlist-page">
    <div class="page-header">
        <h1 class="page-title">My Wishlist</h1>
    </div>

    <?php displayFlashMessage(); ?>

    <div class="table-container wishlist-container">
        <table class="orders-table wishlist-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Added</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="wishlist-tbody">
                <?php if (!empty($items)): ?>
                    <?php foreach ($items as $it): ?>
                        <tr class="order-row wishlist-row">
                            <td class="wishlist-cell wishlist-cell--product">
                                <div class="wishlist-product">
                                    <img class="wishlist-product__img" src="../../<?= htmlspecialchars($it->file_path) ?>" alt="<?= htmlspecialchars($it->alt_text) ?>">
                                    <div class="wishlist-product__info">
                                        <div class="wishlist-product__name"><?= htmlspecialchars($it->product_name) ?></div>
                                        <small class="wishlist-product__meta">Product ID: <?= (int)$it->product_id ?></small>
                                    </div>
                                </div>
                            </td>
                            <td class="price-total wishlist-cell wishlist-cell--price">RM <?= number_format((float)$it->unit_price, 2) ?></td>
                            <td class="wishlist-cell wishlist-cell--added"><small><?= htmlspecialchars($it->created_at) ?></small></td>
                            <td class="actions wishlist-actions wishlist-cell wishlist-cell--actions">
                                <a class="btn-view wishlist-actions__btn" href="<?= $rootDir ?>/pages/product/product-detail.php?id=<?= (int)$it->product_id ?>">View</a>
                                <form method="POST" class="wishlist-actions__form">
                                    <input type="hidden" name="remove_product_id" value="<?= (int)$it->product_id ?>">
                                    <button type="submit" class="btn-view wishlist-actions__btn" onclick="return confirm('Remove from wishlist?')">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="empty-state">Your wishlist is empty.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php include '../../_foot.php'; ?>