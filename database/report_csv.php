<?php
    session_start();
    include('connection.php');
    
    if(!isset($_SESSION['user'])) {
        header('location: ../login.php');
        exit();
    }
    
    $report_type = $_GET['report'];
    
    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $report_type . '_report.csv"');
    
    $output = fopen('php://output', 'w');
    
    try {
        if($report_type == 'product') {
            // Product report
            fputcsv($output, ['ID', 'Product Name', 'Description', 'Price', 'Stock', 'Category', 'Size', 'Color', 'Brand', 'Created At']);
            
            $stmt = $conn->prepare("SELECT id, product_name, description, price, stock, category, size, color, brand, created_at FROM products ORDER BY created_at DESC");
            $stmt->execute();
            
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                fputcsv($output, $row);
            }
            
        } elseif($report_type == 'supplier') {
            // Supplier report
            fputcsv($output, ['ID', 'Supplier Name', 'Location', 'Email', 'Phone', 'Created At']);
            
            $stmt = $conn->prepare("SELECT id, supplier_name, supplier_location, email, phone, created_at FROM suppliers ORDER BY created_at DESC");
            $stmt->execute();
            
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                fputcsv($output, $row);
            }
            
        } elseif($report_type == 'delivery') {
            // Delivery report
            fputcsv($output, ['ID', 'Product', 'Quantity Received', 'Date Received', 'Notes']);
            
            $stmt = $conn->prepare("
                SELECT dh.id, p.product_name, dh.qty_received, dh.date_received, dh.notes
                FROM delivery_history dh
                JOIN order_product op ON dh.order_product_id = op.id
                JOIN products p ON op.product = p.id
                ORDER BY dh.date_received DESC
            ");
            $stmt->execute();
            
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                fputcsv($output, $row);
            }
            
        } elseif($report_type == 'purchase_orders') {
            // Purchase orders report
            fputcsv($output, ['ID', 'Product', 'Supplier', 'Quantity Ordered', 'Quantity Received', 'Status', 'Requested By', 'Created At']);
            
            $stmt = $conn->prepare("
                SELECT op.id, p.product_name, s.supplier_name, op.quantity_ordered, op.quantity_received, op.status, op.requested_by, op.created_at
                FROM order_product op
                JOIN products p ON op.product = p.id
                JOIN suppliers s ON op.supplier = s.id
                ORDER BY op.created_at DESC
            ");
            $stmt->execute();
            
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                fputcsv($output, $row);
            }
        }
        
    } catch(PDOException $e) {
        fputcsv($output, ['Error', $e->getMessage()]);
    }
    
    fclose($output);
?>
