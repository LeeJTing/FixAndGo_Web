<?php
require_once __DIR__ . '/../_base.php';
require __DIR__ . '/../DAO/product_dao.php';

$function = $_GET['function'] ?? null;;

if ($function === 'allProduct') {
    header('Content-Type: application/json');

    $category_code = $_GET['category'] ?? "";
    $sortBy        = $_GET['sort'] ?? "";
    $priceValue    = $_GET['price'] ?? "";
    $searchValue   = $_GET['search'] ?? ""; // NEW: search input

    $page  = max(1, (int)($_GET['page'] ?? 1));
    $limit = (int)($_GET['limit'] ?? 8);
    $offset = ($page - 1) * $limit;

    // Fetch filtered products
    $products = getAllProductFilterDao(
        $category_code,
        $sortBy,
        $priceValue,
        $searchValue, // Pass search
        $limit,
        $offset
    );

    // Attach wishlist state for the current user (if logged in)
    $user_id = temp('USER_ID');
    if (!empty($products)) {
        foreach ($products as $p) {
            $p->is_wishlisted = 0;
        }
    }

    if ($user_id && $user_id !== 'Guest' && !empty($products)) {
        try {
            require_once __DIR__ . '/../DAO/wishlist_dao.php';

            $productIds = array_map(fn($p) => (int)($p->product_id ?? 0), $products);
            $wishIds = getWishlistProductIdsForUserDao((string)$user_id, $productIds);
            $wishSet = array_fill_keys($wishIds, true);

            foreach ($products as $p) {
                $pid = (int)($p->product_id ?? 0);
                $p->is_wishlisted = isset($wishSet[$pid]) ? 1 : 0;
            }
        } catch (Exception $e) {
            error_log('Wishlist lookup failed in allProduct: ' . $e->getMessage());
        }
    }

    // Fetch total rows for pagination (with same filters, including search)
    $totalRows = getAllProductFilterCountDao(
        $category_code,
        $priceValue,
        $searchValue // Pass search to count function
    );

    echo json_encode([
        'data' => $products,
        'totalPages' => ceil($totalRows / $limit)
    ]);
    exit;
} else if ($function == 'Search') {

    $search_value = $_GET['search'] ?? '';  // prevent warning too

    header('Content-Type: application/json');

    $result = getProductBySearchDao($search_value);

    // Keep response shape consistent with allProduct (JS expects res.data)
    // Add wishlist flag if logged in (fail-safe like above)
    $user_id = temp('USER_ID');
    if (!empty($result)) {
        foreach ($result as $p) {
            $p->is_wishlisted = 0;
        }
    }

    if ($user_id && $user_id !== 'Guest' && !empty($result)) {
        try {
            require_once __DIR__ . '/../DAO/wishlist_dao.php';

            $productIds = [];
            foreach ($result as $p) {
                $productIds[] = (int)($p->product_id ?? 0);
            }

            $wishIds = getWishlistProductIdsForUserDao((string)$user_id, $productIds);
            $wishSet = array_fill_keys($wishIds, true);

            foreach ($result as $p) {
                $pid = (int)($p->product_id ?? 0);
                $p->is_wishlisted = isset($wishSet[$pid]) ? 1 : 0;
            }
        } catch (Exception $e) {
            error_log('Wishlist lookup failed in Search: ' . $e->getMessage());
        }
    }

    echo json_encode([
        'data' => $result,
        'totalPages' => 1
    ]);
    exit;
} else if ($function === 'clickCategory') {
    $category = get('category'); // from GET parameter

    if ($category !== null && $category !== '') {
        $_SESSION['selected_category'] = (int)$category; // Sanitize as integer
    } else {
        unset($_SESSION['selected_category']); // Clear if empty
    }

    // Redirect to product list page
    redirect("/pages/product/product-list.php");
}

function getProductById($id)
{
    return getProductByIdDao($id);
}

function getProductList()
{
    return getProductListDao();
}
function getCountAllProduct()
{
    return getCountAllProductDao();
}

function getAllCategory()
{
    return getAllCategoryGuestDao();
}

function getProductImages($id)
{
    return getProductImagesDao($id);
}
function getNewArrivalProduct($limit = 8)
{
    return getNewArrivalProductDao($limit);
}
function getTopSellingProducts($limit)
{
    return getTopSellingProductsDao($limit);
}
