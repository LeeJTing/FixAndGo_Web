<?php
require_once __DIR__ . '/../dao/security_dao.php';

if (is_post() && get('update') == 'profile') {
    $id = temp('USER_ID');
    $name = post('user_name');
    $file = $_FILES['profile_image'] ?? NULL;
    $file_path = getUserProfilePicture($id);
    $contact_num = post('contact_number') ? post('contact_number') : NULL;
    $dob = post('dob') ? post('dob') : NULL;
    $gender = post('gender') ? post('gender') : NULL;

    if ($file && $file['error'] === UPLOAD_ERR_OK) {
        $file_path = generateFilePath($file['name']);

        // Build filesystem target path and ensure directory exists
        $targetPath = realpath(__DIR__ . '/..') . $file_path; // realpath(__DIR__.'/..') + web-path
        $targetDir = dirname($targetPath);
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            // success: $file_path holds the web-accessible path to store in DB
        } else {
            error_log('Failed to move uploaded file to ' . $targetPath);
        }
    }

    temp('USER_NAME', $name);

    updateProfile($id, $name, $contact_num, $dob, $gender, $file_path);
}

if (is_post() && get('update')) {
    $id = temp('USER_ID');
    $address_id = post('address_id');
    $address_name = post('address_name');
    $address_one = post('address_one');
    $address_two = post('address_two') ? post('address_two') : NULL;
    $address_three = post('address_three') ? post('address_three') : NULL;
    $post_code = post('post_code');
    $state = post('state');
    $country = post('country');

    if (get('update') == 'addaddress')
        addAddress($id, $address_one, $address_two, $address_three, $state, $post_code, $country, $address_name);

    if (get('update') == 'updateaddress')
        updateAddress($address_id, $address_name, $address_one, $address_two, $address_three, $state, $post_code, $country, $address_name);
}

if (is_get() && get('deleteAddress') != '') {
    $id = get('deleteAddress');
    if (is_exists($id, 'Address', 'address_id')) {
        deleteAddress($id);
    }
}


// Handle change password submitted from profile page
if (is_post() && get('update') == 'password') {
    $id = temp('USER_ID');
    $current = post('old_password');
    $new = post('new_password');
    $confirm = post('confirm_password');
    $user = getUserById($id);

    // Verify current password using password_verify (supports bcrypt)
    if (!password_verify($current, $user->hash_password)) {
        echo "<script>alert('Invalid old password! Please Try Again!!');</script>";
    } else {
        // Hash new password using PHP's password_hash
        $newHash = password_hash($new, PASSWORD_DEFAULT);

        updateUserPassword($id, $newHash);
        echo "<script>alert('Password has been changed!!');</script>";
    }
}
