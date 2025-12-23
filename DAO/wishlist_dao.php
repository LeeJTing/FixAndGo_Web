<?php

function isProductInWishlistDao(string $user_id, int $product_id): bool
{
    global $_db;
    $stmt = $_db->prepare('SELECT 1 FROM wishlist WHERE user_id = ? AND product_id = ? LIMIT 1');
    $stmt->execute([$user_id, $product_id]);
    return (bool)$stmt->fetchColumn();
}

function getWishlistProductIdsForUserDao(string $user_id, array $product_ids): array
{
    global $_db;

    $clean = [];
    foreach ($product_ids as $id) {
        $id = (int)$id;
        if ($id > 0) $clean[] = $id;
    }

    if (empty($clean)) return [];

    $placeholders = implode(',', array_fill(0, count($clean), '?'));
    $sql = "SELECT product_id FROM wishlist WHERE user_id = ? AND product_id IN ($placeholders)";

    $params = array_merge([$user_id], $clean);
    $stmt = $_db->prepare($sql);
    $stmt->execute($params);

    return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
}

function addToWishlistDao(string $user_id, int $product_id): bool
{
    global $_db;

    // Insert-if-not-exists to avoid duplicate key errors
    $stmt = $_db->prepare('INSERT IGNORE INTO wishlist (user_id, product_id) VALUES (?, ?)');
    return $stmt->execute([$user_id, $product_id]);
}

function removeFromWishlistDao(string $user_id, int $product_id): bool
{
    global $_db;
    $stmt = $_db->prepare('DELETE FROM wishlist WHERE user_id = ? AND product_id = ?');
    return $stmt->execute([$user_id, $product_id]);
}

function toggleWishlistDao(string $user_id, int $product_id): bool
{
    if (isProductInWishlistDao($user_id, $product_id)) {
        removeFromWishlistDao($user_id, $product_id);
        return false;
    }

    addToWishlistDao($user_id, $product_id);
    return true;
}

function getWishlistItemsByUserIdDao(string $user_id): array
{
    global $_db;

    $stmt = $_db->prepare(
        "SELECT w.created_at,
                p.product_id,
                p.product_name,
                p.unit_price,
                COALESCE(pvm.file_path, 'images/no-image.jpg') AS file_path,
                COALESCE(pvm.alt, p.product_name) AS alt_text
         FROM wishlist w
         JOIN product p ON p.product_id = w.product_id
         LEFT JOIN productvisualmedia pvm ON pvm.product_id = p.product_id AND pvm.is_show = 1
         WHERE w.user_id = ?
            ORDER BY w.created_at DESC, w.wishlist_id DESC"
    );

    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}
