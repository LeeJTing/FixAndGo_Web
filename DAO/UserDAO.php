<?php
/**
 * UserDAO.php
 * location: DAO/UserDAO.php
 * Be responsible for all database operations related to users
 */

class UserDAO {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Obtain the total number of customers (members)
     */
    public function getTotalCustomerCount() {
        try {
            $stmt = $this->db->query(
                "SELECT COUNT(*) as total_customers 
                 FROM users 
                 WHERE user_role = 'Member'"
            );
            return $stmt->fetch()->total_customers;
        } catch (Exception $e) {
            error_log("Error in getTotalCustomerCount: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Obtain the number of new customers added last month
     */
    public function getLastMonthCustomerCount() {
        try {
            $stmt = $this->db->query(
                "SELECT COUNT(*) as last_month_customers 
                 FROM users 
                 WHERE user_role = 'Member'
                 AND MONTH(created_at) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH))
                 AND YEAR(created_at) = YEAR(DATE_SUB(NOW(), INTERVAL 1 MONTH))"
            );
            return $stmt->fetch()->last_month_customers;
        } catch (Exception $e) {
            error_log("Error in getLastMonthCustomerCount: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get the number of new customers added this month
     */
    public function getCurrentMonthCustomerCount() {
        try {
            $stmt = $this->db->query(
                "SELECT COUNT(*) as current_month_customers 
                 FROM users 
                 WHERE user_role = 'Member'
                 AND MONTH(created_at) = MONTH(NOW())
                 AND YEAR(created_at) = YEAR(NOW())"
            );
            return $stmt->fetch()->current_month_customers;
        } catch (Exception $e) {
            error_log("Error in getCurrentMonthCustomerCount: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get the most recently registered customers
     * @param int $limit returned qty
     */
    public function getRecentCustomers($limit = 10) {
        try {
            $stmt = $this->db->prepare(
                "SELECT 
                    u.user_id,
                    u.user_name,
                    u.user_email,
                    u.created_at,
                    up.contact_num,
                    up.gender
                 FROM users u
                 LEFT JOIN userprofile up ON u.user_id = up.user_id
                 WHERE u.user_role = 'Member'
                 ORDER BY u.created_at DESC
                 LIMIT ?"
            );
            $stmt->execute([$limit]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error in getRecentCustomers: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Search for customers
     */
    public function searchCustomers($searchTerm, $limit = 50) {
        try {
            $searchPattern = "%{$searchTerm}%";
            
            $stmt = $this->db->prepare(
                "SELECT 
                    u.user_id,
                    u.user_name,
                    u.user_email,
                    u.created_at,
                    up.contact_num,
                    up.gender
                 FROM users u
                 LEFT JOIN userprofile up ON u.user_id = up.user_id
                 WHERE u.user_role = 'Member'
                 AND (u.user_name LIKE ? 
                      OR u.user_email LIKE ? 
                      OR up.contact_num LIKE ?)
                 ORDER BY u.created_at DESC
                 LIMIT ?"
            );
            
            $stmt->execute([
                $searchPattern, 
                $searchPattern, 
                $searchPattern, 
                $limit
            ]);
            
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error in searchCustomers: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtain user information based on the ID
     */
    public function getUserById($userId) {
        try {
            $stmt = $this->db->prepare(
                "SELECT 
                    u.*,
                    up.gender,
                    up.contact_num,
                    up.date_of_birth
                 FROM users u
                 LEFT JOIN userprofile up ON u.user_id = up.user_id
                 WHERE u.user_id = ?"
            );
            $stmt->execute([$userId]);
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log("Error in getUserById: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Obtain customer growth trend data (for the past 12 months)
     */
    public function getCustomerGrowthData() {
        try {
            $stmt = $this->db->query(
                "SELECT 
                    DATE_FORMAT(created_at, '%b %Y') as label,
                    MONTH(created_at) as month,
                    YEAR(created_at) as year,
                    COUNT(*) as count
                 FROM users
                 WHERE user_role = 'Member'
                 AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                 GROUP BY YEAR(created_at), MONTH(created_at)
                 ORDER BY year, month"
            );
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error in getCustomerGrowthData: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtain all user statistics
     */
    public function getUserStats() {
        try {
            $stmt = $this->db->query(
                "SELECT 
                    COUNT(*) as total_users,
                    SUM(CASE WHEN user_role = 'Member' THEN 1 ELSE 0 END) as total_members,
                    SUM(CASE WHEN user_role = 'Admin' THEN 1 ELSE 0 END) as total_admins,
                    SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today_signups
                 FROM users"
            );
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log("Error in getUserStats: " . $e->getMessage());
            return (object)[
                'total_users' => 0,
                'total_members' => 0,
                'total_admins' => 0,
                'today_signups' => 0
            ];
        }
    }
}
?>