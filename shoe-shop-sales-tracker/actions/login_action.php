<?php
session_start();
require_once '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("HTTP/1.1 405 Method Not Allowed");
    exit;
}

// Rate limiting - simple implementation
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_login_attempt'] = time();
}

if ($_SESSION['login_attempts'] >= 5 && (time() - $_SESSION['last_login_attempt'] < 300)) {
    header("Location: ../pages/login.php?error=Too many login attempts. Please try again in 5 minutes.");
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Validate input
if (empty($username) || empty($password)) {
    $_SESSION['login_attempts']++;
    $_SESSION['last_login_attempt'] = time();
    header("Location: ../pages/login.php?error=Username and password are required");
    exit;
}

// Get user from database
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

// Verify password
if ($user && password_verify($password, $user['password'])) {
    // Password is correct, check if needs rehash
    if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        $conn->prepare("UPDATE users SET password = ? WHERE id = ?")
             ->execute([$newHash, $user['id']]);
    }
    
    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['last_login'] = time();
    
    // Update last login in database
    $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")
         ->execute([$user['id']]);
    
    // Reset login attempts
    unset($_SESSION['login_attempts']);
    
    // Redirect to intended page or dashboard
    $redirect = $_SESSION['redirect_url'] ?? '../pages/dashboard.php';
    unset($_SESSION['redirect_url']);
    header("Location: $redirect");
    exit;
} else {
    $_SESSION['login_attempts']++;
    $_SESSION['last_login_attempt'] = time();
    header("Location: ../pages/login.php?error=Invalid credentials");
    exit;
}
?>