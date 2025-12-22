<?php
// DAO/customer_dao.php

class CustomerDAO {
    
    // Obtain all users (supporting search, filtering, sorting, and adding pagination parameters)
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
        
        // Prevent SQL injection - Whitelist validates sorted fields
        $allowedSort = ['user_id', 'user_name', 'account_status', 'email'];
        if (!in_array($sortBy, $allowedSort)) {
            $sortBy = 'user_id';
        }
        
        // Determine the sorting direction based on the fields
        if ($sortBy == 'user_name' || $sortBy == 'email' || $sortBy == 'user_id') {
            // Names , id and email addresses should be in ascending order (A-Z).
            $query .= " ORDER BY u.$sortBy ASC";
        } else {
            // ID and status use descending order (latest first)
            $query .= " ORDER BY u.$sortBy DESC";
        }
        
        // 【 New Paging Logic 】: Only when limit is not empty and greater than 0 will LIMIT and OFFSET be added
        if (is_numeric($limit) && $limit > 0) {
            $query .= " LIMIT ? OFFSET ?";
            $params[] = (int)$limit;
            $params[] = (int)$offset;
        }
        
        $stmt = $_db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    // Obtain a single user based on the ID
    public static function getCustomerById($id) {
        global $_db;
        
        $stmt = $_db->prepare("SELECT u.*, up.contact_num, up.dob, up.gender 
                                FROM users u
                                LEFT JOIN userprofile up ON u.user_id = up.user_id
                                WHERE u.user_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    // Create a new user
    public static function createCustomer($data) {
        global $_db;
        
        try {
            $_db->beginTransaction();
            
            // Generate a new user_id
            if (!empty($data['custom_user_id'])) {
                $userId = trim($data['custom_user_id']);

                // Format check: 4-12. No @ and Spaces are allowed. Only alphanumeric _ - is permitted
                if (!preg_match('/^[A-Za-z0-9_-]{4,12}$/', $userId)) {
                    throw new Exception(
                        "User ID must be 4–12 characters long and can only contain letters, numbers, '_' or '-'."
                    );
                }

                // Just do one thing: Check if it already exists
                $stmt = $_db->prepare("SELECT COUNT(*) FROM users WHERE user_id = ?");
                $stmt->execute([$userId]);
                if ($stmt->fetchColumn() > 0) {
                    throw new Exception("User ID already exists.");
                }
            } else {
                // Automatically generate the User ID
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

            // Verification will only be conducted if a new password is entered
            if (!empty($data['password'])) {
                if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,12}$/', $data['password'])) {
                    throw new Exception(
                        'Password must be 8–12 characters long and contain at least one letter and one number.'
                    );
                }
            }

            if (!empty($data['contact_num'])) {
                if (!preg_match('/^\+[1-9][0-9]{7,14}$/', $data['contact_num'])) {
                    throw new Exception(
                        'Contact number must be in international format, e.g. +60187824530'
                    );
                }
            }

            $userName = trim($data['user_name']);

            if (!preg_match('/^[A-Za-z ]{4,50}$/', $userName)) {
                throw new Exception(
                    'User name must be 4–50 characters long and contain only English letters and spaces.'
                );
            }
            
            // Insert the users table
            $query = "INSERT INTO users (user_id, user_name, user_role, email, hash_password, account_status) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $_db->prepare($query);
            $password = !empty($data['password']) 
                ? $data['password'] 
                : '123456abc';

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt->execute([
                $userId,
                $userName,
                $data['user_role'],
                $data['email'],
                $hashedPassword,
                $data['account_status']
            ]);
            
            // Insert the userprofile table (if there is a phone number)
            if (!empty($data['contact_num'])) {
                $stmt = $_db->prepare("INSERT INTO userprofile (user_id, contact_num, gender) VALUES (?, ?, ?)");
                $stmt->execute([$userId, $data['contact_num'], $data['gender'] ?? null]);
            }

            // If it's a Member, create a shopping cart
            if ($data['user_role'] == 'Member') {
                $stmt = $_db->prepare("INSERT INTO cart (user_id) VALUES (?)");
                $stmt->execute([$userId]);
            }
            
            $_db->commit();
            return true;
        } catch (Exception $e) {
            $_db->rollBack();
            throw $e;
        }
    }
    
    // Update user
    public static function updateCustomer($id, $data) {
        global $_db;
        
        try {
            $_db->beginTransaction();

            // User Name verification (when updated)
            $userName = trim($data['user_name']);

            if (!preg_match('/^[A-Za-z ]{4,50}$/', $userName)) {
                throw new Exception(
                    'User name must be 4–50 characters long and contain only English letters and spaces.'
                );
            }

            // password verifying
            if (!empty($data['password'])) {
                if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,12}$/', $data['password'])) {
                    throw new Exception(
                        'Password must be 8–12 characters long and contain at least one letter and one number.'
                    );
                }
            }

            if (!empty($data['contact_num'])) {
                if (!preg_match('/^\+[1-9][0-9]{7,14}$/', $data['contact_num'])) {
                    throw new Exception(
                        'Contact number must be in international format, e.g. +60187824530'
                    );
                }
            }
            
            // Build dynamic SQL queries
            $query = "UPDATE users SET user_name = ?, email = ?, account_status = ?";
            $params = [
                $userName,
                $data['email'],
                $data['account_status']
            ];

            // Core logic: Only update the password field if a new password is provided
            if (!empty($data['password'])) {
                $query .= ", hash_password = ?";
                // Must be hashed
                $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            $query .= " WHERE user_id = ?";
            $params[] = $id;

            // Execute users table update
            $stmt = $_db->prepare($query);
            $stmt->execute($params);
            
            // 2. Update or insert userprofile (this part remains unchanged)
            $stmt = $_db->prepare("SELECT user_id FROM userprofile WHERE user_id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->fetch()) {
                // Update
                $stmt = $_db->prepare("UPDATE userprofile SET contact_num = ?, gender = ? WHERE user_id = ?");
                $stmt->execute([$data['contact_num'], $data['gender'] ?? null, $id]);
            } else {
                // Insert
                $stmt = $_db->prepare("INSERT INTO userprofile (user_id, contact_num, gender) VALUES (?, ?, ?)");
                $stmt->execute([$id, $data['contact_num'], $data['gender'] ?? null]);
            }
            
            $_db->commit();
            return true;
        } catch (Exception $e) {
            $_db->rollBack();
            throw $e;
        }
    }
    
    // Delete User
    public static function deleteCustomer($id) {
        global $_db;

        try {
            $stmt = $_db->prepare("DELETE FROM users WHERE user_id = ?");
            $stmt->execute([$id]);

            return $stmt->rowCount() > 0;

        } catch (Exception $e) {
            error_log("Delete Customer Error: " . $e->getMessage());
            return false;
        }
    }
    
    // Get the total (for pagination)
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