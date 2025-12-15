<?php

if (is_post()) {
    $id = temp('USER_ID');
    $name = post('user_name');
    $file = $_FILES['profile_image'] ?? null;
    $file_path = getUserProfilePicture($id);
    $contact_num = post('contact_number') ?? null;
    $dob = post('dob') ?? null;
    $gender = post('gender') ?? null;

    if ($file && $file['error'] === UPLOAD_ERR_OK) {
       $file_path = generateFilePath($file['name']);
       echo $file_path;
       if (move_uploaded_file($file['tmp_name'], __DIR__ . '/..' . $file_path)) {
            echo "File uploaded successfully!";
        } else {
            echo "Failed to upload file.";
        }
    }

    temp('USER_NAME', $name);

    updateProfile($id, $name, $contact_num, $dob, $gender, $file_path);

}
?>