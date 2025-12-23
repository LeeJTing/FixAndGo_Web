<?php
function getCartItems($cart_id)
{
    global $_db;
    $stmt = $_db->prepare("
        SELECT ci.*, p.product_name, p.unit_price, pvm.file_path 
        FROM cartitem ci
        JOIN product p ON ci.product_id = p.product_id
        LEFT JOIN productvisualmedia pvm ON p.product_id = pvm.product_id AND pvm.is_show = 1
        WHERE ci.cart_id = ?
    ");
    $stmt->execute([$cart_id]);
    return $stmt->fetchAll();
}

function addToCart($cart_id, $product_id, $quantity = 1)
{
    global $_db;

    $stmt = $_db->prepare("SELECT * FROM cartitem WHERE cart_id = ? AND product_id = ?");
    $stmt->execute([$cart_id, $product_id]);
    $existing = $stmt->fetch();

    if ($existing) {
        $stmt = $_db->prepare("UPDATE cartitem SET qty = qty + ? WHERE cart_id = ? AND product_id = ?");
        return $stmt->execute([$quantity, $cart_id, $product_id]);
    } else {
        $stmt = $_db->prepare("INSERT INTO cartitem (cart_id, product_id, qty, is_check, is_take) VALUES (?, ?, ?, 1, 1)");
        return $stmt->execute([$cart_id, $product_id, $quantity]);
    }
}

function updateCartItem($item_id, $quantity)
{
    global $_db;
    $stmt = $_db->prepare("UPDATE cartitem SET qty = ? WHERE item_id = ?");
    return $stmt->execute([$quantity, $item_id]);
}

function updateCartItemTake($item_id, $is_take)
{
    global $_db;
    $stmt = $_db->prepare("UPDATE cartitem SET is_take = ? WHERE item_id = ?");
    return $stmt->execute([$is_take, $item_id]);
}

function updateCartItemCheck($item_id, $is_check)
{
    global $_db;
    $stmt = $_db->prepare("UPDATE cartitem SET is_check = ? WHERE item_id = ?");
    return $stmt->execute([$is_check, $item_id]);
}

function removeFromCart($item_id)
{
    global $_db;
    $stmt = $_db->prepare("DELETE FROM cartitem WHERE item_id = ?");
    return $stmt->execute([$item_id]);
}

function removeFromCartItems($item_ids)
{
    global $_db;

    if (!is_array($item_ids)) {
        return 0;
    }

    $clean_ids = [];
    foreach ($item_ids as $id) {
        $id = (int)$id;
        if ($id > 0) {
            $clean_ids[] = $id;
        }
    }

    if (empty($clean_ids)) {
        return 0;
    }

    $placeholders = implode(',', array_fill(0, count($clean_ids), '?'));
    $stmt = $_db->prepare("DELETE FROM cartitem WHERE item_id IN ($placeholders)");
    $stmt->execute($clean_ids);
    return $stmt->rowCount();
}

function getCartByUserId($user_id)
{
    global $_db;
    $stmt = $_db->prepare("SELECT cart_id FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch();
}
function getCheckedCartItems($cart_id)
{
    global $_db;
    $stmt = $_db->prepare("
        SELECT ci.*, p.product_name, p.unit_price, p.stock_quantity
        FROM cartitem ci
        JOIN product p ON ci.product_id = p.product_id
        WHERE ci.cart_id = ? AND ci.is_check = 1
    ");
    $stmt->execute([$cart_id]);
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

function clearCheckedCartItems($cart_id)
{
    global $_db;
    $stmt = $_db->prepare("DELETE FROM cartitem WHERE cart_id = ? AND is_check = 1");
    return $stmt->execute([$cart_id]);
}
