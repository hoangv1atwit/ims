<?php
    session_start();
    
    // Check if user is logged in
    if(!isset($_SESSION['user'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
    
    $user = $_SESSION['user'];
    
    // Check dashboard permission
    if(!in_array('dashboard_view', $user['permissions'])) {
        http_response_code(403);
        echo json_encode(['error' => 'Access denied']);
        exit;
    }
    
    include('database/connection.php');
    
    try {
        // Get real-time statistics
        $low_stock_threshold = 10;
        
        // Get total products
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM products");
        $stmt->execute();
        $total_products = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Get current month revenue
        $stmt = $conn->prepare("
            SELECT SUM(op.quantity_received * p.price) as total_revenue
            FROM order_product op
            JOIN products p ON op.product = p.id
            WHERE DATE_TRUNC('month', op.created_at) = DATE_TRUNC('month', CURRENT_DATE)
            AND op.status = 'complete'
        ");
        $stmt->execute();
        $total_revenue = $stmt->fetch(PDO::FETCH_ASSOC)['total_revenue'] ?? 0;
        
        // Get low stock products
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM products WHERE stock < ?");
        $stmt->execute([$low_stock_threshold]);
        $low_stock_products = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Get stock-out products
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM products WHERE stock = 0");
        $stmt->execute();
        $stock_out_products = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Get current month revenue data for chart
        $stmt = $conn->prepare("
            SELECT 
                DATE(op.created_at) as order_date,
                SUM(op.quantity_received * p.price) as daily_revenue
            FROM order_product op
            JOIN products p ON op.product = p.id
            WHERE DATE_TRUNC('month', op.created_at) = DATE_TRUNC('month', CURRENT_DATE)
            AND op.status = 'complete'
            GROUP BY DATE(op.created_at)
            ORDER BY order_date
        ");
        $stmt->execute();
        $revenue_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get low stock items details
        $stmt = $conn->prepare("
            SELECT p.id, p.product_name, p.stock, p.category
            FROM products p
            WHERE p.stock < ?
            ORDER BY p.stock ASC
            LIMIT 10
        ");
        $stmt->execute([$low_stock_threshold]);
        $low_stock_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get stock-out items details
        $stmt = $conn->prepare("
            SELECT p.id, p.product_name, p.category
            FROM products p
            WHERE p.stock = 0
            ORDER BY p.product_name
            LIMIT 10
        ");
        $stmt->execute();
        $stock_out_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Prepare response
        $response = [
            'status' => 'success',
            'timestamp' => date('Y-m-d H:i:s'),
            'stats' => [
                'total_products' => $total_products,
                'total_revenue' => $total_revenue,
                'low_stock_products' => $low_stock_products,
                'stock_out_products' => $stock_out_products
            ],
            'revenue_data' => $revenue_data,
            'low_stock_items' => $low_stock_items,
            'stock_out_items' => $stock_out_items
        ];
        
        header('Content-Type: application/json');
        echo json_encode($response);
        
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    }
?>