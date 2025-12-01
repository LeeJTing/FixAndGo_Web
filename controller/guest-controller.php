<?php

require __DIR__ . '/../DAO/product_dao.php';

function getGuestAllProduct()
{
    return getAllProduct();
}

function getGuestAllCategory()
{
    return getAllCategory();
}

function getGuestFilterItemByCategory() {}
