<?php
require "../_base.php";
require "../DAO/wishlist_dao.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['toggle_wishlist'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

$user_id = temp('USER_ID');
if (!$user_id || $user_id === 'Guest') {
    echo json_encode([
        'success' => false,
        'error' => 'LOGIN_REQUIRED',
        'message' => 'Please login to use Wishlist.'
    ]);
    exit;
}

$product_id = (int)($_POST['product_id'] ?? 0);
if ($product_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid product id']);
    exit;
}

try {
    $inWishlist = toggleWishlistDao((string)$user_id, $product_id);
    echo json_encode(['success' => true, 'in_wishlist' => $inWishlist]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}
exit;
