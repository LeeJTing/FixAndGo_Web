<?php
require_once __DIR__ . '/../_base.php';
require_once __DIR__ . '/../component/files.php';
require_once __DIR__ . '/../DAO/product_dao.php';

// Function to sanitize input
function sanitizeInput($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Function to validate image upload
function validateImageUpload($fileInput)
{
    $errors = [];

    if (!isset($_FILES[$fileInput]) || $_FILES[$fileInput]['error'] == UPLOAD_ERR_NO_FILE) {
        return $errors; // No file uploaded is OK for updates
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 2 * 1024 * 1024; // 2MB

    $file = $_FILES[$fileInput];

    // Check file size
    if ($file['size'] > $maxSize) {
        $errors[] = 'Image size must be less than 2MB';
    }

    // Check file type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);

    if (!in_array($mimeType, $allowedTypes)) {
        $errors[] = 'Only JPG, PNG, GIF, and WebP images are allowed';
    }

    // Check if file is actually an image
    $imageInfo = @getimagesize($file['tmp_name']);
    if (!$imageInfo) {
        $errors[] = 'Uploaded file is not a valid image';
    }

    return $errors;
}

$function = post('function');
if (is_post()) {
    $category_code = (int) post('category_code');
    $existed_image = $category_code ? (getCategoryByCode($category_code)->img_path ?? '') : '';

    if ($function === 'add_category') {
        // Sanitize inputs
        $category_name = sanitizeInput(trim(post('category_name') ?? ''));
        $description   = sanitizeInput(trim(post('description') ?? ''));
        $status        = post('status') === 'inactive' ? false : true;

        // Validate required fields
        $errors = [];

        // Required field validation
        if (empty($category_name)) {
            $errors[] = 'Category name is required.';
        } elseif (strlen($category_name) > 30) {
            $errors[] = 'Category name must be less than 30 characters.';
        }

        // Description validation
        if (strlen($description) > 1500) {
            $errors[] = 'Description must be less than 1500 characters.';
        }

        // Check for duplicate category name
        if (!empty($category_name) && checkCategoryExists($category_name)) {
            $errors[] = 'Category name already exists.';
        }

        if ($status == true) { // Only validate image if status is active
            $imageErrors = validateImageUpload('category_image');
            if (!empty($imageErrors)) {
                $errors = array_merge($errors, $imageErrors);
            }
        } else {
            // For inactive/draft status, image is optional
            // But still validate if image is uploaded (for security)
            if (!empty($_FILES['category_image']['name'])) {
                $imageErrors = validateImageUpload('category_image');
                if (!empty($imageErrors)) {
                    $errors = array_merge($errors, $imageErrors);
                }
            }
        }

        if (!empty($errors)) {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'text' => implode('<br>', $errors)
            ];
            redirect("/pages/admin/admin-add-category.php");
        }

        $targetDir = __DIR__ . "/../images/category/";
        $dbPath    = "images/category/";

        $uploadResult = uploadFiles('category_image', $targetDir, $dbPath);
        if (!empty($uploadResult['errors'])) {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'text' => 'Image upload error: ' . implode(', ', $uploadResult['errors'])
            ];
            redirect("/pages/admin/admin-add-category.php");
        }

        $img_path = $uploadResult[0] ?? '';
        $success = insertCategory($category_name, $description, $img_path, $status);

        if ($success) {
            if ($status == 1) { // Active status
                $text = "Category added successfully";
            } else { // Status 0 (inactive/draft)
                $text = "Category saved as draft successfully";
            }
            $_SESSION['flash_message'] = [
                'type' => 'success',
                'text' => $text,
            ];
        } else {
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
        // Enhanced validation for delete
        if ($category_code <= 0) {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'text' => 'Invalid category code.'
            ];
        } else {
            // Check if category exists
            $category = getCategoryByCode($category_code);
            if (!$category) {
                $_SESSION['flash_message'] = [
                    'type' => 'error',
                    'text' => 'Category does not exist.'
                ];
            } else {
                // Check if category has products (prevent deletion if used)
                if (hasProductsInCategory($category_code)) {
                    $_SESSION['flash_message'] = [
                        'type' => 'error',
                        'text' => 'Cannot delete category that contains products.'
                    ];
                } else {
                    $success = deleteCategory($category_code);
                    if ($success) {
                        // Delete image file if exists
                        if (!empty($category->img_path) && file_exists('../../' . $category->img_path)) {
                            unlink('../../' . $category->img_path);
                        }
                        $_SESSION['flash_message'] = [
                            'type' => 'success',
                            'text' => 'Category successfully deleted.'
                        ];
                    } else {
                        $_SESSION['flash_message'] = [
                            'type' => 'error',
                            'text' => 'Failed to delete category.'
                        ];
                    }
                }
            }
        }

        header("Location: ../../pages/admin/admin-category.php");
        exit;
    } else if ($function === 'delete_image') {
        // Validate category exists before deleting image
        if ($category_code <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid category']);
            exit;
        }

        $category = getCategoryByCode($category_code);
        if (!$category || empty($category->img_path)) {
            echo json_encode(['success' => false, 'message' => 'No image to delete']);
            exit;
        }

        $success = deleteImageCategoryDao($category_code);
        if ($success && file_exists('../../' . $category->img_path)) {
            unlink('../../' . $category->img_path);
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
        exit;
    } else if ($function === 'update_category') {
        $category_name = sanitizeInput(trim(post('category_name') ?? ''));
        $description   = sanitizeInput(trim(post('description') ?? ''));
        $status        = post('status') === 'inactive' ? false : true;
        // Validate inputs
        $errors = [];

        if ($category_code <= 0) {
            $errors[] = 'Invalid category code.';
        }

        if (empty($category_name)) {
            $errors[] = 'Category name is required.';
        } elseif (strlen($category_name) > 100) {
            $errors[] = 'Category name must be less than 100 characters.';
        }

        if (strlen($description) > 500) {
            $errors[] = 'Description must be less than 500 characters.';
        }

        // Validate image if uploaded
        if (!empty($_FILES['category_image']['name'])) {
            $imageErrors = validateImageUpload('category_image');
            if (!empty($imageErrors)) {
                $errors = array_merge($errors, $imageErrors);
            }
        }

        if (!empty($errors)) {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'text' => implode('<br>', $errors)
            ];
            redirect("/pages/admin/admin-update-category.php?code=$category_code");
        }

        $targetDir = __DIR__ . "/../images/category/";
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

            // Delete old image if new one uploaded
            if ($imgPath && !empty($existed_image) && file_exists('../../' . $existed_image)) {
                unlink('../../' . $existed_image);
            }
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
                'text' => $status === false
                    ? 'Category updated and deactivated successfully.'
                    : 'Category updated successfully.'
            ];
        } else {
            $_SESSION['flash_message'] = [
                'type' => 'error',
                'text' => 'Failed to update category. No changes were made.'
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

function hasProductsInCategory($category_code)
{
    return hasProductsInCategoryDao($category_code);
}

function checkCategoryExists($category_name, $exclude_id = 0)
{
    return checkCategoryExistsDao($category_name, $exclude_id);
}
