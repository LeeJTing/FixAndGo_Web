<?php
// controller/customer-controller.php

require_once __DIR__ . '/../DAO/customer_dao.php';

class CustomerController {
    
    // 处理文件上传
    private static function handleFileUpload($userId) {
        global $_db;
        
        // 检查是否有文件上传
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
        
        // 删除旧文件（如果存在）
        if (file_exists($targetFile)) {
            unlink($targetFile);
        }
        
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFile)) {
            // 更新或插入 profilepicture 表
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
    
    // 处理创建用户
    public static function handleCreate() {
        if (is_post()) {
            $data = [
                'custom_user_id' => post('custom_user_id'), // 新增：自定义 User ID
                'user_name' => post('user_name'),
                'email' => post('email'),
                'password' => post('password', '123456abc'), // 默认密码
                'contact_num' => post('contact_num'),
                'gender' => post('gender'),
                'user_role' => post('user_role', 'Member'),
                'account_status' => post('account_status', 'Unblock')
            ];
            
            if (!is_unique($data['email'], 'users', 'email')) {
                flash('error', 'Email already exists!');
                redirect('adminCustomer.php');
                return;
            }
            
            if (CustomerDAO::createCustomer($data)) {
                flash('success', 'Customer created successfully!');
                redirect('adminCustomer.php');
            } else {
                flash('error', 'Failed to create customer. Check if User ID already exists.');
                redirect('adminCustomer.php');
            }
        }
    }
    
    // 处理更新用户
    public static function handleUpdate() {
        if (is_post()) {
            $id = post('user_id');
            
            $data = [
                'user_name' => post('user_name'),
                'email' => post('email'),
                'contact_num' => post('contact_num'),
                'gender' => post('gender'),
                'account_status' => post('account_status', 'Unblock'),
                'password' => post('password')
            ];
            
            if (CustomerDAO::updateCustomer($id, $data)) {
                // 处理头像上传
                $uploadResult = self::handleFileUpload($id);
                
                if ($uploadResult) {
                    flash('success', 'Customer and profile picture updated successfully!');
                } else {
                    flash('success', 'Customer updated successfully!');
                }
                redirect('adminCustomer.php?edit=' . $id);
            } else {
                flash('error', 'Failed to update customer');
                redirect('adminCustomer.php?edit=' . $id);
            }
        }
    }
    
    // 处理删除用户
    public static function handleDelete($id) {
        global $_db;

        try {
            // 1️⃣ 查头像路径（如果有）
            $stmt = $_db->prepare("SELECT file_path FROM profilepicture WHERE user_id = ?");
            $stmt->execute([$id]);
            $profile = $stmt->fetch(PDO::FETCH_ASSOC);

            // 2️⃣ 删除用户（主表）
            if (!CustomerDAO::deleteCustomer($id)) {
                throw new Exception('Delete user failed');
            }

            // 3️⃣ 删除 profilepicture 记录
            if ($profile) {
                $stmt = $_db->prepare("DELETE FROM profilepicture WHERE user_id = ?");
                $stmt->execute([$id]);

                // 4️⃣ 删除实体图片文件
                $pattern = __DIR__ . '/../images/profile/' . $id . '.*';
                foreach (glob($pattern) as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
            }

            flash('success', 'Customer deleted successfully!');
        } catch (Exception $e) {
            error_log($e->getMessage());
            flash('error', 'Failed to delete customer');
        }

        redirect('adminCustomer.php');
    }

}
?>