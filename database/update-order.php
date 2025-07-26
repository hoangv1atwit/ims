<?php
    session_start();
    include('connection.php');
    
    if(!isset($_SESSION['user'])) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit();
    }
    
    $response = ['success' => false, 'message' => ''];
    
    try {
        $payload = $_POST['payload'];
        
        foreach($payload as $order) {
            $id = $order['id'];
            $qty_delivered = intval($order['qtyDelivered']);
            $status = $order['status'];
            $qty_received = intval($order['qtyReceive']);
            $qty_ordered = intval($order['qtyOrdered']);
            $product_id = $order['pid'];
            
            // Calculate new quantity received
            $new_qty_received = $qty_received + $qty_delivered;
            
            // Update order
            $stmt = $conn->prepare("UPDATE order_product SET quantity_received = ?, status = ? WHERE id = ?");
            $stmt->execute([$new_qty_received, $status, $id]);
            
            // Add delivery history
            if($qty_delivered > 0) {
                $stmt = $conn->prepare("INSERT INTO delivery_history (order_product_id, qty_received) VALUES (?, ?)");
                $stmt->execute([$id, $qty_delivered]);
            }
            
            // Update product stock
            $stmt = $conn->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
            $stmt->execute([$qty_delivered, $product_id]);
        }
        
        $response['success'] = true;
        $response['message'] = 'Orders updated successfully!';
        
    } catch(PDOException $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }
    
    echo json_encode($response);
?>
