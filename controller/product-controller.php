<?php
require_once __DIR__ . '/../_base.php';
require __DIR__ . '/../DAO/product_dao.php';

$function = $_GET['function'] ?? null;;

if ($function === 'allProduct') {

    $category_code = $_GET['category'] ?? "";
    $sortBy        = $_GET['sort'] ?? "";
    $priceValue    = $_GET['price'] ?? "";

    $page  = max(1, (int)($_GET['page'] ?? 1));
    $limit = (int)($_GET['limit'] ?? 8);

    $offset = ($page - 1) * $limit;

    $products = getAllProductFilterDao(
        $category_code,
        $sortBy,
        $priceValue,
        $limit,
        $offset
    );

    $totalRows = getAllProductFilterCountDao(
        $category_code,
        $priceValue
    );

    echo json_encode([
        'data' => $products,
        'totalPages' => ceil($totalRows / $limit)
    ]);
    exit;
} else if ($function == 'Search') {

    $search_value = $_GET['search'] ?? '';  // prevent warning too
    $result = getProductBySearchDao($search_value);

    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
} // In product-controller.php
else if ($function === 'clickCategory') {
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
