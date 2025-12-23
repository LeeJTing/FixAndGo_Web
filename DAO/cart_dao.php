<?php
function getCartItems($cart_id)
{
    global $_db;
    $stmt = $_db->prepare("
        SELECT ci.*, p.product_name, p.unit_price, p.stock_quantity, p.isdeleted, p.status, pvm.file_path 
        FROM cartitem ci
        JOIN product p ON ci.product_id = p.product_id
        LEFT JOIN productvisualmedia pvm ON p.product_id = pvm.product_id AND pvm.is_show = 1
        WHERE ci.cart_id = ?
    ");
    $stmt->execute([$cart_id]);
    return $stmt->fetchAll();
}

function getProductStockQuantityDao(int $product_id): int
{
    global $_db;
    $stmt = $_db->prepare('SELECT stock_quantity FROM product WHERE product_id = ? LIMIT 1');
    $stmt->execute([$product_id]);
    $v = $stmt->fetchColumn();
    return (int)($v ?? 0);
}

function getProductAvailabilityInfoDao(int $product_id): array
{
    global $_db;
    $stmt = $_db->prepare('SELECT stock_quantity, isdeleted, status FROM product WHERE product_id = ? LIMIT 1');
    $stmt->execute([$product_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        return ['exists' => false, 'available' => false, 'stock' => 0];
    }
    $isDeleted = (int)($row['isdeleted'] ?? 0) === 1;
    $status = strtolower((string)($row['status'] ?? 'active'));
    $available = (!$isDeleted) && ($status === 'active');
    return ['exists' => true, 'available' => $available, 'stock' => (int)($row['stock_quantity'] ?? 0)];
}

function getCartItemStockInfoDao(int $item_id): ?object
{
    global $_db;
    $stmt = $_db->prepare(
        'SELECT ci.item_id, ci.product_id, ci.qty, p.stock_quantity, p.isdeleted, p.status
         FROM cartitem ci
         JOIN product p ON p.product_id = ci.product_id
         WHERE ci.item_id = ?
         LIMIT 1'
    );
    $stmt->execute([$item_id]);
    $row = $stmt->fetch(PDO::FETCH_OBJ);
    return $row ?: null;
}

function addToCart($cart_id, $product_id, $quantity = 1)
{
    global $_db;

    $product_id = (int)$product_id;
    $quantity = (int)$quantity;
    if ($quantity < 1) $quantity = 1;

    $avail = getProductAvailabilityInfoDao($product_id);
    if (empty($avail['exists']) || empty($avail['available'])) {
        return [
            'success' => false,
            'error' => 'PRODUCT_UNAVAILABLE',
            'stock' => (int)($avail['stock'] ?? 0),
            'quantity' => 0,
            'capped' => true,
        ];
    }

    $stock = (int)($avail['stock'] ?? 0);
    if ($stock <= 0) {
        return [
            'success' => false,
            'error' => 'OUT_OF_STOCK',
            'stock' => 0,
            'quantity' => 0,
            'capped' => true,
        ];
    }

    $stmt = $_db->prepare("SELECT * FROM cartitem WHERE cart_id = ? AND product_id = ?");
    $stmt->execute([$cart_id, $product_id]);
    $existing = $stmt->fetch();

    if ($existing) {
        $existingQty = 0;
        if (is_array($existing)) {
            $existingQty = (int)($existing['qty'] ?? 0);
        } elseif (is_object($existing)) {
            $existingQty = (int)($existing->qty ?? 0);
        }
        $newQty = $existingQty + $quantity;
        $appliedQty = min($newQty, $stock);

        if ($appliedQty === $existingQty) {
            return [
                'success' => true,
                'stock' => $stock,
                'quantity' => $existingQty,
                'capped' => true,
            ];
        }

        $stmt = $_db->prepare("UPDATE cartitem SET qty = ? WHERE cart_id = ? AND product_id = ?");
        $ok = $stmt->execute([$appliedQty, $cart_id, $product_id]);
        return [
            'success' => (bool)$ok,
            'stock' => $stock,
            'quantity' => $appliedQty,
            'capped' => ($appliedQty < $newQty),
        ];
    } else {
        $appliedQty = min($quantity, $stock);
        $stmt = $_db->prepare("INSERT INTO cartitem (cart_id, product_id, qty, is_check, is_take) VALUES (?, ?, ?, 1, 1)");
        $ok = $stmt->execute([$cart_id, $product_id, $appliedQty]);
        return [
            'success' => (bool)$ok,
            'stock' => $stock,
            'quantity' => $appliedQty,
            'capped' => ($appliedQty < $quantity),
        ];
    }
}

function updateCartItem($item_id, $quantity)
{
    global $_db;

    $item_id = (int)$item_id;
    $quantity = (int)$quantity;
    if ($quantity < 1) $quantity = 1;

    $info = getCartItemStockInfoDao($item_id);
    if (!$info) {
        return [
            'success' => false,
            'error' => 'ITEM_NOT_FOUND',
        ];
    }

    $isDeleted = (int)($info->isdeleted ?? 0) === 1;
    $status = strtolower((string)($info->status ?? 'active'));
    if ($isDeleted || $status !== 'active') {
        return [
            'success' => false,
            'error' => 'PRODUCT_UNAVAILABLE',
            'stock' => (int)($info->stock_quantity ?? 0),
            'quantity' => (int)($info->qty ?? 1),
        ];
    }

    $stock = (int)($info->stock_quantity ?? 0);
    if ($stock <= 0) {
        return [
            'success' => false,
            'error' => 'OUT_OF_STOCK',
            'stock' => 0,
            'quantity' => (int)($info->qty ?? 1),
        ];
    }

    $appliedQty = min($quantity, $stock);
    $stmt = $_db->prepare("UPDATE cartitem SET qty = ? WHERE item_id = ?");
    $ok = $stmt->execute([$appliedQty, $item_id]);
    return [
        'success' => (bool)$ok,
        'stock' => $stock,
        'quantity' => $appliedQty,
        'capped' => ($appliedQty < $quantity),
    ];
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
        SELECT ci.*, p.product_name, p.unit_price, p.stock_quantity, p.isdeleted, p.status
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
