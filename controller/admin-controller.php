<?php
require __DIR__ . '/../_base.php';
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
    deleteByProductId($product_id);

    print_r($product_id);
} else if (is_post()) {

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

    // Save $uploadedFiles to DB, or use for preview
    print_r($uploadedFiles);

    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'uploaded_files' => $filesArray,
        'product_data' => [
            'name' => $product_name,
            'short_desc' => $short_desc,
            'category_id' => $category_id,
            'price' => $price,
            'stock' => $stock,
            'status' => $status,
            'point' => $point,
            'description' => $description
        ]
    ], JSON_PRETTY_PRINT);

    exit;

    // 3. Insert into DB (pseudo DAO function)
    $productId = ([
        'product_name' => $product_name,
        'short_desc'   => $short_desc,
        'category_id'  => $category_id,
        'price'        => $price,
        'stock'        => $stock,
        'status'       => $status,
        'point'        => $point,
        'description'  => $description
    ]);

    // 4. Insert uploaded images into separate table
    foreach ($uploadedFiles as $file) {
        //($productId, $file); // implement in DAO
    }

    // 5. Redirect or return success

}

function getAdminProducts()
{
    return getProductListDao();
}
function getAllCategory()
{
    return getAllCategoryDao();
}
