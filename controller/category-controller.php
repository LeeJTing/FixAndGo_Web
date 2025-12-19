<?php
require_once  __DIR__ . '/../_base.php';
require_once  __DIR__ . '/../component/files.php';
require_once __DIR__ . '/../DAO/product_dao.php';

$function = post('function');
if (is_post()) {
    $category_code = (int) post('category_code');
    $existed_image = getCategoryByCode($category_code)->img_path;
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

        // Basic validation
        if (empty($category_code) || !is_numeric($category_code)) {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'text' => 'Invalid category code.'
            ];
        } else {

            $success = deleteCategory((int) $category_code);
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
    } else if ($function === 'delete_image') {

        $success = deleteImageCategoryDao($category_code);

        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
        exit;
    } else if ($function === 'update_category') {
        $category_name = trim(post('category_name') ?? '');
        $description   = trim(post('description') ?? '');
        $status = (int) post('status');

        if ($category_code <= 0 || $category_name === '') {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'text' => 'Invalid category data.'
            ];
            redirect("/pages/admin/admin-update-category.php?code=$category_code");
        }

        $targetDir = __DIR__ . "/../images/category/";  // __DIR__ = controller folder
        $dbPath    = "images/category/";

        $imgPath = null;

        if (!empty($_FILES['category_image']['name'])) {
            $uploadResult = uploadFiles('category_image', $targetDir, $dbPath);

            if (!empty($uploadResult['errors'])) {
                $_SESSION['flash_message'] = [
                    'type' => 'error',
                    'text' => 'Image upload error: ' . implode(', ', $uploadResult['errors'])
                ];
                redirect("/pages/admin/admin-update-category.php?code=$category_code");
            }

            $imgPath = $uploadResult[0] ?? null;
        }
        $finalImgPath = $imgPath !== null ? $imgPath : $existed_image;

        $updated = updateCategory(
            $category_code,
            $category_name,
            $description,
            $finalImgPath,
            $status
        );
        if ($updated) {
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'text' => $status === 0
                    ? 'Temporary category updated successfully.'
                    : 'Category updated successfully.' . $existed_image
            ];
        } else {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'text' => 'Failed to update category.'
            ];
        }

        redirect("/pages/admin/admin-update-category.php?code=$category_code");
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
