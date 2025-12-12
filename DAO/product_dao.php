
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
            LEFT JOIN productvisualmedia pvm ON pvm.product_id = p.product_id AND pvm.is_show = 1
            WHERE 1=1 AND p.isdeleted = 0 AND p.status = 'active'";
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

function getCountAllProductDao()
{
    global $_db;
    $sql = "SELECT COUNT(*) AS items FROM product WHERE isdeleted = 0 AND status = 'active'";
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
                AND pvm.is_show = 1 AND p.isdeleted = 0 AND p.status = 'active'
            ORDER BY p.product_id ASC";

    $stmt = $_db->query($sql);
    return $stmt->fetchAll();
}

/**
 * Get single product by ID (for product detail page)
 */
function getProductByIdDao($id)
{
    global $_db;
    $stmt = $_db->prepare("SELECT 
                            p.*, 
                            c.category_name
                           FROM product p 
                           JOIN category c ON p.category_code = c.category_code 
                           WHERE p.isdeleted = 0 AND p.status = 'active' AND p.product_id = ? ");
    $stmt->execute([$id]);
    return $stmt->fetch();
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
function getProductImagesDao($product_id)
{
    global $_db;
    $stmt = $_db->prepare("SELECT pvm.file_path, pvm.alt, pvm.position 
                           FROM productvisualmedia pvm
                           JOIN product p ON p.product_id = pvm.product_id 
                           WHERE pvm.product_id = ? 
                            AND p.isdeleted = 0");
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
                            AND p.isdeleted = 0
                            AND p.status = 'active'
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
                           WHERE p.isdeleted = 0 
                              AND p.status = 'active'
                              AND p.product_name LIKE ? 
                              OR p.short_desc LIKE ? 
                              OR p.description LIKE ?
                           ORDER BY p.product_name");
    $stmt->execute([$keyword, $keyword, $keyword]);
    return $stmt->fetchAll();
}

function getAllCategoryDao()
{
    global $_db;
    $stmt = $_db->query("SELECT * FROM category");
    return $stmt->fetchAll();
}

function updateProductById($id, $name, $category, $price, $qty, $points, $short_desc, $desc, $status)
{
    global $_db;
    session_start(); // make sure session started

    // Sanitize
    $name       = mb_substr(trim($name), 0, 50);
    $short_desc = mb_substr(trim($short_desc), 0, 90);
    $price      = number_format(floatval($price), 2, '.', '');

    $sql = "UPDATE product
            SET product_name = :name,
                short_desc = :short_desc,
                category_code = :category_code,
                unit_price = :price,
                stock_quantity = :stock,
                status = :status,
                product_point = :point,
                description = :description
            WHERE product_id = :id";

    try {
        $stmt = $_db->prepare($sql);
        $executed = $stmt->execute([
            ':name'         => $name,
            ':short_desc'   => $short_desc,
            ':category_code' => $category,
            ':price'        => $price,
            ':stock'        => $qty,
            ':status'       => $status,
            ':point'        => $points,
            ':description'  => $desc,
            ':id'           => $id
        ]);

        if ($executed) {
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'text' => 'Product update executed successfully.'
            ];
        } else {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'text' => 'Failed to execute update.'
            ];
        }

        header('Location: ../pages/admin/admin-product-update.php?id=' . $id);
        exit;
    } catch (PDOException $e) {
        $_SESSION['flash_message'] = [
            'type' => 'error',
            'text' => 'Database Error: ' . $e->getMessage()
        ];
        header('Location: ../pages/admin/admin-product-update.php?id=' . $id);
        exit;
    }
}


function getProductBySearchDao($keyword)
{
    global $_db;

    $searchTerm = "%" . $keyword . "%";

    $sql = "SELECT p.*,pvm.alt,pvm.file_path,c.category_name
            FROM product p
            JOIN category c ON c.category_code = p.category_code 
            LEFT JOIN productvisualmedia pvm ON pvm.product_id = p.product_id AND pvm.is_show = 1
            WHERE p.isdeleted = 0 
            AND p.status = 'active'
            AND p.product_id LIKE ? 
            OR p.product_name LIKE ? 
            OR p.description LIKE ?     
            OR c.category_name LIKE ?
            OR p.unit_price LIKE ?       
            OR p.product_point LIKE ?    
            OR p.short_desc LIKE ?";

    $stmt = $_db->prepare($sql);

    $stmt->execute([
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm
    ]);

    return $stmt->fetchAll();
}

function deleteByProductId($id)
{
    global $_db;
    try {
        $sql = "UPDATE product SET isdeleted = 1 WHERE product_id = ?";
        $stmt = $_db->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        // Log error if needed
        error_log("Error deleting product: " . $e->getMessage());
        return false;
    }
}

function deleteImageDao($img_path)
{
    global $_db;

    try {
        $sql = "DELETE FROM productvisualmedia WHERE file_path = ?";
        $stmt = $_db->prepare($sql);
        $stmt->execute([$img_path]);

        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        return false;
    }
}

function addNewImage($file_path, $id, $name, $position)
{
    global $_db;

    try {
        $sql = "INSERT INTO productvisualmedia 
                (product_id, position, file_path, alt, is_show, type)
                VALUES (:id, :position, :file_path, :alt, 0, 'Image')";

        $stmt = $_db->prepare($sql);
        $stmt->execute([
            ':id'       => $id,
            ':position' => $position + 1,
            ':file_path' => $file_path,
            ':alt'      => $name
        ]);

        return true;
    } catch (PDOException $e) {
        return $e->getMessage(); // string, not array
    }
}

function addNewProduct($product_name, $short_desc, $category_id, $price, $stock, $status, $point, $description)
{
    global $_db; // PDO connection

    try {
        // Insert product main info
        $sql = "INSERT INTO products 
                (product_name, short_desc, description, category_code, unit_price, stock_quantity, status, product_point) 
                VALUES 
                (:name, :short_desc, :description, :category_id, :price, :stock, :status, :point)";
        $stmt = $_db->prepare($sql);
        $stmt->execute([
            ':name' => $product_name,
            ':short_desc' => $short_desc,
            ':description' => $description,
            ':category_id' => $category_id,
            ':price' => $price,
            ':stock' => $stock,
            ':status' =>  $status,
            ':point' => $point
        ]);
        return true;
    } catch (PDOException $e) {
        error_log("Insert Product Error: " . $e->getMessage());
        return false;
    }
}
