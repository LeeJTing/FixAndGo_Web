<?php

function getOrderHistoryAdminDao($id)
{
    global $_db;

    try {
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
        $stmt->execute([$id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Get Order History Error: " . $e->getMessage());
        return false;
    }
}

function getOrderItemsDao($order_id)
{
    global $_db;

    try {
        $sql = "SELECT 
                    o.order_id,
                    o.order_at,
                    o.payment_status,
                    o.status AS order_status,
                    o.total_price,

                    oi.product_id,
                    p.product_name,
                    oi.qty,
                    oi.unit_price,
                    (oi.qty * oi.unit_price) AS subtotal,

                    pvm.file_path AS product_image
                FROM orders o
                INNER JOIN orderitem oi ON o.order_id = oi.order_id
                INNER JOIN product p ON oi.product_id = p.product_id
                LEFT JOIN productvisualmedia pvm 
                       ON p.product_id = pvm.product_id
                      AND pvm.position = 0
                      AND pvm.is_show = 1
                WHERE o.order_id = ?";

        $stmt = $_db->prepare($sql);
        $stmt->execute([$order_id]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        error_log("Get Order Items Error: " . $e->getMessage());
        return [];
    }
}

function updateOrderStatus($order_id, $new_status)
{
    global $_db;

    try {
        $sql = "UPDATE orders 
                SET status = ? 
                WHERE order_id = ?";

        $stmt = $_db->prepare($sql);
        $result = $stmt->execute([$new_status, $order_id]);

        return $result;
    } catch (PDOException $e) {
        error_log("Update Order Status Error: " . $e->getMessage());
        return false;
    }
}


function getAllOrdersAdmin()
{
    global $_db;

    try {
        $sql = "SELECT 
                    o.order_id,
                    o.order_at,
                    u.user_name,
                    u.email,
                    o.total_price,
                    o.payment_status,
                    o.status,
                    o.utilize_point,
                    COUNT(DISTINCT oi.product_id) AS total_items,
                    SUM(oi.qty) AS total_quantity
                FROM orders o
                INNER JOIN users u ON o.user_id = u.user_id
                LEFT JOIN orderitem oi ON o.order_id = oi.order_id
                GROUP BY o.order_id
                ORDER BY o.order_at ASC";

        $stmt = $_db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ); // Changed to fetchAll
    } catch (PDOException $e) {
        error_log("Get All Orders Admin Error: " . $e->getMessage());
        return [];
    }
}

function getOrderDetailsAdmin($order_id)
{
    global $_db;

    try {
        $sql = "SELECT 
                    o.order_id,
                    o.order_at,
                    o.user_id,
                    u.user_name,
                    u.email,
                    up.contact_num,
                    o.total_price,
                    o.payment_status,
                    o.status,
                    o.utilize_point,
                    p.payment_method,
                    p.paid_at,
                    a.address_name,
                    a.address_one,
                    a.address_two,
                    a.address_three,
                    a.state,
                    a.post_code,
                    a.country
                FROM orders o
                INNER JOIN users u ON o.user_id = u.user_id
                LEFT JOIN userprofile up ON u.user_id = up.user_id
                LEFT JOIN payment p ON o.order_id = p.order_id
                LEFT JOIN address a ON o.address_id = a.address_id
                WHERE o.order_id = ?";

        $stmt = $_db->prepare($sql);
        $stmt->execute([$order_id]);

        return $stmt->fetch(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        error_log("Get Order Details Admin Error: " . $e->getMessage());
        return false;
    }
}

function getMemberOrderHistory($user_id)
{
    global $_db;

    try {
        $sql = "SELECT 
                    o.order_id,
                    DATE_FORMAT(o.order_at, '%d %b %Y') AS order_date,
                    DATE_FORMAT(o.order_at, '%h:%i %p') AS order_time,
                    o.total_price,
                    o.payment_status,
                    o.status,
                    COUNT(DISTINCT oi.product_id) AS total_items,
                    SUM(oi.qty) AS total_quantity
                FROM orders o
                LEFT JOIN orderitem oi ON o.order_id = oi.order_id
                LEFT JOIN payment p ON o.order_id = p.order_id
                LEFT JOIN address a ON o.address_id = a.address_id
                WHERE o.user_id = ?
                GROUP BY o.order_id
                ORDER BY o.order_at ASC";

        $stmt = $_db->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        error_log("Get Member Order History Error: " . $e->getMessage());
        return false;
    }
}

function createPaymentPendingDao($order_id, $payment_method) {
    global $_db;
    // payment_method enum in DB: Cash / Credit Card / Debit Card / Bank Transfer / PayPal  (from your SQL)
    // We'll map your UI values into DB values
    $map = [
        'Cash' => 'Cash',
        'Credit/Debit Card' => 'Credit Card',
        'Online Banking' => 'Bank Transfer',
    ];
    $pm = $map[$payment_method] ?? 'Cash';

    $stmt = $_db->prepare("
        INSERT INTO payment (order_id, payment_method, paid_at)
        VALUES (?, ?, NULL)
    ");
    return $stmt->execute([$order_id, $pm]);
}

function getOrderDetailsForUserDao($order_id, $user_id) {
    global $_db;
    $stmt = $_db->prepare("
        SELECT o.*, p.payment_method, p.paid_at,
               a.address_name, a.address_one, a.address_two, a.address_three, a.state, a.post_code, a.country
        FROM orders o
        LEFT JOIN payment p ON o.order_id = p.order_id
        LEFT JOIN address a ON o.address_id = a.address_id
        WHERE o.order_id = ? AND o.user_id = ?
        LIMIT 1
    ");
    $stmt->execute([$order_id, $user_id]);
    return $stmt->fetch(PDO::FETCH_OBJ);
}

function markOrderPaidDao($order_id, $user_id) {
    global $_db;
    $stmt = $_db->prepare("UPDATE orders SET payment_status='Paid', status='Processing' WHERE order_id=? AND user_id=?");
    return $stmt->execute([$order_id, $user_id]);
}

function markPaymentPaidDao($order_id) {
    global $_db;
    $stmt = $_db->prepare("UPDATE payment SET paid_at = NOW() WHERE order_id = ?");
    return $stmt->execute([$order_id]);
}
function saveStripeSessionIdDao($order_id, $stripe_session_id) {
  global $_db;
  $stmt = $_db->prepare("UPDATE payment SET stripe_session_id=? WHERE order_id=?");
  return $stmt->execute([$stripe_session_id, $order_id]);
}

function saveStripePaymentIntentIdDao($order_id, $pi) {
  global $_db;
  $stmt = $_db->prepare("UPDATE payment SET stripe_payment_intent_id=? WHERE order_id=?");
  return $stmt->execute([$pi, $order_id]);
}

function paymentExistsForOrderDao($order_id) {
  global $_db;
  $stmt = $_db->prepare("SELECT 1 FROM payment WHERE order_id=? LIMIT 1");
  $stmt->execute([$order_id]);
  return (bool)$stmt->fetchColumn();
}

function createOrderDao($user_id, $address_id, $total_price, $payment_status='Pending', $status='Pending', $utilize_point=0)
{
    global $_db;
    $stmt = $_db->prepare("
        INSERT INTO orders (user_id, payment_status, status, total_price, utilize_point, address_id)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$user_id, $payment_status, $status, $total_price, $utilize_point, $address_id]);
    return (int)$_db->lastInsertId();
}

function addOrderItemDao($order_id, $product_id, $qty, $unit_price)
{
    global $_db;
    $stmt = $_db->prepare("
        INSERT INTO orderitem (order_id, product_id, qty, unit_price)
        VALUES (?, ?, ?, ?)
    ");
    return $stmt->execute([$order_id, $product_id, $qty, $unit_price]);
}

function getOrderByIdAndUserDao($order_id, $user_id)
{
    global $_db;
    $stmt = $_db->prepare("SELECT * FROM orders WHERE order_id=? AND user_id=? LIMIT 1");
    $stmt->execute([$order_id, $user_id]);
    return $stmt->fetch(PDO::FETCH_OBJ);
}

function getPaymentRowByOrderIdDao($order_id)
{
    global $_db;
    $stmt = $_db->prepare("SELECT * FROM payment WHERE order_id=? LIMIT 1");
    $stmt->execute([$order_id]);
    return $stmt->fetch(PDO::FETCH_OBJ);
}

function markOrderPaidByOrderIdDao($order_id)
{
    global $_db;
    $stmt = $_db->prepare("UPDATE orders SET payment_status='Paid', status='Processing' WHERE order_id=?");
    return $stmt->execute([$order_id]);
}

function insertPaymentForOrderDao($order_id, $payment_method_enum_value)
{
    global $_db;
    // must match enum in your DB: Cash / Credit Card / Debit Card / Bank Transfer / PayPal :contentReference[oaicite:3]{index=3}
    $stmt = $_db->prepare("INSERT INTO payment (order_id, payment_method) VALUES (?, ?)");
    return $stmt->execute([$order_id, $payment_method_enum_value]);
}
function getOrderWithAddressByIdAndUserDao($order_id, $user_id)
{
    global $_db;
    $stmt = $_db->prepare("
        SELECT o.*,
               a.address_name, a.address_one, a.address_two, a.address_three,
               a.state, a.post_code, a.country
        FROM orders o
        JOIN address a ON a.address_id = o.address_id
        WHERE o.order_id = ? AND o.user_id = ?
        LIMIT 1
    ");
    $stmt->execute([$order_id, $user_id]);
    return $stmt->fetch(PDO::FETCH_OBJ);
}
