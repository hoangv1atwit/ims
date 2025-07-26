<?php
session_start();
include('connection.php');

header('Content-Type: application/json');

if(!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Session expired. Please login again.']);
    exit();
}

$response = ['success' => false, 'message' => ''];

try {
    // Get form data
    $id = $_POST['id'] ?? null;
    $product_name = $_POST['product_name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? 0;
    $stock = $_POST['stock'] ?? 0;
    $category = $_POST['category'] ?? '';
    $size = $_POST['size'] ?? '';
    $color = $_POST['color'] ?? '';
    $brand = $_POST['brand'] ?? '';

    if (!$id) {
        $response['message'] = 'Product ID is required';
    } else {
        // Handle image upload
        $img_filename = null;
        if(isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
            $upload_dir = '../uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_extension = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if(move_uploaded_file($_FILES['img']['tmp_name'], $upload_path)) {
                $img_filename = $new_filename;
            }
        }
        
        // Update with or without image
        if($img_filename) {
            $stmt = $conn->prepare("UPDATE products SET product_name = ?, description = ?, price = ?, stock = ?, category = ?, size = ?, color = ?, brand = ?, img = ? WHERE id = ?");
            $stmt->execute([$product_name, $description, $price, $stock, $category, $size, $color, $brand, $img_filename, $id]);
        } else {
            $stmt = $conn->prepare("UPDATE products SET product_name = ?, description = ?, price = ?, stock = ?, category = ?, size = ?, color = ?, brand = ? WHERE id = ?");
            $stmt->execute([$product_name, $description, $price, $stock, $category, $size, $color, $brand, $id]);
        }
        
        $response['success'] = true;
        $response['message'] = 'Product updated successfully!';
    }
    
} catch(PDOException $e) {
    $response['message'] = 'Database Error: ' . $e->getMessage();
} catch(Exception $e) {
    $response['message'] = 'General Error: ' . $e->getMessage();
}

echo json_encode($response);
?>