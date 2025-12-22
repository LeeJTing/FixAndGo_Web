<?php
/**
 * OrderDAO.php
 * location: DAO/OrderDAO.php
 * Be responsible for all database operations related to orders
 */

class OrderDAO {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Obtain the total revenue (paid orders)
     */
    public function getTotalRevenue() {
        $stmt = $this->db->query(
            "SELECT COALESCE(SUM(total_price), 0) as total_revenue 
             FROM orders 
             WHERE payment_status = 'Paid'"
        );
        return $stmt->fetch()->total_revenue;
    }
    
    /**
     * Obtain the revenue from last month
     */
    public function getLastMonthRevenue() {
        $stmt = $this->db->query(
            "SELECT COALESCE(SUM(total_price), 0) as last_month_revenue 
             FROM orders 
             WHERE payment_status = 'Paid' 
             AND MONTH(order_at) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH))
             AND YEAR(order_at) = YEAR(DATE_SUB(NOW(), INTERVAL 1 MONTH))"
        );
        return $stmt->fetch()->last_month_revenue;
    }
    
    /**
     * Get today's order quantity
     */
    public function getTodayOrderCount() {
        $stmt = $this->db->query(
            "SELECT COUNT(*) as today_orders 
             FROM orders 
             WHERE DATE(order_at) = CURDATE()"
        );
        return $stmt->fetch()->today_orders;
    }
    
    /**
     * Get the number of orders from yesterday
     */
    public function getYesterdayOrderCount() {
        $stmt = $this->db->query(
            "SELECT COUNT(*) as yesterday_orders 
             FROM orders 
             WHERE DATE(order_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)"
        );
        return $stmt->fetch()->yesterday_orders;
    }
    
    /**
     * Obtain the average order amount
     */
    public function getAverageOrderValue() {
        $stmt = $this->db->query(
            "SELECT COALESCE(AVG(total_price), 0) as avg_order 
             FROM orders 
             WHERE payment_status = 'Paid'"
        );
        return $stmt->fetch()->avg_order;
    }
    
    /**
     * Obtain the average order amount of last month
     */
    public function getLastMonthAverageOrderValue() {
        $stmt = $this->db->query(
            "SELECT COALESCE(AVG(total_price), 0) as last_month_avg 
             FROM orders 
             WHERE payment_status = 'Paid'
             AND MONTH(order_at) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH))
             AND YEAR(order_at) = YEAR(DATE_SUB(NOW(), INTERVAL 1 MONTH))"
        );
        return $stmt->fetch()->last_month_avg;
    }
    
    /**
     * Get the latest order list
     * @param int $limit he number of returned orders
     * @return array order list
     */
    public function getRecentOrders($limit = 10) {
        $stmt = $this->db->prepare(
            "SELECT 
                o.order_id,
                o.order_at,
                o.deliver_at,
                o.status,
                o.payment_status,
                o.total_price,
                u.user_name
             FROM orders o
             LEFT JOIN users u ON o.user_id = u.user_id
             ORDER BY o.order_at DESC
             LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Obtain sales data based on time periods
     * @param string $period 'month', 'week', or 'day'
     * @return array sales statistics
     */
    public function getSalesDataByPeriod($period) {
        switch($period) {
            case 'month':
                return $this->getSalesDataByMonth();
            case 'week':
                return $this->getSalesDataByWeek();
            case 'day':
                return $this->getSalesDataByDay();
            default:
                return [];
        }
    }
    
    /**
     * Obtain the sales data for the past 12 months
     */
    private function getSalesDataByMonth() {
        $stmt = $this->db->query(
            "SELECT 
                DATE_FORMAT(order_at, '%b %Y') as label,
                MONTH(order_at) as month,
                YEAR(order_at) as year,
                DATE(order_at) as order_date,
                COALESCE(SUM(total_price), 0) as total
             FROM orders 
             WHERE payment_status = 'Paid'
             AND order_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
             GROUP BY YEAR(order_at), MONTH(order_at)
             ORDER BY year, month"
        );
        return $stmt->fetchAll();
    }
    
    /**
     * Obtain the sales data of the past 7 days
     */
    private function getSalesDataByWeek() {
        $stmt = $this->db->query(
            "SELECT 
                DATE_FORMAT(order_at, '%a') as label,
                DATE(order_at) as date,
                COALESCE(SUM(total_price), 0) as total
             FROM orders 
             WHERE payment_status = 'Paid'
             AND order_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
             GROUP BY DATE(order_at)
             ORDER BY date"
        );
        return $stmt->fetchAll();
    }
    
    /**
     * Get the sales data for each hour today
     */
    private function getSalesDataByDay() {
        $stmt = $this->db->query(
            "SELECT 
                HOUR(order_at) as hour,
                COALESCE(SUM(total_price), 0) as total
             FROM orders 
             WHERE payment_status = 'Paid'
             AND DATE(order_at) = CURDATE()
             GROUP BY HOUR(order_at)
             ORDER BY hour"
        );
        return $stmt->fetchAll();
    }
    
    /**
     * Search for orders
     * @param string $searchTerm search keyword
     * @param int $limit returned quantity limit
     * @return array order list
     */
    public function searchOrders($searchTerm, $limit = 50) {
        $searchPattern = "%{$searchTerm}%";
        
        $stmt = $this->db->prepare(
            "SELECT 
                o.order_id,
                o.order_at,
                o.deliver_at,
                o.status,
                o.payment_status,
                o.total_price,
                u.user_name
             FROM orders o
             LEFT JOIN users u ON o.user_id = u.user_id
             WHERE o.order_id LIKE ?
                OR u.user_name LIKE ?
                OR o.status LIKE ?
                OR o.payment_status LIKE ?
             ORDER BY o.order_at DESC
             LIMIT ?"
        );
        
        $stmt->execute([
            $searchPattern, 
            $searchPattern, 
            $searchPattern, 
            $searchPattern, 
            $limit
        ]);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Obtain the order details based on the ID
     */
    public function getOrderById($orderId) {
        $stmt = $this->db->prepare(
            "SELECT 
                o.*,
                u.user_name,
                u.user_email
             FROM orders o
             LEFT JOIN users u ON o.user_id = u.user_id
             WHERE o.order_id = ?"
        );
        $stmt->execute([$orderId]);
        return $stmt->fetch();
    }
    
    /**
     * Count the number of orders in each status
     */
    public function getOrderStatusCounts() {
        $stmt = $this->db->query(
            "SELECT 
                status,
                COUNT(*) as count
             FROM orders
             GROUP BY status"
        );
        return $stmt->fetchAll();
    }
    
    /**
     * Count the number of orders in each payment status
     */
    public function getPaymentStatusCounts() {
        $stmt = $this->db->query(
            "SELECT 
                payment_status,
                COUNT(*) as count
             FROM orders
             GROUP BY payment_status"
        );
        return $stmt->fetchAll();
    }
}
?>