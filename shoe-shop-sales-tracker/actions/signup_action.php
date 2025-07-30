<?php
session_start();
require_once '../config/db.php';
require_once '../includes/auth.php';

// Verify CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header("Location: ../pages/signup.php?error=Invalid request");
    exit;
}

// Only owner can create accounts
if ($_SESSION['role'] !== 'owner') {
    header("Location: ../pages/dashboard.php?error=Unauthorized access");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $full_name = trim($_POST['full_name'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);

    // Validate inputs
    $errors = [];
    
    if (empty($username) || strlen($username) < 4) {
        $errors[] = "Username must be at least 4 characters";
    }
    
    if (empty($password) || strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters";
    }
    
    if (empty($full_name)) {
        $errors[] = "Full name is required";
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address";
    }
    
    if (!empty($errors)) {
        header("Location: ../pages/signup.php?error=" . urlencode(implode(", ", $errors)));
        exit;
    }

    // Check if username exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    
    if ($stmt->fetch()) {
        header("Location: ../pages/signup.php?error=Username already exists");
        exit;
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Create user
    try {
        $stmt = $conn->prepare("INSERT INTO users (username, password, role, full_name, email) VALUES (?, ?, 'attendant', ?, ?)");
        $stmt->execute([$username, $hashedPassword, $full_name, $email]);
        
        header("Location: ../pages/signup.php?success=Account created successfully");
        exit;
    } catch (PDOException $e) {
        error_log("Signup Error: " . $e->getMessage());
        header("Location: ../pages/signup.php?error=Account creation failed. Please try again.");
        exit;
    }
}

header("Location: ../pages/signup.php");
exit;
?>