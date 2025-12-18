<?php
/**
 * OrderDAO.php
 * 位置: DAO/OrderDAO.php
 * 负责所有与订单相关的数据库操作
 */

class OrderDAO {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * 获取总收入（已支付的订单）
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
     * 获取上月收入
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
     * 获取今天的订单数
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
     * 获取昨天的订单数
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
     * 获取平均订单金额
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
     * 获取上月平均订单金额
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
     * 获取最近的订单列表
     * @param int $limit 返回的订单数量
     * @return array 订单列表
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
     * 根据时间段获取销售数据
     * @param string $period 'month', 'week', 或 'day'
     * @return array 销售数据
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
     * 获取过去12个月的销售数据
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
     * 获取过去7天的销售数据
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
     * 获取今天每小时的销售数据
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
     * 搜索订单
     * @param string $searchTerm 搜索关键词
     * @param int $limit 返回数量限制
     * @return array 订单列表
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
     * 根据ID获取订单详情
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
     * 统计各状态的订单数量
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
     * 统计各支付状态的订单数量
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