<?php

date_default_timezone_set('Asia/Kuala_Lumpur');
session_start();

// ============================================================================
// Global variable
// ============================================================================
$rootDir = 'http://' . $_SERVER['HTTP_HOST'];

$pathPrefix = (strpos($_SERVER['SCRIPT_NAME'], '/FixAndGo_Web') === 0) ? '/FixAndGo_Web' : '';

// ============================================================================
// General Page Functions
// ============================================================================

// hashing password
function hash_password($password)
{
    return password_hash($password, PASSWORD_DEFAULT);
}

// Get session user ID
function get_session_id()
{
    return isset($_SESSION['USER_ID']) && !empty($_SESSION['USER_ID']) ? $_SESSION['USER_ID'] : 'Guest';
}

// Get current logged in user info
function getCurrentUser()
{
    global $_db;
    $userId = temp('USER_ID');

    if ($userId === 'Guest') {
        return null;
    }

    try {
        $stmt = $_db->prepare("SELECT u.*, up.gender, up.contact_num 
                               FROM users u 
                               LEFT JOIN userprofile up ON u.user_id = up.user_id 
                               WHERE u.user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    } catch (Exception $e) {
        error_log("Error fetching current user: " . $e->getMessage());
        return null;
    }
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

// Get home link
function homePageURL()
{
    global $rootDir;
    $homelink = $rootDir . "/index.php";

    if (temp('USER_ROLE') === 'Member') {
        $homelink = $rootDir . '/pages/member/memberHome.php';
    }

    if (temp('USER_ROLE') === 'Admin') {
        $homelink = $rootDir . '/pages/admin/adminDashboard.php';
    }

    return $homelink;
}

// Set or get temporary session variable
function temp($key, $value = null)
{
    if ($value !== null) {
        $_SESSION["temp_$key"] = $value;
    } else {
        $value = $_SESSION["temp_$key"] ?? null;
        //unset($_SESSION["temp_$key"]);
        return $value;
    }
}

function flash($key, $value = null)
{
    if ($value !== null) {
        $_SESSION["flash_$key"] = $value;
    } else {
        $value = $_SESSION["flash_$key"] ?? null;
        unset($_SESSION["flash_$key"]);
        return $value;
    }
}

function clearSession()
{
    session_unset();
    session_destroy();
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
    global $pathPrefix; // 【key】Introduce the defined global variable

    // Modify the default path using a variable
    $defaultPath = $pathPrefix . "/images/profile/default_profile_picture.webp";

    if (!$userId) {
        return $defaultPath;
    }

    try {
        $stmt = $_db->prepare("SELECT file_path FROM profilepicture WHERE user_id = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch();

        if ($result && isset($result->file_path) && !empty($result->file_path)) {
            $filePath = $result->file_path;

            // 1. Make sure it starts with /
            if (substr($filePath, 0, 1) !== '/') {
                $filePath = '/' . $filePath;
            }

            // 2. Intelligent splicing prefix
            // If a prefix (XAMPP) is currently needed and there is no prefix in the path, add it
            if ($pathPrefix && strpos($filePath, $pathPrefix) !== 0) {
                return $pathPrefix . $filePath;
            }
            // If the prefix (:8000) is not needed at present, but there is a prefix in the path, remove it
            else if (!$pathPrefix && strpos($filePath, '/FixAndGo_Web') === 0) {
                return str_replace('/FixAndGo_Web', '', $filePath);
            }

            return $filePath;
        }
    } catch (Exception $e) {
        error_log("Profile picture error for user $userId: " . $e->getMessage());
    }

    return $defaultPath;
}

function generateFilePath($fileName)
{
    $uploadDir = '/images/profile/';

    $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
    $filePath = $uploadDir . '_' . time() . '.' . $fileExt;
    return $filePath;
}
