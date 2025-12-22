
<?php
/**
 * Get all products with category name
 */
function getAllProductFilterDao($category, $sort, $price, $limit, $offset)
{
    global $_db;

    $sql = "SELECT p.*, c.category_name, pvm.file_path, pvm.alt
            FROM product p
            JOIN category c 
                ON p.category_code = c.category_code AND c.is_deleted = 0
            LEFT JOIN productvisualmedia pvm 
                ON pvm.product_id = p.product_id AND pvm.is_show = 1
            WHERE p.isdeleted = 0 
              AND p.status = 'active'";

    $param = [];

    if (!empty($category)) {
        $sql .= " AND p.category_code = ?";
        $param[] = $category;
    }

    if ($price !== "" && $price !== null) {
        $sql .= " AND p.unit_price <= ?";
        $param[] = $price;
    }

    // Sorting
    switch ($sort) {
        case "LowtoHigh":
            $sql .= " ORDER BY p.unit_price ASC";
            break;
        case "HightoLow":
            $sql .= " ORDER BY p.unit_price DESC";
            break;
        case "newest":
            $sql .= " ORDER BY p.created_at DESC";
            break;
        default:
            $sql .= " ORDER BY p.product_id ASC";
    }

    // Pagination
    $sql .= " LIMIT ? OFFSET ?";

    $stmt = $_db->prepare($sql);

    // Bind normal params
    $i = 1;
    foreach ($param as $p) {
        $stmt->bindValue($i++, $p);
    }

    // Bind limit & offset (IMPORTANT: must be INT)
    $stmt->bindValue($i++, (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue($i++, (int)$offset, PDO::PARAM_INT);

    $stmt->execute();
    return $stmt->fetchAll();
}

function getAllProductFilterCountDao($category, $price)
{
    global $_db;

    $sql = "SELECT COUNT(*) 
            FROM product p
            JOIN category c 
                ON p.category_code = c.category_code AND c.is_deleted = 0
            WHERE p.isdeleted = 0 
              AND p.status = 'active'";

    $param = [];

    if (!empty($category)) {
        $sql .= " AND p.category_code = ?";
        $param[] = $category;
    }

    if ($price !== "" && $price !== null) {
        $sql .= " AND p.unit_price <= ?";
        $param[] = $price;
    }

    $stmt = $_db->prepare($sql);
    $stmt->execute($param);
    return (int)$stmt->fetchColumn();
}

function getAllProductFilterAdminDao($category, $sort, $price)
{
    global $_db;
    $sql = "SELECT p.*, c.category_name, 
            COALESCE(pvm.file_path, 'no-image.jpg') AS file_path, 
            pvm.alt
                FROM product p
                JOIN category c ON p.category_code = c.category_code
                LEFT JOIN productvisualmedia pvm ON pvm.product_id = p.product_id 
                AND pvm.is_show = 1
                AND c.is_deleted = 0
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
    $stmt = $_db->prepare($sql);
    $stmt->execute();
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
                AND c.is_deleted = 0
            ORDER BY p.product_id ASC";

    $stmt = $_db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getProductListAdminDao()
{
    global $_db;
    $sql = "SELECT DISTINCT p.product_id, p.product_name, p.unit_price, p.stock_quantity, p.product_point, p.description, p.isdeleted, c.category_name, c.category_code, COALESCE(pvm.file_path, 'images/no-image.jpg') AS file_path, pvm.alt AS alt_text FROM product p JOIN category c ON p.category_code = c.category_code LEFT JOIN productvisualmedia pvm ON pvm.product_id = p.product_id AND pvm.is_show = 1 WHERE p.isdeleted = 0 AND c.is_deleted = 0 ORDER BY p.product_id ASC";

    $stmt = $_db->prepare($sql);
    $stmt->execute();
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
                           WHERE p.isdeleted = 0 AND p.status = 'active' AND c.is_deleted = 0 AND p.product_id = ? ");
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
                           WHERE p.isdeleted = 0 AND c.is_deleted = 0 AND p.product_id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
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
                            AND c.is_deleted = 0
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

    $stmt = $_db->prepare("
        SELECT p.*, c.category_name 
        FROM product p 
        JOIN category c ON p.category_code = c.category_code 
        WHERE p.isdeleted = 0 
          AND p.status = 'active'
          AND c.is_deleted = 0
          AND (
              p.product_name LIKE ?
              OR p.short_desc LIKE ?
              OR p.description LIKE ?
          )
        ORDER BY p.product_name
    ");

    $stmt->execute([$keyword, $keyword, $keyword]);
    return $stmt->fetchAll();
}

function getAllCategoryDao()
{
    global $_db;

    $stmt = $_db->prepare("SELECT * FROM category WHERE is_deleted = 0");
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

function getAllCategoryGuestDao()
{
    global $_db;
    $sql = "SELECT * FROM category WHERE is_show = 1 AND is_deleted = 0";
    $stmt = $_db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getCategoryByCodeDao($id)
{
    global $_db;
    $sql = "SELECT * FROM category WHERE is_deleted = 0 AND category_code = ?";
    $stmt = $_db->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_OBJ);
}

function deleteCategory(int $code): bool
{
    global $_db;

    try {
        $sql = "UPDATE category
            SET is_deleted = 1
            WHERE category_code = ?
        ";

        $stmt = $_db->prepare($sql);
        $stmt->execute([$code]);
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log("Delete category failed (code=$code): " . $e->getMessage());
        return false;
    }
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
    $lowstock,
    $sold_number
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
                low_stock_threshold = :lowStock,
                sold_number = :sold
            WHERE product_id = :id";

    try {
        $stmt = $_db->prepare($sql);
        return $stmt->execute([
            ':name' => $name,
            ':short_desc' => $short_desc,
            ':category_code' => $category,
            ':price' => $price,
            ':stock' => $qty,
            ':status' => $status,
            ':point' => $points,
            ':description' => $desc,
            ':lowStock' => $lowstock,
            ':sold' => $sold_number,
            ':id' => $id
        ]);
    } catch (PDOException $e) {
        error_log("Update Product Error: " . $e->getMessage());
        return false;
    }
}

function getProductBySearchDao($keyword)
{
    global $_db;

    $searchTerm = "%" . $keyword . "%";

    $sql = "SELECT p.*,pvm.alt,pvm.file_path,c.category_name
            FROM product p
            JOIN category c ON c.category_code = p.category_code AND c.is_deleted = 0
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
            JOIN category c ON c.category_code = p.category_code AND c.is_deleted = 0
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
        $stmt = $_db->prepare($sql);
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
            return false;
        }

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

        // Execute the statement
        $result = $stmt->execute($params);

        // Debug: Check execution result
        if ($result) {
            $lastId = $_db->lastInsertId();
            return true;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        return $e->getMessage();
    }
}

function getProductNamesExceptId($product_id)
{
    global $_db;

    try {
        $stmt = $_db->prepare(
            "SELECT product_name FROM product WHERE product_id != ?"
        );
        $stmt->execute([$product_id]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        error_log("Get Product Names Error: " . $e->getMessage());
        return [];
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

function insertCategory($category_name, $description = null, $img_path = null, $is_show = true)
{
    global $_db;

    // Basic validation
    if (empty(trim($category_name))) {
        return false;
    }

    try {
        $sql = "INSERT INTO category 
                (category_name, description, img_path, is_show) 
                VALUES 
                (?, ?, ?, ?)";

        $stmt = $_db->prepare($sql);

        $stmt->execute([
            trim($category_name),
            $description,
            $img_path,
            $is_show ? 1 : 0,     // Convert bool to int for MySQL
        ]);

        // Return true if a row was inserted
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        // Log error (never show raw error to user)
        error_log("Insert category failed: " . $e->getMessage());
        return false;
    }
}

function deleteImageCategoryDao(int $code): bool
{
    global $_db;

    try {
        $sql = "UPDATE category 
                SET img_path = NULL, is_show = 0
                WHERE category_code = ?";

        $stmt = $_db->prepare($sql);
        $stmt->execute([$code]);

        // Return true only if a row was actually updated
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log("Delete category image failed: " . $e->getMessage());
        return false;
    }
}

function updateCategory(
    int $category_code,
    string $category_name,
    ?string $description,
    ?string $img_path,
    int $is_show
): bool {
    global $_db;

    // Validate required fields
    if ($category_code <= 0 || trim($category_name) === '') {
        return false;
    }

    try {
        $sql = "UPDATE category 
                SET category_name = :name,
                    description   = :description,
                    img_path       = :img_path,
                    is_show        = :is_show
                WHERE category_code = :code
                  AND is_deleted = 0";

        $stmt = $_db->prepare($sql);

        $stmt->execute([
            ':name'        => trim($category_name),
            ':description' => $description ?: null,
            ':img_path'    => $img_path ?: null,
            ':is_show'     => $is_show,   // 1 or 0
            ':code'        => $category_code,
        ]);

        // True if update executed successfully
        return $stmt->rowCount() >= 0;
    } catch (PDOException $e) {
        error_log('Update category failed: ' . $e->getMessage());
        return false;
    }
}
function getTop5BestSellersDifferentCategories()
{
    global $_db;

    $sql = "WITH sales_by_product AS (
                SELECT
                    p.product_id,
                    p.product_name,
                    p.unit_price,
                    p.short_desc,
                    c.category_name,
                    SUM(oi.qty) AS total_sold
                FROM product p
                JOIN category c ON c.category_code = p.category_code
                JOIN orderitem oi ON oi.product_id = p.product_id
                JOIN orders o ON o.order_id = oi.order_id
                WHERE o.status NOT IN ('Pending', 'Cancelled')
                AND c.is_deleted = FALSE
                AND c.is_show = 1
                GROUP BY p.product_id, p.product_name, p.unit_price, p.short_desc, c.category_name
            ),
            ranked_products AS (
                SELECT
                    sp.*,
                    ROW_NUMBER() OVER (PARTITION BY sp.category_name ORDER BY sp.total_sold DESC) AS rank_in_category
                FROM sales_by_product sp
            ),
            top_products AS (
                SELECT
                    rp.product_id,
                    rp.product_name,
                    rp.unit_price,
                    rp.short_desc,
                    rp.category_name,
                    rp.total_sold
                FROM ranked_products rp
                WHERE rp.rank_in_category = 1
                ORDER BY rp.total_sold DESC
                LIMIT 5
            ),
            top_with_image AS (
                SELECT
                    tp.*,
                    pvm.file_path,
                    pvm.alt,
                    ROW_NUMBER() OVER (PARTITION BY tp.product_id ORDER BY pvm.media_id ASC) AS image_rank
                FROM top_products tp
                LEFT JOIN productvisualmedia pvm ON pvm.product_id = tp.product_id
            )
            SELECT
                product_id,
                product_name,
                unit_price,
                short_desc,
                category_name,
                total_sold,
                file_path,
                alt
            FROM top_with_image
            WHERE image_rank = 1 OR file_path IS NULL
            ORDER BY total_sold DESC";

    try {
        $stmt = $_db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        error_log("getTop5BestSellersDifferentCategories error: " . $e->getMessage());
        return [];
    }
}

function hasProductsInCategoryDao($category_code)
{
    global $_db;

    try {
        $sql = "SELECT COUNT(*) FROM product 
                  WHERE category_code = :category_code 
                  AND isdeleted = 0";

        $stmt = $_db->prepare($sql);
        $stmt->bindParam(':category_code', $category_code, PDO::PARAM_INT);
        $stmt->execute();

        $count = $stmt->fetchColumn();
        return $count > 0;
    } catch (PDOException $e) {
        error_log("PDO Error: " . $e->getMessage());
        return false;
    }
}

function checkCategoryExistsDao($category_name, $exclude_id = 0)
{
    global $_db;

    try {
        if ($exclude_id > 0) {
            $sql = "SELECT COUNT(*) FROM category 
                    WHERE category_name = ? 
                    AND category_code != ? 
                    AND is_deleted = 0";
            $stmt = $_db->prepare($sql);
            $stmt->execute([$category_name, $exclude_id]);
        } else {
            $sql = "SELECT COUNT(*) FROM category 
                    WHERE category_name = ? 
                    AND is_deleted = 0";
            $stmt = $_db->prepare($sql);
            $stmt->execute([$category_name]);
        }

        $count = $stmt->fetchColumn();
        return $count > 0;
    } catch (PDOException $e) {
        error_log("PDO Error: " . $e->getMessage());
        return false;
    }
}
