
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

function getAllProductFilterAdminDao($category, $sort, $price)
{
    global $_db;
    $sql = "SELECT p.*, c.category_name, 
            COALESCE(pvm.file_path, 'no-image.jpg') AS file_path, 
            pvm.alt
                FROM product p
                JOIN category c ON p.category_code = c.category_code
                LEFT JOIN productvisualmedia pvm ON pvm.product_id = p.product_id AND pvm.is_show = 1
                WHERE 1=1 AND p.isdeleted = 0";
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

function getProductListAdminDao()
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
                COALESCE(pvm.file_path, 'images/no-image.jpg') AS file_path,
                pvm.alt AS alt_text
            FROM product p
            JOIN category c ON p.category_code = c.category_code
            LEFT JOIN productvisualmedia pvm ON pvm.product_id = p.product_id 
                AND pvm.is_show = 1 AND p.isdeleted = 0 
            ORDER BY p.product_id ASC;";

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

function getProductByIdAdminDao($id)
{
    global $_db;
    $stmt = $_db->prepare("SELECT 
                            p.*, 
                            c.category_name
                           FROM product p 
                           JOIN category c ON p.category_code = c.category_code 
                           WHERE p.isdeleted = 0 AND p.product_id = ?");
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

function getProductImagesAdminDao($product_id)
{
    global $_db;
    $stmt = $_db->prepare("SELECT 
                            COALESCE(pvm.file_path, 'images/no-image.jpg') AS file_path, 
                            COALESCE(pvm.alt, 'no image') AS alt, 
                            COALESCE(pvm.position, 0) AS position 
                            FROM product p
                            LEFT JOIN productvisualmedia pvm ON p.product_id = pvm.product_id 
                            WHERE p.product_id = ? AND p.isdeleted = 0;");
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

function getAllCategoryGuestDao()
{
    global $_db;
    $stmt = $_db->query("SELECT * FROM category WHERE is_show = 1");
    return $stmt->fetchAll();
}

function updateProductById(
    $id,
    $name,
    $category,
    $price,
    $qty,
    $points,
    $short_desc,
    $desc,
    $status,
    $lowstock
) {
    global $_db;

    $sql = "UPDATE product
            SET product_name = :name,
                short_desc = :short_desc,
                category_code = :category_code,
                unit_price = :price,
                stock_quantity = :stock,
                status = :status,
                product_point = :point,
                description = :description,
                low_stock_threshold = :lowStock
            WHERE product_id = :id";

    try {
        $stmt = $_db->prepare($sql);
        $stmt->execute([
            ':name'         => $name,
            ':short_desc'   => $short_desc,
            ':category_code' => $category,
            ':price'        => $price,
            ':stock'        => $qty,
            ':status'       => $status,
            ':point'        => $points,
            ':description'  => $desc,
            ':lowStock'     => $lowstock,
            ':id'           => $id
        ]);

        $_SESSION['flash_message'] = [
            'type' => 'success',
            'text' => 'Product updated successfully.'
        ];
    } catch (PDOException $e) {
        $_SESSION['flash_message'] = [
            'type' => 'error',
            'text' => 'Database Error: ' . $e->getMessage()
        ];
    }

    header('Location: ../pages/admin/admin-product-update.php?id=' . $id);
    exit;
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

function getProductBySearchAdminDao($keyword)
{
    global $_db;

    $searchTerm = "%" . $keyword . "%";

    $sql = "SELECT p.*,pvm.alt,COALESCE(pvm.file_path, 'no-image.jpg') AS file_path,c.category_name
            FROM product p
            JOIN category c ON c.category_code = p.category_code 
            LEFT JOIN productvisualmedia pvm ON pvm.product_id = p.product_id AND pvm.is_show = 1
            WHERE p.isdeleted = 0 
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

function addNewImage($file_path, $id, $name, $position, $is_show)
{
    global $_db;

    try {
        $sql = "INSERT INTO productvisualmedia 
                (product_id, position, file_path, alt, is_show, type)
                VALUES (:id, :position, :file_path, :alt, :is_show, 'Image')";

        $stmt = $_db->prepare($sql);
        $stmt->execute([
            ':id'        => $id,
            ':position'  => $position + 1,
            ':file_path' => $file_path,
            ':alt'       => $name,
            ':is_show'   => $is_show
        ]);

        return true;
    } catch (PDOException $e) {
        return $e->getMessage(); // string, not array
    }
}

function searchProductByName($name)
{
    global $_db;
    try {
        $sql = "SELECT * FROM product WHERE product_name = ?";

        $stmt = $_db->prepare($sql);
        $stmt->execute([$name]);
        $product = $stmt->fetch();

        return $product->product_id;
    } catch (PDOException $e) {
        return $e->getMessage(); // string, not array
    }
}

function getLastInsertedProduct()
{
    global $_db;
    try {
        // Get the last inserted product based on auto-increment ID
        $sql = "SELECT * FROM product ORDER BY product_id DESC LIMIT 1";
        $stmt = $_db->query($sql);
        $product = $stmt->fetch(); // fetch as object

        return $product->product_id ?: null; // return null if none
    } catch (PDOException $e) {
        error_log("Error fetching last product: " . $e->getMessage());
        return null;
    }
}

function addNewProduct($product_name, $short_desc, $category_id, $price, $stock, $status, $point, $description)
{
    global $_db;

    // Debug: Log function entry
    error_log("=== addNewProduct Function Called ===");
    error_log("Parameters received:");
    error_log("- product_name: " . var_export($product_name, true));
    error_log("- short_desc: " . var_export($short_desc, true));
    error_log("- category_id: " . var_export($category_id, true));
    error_log("- price: " . var_export($price, true));
    error_log("- stock: " . var_export($stock, true));
    error_log("- status: " . var_export($status, true));
    error_log("- point: " . var_export($point, true));
    error_log("- description: " . var_export($description, true));

    // Debug: Check database connection
    if (!$_db) {
        error_log("ERROR: Database connection is null or not initialized");
        return false;
    }
    error_log("Database connection: OK");

    try {
        // Insert product main info
        $sql = "INSERT INTO product 
                (product_name, short_desc, description, category_code, unit_price, stock_quantity, status, product_point) 
                VALUES 
                (:name, :short_desc, :description, :category_id, :price, :stock, :status, :point)";

        error_log("SQL Query: " . $sql);

        $stmt = $_db->prepare($sql);

        // Debug: Check if prepare was successful
        if (!$stmt) {
            error_log("ERROR: Failed to prepare statement");
            error_log("PDO Error Info: " . print_r($_db->errorInfo(), true));
            return false;
        }
        error_log("Statement prepared successfully");

        // Prepare parameters array
        $params = [
            ':name' => $product_name,
            ':short_desc' => $short_desc,
            ':description' => $description,
            ':category_id' => $category_id,
            ':price' => $price,
            ':stock' => $stock,
            ':status' => $status,
            ':point' => $point
        ];

        error_log("Bound parameters: " . print_r($params, true));

        // Execute the statement
        $result = $stmt->execute($params);

        // Debug: Check execution result
        if ($result) {
            $lastId = $_db->lastInsertId();
            error_log("SUCCESS: Product inserted successfully");
            error_log("Last Insert ID: " . $lastId);
            error_log("Rows affected: " . $stmt->rowCount());
            return true;
        } else {
            error_log("ERROR: Execute returned false");
            error_log("Statement Error Info: " . print_r($stmt->errorInfo(), true));
            return false;
        }
    } catch (PDOException $e) {
        error_log("=== PDO EXCEPTION CAUGHT ===");
        error_log("Error Message: " . $e->getMessage());
        error_log("Error Code: " . $e->getCode());
        error_log("Stack Trace: " . $e->getTraceAsString());
        return false;
    } catch (Exception $e) {
        error_log("=== GENERAL EXCEPTION CAUGHT ===");
        error_log("Error Message: " . $e->getMessage());
        error_log("Stack Trace: " . $e->getTraceAsString());
        return false;
    }
}

function getProductName($product_id = null)
{
    global $_db;
    try {
        if ($product_id === null) {
            // If no ID provided, return all product names
            $stmt = $_db->query("SELECT product_name FROM product");
        } else {
            // Exclude the product with given ID
            $sql = "SELECT product_name FROM product WHERE product_id != ?";
            $stmt = $_db->prepare($sql);
            $stmt->execute([$product_id]);
        }

        $products = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $products;
    } catch (PDOException $e) {
        error_log("Get Product Names Error: " . $e->getMessage());
        return false;
    }
}

function getLowStockProduct()
{
    global $_db;
    try {
        $sql = "SELECT product_id, product_name 
                FROM product
                WHERE stock_quantity <= low_stock_threshold
                AND status = 'active' 
                AND isdeleted = 0";

        $stmt = $_db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        echo "DB Error: " . $e->getMessage();
    }
}
