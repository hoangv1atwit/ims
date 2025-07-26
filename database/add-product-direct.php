<?php
session_start();
include('connection.php');

// Clean up - remove debug logging for production

if(!isset($_SESSION['user'])) {
    $_SESSION['response'] = [
        'success' => false,
        'message' => 'Session expired. Please login again.'
    ];
    header('Location: ../products-add.php');
    exit();
}

$user = $_SESSION['user'];

// Handle different content types and potential POST data issues
$post_data = $_POST;

// If POST is empty, try to parse raw input
if (empty($post_data) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw_input = file_get_contents('php://input');
    if (!empty($raw_input)) {
        parse_str($raw_input, $post_data);
    }
}

try {
    // Get all form data with comprehensive fallbacks
    $product_name = isset($post_data['product_name']) ? trim($post_data['product_name']) : '';
    $description = isset($post_data['description']) ? trim($post_data['description']) : '';
    $price = isset($post_data['price']) ? floatval($post_data['price']) : 0;
    $stock = isset($post_data['stock']) ? intval($post_data['stock']) : 0;
    $category = isset($post_data['category']) ? trim($post_data['category']) : '';
    $size = isset($post_data['size']) ? trim($post_data['size']) : '';
    $color = isset($post_data['color']) ? trim($post_data['color']) : '';
    $brand = isset($post_data['brand']) ? trim($post_data['brand']) : '';

    // Values parsed successfully

    // Validate required fields
    if (empty($product_name)) {
        throw new Exception('Product name is required');
    }
    
    if ($price <= 0) {
        throw new Exception('Price must be greater than 0');
    }
    
    if ($stock < 0) {
        throw new Exception('Stock cannot be negative');
    }

    // Handle image upload
    $img_filename = '';
    if(isset($_FILES['img']) && $_FILES['img']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($file_extension, $allowed_extensions)) {
            $img_filename = uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $img_filename;
            
            if(!move_uploaded_file($_FILES['img']['tmp_name'], $upload_path)) {
                $img_filename = '';
            }
        }
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO products (product_name, description, price, stock, category, size, color, brand, img, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$product_name, $description, $price, $stock, $category, $size, $color, $brand, $img_filename, $user['id']]);

    $_SESSION['response'] = [
        'success' => true,
        'message' => 'Product "' . $product_name . '" created successfully!'
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

header('Location: ../products-add.php');
exit();
?>