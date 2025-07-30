<?php
require_once __DIR__ . '/security.php';
session_start();

// Regenerate session ID periodically for security
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} elseif (time() - $_SESSION['created'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'] ?? '/';
    header("Location: ../pages/login.php");
    exit;
}

// Check last activity for timeout (30 minutes)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_unset();
    session_destroy();
    header("Location: ../pages/login.php?error=Session expired. Please login again.");
    exit;
}
$_SESSION['last_activity'] = time();

// Verify user still exists in database (prevent deleted user access)
require_once __DIR__ . '/db.php';
$stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
if (!$stmt->fetch()) {
    session_unset();
    session_destroy();
    header("Location: ../pages/login.php?error=User account no longer exists.");
    exit;
}

// CSRF token generation for forms
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>