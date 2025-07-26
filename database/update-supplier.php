<?php
    session_start();
    include('connection.php');
    
    if(!isset($_SESSION['user'])) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit();
    }
    
    $response = ['success' => false, 'message' => ''];
    
    try {
        $sid = $_POST['id'] ?? $_POST['sid'];
        $supplier_name = $_POST['supplier_name'];
        $supplier_location = $_POST['supplier_location'];
        $email = $_POST['email'];
        $products = isset($_POST['products']) ? $_POST['products'] : [];
        
        // Update supplier info
        $stmt = $conn->prepare("UPDATE suppliers SET supplier_name = ?, supplier_location = ?, email = ? WHERE id = ?");
        $stmt->execute([$supplier_name, $supplier_location, $email, $sid]);
        
        // Update product-supplier relationships
        $stmt = $conn->prepare("DELETE FROM productsuppliers WHERE supplier = ?");
        $stmt->execute([$sid]);
        
        if(!empty($products)) {
            foreach($products as $product_id) {
                $stmt = $conn->prepare("INSERT INTO productsuppliers (product, supplier) VALUES (?, ?)");
                $stmt->execute([$product_id, $sid]);
            }
        }
        
        $response['success'] = true;
        $response['message'] = 'Supplier updated successfully!';
        
    } catch(PDOException $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }
    
    echo json_encode($response);
?>
