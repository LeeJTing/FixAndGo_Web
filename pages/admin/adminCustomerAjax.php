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
