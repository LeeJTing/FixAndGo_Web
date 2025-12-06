<?php

/**
 * Get all products with category name
 */
function getAllProductFilterDao($category, $sort, $price)
{
    global $_db;
    $sql = "SELECT p.*, c.category_name, pvm.file_path, pvm.alt
            FROM product p
            JOIN category c ON p.category_code = c.category_code
            LEFT JOIN productvisualmedia pvm ON pvm.product_id = p.product_id
            WHERE 1=1";
    $param = [];

    if (!empty($category)) {
        $sql .= " AND p.category_code = ?";
        $param[] = $category;
    }

    if ($price != "") {
        $sql .= " AND p.unit_price <= ?";
        $param[] = $price;
    }

    if ($sort == "LowtoHigh") {
        $sql .= " ORDER BY p.unit_price ASC";
    } else if ($sort == "HightoLow") {
        $sql .= " ORDER BY p.unit_price DESC";
    } else if ($sort == "newest") {
        $sql .= " AND p.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
    } else {
        $sql .= " ORDER BY p.product_id ASC";
    }

    $stmt = $_db->prepare($sql);
    $stmt->execute($param);
    return $stmt->fetchAll();
}

function getCountAllProduct()
{
    global $_db;
    $sql = "SELECT COUNT(*) AS items FROM product";
    $stmt = $_db->query($sql);
    return (int)$stmt->fetch()->items;
}

/**
 * Get products for display on homepage/shop (with main image)
 */
function getProductListDao()
{
    global $_db;
    $sql = "SELECT DISTINCT
                p.product_id,
                p.product_name,
                p.unit_price,
                p.stock_quantity,
                p.product_point,
                p.description,
                c.category_name,
                c.category_code,
                pvm.file_path,
                COALESCE(pvm.alt, p.product_name) AS alt_text
            FROM product p
            JOIN category c ON p.category_code = c.category_code
            JOIN productvisualmedia pvm ON pvm.product_id = p.product_id 
                AND pvm.is_show = 1 
            ORDER BY p.product_id ASC";

    $stmt = $_db->query($sql);
    return $stmt->fetchAll();
}

/**
 * Get single product by ID (for product detail page)
 */
function getProductById($id)
{
    global $_db;
    $stmt = $_db->prepare("SELECT 
                            p.*, 
                            c.category_name,
                            pvm.file_path,
                            pvm.alt
                           FROM product p 
                           JOIN category c ON p.category_code = c.category_code 
                           JOIN productvisualmedia pvm ON pvm.product_id = p.product_id
                           WHERE p.product_id = ? 
                           LIMIT 1");
    $stmt->execute([$id]);
    return $stmt->fetch();  // Return false if not found
}

function getProductImageById($id)
{
    global $_db;
    $stmt = $_db->prepare("SELECT file_path, alt 
                           FROM productvisualmedia 
                           WHERE product_id = ? 
                           ");
    $stmt->execute([$id]);
    return $stmt->fetchAll(); // Returns object (consistent with your style)
}

function getProductReviews($product_id)
{
    global $_db;
    $stmt = $_db->prepare("
        SELECT r.*, c.name AS customer_name 
        FROM reviews r 
        LEFT JOIN customers c ON r.customer_id = c.customer_id 
        WHERE r.product_id = ? AND r.is_approved = 1 
        ORDER BY r.created_at DESC
    ");
    $stmt->execute([$product_id]);
    return $stmt->fetchAll();
}

/**
 * Get all images for a product (for detail page gallery)
 */
function getProductImages($product_id)
{
    global $_db;
    $stmt = $_db->prepare("SELECT file_path, alt, position 
                           FROM productvisualmedia 
                           WHERE product_id = ? AND is_show = 1 
                           ORDER BY position ASC");
    $stmt->execute([$product_id]);
    return $stmt->fetchAll();
}

/**
 * Get products by category code
 */
function getProductsByCategoryDao($category_code)
{
    global $_db;
    $stmt = $_db->prepare("SELECT p.*, c.category_name,
                            pvm.file_path,
                            pvm.alt
                           FROM product p 
                           JOIN category c ON p.category_code = c.category_code 
                           JOIN productvisualmedia pvm ON pvm.product_id = p.product_id
                           WHERE p.category_code = ? 
                           ORDER BY p.product_name ASC");
    $stmt->execute([$category_code]);
    return $stmt->fetchAll();
}

/**
 * Search products by name or description
 */
function searchProducts($keyword)
{
    global $_db;
    $keyword = "%$keyword%";
    $stmt = $_db->prepare("SELECT p.*, c.category_name 
                           FROM product p 
                           JOIN category c ON p.category_code = c.category_code 
                           WHERE p.product_name LIKE ? 
                              OR p.short_desc LIKE ? 
                              OR p.description LIKE ?
                           ORDER BY p.product_name");
    $stmt->execute([$keyword, $keyword, $keyword]);
    return $stmt->fetchAll();
}

function getAllCategoryDAO()
{
    global $_db;
    $stmt = $_db->query("SELECT * FROM category");
    return $stmt->fetchAll();
}

function updateProductById($id, $data)
{
    global $_db;

    $name         = trim($data['product_name'] ?? '');
    $category     = intval(trim($data['category_code'] ?? ''));   // ← could be code or ID
    $price        = floatval($data['unit_price'] ?? 0);
    $qty          = intval($data['stock_quantity'] ?? 0);
    $points       = intval($data['product_point'] ?? 0);
    $short_desc   = trim($data['short_desc'] ?? '');
    $desc         = trim($data['description'] ?? '');

    $sql = "UPDATE product SET
                product_name     = ?,
                category_code    = ?,    
                unit_price       = ?,
                stock_quantity   = ?,
                product_point    = ?,
                short_desc       = ?,
                description      = ?
            WHERE product_id = ?";

    try {
        $stmt = $_db->prepare($sql);
        $executed = $stmt->execute([
            $name,
            $category,
            $price,
            $qty,
            $points,
            $short_desc,
            $desc,
            $id
        ]);

        $changed = $stmt->rowCount() > 0;

        if ($executed && $changed) {
            return ['success' => true];
        } else {
            return ['success' => false, 'errors' => ['No changes made (data same as before)']];
        }
    } catch (PDOException $e) {
        $errorMsg = $e->getMessage();
        return ['success' => false, 'errors' => [$errorMsg]];
    }
}
