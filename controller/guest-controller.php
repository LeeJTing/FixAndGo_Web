<?php

require __DIR__ . '/../DAO/product_dao.php';

function getGuestAllProduct()
{
    return getProductListDao();
}

function getGuestAllCategory()
{
    return getAllCategoryGuestDao();
}
