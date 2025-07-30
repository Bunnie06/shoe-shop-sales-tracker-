<?php
// Database configuration with environment variables for security
$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: 'shoe_shop_db';
$username = getenv('DB_USER') ?: 'shoe_shop_user';
$password = getenv('DB_PASS') ?: 'secure_password_123';

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', '0'); // Don't show errors to users
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../logs/db_errors.log');

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_STRINGIFY_FETCHES => false
    ]);
    
    // Set timezone if needed
    $conn->exec("SET time_zone = '+00:00'");
    
} catch (PDOException $e) {
    // Log the error securely
    error_log("Database Connection Failed: " . $e->getMessage());
    
    // Show generic error to user
    header('HTTP/1.1 503 Service Unavailable');
    die("We're experiencing technical difficulties. Please try again later.");
}
?>