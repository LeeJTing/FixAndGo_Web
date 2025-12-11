<?php

date_default_timezone_set('Asia/Kuala_Lumpur');
session_start();

// ============================================================================
// Global variable
// ============================================================================
$rootDir = 'http://' . $_SERVER['HTTP_HOST'];

// ============================================================================
// General Page Functions
// ============================================================================

// hashing password
function hash_password($password)
{
    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);

    return $password ? $hashedPassword : $password;
}

// Get session user ID
function get_session_id()
{
    return !$_SESSION['USER_ID'] ? 'Guest' : $_SESSION['USER_ID'];
}

// Is GET request?
function is_get()
{
    return $_SERVER['REQUEST_METHOD'] == 'GET';
}

// Is POST request?
function is_post()
{
    return $_SERVER['REQUEST_METHOD'] == 'POST';
}

// Obtain GET parameter - FIXED: Handle null values
function get($key, $value = '')
{
    $value = $_GET[$key] ?? $value;
    if ($value === null) {
        return '';
    }
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Obtain POST parameter - FIXED: Handle null values
function post($key, $value = '')
{
    $value = $_POST[$key] ?? $value;
    if ($value === null) {
        return '';
    }
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Obtain REQUEST (GET and POST) parameter - FIXED: Handle null values
function req($key, $value = '')
{
    $value = $_REQUEST[$key] ?? $value;
    if ($value === null) {
        return '';
    }
    return is_array($value) ? array_map('trim', $value) : trim($value);
}


// Redirect to URL
function redirect($url = null)
{
    $url ??= $_SERVER['REQUEST_URI'];
    header("Location: $url");
    exit();
}

// Set or get temporary session variable
function temp($key, $value = null)
{
    if ($value !== null) {
        $_SESSION["temp_$key"] = $value;
    } else {
        $value = $_SESSION["temp_$key"] ?? null;
        unset($_SESSION["temp_$key"]);
        return $value;
    }
}

// ============================================================================
// Database Setups and Functions
// ============================================================================

$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'fixandgo_db';

// Global PDO object
try {
    $_db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password, [
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    //echo "Connected successfully!";
} catch (PDOException $err) {
    die("Connection failed: " . $err->getMessage());
}

// connect to db
// $stmt = $_db->query("SELECT * FROM users");
// while($row = $stmt->fetch()){
//     echo $row->user_name . "<br>";
// };

// Is unique?
function is_unique($value, $table, $field)
{
    global $_db;
    $stm = $_db->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
    $stm->execute([$value]);
    return $stm->fetchColumn() == 0;
}

// Is exists?
function is_exists($value, $table, $field)
{
    global $_db;
    $stm = $_db->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
    $stm->execute([$value]);
    return $stm->fetchColumn() > 0;
}

// get user profile picture
function getUserProfilePicture($userId)
{
    global $_db;
    $defaultPath = "/FixAndGo_Web/images/profile/default_profile_picture.webp";

    if (!$userId) {
        return $defaultPath;
    }

    try {
        $stmt = $_db->prepare("SELECT file_path FROM profilepicture WHERE user_id = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        
        // Check if there is a result and file_path is not empty
        if ($result && isset($result->file_path) && !empty($result->file_path)) {
            // Make sure the path begins with /
            $filePath = $result->file_path;
            if (substr($filePath, 0, 1) !== '/') {
                $filePath = '/' . $filePath;
            }
            return $filePath;
        }
    } catch (Exception $e) {
        // If the query fails, return the default avatar
        error_log("Profile picture error for user $userId: " . $e->getMessage());
    }
    
    return $defaultPath;
}