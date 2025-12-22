<?php
if (substr(php_sapi_name(), 0, 3) !== 'cli') {
    @header('Content-Type: application/json; charset=utf-8');
}

ob_start();

try {
    require_once __DIR__ . '/../_base.php';
    require_once __DIR__ . '/../DAO/profile_dao.php';
} catch (Throwable $e) {
    if (!headers_sent()) header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => 'Server include error: ' . $e->getMessage()]);
    exit;
}

$response = ['success' => false, 'message' => ''];

try {
    $user_id = temp('USER_ID') ?? null;
    if (!$user_id) {
        $response['message'] = 'You must be logged in to add addresses';
        ob_end_clean();
        echo json_encode($response);
        exit();
    }

    if (!is_post()) {
        $response['message'] = 'Invalid request method';
        ob_end_clean();
        echo json_encode($response);
        exit();
    }
    $address_id = post('address_id', '');

    if (empty($address_id) && getAddressCountByUserId($user_id) >= 3) {
        $response['message'] = 'You may add at most 3 addresses';
        ob_end_clean();
        echo json_encode($response);
        exit();
    }
    $address_name = post('address_name', 'Home');
    $address_one = post('address_one');
    $address_two = post('address_two');
    $address_three = post('address_three');
    $state = post('state');
    $post_code = post('post_code');
    $country = post('country', 'Malaysia');
    $address_name = post('address_name', 'Home');

    // Validate required fields
    if (!$address_one || !$state || !$post_code) {
        $response['message'] = 'Missing required fields';
        ob_end_clean();
        echo json_encode($response);
        exit();
    }

    // Validate postal code (5 digits)
    if (!preg_match('/^\d{5}$/', $post_code)) {
        $response['message'] = 'Postal code must be 5 digits';
        ob_end_clean();
        echo json_encode($response);
        exit();
    }

    if (!empty($address_id)) {
        // updateAddress($address_id, $address_one, $address_two, $address_three, $state, $post_code, $country)
        $ok = updateAddress($address_id,$address_name, $address_one, $address_two, $address_three, $state, $post_code, $country);
        if ($ok) {
            $response['success'] = true;
            $response['message'] = 'Address updated successfully';
            $response['address_id'] = (int)$address_id;
            $response['address_display'] = [
                'address_id' => (int)$address_id,
                'address_name' => $address_name,
                'address_one' => $address_one,
                'address_two' => $address_two,
                'address_three' => $address_three,
                'state' => $state,
                'post_code' => $post_code,
                'country' => $country
            ];
        } else {
            $response['message'] = 'Failed to update address';
        }
    } else {
        $newId = addAddress($user_id, $address_one, $address_two, $address_three, $state, $post_code, $country, $address_name);
        if ($newId !== false && (int)$newId > 0) {
            // updateAddress($address_id, $address_name, $address_one, $address_two, $address_three, $state, $post_code, $country)
            $ok = updateAddress($address_id, $address_name, $address_one, $address_two, $address_three, $state, $post_code, $country);
            $response['address_id'] = (int)$newId;
            $response['address_display'] = [
                'address_id' => (int)$newId,
                'address_name' => $address_name,
                'address_one' => $address_one,
                'address_two' => $address_two,
                'address_three' => $address_three,
                'state' => $state,
                'post_code' => $post_code,
                'country' => $country
            ];
        } else {
            $response['message'] = 'Failed to save address (DB returned false)';
        }
    }
} catch (Throwable $e) {
    $response['message'] = 'Server error: ' . $e->getMessage();
}

// Clear any buffered output (warnings/HTML) and send JSON
if (ob_get_length() !== false) {
    @ob_end_clean();
}
if (!headers_sent()) header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
exit;
