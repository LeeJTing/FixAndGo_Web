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
function addAddress($user_id, $address_one, $address_two, $address_three, $state, $post_code, $country = 'Malaysia')
{
    global $_db;
    $stmt = $_db->prepare("
        INSERT INTO address (user_id, address_one, address_two, address_three, state, post_code, country) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    return $stmt->execute([$user_id, $address_one, $address_two, $address_three, $state, $post_code, $country]);
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

// Delete an address
function deleteAddress($address_id)
{
    global $_db;
    $stmt = $_db->prepare("DELETE FROM address WHERE address_id = ?");
    return $stmt->execute([$address_id]);
}
