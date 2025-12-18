<?php
require_once  __DIR__ . '/../_base.php';
require_once  __DIR__ . '/../component/files.php';
require_once __DIR__ . '/../DAO/product_dao.php';

$function = post('function');
if (is_post()) {
    if ($function === 'add_category') {
        $category_name = trim(post('category_name') ?? '');
        $description   = trim(post('description') ?? '');
        $status        = post('status') === 'inactive' ? false : true; // active by default

        // Validate required fields
        if (empty($category_name)) {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'text' => 'Category name is required.'
            ];
            header("Location: ../../admin/admin-category.php");
            exit;
        }

        $targetDir = __DIR__ . "/../images/category/";  // __DIR__ = controller folder
        $dbPath    = "images/category/";

        $uploadResult = uploadFiles('category_image', $targetDir, $dbPath);
        if (!empty($uploadResult['errors'])) {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'text' => 'Image upload errors: ' . $uploadResult[0]
            ];
            header("Location: ../../admin/admin-category.php");
            exit;
        }
        $success = insertCategory($category_name, $description, $uploadResult[0], $status);

        if ($success) {
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'text' => 'Category added successfully!'
            ];
        } else {
            // Optional: delete uploaded image if DB insert failed
            if ($img_path && file_exists('../../' . $img_path)) {
                unlink('../../' . $img_path);
            }
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'text' => 'Failed to save category to database.'
            ];
        }
        redirect("/pages/admin/admin-category.php");
    } else if ($function === 'delete') {
        $code = post('category_code') ?? '';

        // Basic validation
        if (empty($code) || !is_numeric($code)) {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'text' => 'Invalid category code.'
            ];
        } else {

            $success = deleteCategory((int) $code);
            if ($success) {
                $_SESSION['flash_message'] = [
                    'type' => 'success',
                    'text' => 'Category successfully deleted.'
                ];
            } else {
                $_SESSION['flash_message'] = [
                    'type' => 'error',
                    'text' => 'Failed to delete category. It may not exist or already be deleted.'
                ];
            }
        }

        // Redirect back to the category list page (not admin-order.php!)
        header("Location: ../../pages/admin/admin-category.php"); // Adjust path as needed
        exit;
    }
}

function getAllCategory()
{
    return getAllCategoryDao();
}

function getCategoryByCode($code)
{
    return getCategoryByCodeDao($code);
}
