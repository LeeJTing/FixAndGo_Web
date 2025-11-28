<?php
require '../../DAO/product_dao.php';

function getAdminProducts()
{
    $products = getProductForDisplay();
    return $products;
}
