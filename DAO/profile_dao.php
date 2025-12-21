<?php

// Get all addresses for a user
function getAddressesByUserId($user_id)
{
    global $_db;
    $stmt = $_db->prepare("SELECT * FROM address WHERE user_id = ? ORDER BY address_id DESC");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}

// Get a specific address by ID
function getAddressById($address_id)
{
    global $_db;
    $stmt = $_db->prepare("SELECT * FROM address WHERE address_id = ?");
    $stmt->execute([$address_id]);
    return $stmt->fetch();
}

// Add a new address for a user
function addAddress($user_id, $address_one, $address_two, $address_three, $state, $post_code, $country = 'Malaysia', $address_name = 'Home')
{
    global $_db;
    try {
        $stmt = $_db->prepare("
            INSERT INTO address (address_name, user_id, address_one, address_two, address_three, state, post_code, country) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$address_name, $user_id, $address_one, $address_two, $address_three, $state, $post_code, $country]);
        return (int) $_db->lastInsertId();
    } catch (PDOException $e) {
        error_log("Add address error: " . $e->getMessage());
        return false;
    }
}
// Update an existing address
function updateAddress($address_id, $address_one, $address_two, $address_three, $state, $post_code, $country = 'Malaysia')
{
    global $_db;
    $stmt = $_db->prepare("
        UPDATE address 
        SET address_one = ?, address_two = ?, address_three = ?, state = ?, post_code = ?, country = ? 
        WHERE address_id = ?
    ");
    return $stmt->execute([$address_one, $address_two, $address_three, $state, $post_code, $country, $address_id]);
}

//make sure only 3 address at most per user
function getAddressCountByUserId($user_id)
{
    global $_db;
    $stmt = $_db->prepare("SELECT COUNT(*) FROM address WHERE user_id = ?");
    $stmt->execute([$user_id]);
    return (int) $stmt->fetchColumn();
}
// Delete an address
function deleteAddress($address_id)
{
    global $_db;
    $stmt = $_db->prepare("DELETE FROM address WHERE address_id = ?");
    return $stmt->execute([$address_id]);
}

function getUserProfileDetails($user_id)
{
    global $_db;
    $stmt = $_db->prepare("SELECT * FROM UserProfile WHERE user_id = ?");
    $stmt->execute([$user_id]);

    return $stmt->fetch();
}

// function getUserDevices($id)
// {
//     global $_db;
//     $stmt = $_db->prepare("SELECT * FROM UserDevices WHERE user_id = ?");
//     $stmt->execute([$id]);

//     return $stmt->fetchAll();
// }

function countOrdersById($id)
{
    global $_db;
    $stmt = $_db->prepare("SELECT COUNT(order_id) AS num FROM ORDERS
                           WHERE user_id = ?");
    $stmt->execute([$id]);

    return $stmt->fetch()->num;
}

function countReviewById($id)
{
    global $_db;
    $stmt = $_db->prepare("SELECT COUNT(review_id) AS num FROM REVIEW WHERE user_id = ?");
    $stmt->execute([$id]);

    return $stmt->fetch()->num;
}

function updateProfile($id, $name, $contact, $dob, $gender, $file_path)
{
    global $_db;

    //update user table
    $stmt = $_db->prepare("UPDATE USERS SET 
                            user_name = ?
                            WHERE user_id = ?");
    $stmt->execute([$name, $id]);

    if (is_exists($id, 'userprofile', 'user_id')) {
        $stmt =  $_db->prepare("UPDATE userprofile 
                            SET dob = ?,
                            contact_num = ?,
                            gender = ?
                            WHERE user_id = ?");
        $stmt->execute([$dob, $contact, $gender, $id]);
    } else {
        $stmt =  $_db->prepare("INSERT INTO userprofile(user_id, dob, contact_num, gender) 
                            VALUES (?, ?, ?, ?)");
        $stmt->execute([$id, $dob, $contact, $gender]);
    }

    if (is_exists($id, 'profilepicture', 'user_id')) {
        $stmt = $_db->prepare("UPDATE profilepicture 
                           SET file_path = ?
                           WHERE user_id = ?");
        $stmt->execute([$file_path, $id]);
    } else {
        $stmt = $_db->prepare("INSERT INTO PROFILEPICTURE(user_id, file_path)
                                VALUES (?, ?)");
        $stmt->execute([$id, $file_path]);
    }
}

// function updateUserPassword($id, $hash_password){
//     global $_db;
//     $stmt = $_db->prepare('UPDATE users
//                            SET hash_password = ?
//                            WHERE user_id = ?');
//     $stmt->execute([$hash_password, $id]);
// }
