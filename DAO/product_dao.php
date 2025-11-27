<?php

/**
 * Get all products with category name
 */
function getAllProduct()
{
    global $_db;
    $sql = "SELECT p.*, c.category_name 
            FROM product p
            JOIN category c ON p.category_code = c.category_code
            ORDER BY p.product_id DESC";

    $stmt = $_db->query($sql);
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
function getProductForDisplay()
{
    global $_db;
    $sql = "SELECT DISTINCT
                p.product_id,
                p.product_name,
                p.unit_price,
                p.stock_quantity,
                p.product_point,
                c.category_name,
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
    $stmt = $_db->prepare("SELECT p.*, c.category_name 
                           FROM product p 
                           JOIN category c ON p.category_code = c.category_code 
                           WHERE p.product_id = ? 
                           LIMIT 1");
    $stmt->execute([$id]);
    return $stmt->fetch() ?: false;  // Return false if not found
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
function getProductsByCategory($category_code)
{
    global $_db;
    $stmt = $_db->prepare("SELECT p.*, c.category_name 
                           FROM product p 
                           JOIN category c ON p.category_code = c.category_code 
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
