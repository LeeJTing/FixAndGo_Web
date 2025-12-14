<?php
require_once  __DIR__ . '/../_base.php';
require __DIR__ . '/../DAO/order-dao.php';

$order_id = get('order_id');
$user_id = get('user_id');

function getOrderHistoryAdmin()
{
    return getAllOrdersAdmin();
}

function getOrderItem($id)
{
    return getOrderItemsDao($id);
}

function getOrderHistoryMember($id)
{
    return getMemberOrderHistory($id);
}
