<?php
session_start();
include('connection.php');

if(!isset($_SESSION['user'])) {
    $_SESSION['response'] = [
        'success' => false,
        'message' => 'Session expired. Please login again.'
    ];
    header('Location: ../users-add.php');
    exit();
}

$user = $_SESSION['user'];

try {
    // Get all form data directly from $_POST
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $permissions = $_POST['permissions'] ?? '';

    // Validate required fields
    if (empty($first_name)) {
        throw new Exception('First name is required');
    }
    
    if (empty($last_name)) {
        throw new Exception('Last name is required');
    }
    
    if (empty($email)) {
        throw new Exception('Email is required');
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Please enter a valid email address');
    }
    
    if (empty($password)) {
        throw new Exception('Password is required');
    }
    
    if (strlen($password) < 6) {
        throw new Exception('Password must be at least 6 characters long');
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        throw new Exception('Email address already exists');
    }

    // Hash the password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, permissions, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$first_name, $last_name, $email, $password_hash, $permissions]);

    $_SESSION['response'] = [
        'success' => true,
        'message' => 'User "' . $first_name . ' ' . $last_name . '" created successfully!'
    ];

} catch(PDOException $e) {
    $_SESSION['response'] = [
        'success' => false,
        'message' => 'Database Error: ' . $e->getMessage()
    ];
} catch(Exception $e) {
    $_SESSION['response'] = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

header('Location: ../users-add.php');
exit();
?>