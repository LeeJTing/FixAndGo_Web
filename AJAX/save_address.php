<?php
require "../../_base.php";
require "../../DAO/profile_dao.php";

$response = ['success' => false, 'message' => ''];

// Only allow logged-in users to add addresses
$user_id = temp('USER_ID');
if (!$user_id) {
    $response['message'] = 'You must be logged in to add addresses';
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

if (is_post()) {
    $address_one = post('address_one');
    $address_two = post('address_two');
    $address_three = post('address_three');
    $state = post('state');
    $post_code = post('post_code');
    $country = post('country', 'Malaysia');

    // Validate required fields
    if (!$address_one || !$state || !$post_code) {
        $response['message'] = 'Missing required fields';
        echo json_encode($response);
        exit();
    }

    // Validate postal code (5 digits)
    if (!preg_match('/^\d{5}$/', $post_code)) {
        $response['message'] = 'Postal code must be 5 digits';
        echo json_encode($response);
        exit();
    }

    try {
        $result = addAddress($user_id, $address_one, $address_two, $address_three, $state, $post_code, $country);

        if ($result) {
            $response['success'] = true;
            $response['message'] = 'Address saved successfully';
            // Get the last inserted ID
            $lastId = $_db->lastInsertId();
            $response['address_id'] = $lastId;
            $response['address_display'] = [
                'address_id' => $lastId,
                'address_one' => $address_one,
                'address_two' => $address_two,
                'address_three' => $address_three,
                'state' => $state,
                'post_code' => $post_code,
                'country' => $country
            ];
        } else {
            $response['message'] = 'Failed to save address';
        }
    } catch (Exception $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Invalid request method';
}

header('Content-Type: application/json');
echo json_encode($response);
