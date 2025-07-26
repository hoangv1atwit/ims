<?php
    include('connection.php');
    
    $id = $_POST['id'] ?? $_GET['id'];
    
    try {
        // Get supplier info
        $stmt = $conn->prepare("SELECT * FROM suppliers WHERE id = ?");
        $stmt->execute([$id]);
        $supplier = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Get supplier's products
        $stmt = $conn->prepare("SELECT product FROM productsuppliers WHERE supplier = ?");
        $stmt->execute([$id]);
        $products = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $supplier['products'] = $products;
        
        echo json_encode($supplier);
        
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
?>
