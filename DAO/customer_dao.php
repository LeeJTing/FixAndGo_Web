<?php
// DAO/customer_dao.php

class CustomerDAO {
    
    // 获取所有用户（支持搜索、筛选、排序, 增加分页参数）
    public static function getAllCustomers($search = '', $status = '', $sortBy = 'user_id', $role = 'Member', $limit = null, $offset = 0) {
        global $_db;
        
        $query = "SELECT u.*, up.contact_num, up.dob, up.gender, a.address_one, a.state 
                  FROM users u
                  LEFT JOIN userprofile up ON u.user_id = up.user_id
                  LEFT JOIN (
                    SELECT user_id, MIN(address_one) AS address_one, MIN(state) AS state
                    FROM address
                    GROUP BY user_id
                ) a ON u.user_id = a.user_id
                  WHERE u.user_role = ?";
        $params = [$role];
        
        if (!empty($search)) {
            $query .= " AND (u.user_name LIKE ? OR u.email LIKE ? OR u.user_id LIKE ?)";
            $searchParam = "%{$search}%";
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
        }
        
        if (!empty($status) && $status != 'All') {
            $query .= " AND u.account_status = ?";
            $params[] = $status;
        }
        
        // 防止 SQL 注入 - 白名单验证排序字段
        $allowedSort = ['user_id', 'user_name', 'account_status', 'email'];
        if (!in_array($sortBy, $allowedSort)) {
            $sortBy = 'user_id';
        }
        
        // 根据字段决定排序方向
        if ($sortBy == 'user_name' || $sortBy == 'email' || $sortBy == 'user_id') {
            // 名字和邮箱用升序 (A-Z)
            $query .= " ORDER BY u.$sortBy ASC";
        } else {
            // ID 和状态用降序 (最新的在前面)
            $query .= " ORDER BY u.$sortBy DESC";
        }
        
        // 【新增分页逻辑】: 只有当 limit 不为空且大于 0 时，才添加 LIMIT 和 OFFSET
        if (is_numeric($limit) && $limit > 0) {
            $query .= " LIMIT ? OFFSET ?";
            $params[] = (int)$limit;
            $params[] = (int)$offset;
        }
        
        $stmt = $_db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    // 根据 ID 获取单个用户
    public static function getCustomerById($id) {
        global $_db;
        
        $stmt = $_db->prepare("SELECT u.*, up.contact_num, up.dob, up.gender 
                                FROM users u
                                LEFT JOIN userprofile up ON u.user_id = up.user_id
                                WHERE u.user_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    // 创建新用户
    public static function createCustomer($data) {
        global $_db;
        
        try {
            $_db->beginTransaction();
            
            // 生成新的 user_id
            if (!empty($data['custom_user_id'])) {
                $userId = trim($data['custom_user_id']);

                // 只做一件事：检查是否已存在
                $stmt = $_db->prepare("SELECT COUNT(*) FROM users WHERE user_id = ?");
                $stmt->execute([$userId]);
                if ($stmt->fetchColumn() > 0) {
                    throw new Exception("User ID already exists.");
                }
            } else {
                // 自动生成 User ID
                $prefix = ($data['user_role'] === 'Admin') ? 'A' : 'M';

                $stmt = $_db->prepare("
                    SELECT MAX(CAST(SUBSTRING(user_id, 2) AS UNSIGNED)) AS max_id
                    FROM users
                    WHERE user_id REGEXP ?
                ");
                $stmt->execute(['^' . $prefix . '[0-9]{3}$']);
                $result = $stmt->fetch();

                $nextNum = ($result->max_id ?? 0) + 1;
                $userId = $prefix . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
            }
            
            // 插入 users 表
            $query = "INSERT INTO users (user_id, user_name, user_role, email, hash_password, account_status) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $_db->prepare($query);
            $password = !empty($data['password']) 
                ? $data['password'] 
                : '123456abc';

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt->execute([
                $userId,
                $data['user_name'],
                $data['user_role'],
                $data['email'],
                $hashedPassword,
                $data['account_status']
            ]);
            
            // 插入 userprofile 表（如果有电话号码）
            if (!empty($data['contact_num'])) {
                $stmt = $_db->prepare("INSERT INTO userprofile (user_id, contact_num, gender) VALUES (?, ?, ?)");
                $stmt->execute([$userId, $data['contact_num'], $data['gender'] ?? null]);
            }
            
            // 如果是 Member，创建购物车
            if ($data['user_role'] == 'Member') {
                $stmt = $_db->prepare("INSERT INTO cart (user_id) VALUES (?)");
                $stmt->execute([$userId]);
            }
            
            $_db->commit();
            return true;
        } catch (Exception $e) {
            $_db->rollBack();
            error_log("Create Customer Error: " . $e->getMessage());
            return false;
        }
    }
    
    // 更新用户
    public static function updateCustomer($id, $data) {
        global $_db;
        
        try {
            $_db->beginTransaction();
            
            // 1. 构建动态 SQL 查询
            $query = "UPDATE users SET user_name = ?, email = ?, account_status = ?";
            $params = [
                $data['user_name'],
                $data['email'],
                $data['account_status']
            ];

            // ✅ 核心逻辑：只有当密码不为空时，才更新密码字段
            if (!empty($data['password'])) {
                $query .= ", hash_password = ?";
                // 必须进行哈希加密
                $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            $query .= " WHERE user_id = ?";
            $params[] = $id;

            // 执行 users 表更新
            $stmt = $_db->prepare($query);
            $stmt->execute($params);
            
            // 2. 更新或插入 userprofile (这部分保持不变)
            $stmt = $_db->prepare("SELECT user_id FROM userprofile WHERE user_id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->fetch()) {
                // 更新
                $stmt = $_db->prepare("UPDATE userprofile SET contact_num = ?, gender = ? WHERE user_id = ?");
                $stmt->execute([$data['contact_num'], $data['gender'] ?? null, $id]);
            } else {
                // 插入
                $stmt = $_db->prepare("INSERT INTO userprofile (user_id, contact_num, gender) VALUES (?, ?, ?)");
                $stmt->execute([$id, $data['contact_num'], $data['gender'] ?? null]);
            }
            
            $_db->commit();
            return true;
        } catch (Exception $e) {
            $_db->rollBack();
            error_log("Update Error: " . $e->getMessage()); // 建议加上错误日志
            return false;
        }
    }
    
    // 删除用户
    public static function deleteCustomer($id) {
        global $_db;
        
        try {
            $_db->beginTransaction();
            
            // 删除相关数据
            $_db->prepare("DELETE FROM userprofile WHERE user_id = ?")->execute([$id]);
            $_db->prepare("DELETE FROM profilepicture WHERE user_id = ?")->execute([$id]);
            $_db->prepare("DELETE FROM address WHERE user_id = ?")->execute([$id]);
            $_db->prepare("DELETE FROM userdevices WHERE user_id = ?")->execute([$id]);
            $_db->prepare("DELETE FROM loyaltypoint WHERE user_id = ?")->execute([$id]);
            
            // 删除购物车相关
            $stmt = $_db->prepare("SELECT cart_id FROM cart WHERE user_id = ?");
            $stmt->execute([$id]);
            $cart = $stmt->fetch();
            if ($cart) {
                $_db->prepare("DELETE FROM cartitem WHERE cart_id = ?")->execute([$cart->cart_id]);
                $_db->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$id]);
            }
            
            // 删除用户
            $stmt = $_db->prepare("DELETE FROM users WHERE user_id = ?");
            $stmt->execute([$id]);
            
            $_db->commit();
            return true;
        } catch (Exception $e) {
            $_db->rollBack();
            return false;
        }
    }
    
    // 获取总数（用于分页）
    public static function getTotalCount($search = '', $status = '', $role = 'Member') {
        global $_db;

        $query = "SELECT COUNT(DISTINCT u.user_id) AS total
                FROM users u
                LEFT JOIN userprofile up ON u.user_id = up.user_id
                WHERE u.user_role = ?";
        $params = [$role];

        if (!empty($search)) {
            $query .= " AND (u.user_name LIKE ? OR u.email LIKE ? OR u.user_id LIKE ?)";
            $searchParam = "%{$search}%";
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
        }

        if (!empty($status) && $status != 'All') {
            $query .= " AND u.account_status = ?";
            $params[] = $status;
        }

        $stmt = $_db->prepare($query);
        $stmt->execute($params);
        return (int) $stmt->fetch()->total;
    }

}
?>