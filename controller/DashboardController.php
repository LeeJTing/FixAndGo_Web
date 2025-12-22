<?php
/**
 * DashboardController.php
 * location: controller/DashboardController.php
 * Be responsible for the business logic of the dashboard
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
     * Get dashboard statistics
     * @return array An array containing all statistical indicators
     */
    public function getDashboardStats() {
        // income data
        $totalRevenue = $this->orderDAO->getTotalRevenue();
        $lastMonthRevenue = $this->orderDAO->getLastMonthRevenue();
        $revenueChange = $this->calculatePercentageChange($lastMonthRevenue, $totalRevenue);
        
        // Order data
        $todayOrders = $this->orderDAO->getTodayOrderCount();
        $yesterdayOrders = $this->orderDAO->getYesterdayOrderCount();
        $ordersChange = $this->calculatePercentageChange($yesterdayOrders, $todayOrders);
        
        // Customer data
        $totalCustomers = $this->userDAO->getTotalCustomerCount();
        $currentMonthCustomers = $this->userDAO->getCurrentMonthCustomerCount();
        $lastMonthCustomers = $this->userDAO->getLastMonthCustomerCount();
        
        // Calculate customer growth (current month vs last month)
        $customersChange = $this->calculatePercentageChange($lastMonthCustomers, $currentMonthCustomers);
        
        // Average order value
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
     * Get the latest order
     * @param int $limit returned qty
     * @return array order list
     */
    public function getRecentOrders($limit = 10) {
        return $this->orderDAO->getRecentOrders($limit);
    }
    
    /**
     * Obtain sales chart data
     * @param string $period time quantum ('month', 'week', 'day')
     * @return array Graph Data ['labels' => [], 'values' => []]
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
     * Search orders
     * @param string $searchTerm search keyword
     * @return array order list
     */
    public function searchOrders($searchTerm) {
        if (empty(trim($searchTerm))) {
            return $this->getRecentOrders(10);
        }
        return $this->orderDAO->searchOrders($searchTerm);
    }
    
    /**
     * Get order status statistics
     * @return array Order count for each status
     */
    public function getOrderStatusStats() {
        $statusCounts = $this->orderDAO->getOrderStatusCounts();
        $paymentCounts = $this->orderDAO->getPaymentStatusCounts();
        
        return [
            'orderStatus' => $statusCounts,
            'paymentStatus' => $paymentCounts
        ];
    }

    // ========== Private Helper Methods ==========
    
    /**
     * Calculate percentage change
     * @param float $oldValue Old value
     * @param float $newValue New value
     * @return float 变化百分比
     */
    private function calculatePercentageChange($oldValue, $newValue) {
        if ($oldValue == 0) {
            return $newValue > 0 ? 100 : 0;
        }
        return round((($newValue - $oldValue) / $oldValue) * 100, 1);
    }
    
    /**
     * Format monthly data
     */
    private function formatMonthlyData($rawData) {
        $labels = [];
        $values = [];
        
        // Generate past 12 months' labels
        for ($i = 11; $i >= 0; $i--) {
            $date = date('M Y', strtotime("-$i months"));
            $labels[] = $date;
            
            // Find corresponding month's data
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
     * Format weekly data
     */
    private function formatWeeklyData($rawData) {
        $labels = [];
        $values = [];
        
        // Generate past 7 days' labels
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
     * Format daily data (24 hours)
     */
    private function formatDailyData($rawData) {
        $labels = [];
        $values = [];

        // Generate 24-hour labels
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