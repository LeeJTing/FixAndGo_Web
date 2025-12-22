<?php
/**
 * getDashboardData.php
 * location: pages/admin/getDashboardData.php
 * API interface - Provides chart data
 */

require '../../_base.php';
require_once '../../controller/DashboardController.php';

// Set the response header to JSON format
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Check if the user is logged in and is an administrator
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
    // Obtain the time period parameters
    $period = $_GET['period'] ?? 'month';
    $type   = $_GET['type'] ?? 'sales';
    
    // Validate period parameter
    if (!in_array($period, ['month', 'week', 'day'])) {
        $period = 'month';
    }
    
    // Initialize the controller
    $dashboardController = new DashboardController($_db);
    
    // Decide which chart data to return
    switch ($type) {
        case 'sales':
            $data = $dashboardController->getSalesChartData($period);
            break;

        case 'orders':
            $data = $dashboardController->getOrderCountChartData($period);
            break;

        case 'status':
            $data = $dashboardController->getOrderStatusPieData();
            break;

        default:
            $data = ['labels' => [], 'values' => []];
    }

    // Return JSON data
    echo json_encode($data);
    
} catch (Exception $e) {
    // Error handling
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