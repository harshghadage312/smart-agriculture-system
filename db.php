<?php
// Database Configuration
$host = "localhost";
$user = "root";
$pass = "";       // leave empty for XAMPP default
$db   = "smart_agri";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("<div style='color:red;font-family:sans-serif;padding:30px;'>
        <h2>Database Connection Failed!</h2>
        <p><b>Error:</b> " . mysqli_connect_error() . "</p>
        <p>Steps to fix:<br>
        1. Make sure XAMPP Apache and MySQL are both running (green)<br>
        2. Open phpMyAdmin and import <b>database.sql</b><br>
        3. Make sure database name is exactly: <b>smart_agri</b>
        </p>
    </div>");
}

mysqli_set_charset($conn, "utf8");

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sanitize input
function clean($conn, $data) {
    return mysqli_real_escape_string($conn, trim(htmlspecialchars($data)));
}

// Redirect to login if not logged in
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}

// Redirect if not admin
function requireAdmin() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header("Location: ../login.php");
        exit;
    }
}

// Safe query with error display
function safeQuery($conn, $sql) {
    $result = mysqli_query($conn, $sql);
    if ($result === false) {
        die("<div style='color:red;font-family:sans-serif;padding:20px;'>
            <b>Query Error:</b> " . mysqli_error($conn) . "<br>
            <b>SQL:</b> " . htmlspecialchars($sql) . "
        </div>");
    }
    return $result;
}
?>
