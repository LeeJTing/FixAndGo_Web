<?php
require '../_base.php';
require '../DAO/product_dao.php';

$function = $_GET['function'];

if ($function == 'allProduct') {

    $category_code = $_GET['category'] ?? "";;
    $sortBy = $_GET['sort'] ?? "";;
    $priceValue = $_GET['price'] ?? "";

    $result = getAllProductFilterDao($category_code, $sortBy, $priceValue);
    echo json_encode($result);
    exit;
}

function getProductList()
{
    return getProductListDao();
}
