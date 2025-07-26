<?php
    include('connection.php');
    
    $order_id = $_GET['id'];
    
    try {
        $stmt = $conn->prepare("SELECT * FROM delivery_history WHERE order_product_id = ? ORDER BY date_received DESC");
        $stmt->execute([$order_id]);
        $deliveries = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($deliveries);
        
    } catch(PDOException $e) {
        echo json_encode([]);
    }
?>
