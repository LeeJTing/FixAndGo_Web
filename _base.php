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
    return sha1($password);
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
    $userId = get_session_id();
    
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
        $homelink = $rootDir . '/pages/admin/adminHome.php';
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
    global $pathPrefix; // 【关键】引入刚才定义的全局变量

    // 修改默认路径，使用变量
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
            
            // 如果数据库存的是完整路径（包含 FixAndGo_Web），在 :8000 环境下需要去掉它
            // 如果数据库存的是相对路径（images/...），我们需要加上 $pathPrefix
            
            // 假设数据库存的是 "/images/profile/xxx.jpg" 或 "images/profile/xxx.jpg"
            // 我们统一处理：
            
            // 1. 确保开头有 /
            if (substr($filePath, 0, 1) !== '/') {
                $filePath = '/' . $filePath;
            }

            // 2. 智能拼接前缀
            // 如果当前需要前缀(XAMPP)，且路径里没有前缀，就加上
            if ($pathPrefix && strpos($filePath, $pathPrefix) !== 0) {
                 return $pathPrefix . $filePath;
            }
            // 如果当前不需要前缀(:8000)，但路径里有前缀，就去掉
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
