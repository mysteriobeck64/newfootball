<?php
// Only start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if constants are already defined before defining them
if (!defined('DB_HOST')) {
    // Database configuration
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'football_club');

    // Create connection
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
}

if (!defined('ADMIN_USERNAME')) {
    // Default admin credentials
    define('ADMIN_USERNAME', 'admin');
    define('ADMIN_PASSWORD', 'password123');
}

if (!defined('REQUEST_PENDING')) {
    // Request status constants
    define('REQUEST_PENDING', 0);
    define('REQUEST_APPROVED', 1);
    define('REQUEST_REJECTED', 2);
}

// Check if user is logged in
if (!function_exists('isLoggedIn')) {
    function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
}

// Redirect if not logged in
if (!function_exists('checkLogin')) {
    function checkLogin() {
        if (!isLoggedIn()) {
            header("Location: login.php");
            exit();
        }
    }
}

// Get user role
if (!function_exists('getUserRole')) {
    function getUserRole() {
        return $_SESSION['user_role'] ?? 'staff';
    }
}
?>