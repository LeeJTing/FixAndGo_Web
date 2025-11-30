<?php

// Initialize variables
$userID = $userName = $email = '';
$errors = [];
$success = '';

// Process form when submitted
if (is_post()) {
    // Get form data
    $userID = post('userID');
    $userName = post('userName');
    $email = post('email');
    $password = post('password');
    $confirmPassword = post('confirmPassword');
    
    // Validate User ID (4-12 characters)
    if (strlen($userID) < 4 || strlen($userID) > 12) {
        $errors['userID'] = 'User ID must be between 4 and 12 characters.';
    } elseif (!is_unique($userID, 'users', 'user_id')) {
        $errors['userID'] = 'User ID already exists. Please choose a different one.';
    }
    
    // Validate User Name (4-50 characters, letters only)
    $nameRegex = '/^[a-zA-Z\s]+$/';
    if (strlen($userName) < 4 || strlen($userName) > 50 || !preg_match($nameRegex, $userName)) {
        $errors['userName'] = 'User name must be between 1 and 50 characters and no any number.';
    }
    
    // Validate Email
    $emailRegex = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
    if (!preg_match($emailRegex, $email)) {
        $errors['email'] = 'Please enter a valid email address.';
    } elseif (!is_unique($email, 'users', 'email')) {
        $errors['email'] = 'Email already exists. Please use a different email.';
    }
    
    // Validate Password (8-12 characters, at least one letter and one number)
    $passwordRegex = '/^(?=.*[A-Za-z])(?=.*\d).{8,12}$/';
    if (!preg_match($passwordRegex, $password)) {
        $errors['password'] = 'Password must be 8-12 characters with at least one letter and one number.';
    }
    
    // Validate Password Confirmation
    if ($password !== $confirmPassword) {
        $errors['confirmPassword'] = 'Passwords do not match.';
    }
    
    // If no errors, save to database
    if (empty($errors)) {
        try {
            // Hash the password
            $hashedPassword = hash_password($password);
            
            // Insert user into database
            $stmt = $_db->prepare("INSERT INTO users (user_id, user_name, user_role, email, hash_password, account_status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$userID, $userName, "Member", $email, $hashedPassword, "Verified"]);

            // Insert user cart
            $stmt = $_db->prepare("INSERT INTO cart (user_id) VALUE (?)");
            $stmt->execute([$userID]);

            $success = 'Registration successful! You can now login.';
            
            // Clear form fields
            $userID = $userName = $email = '';
            
            // Store success message in temp session for redirect if needed
            temp('register_success', 'Registration successful! Please login.');
            
        } catch (PDOException $e) {
            $errors['general'] = 'Registration failed: ' . $e->getMessage();
        }
    }
}

?>