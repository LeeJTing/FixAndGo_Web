<?php
/**
 * DashboardController.php
 * 位置: controller/DashboardController.php
 * 负责仪表板的业务逻辑
 */

require_once __DIR__ . '/../DAO/OrderDAO.php';
require_once __DIR__ . '/../DAO/UserDAO.php';

class DashboardController {
    private $orderDAO;
    private $userDAO;
    
    public function __construct($db) {
        $this->orderDAO = new OrderDAO($db);
        $this->userDAO = new UserDAO($db);
    }
    
    /**
     * 获取仪表板统计数据
     * @return array 包含所有统计指标的数组
     */
    public function getDashboardStats() {
        // 收入数据
        $totalRevenue = $this->orderDAO->getTotalRevenue();
        $lastMonthRevenue = $this->orderDAO->getLastMonthRevenue();
        $revenueChange = $this->calculatePercentageChange($lastMonthRevenue, $totalRevenue);
        
        // 订单数据
        $todayOrders = $this->orderDAO->getTodayOrderCount();
        $yesterdayOrders = $this->orderDAO->getYesterdayOrderCount();
        $ordersChange = $this->calculatePercentageChange($yesterdayOrders, $todayOrders);
        
        // 客户数据
        $totalCustomers = $this->userDAO->getTotalCustomerCount();
        $currentMonthCustomers = $this->userDAO->getCurrentMonthCustomerCount();
        $lastMonthCustomers = $this->userDAO->getLastMonthCustomerCount();
        
        // 计算客户增长（用本月新增 vs 上月新增）
        $customersChange = $this->calculatePercentageChange($lastMonthCustomers, $currentMonthCustomers);
        
        // 平均订单金额
        $avgOrder = $this->orderDAO->getAverageOrderValue();
        $lastMonthAvg = $this->orderDAO->getLastMonthAverageOrderValue();
        $avgOrderChange = $this->calculatePercentageChange($lastMonthAvg, $avgOrder);
        
        return [
            'revenue' => $totalRevenue,
            'revenueChange' => $revenueChange,
            'orders' => $todayOrders,
            'ordersChange' => $ordersChange,
            'customers' => $totalCustomers,
            'customersChange' => $customersChange,
            'avgOrder' => $avgOrder,
            'avgOrderChange' => $avgOrderChange
        ];
    }
    
    /**
     * 获取最近订单
     * @param int $limit 返回数量
     * @return array 订单列表
     */
    public function getRecentOrders($limit = 10) {
        return $this->orderDAO->getRecentOrders($limit);
    }
    
    /**
     * 获取销售图表数据
     * @param string $period 时间段 ('month', 'week', 'day')
     * @return array 图表数据 ['labels' => [], 'values' => []]
     */
    public function getSalesChartData($period = 'month') {
        $rawData = $this->orderDAO->getSalesDataByPeriod($period);
        
        switch($period) {
            case 'month':
                return $this->formatMonthlyData($rawData);
            case 'week':
                return $this->formatWeeklyData($rawData);
            case 'day':
                return $this->formatDailyData($rawData);
            default:
                return ['labels' => [], 'values' => []];
        }
    }
    
    /**
     * 搜索订单
     * @param string $searchTerm 搜索关键词
     * @return array 订单列表
     */
    public function searchOrders($searchTerm) {
        if (empty(trim($searchTerm))) {
            return $this->getRecentOrders(10);
        }
        return $this->orderDAO->searchOrders($searchTerm);
    }
    
    /**
     * 获取订单状态统计
     * @return array 各状态的订单数量
     */
    public function getOrderStatusStats() {
        $statusCounts = $this->orderDAO->getOrderStatusCounts();
        $paymentCounts = $this->orderDAO->getPaymentStatusCounts();
        
        return [
            'orderStatus' => $statusCounts,
            'paymentStatus' => $paymentCounts
        ];
    }
    
    // ========== 私有辅助方法 ==========
    
    /**
     * 计算百分比变化
     * @param float $oldValue 旧值
     * @param float $newValue 新值
     * @return float 变化百分比
     */
    private function calculatePercentageChange($oldValue, $newValue) {
        if ($oldValue == 0) {
            return $newValue > 0 ? 100 : 0;
        }
        return round((($newValue - $oldValue) / $oldValue) * 100, 1);
    }
    
    /**
     * 格式化月度数据
     */
    private function formatMonthlyData($rawData) {
        $labels = [];
        $values = [];
        
        // 生成过去12个月的标签
        for ($i = 11; $i >= 0; $i--) {
            $date = date('M Y', strtotime("-$i months"));
            $labels[] = $date;
            
            // 查找对应月份的数据
            $found = false;
            foreach ($rawData as $row) {
                if ($row->label === $date) {
                    $values[] = floatval($row->total);
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $values[] = 0;
            }
        }
        
        return ['labels' => $labels, 'values' => $values];
    }
    
    /**
     * 格式化周数据
     */
    private function formatWeeklyData($rawData) {
        $labels = [];
        $values = [];
        
        // 生成过去7天的标签
        for ($i = 6; $i >= 0; $i--) {
            $date = date('D', strtotime("-$i days"));
            $fullDate = date('Y-m-d', strtotime("-$i days"));
            $labels[] = $date;
            
            $found = false;
            foreach ($rawData as $row) {
                if ($row->date === $fullDate) {
                    $values[] = floatval($row->total);
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $values[] = 0;
            }
        }
        
        return ['labels' => $labels, 'values' => $values];
    }
    
    /**
     * 格式化日数据（24小时）
     */
    private function formatDailyData($rawData) {
        $labels = [];
        $values = [];
        
        // 生成24小时的标签
        for ($i = 0; $i < 24; $i++) {
            $labels[] = sprintf('%02d:00', $i);
            
            $found = false;
            foreach ($rawData as $row) {
                if (intval($row->hour) === $i) {
                    $values[] = floatval($row->total);
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $values[] = 0;
            }
        }
        
        return ['labels' => $labels, 'values' => $values];
    }
}
?>