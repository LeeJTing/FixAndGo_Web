<?php
/**
 * getDashboardData.php
 * 位置: pages/admin/getDashboardData.php
 * API接口 - 提供图表数据
 */

require '../../_base.php';
require_once '../../controller/DashboardController.php';

// 设置响应头为JSON格式
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// 检查用户是否登录且为管理员
$currentUser = getCurrentUser();
if (!$currentUser || $currentUser->user_role !== 'Admin') {
    http_response_code(403);
    echo json_encode([
        'error' => 'Unauthorized access',
        'labels' => [],
        'values' => []
    ]);
    exit;
}

try {
    // 获取时间段参数
    $period = $_GET['period'] ?? 'month';
    
    // 验证period参数
    if (!in_array($period, ['month', 'week', 'day'])) {
        $period = 'month';
    }
    
    // 初始化控制器
    $dashboardController = new DashboardController($_db);
    
    // 获取图表数据
    $chartData = $dashboardController->getSalesChartData($period);
    
    // 返回JSON数据
    echo json_encode($chartData);
    
} catch (Exception $e) {
    // 错误处理
    error_log("Dashboard API Error: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'error' => 'Internal server error',
        'message' => $e->getMessage(),
        'labels' => ['No Data'],
        'values' => [0]
    ]);
}
?>