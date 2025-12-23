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
                LEFT JOIN users u ON o.user_id = u.user_id
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

function getOrderByIdDao($id)
{
    global $_db;

    try {
        $sql = "SELECT 
                    o.order_id,
                    o.address_id,
                    o.deliver_at,
                    o.user_id,
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
                LEFT JOIN users u ON o.user_id = u.user_id
                LEFT JOIN orderitem oi ON o.order_id = oi.order_id
                WHERE o.order_id = ?
                GROUP BY o.order_id
                ORDER BY o.order_at ASC";

        $stmt = $_db->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_OBJ); // Changed to fetchAll
    } catch (PDOException $e) {
        error_log("Get All Orders Admin Error: " . $e->getMessage());
        return [];
    }
}

function cancelCustomerOrder($order_id, $user_id)
{
    global $_db;

    try {
        $_db->beginTransaction();

        // 1. Update order status
        $stmt = $_db->prepare("
            UPDATE orders 
            SET status = 'Cancelled', 
                payment_status = 'Refunded'
            WHERE order_id = ? 
              AND user_id = ? 
              AND status IN ('Pending', 'Processing')
        ");
        $stmt->execute([$order_id, $user_id]);

        if ($stmt->rowCount() === 0) {
            throw new Exception("Order cannot be cancelled (already processed or not yours).");
        }

        // 2. Restore stock
        $stmt = $_db->prepare("
            UPDATE product p
            JOIN orderitem oi ON p.product_id = oi.product_id
            SET p.stock_quantity = p.stock_quantity + oi.qty
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$order_id]);

        $_db->commit();

        return ['success' => true, 'message' => 'Order cancelled and stock restored.'];
    } catch (Exception $e) {
        $_db->rollBack();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function getOrderDetailsAdmin(int $orderId)
{
    global $_db;
}

function getAllProductByOrderId($order_id)
{
    global $_db;
    try {
        $sql = "
    SELECT 
        oi.order_id,
        oi.product_id,
        pr.product_name,
        oi.qty,
        oi.unit_price,
        pvm.file_path AS product_image,
        pr.short_desc AS spec
    FROM orderitem oi
    INNER JOIN product pr 
        ON oi.product_id = pr.product_id
    LEFT JOIN productvisualmedia pvm 
        ON pvm.product_id = pr.product_id
        AND pvm.is_show = 1
        AND pvm.position = 0
    WHERE oi.order_id = ?
";
        $stmt = $_db->prepare($sql);
        $stmt->execute([$order_id]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        error_log("Get Member Order History Error: " . $e->getMessage());
        return false;
    }
}

function getOrderWithSelectedAddress(int $orderId)
{
    global $_db;

    $sql = "
        SELECT
            o.order_id,
            o.order_at,
            o.user_id,
            u.user_name,
            u.email,
            o.total_price,
            o.payment_status,
            o.status,
            o.utilize_point,
            a.address_name,
            a.address_one,
            a.address_two,
            a.address_three,
            a.state,
            a.post_code,
            a.country
        FROM orders o
        INNER JOIN users u ON o.user_id = u.user_id
        INNER JOIN address a ON o.address_id = a.address_id
        WHERE o.order_id = ?
    ";

    $stmt = $_db->prepare($sql);
    $stmt->execute([$orderId]);

    return $stmt->fetch(PDO::FETCH_OBJ);
}

function getMemberAddressDao($user_id, $order_id = null)
{
    global $_db;

    // Base query: Select all addresses for this user
    $sql = "SELECT a.* FROM address a 
            WHERE a.user_id = ?";

    $params = [$user_id];

    if ($order_id) {
        $sql .= " AND a.address_id NOT IN (
                    SELECT address_id FROM orders WHERE order_id = ?
                  )";
        $params[] = $order_id;
    }

    $stmt = $_db->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

function updateOrderAddress($order_id, $new_address_id)
{
    global $_db;

    // Optional: Update 'updated_at' timestamp if you have that column
    $sql = "UPDATE orders 
            SET address_id = ? 
            WHERE order_id = ?";

    $stmt = $_db->prepare($sql);

    // Returns true on success, false on failure
    return $stmt->execute([$new_address_id, $order_id]);
}

function updateDeliveryDate($order_id, $deliver_at)
{
    global $_db;

    $sql = "UPDATE orders SET deliver_at = ? WHERE order_id = ?";
    $stmt = $_db->prepare($sql);

    return $stmt->execute([$deliver_at, $order_id]);
}

function cancelOrder($order_id)
{
    global $_db;

    try {
        $_db->beginTransaction(); // Start a transaction

        // 2. Update order status to 'cancelled'
        $sqlOrder = "UPDATE orders SET status = 'Cancelled' WHERE order_id = ?";
        $stmtOrder = $_db->prepare($sqlOrder);
        $stmtOrder->execute([$order_id]);

        // 3. (Optional) Restore inventory if you have stock management
        $sqlRestoreStock = "UPDATE product p 
                            JOIN orderitem oi ON p.product_id = oi.product_id 
                            SET p.stock_quantity = p.stock_quantity + oi.qty
                            WHERE oi.order_id = ?";
        $stmtStock = $_db->prepare($sqlRestoreStock);
        $stmtStock->execute([$order_id]);

        $_db->commit(); // Save changes
        return ['success' => true, 'message' => 'Order cancelled successfully'];
    } catch (Exception $e) {
        $_db->rollBack(); // Undo if something goes wrong
        error_log("Order cancellation failed: " . $e->getMessage());
        return ['success' => false, 'message' => 'Failed to cancel order: ' . $e->getMessage()];
    }
}

function getMemberOrderHistory($id)
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
                    MAX(p.payment_method) AS payment_method,
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
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        error_log("Get Member Order History Error: " . $e->getMessage());
        return false;
    }
}
function getSpecificPaymentDao($order_id)
{
    global $_db;

    try {
        $stmt = $_db->prepare("
            SELECT payment_id, order_id, payment_method, paid_at
            FROM payment
            WHERE order_id = ?
            LIMIT 1
        ");
        $stmt->execute([$order_id]);
        return $stmt->fetch(PDO::FETCH_OBJ); // returns object or false if not found
    } catch (PDOException $e) {
        error_log("Get Payment Error: " . $e->getMessage());
        return false;
    }
}

function createPaymentPendingDao($order_id, $payment_method)
{
    global $_db;
    $map = [
        'Cash' => 'Cash',
        'Cash on Delivery' => 'Cash', 
        'Credit/Debit Card' => 'Credit Card',
        'Online Banking' => 'Bank Transfer',
        'Loyalty Points' => 'Loyalty Points', 
    ];
    $pm = $map[$payment_method] ?? 'Cash';

    $stmt = $_db->prepare("
        INSERT INTO payment (order_id, payment_method, paid_at)
        VALUES (?, ?, NULL)
    ");
    return $stmt->execute([$order_id, $pm]);
}

function getOrderDetailsForUserDao($order_id, $user_id)
{
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

function markOrderPaidDao($order_id, $user_id)
{
    global $_db;
    $stmt = $_db->prepare("UPDATE orders SET payment_status='Paid', status='Processing' WHERE order_id=? AND user_id=?");
    return $stmt->execute([$order_id, $user_id]);
}

function markPaymentPaidDao($order_id)
{
    global $_db;
    $stmt = $_db->prepare("UPDATE payment SET paid_at = NOW() WHERE order_id = ?");
    return $stmt->execute([$order_id]);
}
function saveStripeSessionIdDao($order_id, $stripe_session_id)
{
    global $_db;
    $stmt = $_db->prepare("UPDATE payment SET stripe_session_id=? WHERE order_id=?");
    return $stmt->execute([$stripe_session_id, $order_id]);
}

function saveStripePaymentIntentIdDao($order_id, $pi)
{
    global $_db;
    $stmt = $_db->prepare("UPDATE payment SET stripe_payment_intent_id=? WHERE order_id=?");
    return $stmt->execute([$pi, $order_id]);
}

function paymentExistsForOrderDao($order_id)
{
    global $_db;
    $stmt = $_db->prepare("SELECT 1 FROM payment WHERE order_id=? LIMIT 1");
    $stmt->execute([$order_id]);
    return (bool)$stmt->fetchColumn();
}

function createOrderDao($user_id, $address_id, $total_price, $payment_status = 'Pending', $status = 'Pending', $utilize_point = 0)
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
    // must match enum in DB: Cash / Credit Card / Debit Card / Bank Transfer :contentReference[oaicite:3]{index=3}
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
