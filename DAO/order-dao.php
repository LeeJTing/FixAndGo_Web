<?php

function getOrderHistory($id)
{
    global $_db;

    $sql = "SELECT 
                o.order_id,
                o.order_at,
                o.total_price,
                o.payment_status,
                o.status,
                o.utilize_point
            FROM orders o
            WHERE o.user_id = ?
            ORDER BY o.order_at DESC";
    $stmt = $_db->prepare($sql);
    return $stmt->execute([$id]);
}

function getOrderItem() {}
