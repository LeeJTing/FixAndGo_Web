<?php
// controller/customer-controller.php

require_once __DIR__ . '/../DAO/customer_dao.php';

class CustomerController {
    
    // Handle file upload
    private static function handleFileUpload($userId) {
        global $_db;
        
        // Check if there are any files uploaded
        if (!isset($_FILES['profile_image']) || $_FILES['profile_image']['error'] === UPLOAD_ERR_NO_FILE) {
            return false;
        }
        
        if ($_FILES['profile_image']['error'] !== 0) {
            error_log("File upload error code: " . $_FILES['profile_image']['error']);
            return false;
        }
        
        $targetDir = __DIR__ . '/../images/profile/';
        
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = $_FILES['profile_image']['type'];
        
        if (!in_array($fileType, $allowedTypes)) {
            return false;
        }
        
        if ($_FILES['profile_image']['size'] > 1048576) {
            return false;
        }
        
        $extension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
        $fileName = $userId . '.' . $extension;
        $targetFile = $targetDir . $fileName;
        
        // Delete old files (if they exist)
        if (file_exists($targetFile)) {
            unlink($targetFile);
        }
        
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFile)) {
            // Update or insert the profilepicture table
            $stmt = $_db->prepare("SELECT user_id FROM profilepicture WHERE user_id = ?");
            $stmt->execute([$userId]);
            
            $filePath = 'images/profile/' . $fileName;
            
            try {
                if ($stmt->fetch()) {
                    $stmt = $_db->prepare("UPDATE profilepicture SET file_path = ? WHERE user_id = ?");
                    $stmt->execute([$filePath, $userId]);
                } else {
                    $stmt = $_db->prepare("INSERT INTO profilepicture (user_id, file_path) VALUES (?, ?)");
                    $stmt->execute([$userId, $filePath]);
                }
                return true;
            } catch (Exception $e) {
                error_log("Database error: " . $e->getMessage());
                return false;
            }
        }
        
        return false;
    }
    
    // Handle the creation of users
    public static function handleCreate() {
        if (is_post()) {
            try {
                $data = [
                    'custom_user_id' => post('custom_user_id'),
                    'user_name' => post('user_name'),
                    'email' => post('email'),
                    'password' => post('password', '123456abc'),
                    'contact_num' => post('contact_num'),
                    'gender' => post('gender'),
                    'user_role' => post('user_role', 'Member'),
                    'account_status' => post('account_status', 'Unblock')
                ];

                // Email Uniqueness Check (reserved word)
                if (!is_unique($data['email'], 'users', 'email')) {
                    throw new Exception('Email already exists!');
                }

                // If there are any issues here (User ID rules/duplicate ids)
                // The DAO will directly throw
                CustomerDAO::createCustomer($data);

                flash('success', 'Customer created successfully!');
            } catch (Exception $e) {
                // Display the real errors thrown by the DAO or Controller
                flash('error', $e->getMessage());
            }

            redirect('adminCustomer.php');
        }
    }


    // Handle the update of users
    public static function handleUpdate() {
        if (is_post()) {
            $id = post('user_id');

            try {
                $data = [
                    'user_name'      => post('user_name'),
                    'email'          => post('email'),
                    'contact_num'    => post('contact_num'),
                    'gender'         => post('gender'),
                    'account_status' => post('account_status', 'Unblock'),
                    'password'       => post('password') // 可能为空
                ];

                // The DAO will be responsible for:
                // - user_name verification
                // - password verification (if input is provided)
                // - contact_num international format validation
                // - updating users + userprofile
                CustomerDAO::updateCustomer($id, $data);

                // Avatar upload (Failure does not affect user profile update)
                self::handleFileUpload($id);

                flash('success', 'Customer updated successfully!');
            } catch (Exception $e) {
                // Show the true cause of the error
                flash('error', $e->getMessage());
            }

            redirect('adminCustomer.php?edit=' . $id);
        }
    }

    
    // Handle the deletion of users
    public static function handleDelete($id) {
        if (CustomerDAO::deleteCustomer($id)) {
            flash('success', 'Customer deleted successfully!');
            redirect('adminCustomer.php');
        } else {
            flash('error', 'Failed to delete customer');
            redirect('adminCustomer.php');
        }
    }

}
?>