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
} else if (is_post()) {
    $post_function = post('function');
    $id = post('id');
    if ($post_function === 'add') {
        $filesArray = [];
        // Get POST values
        $product_name  = post('product_name');
        $short_desc    = post('short_desc');
        $category_id   = post('category_id');
        $price         = post('price');
        $stock         = post('stock');
        $status        = post('status');
        $point         = post('point');
        $description   = post('description');

        $uploadedFiles = !empty($_FILES['product_images']['name'][0])
            ? uploadFiles('product_images')
            : ['dark_image.jpg']; // always have $uploadedFiles ready

        // Prepare result array with submitted values
        $result = [
            'status' => 'success',
            'message' => 'Form data received',
            'data' => [
                'product_name' => $product_name,
                'short_desc' => $short_desc,
                'category_id' => $category_id,
                'price' => $price,
                'stock' => $stock,
                'status' => $status,
                'point' => $point,
                'description' => $description,
                'uploaded_files' => $uploadedFiles
            ]
        ];

        header('Content-Type: application/json');
        echo json_encode($result, JSON_PRETTY_PRINT);
        exit;
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

        if (!empty($_FILES['new_images']['name'][0])) {

            // Upload files
            $uploadedFiles = uploadFiles('new_images', "../images/product/");

            // Insert uploaded images
            foreach ($uploadedFiles as $file) {
                // $file should be a string like "images/product/xxxx.png"
                $result = addNewImage($file, $id, $name, $count);
                $count++;

                if ($result !== true) {
                    echo "❌ Error adding image: ";
                    print_r($result); // If it's still an array, debug what it contains
                } else {
                    echo "✔️ Image added successfully!\n";
                }
            }
            updateProductById($id, $name, $category, $price, $qty, $points, $short_desc, $desc, $status);
        } else {
            updateProductById($id, $name, $category, $price, $qty, $points, $short_desc, $desc, $status);
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
    return getProductListDao();
}
function getAllCategory()
{
    return getAllCategoryDao();
}
