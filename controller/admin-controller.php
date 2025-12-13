<?php
require_once  __DIR__ . '/../_base.php';
require __DIR__ . '/../DAO/product_dao.php';
require __DIR__ . '/../component/files.php';
require __DIR__ . '/../component/msg.php';

$function = $_GET['function'] ?? null;   // <-- FIX (no warning)

if ($function === 'Search') {
    $search_value = $_GET['search'] ?? '';  // prevent warning too
    $result = getProductBySearchDao($search_value);

    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
} else if ($function === 'delete') {
    $product_id = get('id');
    $delete = deleteByProductId($product_id);

    if ($delete) {
        $_SESSION['flash_message'] = [
            'type' => 'success',
            'text' => 'Product deleted successfully.'
        ];
    } else {
        $_SESSION['flash_message'] = [
            'type' => 'error',
            'text' => 'Failed to delete product. Product may not exist.'
        ];
    }

    header('Location: ../pages/admin/admin-product.php');
    exit;
} else if ($function === 'getProductName') {
    $productNames = getProductName();

    if ($productNames !== false) {
        echo json_encode([
            'status' => 'success',
            'products' => $productNames
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to fetch product names'
        ]);
    }

    exit;
} else if ($function === 'allProduct') {
    $category_code = $_GET['category'] ?? "";;
    $sortBy = $_GET['sort'] ?? "";;
    $priceValue = $_GET['price'] ?? "";

    $result = getAllProductFilterAdminDao($category_code, $sortBy, $priceValue);
    echo json_encode($result);
    exit;
} else if ($function === 'Search') {
    $search_value = $_GET['search'] ?? '';  // prevent warning too
    $result = getProductBySearchAdminDao($search_value);

    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
} else if (is_post()) {
    $post_function = post('function');
    $id = post('id');
    if ($post_function === 'add') {
        // POST values
        $position = 0;
        $product_name  = post('product_name');
        $short_desc    = post('short_desc');
        $category_id   = post('category_id');
        $price         = post('price');
        $stock         = post('stock');
        $status        = post('status');
        $point         = post('point');
        $description   = post('description');
        $lowStock      = post('lowstock');

        if ($status === 'inactive') {

            
            $isAddProduct = addNewProduct($product_name, $short_desc, $category_id, $price, $stock, $status, $point, $description);
            if ($isAddProduct) {
                $_SESSION['flash_message'] = [
                    'type' => 'success',
                    'text' => 'Temporary Product added successfully!'
                ];

                header('Location: ../pages/admin/admin-product.php');
                exit;
            } else {
                $_SESSION['flash_message'] = [
                    'type' => 'error',
                    'text' => 'Failed to add product'
                ];

                header('Location: ../pages/admin/admin-product.php');
                exit;
            }
        }
        // Upload files
        $uploadedFiles = !empty($_FILES['product_images']['name'][0])
            ? uploadFiles('product_images', "../images/product/")
            : [];

        // Convert paths to **web-accessible**
        $webPaths = array_map(function ($f) {
            return str_replace('../images/product/', 'images/product/', $f);
        }, $uploadedFiles);

        // Insert product in DB
        $isAddProduct = addNewProduct($product_name, $short_desc, $category_id, $price, $stock, $status, $point, $description);

        if ($isAddProduct) {
            // Insert uploaded files into product images table
            $product_id = searchProductByName($product_name);
            foreach ($webPaths as $i => $file) {
                $countAppearProduct = count(getProductImagesDao($product_id));
                // If no images exist and this is the first in $webPaths, mark as main
                $isMain = ($countAppearProduct === 0) ? 1 : 0;
                addNewImage($file, $product_id, $product_name, $position, $isMain);
                $position++;
            }

            // Set flash message for success
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'text' => 'Product added successfully!'
            ];

            header('Location: ../pages/admin/admin-product.php');
            exit;
        } else {
            // Set flash message for error
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'text' => 'Failed to add product'
            ];

            // Redirect back to the add product page
            header('Location: ../pages/admin/admin-product.php');
            exit;
        }
    } else if ($post_function === 'update') {
        $count = count(getProductImagesDao($id));
        $name       = mb_substr(trim(post('product_name') ?? ''), 0, 50);
        $category   = intval(trim(post('category_code') ?? ''));
        $price      = number_format(floatval(post('unit_price') ?? 0), 2, '.', '');
        $qty        = intval(post('stock_quantity') ?? 0);
        $points     = intval(post('product_point') ?? 0);
        $short_desc = mb_substr(trim(post('short_desc') ?? ''), 0, 90);
        $status     = trim(post('status') ?? '');
        $desc       = trim(post('description') ?? '');
        $lowStock   = (int) post('lowstock');
        if (!empty($_FILES['new_images']['name'][0])) {

            // Upload files
            $uploadedFiles = uploadFiles('new_images', "../images/product/");
            // Insert uploaded images
            foreach ($uploadedFiles as $file) {
                $countAppearProduct_update = count(getProductImagesDao($product_id));
                if ($countAppearProduct_update === 0) {
                    $result = addNewImage($file, $id, $name, $count, 1);
                } else {
                    // $file should be a string like "images/product/xxxx.png"
                    $result = addNewImage($file, $id, $name, $count, 0);
                }
                $count++;

                if ($result !== true) {
                    echo "❌ Error adding image: ";
                    print_r($result); // If it's still an array, debug what it contains
                } else {
                    echo "✔️ Image added successfully!\n";
                }
            }
            updateProductById($id, $name, $category, $price, $qty, $points, $short_desc, $desc, $status, $lowStock);
        } else {
            updateProductById($id, $name, $category, $price, $qty, $points, $short_desc, $desc, $status, $lowStock);
        }
    } else if ($post_function === 'file_delete') {
        header('Content-Type: application/json');

        $filename = $_POST['filename'] ?? '';
        $id = $_POST['id'] ?? '';

        if (!$filename) {
            echo json_encode(['status' => 'error', 'message' => 'No filename provided']);
            exit;
        }

        $filePath = __DIR__ . "/../images/product/" . $filename;

        // unlink($filePath);
        $deleted = deleteImageDao($filename);

        if ($deleted) {
            echo json_encode([
                'status' => 'success',
                'message' => "File exists and ready for deletion",
                'filePath' => $filePath
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => "File does not exist",
                'filePath' => $fileName
            ]);
        }

        exit;
    }
}

function getAdminProducts()
{
    return getProductListAdminDao();
}
function getAllCategory()
{
    return getAllCategoryDao();
}
function getProductById($id)
{
    return getProductByIdAdminDao($id);
}

function getProductList()
{
    return getProductListAdminDao();
}

function getProductImages($id)
{
    return getProductImagesAdminDao($id);
}
