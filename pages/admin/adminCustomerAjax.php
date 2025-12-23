<?php
require_once __DIR__ . '/../../_base.php';
require_once __DIR__ . '/../../DAO/customer_dao.php';

header('Content-Type: application/json');

// only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit;
}

// get current login user
$currentUser = getCurrentUser();
if (!$currentUser) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized'
    ]);
    exit;
}

// read JSON
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing user ID'
    ]);
    exit;
}

$userId = $input['id'];

// ❌ 禁止删除自己
if ($userId === $currentUser->user_id) {
    echo json_encode([
        'success' => false,
        'message' => 'You cannot delete your own account.'
    ]);
    exit;
}

// delete
$result = CustomerDAO::deleteCustomer($userId);

if ($result) {
    echo json_encode([
        'success' => true
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to delete user'
    ]);
}
