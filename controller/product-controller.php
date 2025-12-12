<?php
require_once __DIR__ . '/../_base.php';
require __DIR__ . '/../DAO/product_dao.php';

$function = $_GET['function'] ?? null;;

if ($function == 'allProduct') {

    $category_code = $_GET['category'] ?? "";;
    $sortBy = $_GET['sort'] ?? "";;
    $priceValue = $_GET['price'] ?? "";

    $result = getAllProductFilterDao($category_code, $sortBy, $priceValue);
    echo json_encode($result);
    exit;
} else if ($function == 'Search') {

    $search_value = $_GET['search'] ?? '';  // prevent warning too
    $result = getProductBySearchDao($search_value);

    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
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
    return getAllCategoryDao();
}

function getProductImages($id)
{
    return getProductImagesDao($id);
}
