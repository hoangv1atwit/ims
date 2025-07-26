<?php
session_start();
include('connection.php');

if(!isset($_SESSION['user'])) {
    $_SESSION['response'] = [
        'success' => false,
        'message' => 'Session expired. Please login again.'
    ];
    header('Location: ../supplier-add.php');
    exit();
}

$user = $_SESSION['user'];

try {
    // Get all form data directly from $_POST
    $supplier_name = trim($_POST['supplier_name'] ?? '');
    $supplier_location = trim($_POST['supplier_location'] ?? '');
    $email = trim($_POST['email'] ?? '');

    // Validate required fields
    if (empty($supplier_name)) {
        throw new Exception('Supplier name is required');
    }
    
    if (empty($supplier_location)) {
        throw new Exception('Supplier location is required');
    }
    
    if (empty($email)) {
        throw new Exception('Email is required');
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Please enter a valid email address');
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO suppliers (supplier_name, supplier_location, email, created_by, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$supplier_name, $supplier_location, $email, $user['id']]);

    $_SESSION['response'] = [
        'success' => true,
        'message' => 'Supplier "' . $supplier_name . '" created successfully!'
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

header('Location: ../supplier-add.php');
exit();
?>