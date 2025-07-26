<?php
    session_start();
    include('connection.php');
    
    if(!isset($_SESSION['user'])) {
        header('location: ../login.php');
        exit();
    }
    
    $report_type = $_GET['report'];
    
    // Create simple HTML-based PDF content
    $html = '<html><head><title>' . ucfirst($report_type) . ' Report</title></head><body>';
    $html .= '<h1>' . ucfirst($report_type) . ' Report</h1>';
    $html .= '<p>Generated on: ' . date('Y-m-d H:i:s') . '</p>';
    
    try {
        if($report_type == 'product') {
            $html .= '<table border="1" cellpadding="5" cellspacing="0">';
            $html .= '<tr><th>ID</th><th>Product Name</th><th>Price</th><th>Stock</th><th>Category</th></tr>';
            
            $stmt = $conn->prepare("SELECT id, product_name, price, stock, category FROM products ORDER BY created_at DESC");
            $stmt->execute();
            
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $html .= '<tr>';
                $html .= '<td>' . $row['id'] . '</td>';
                $html .= '<td>' . htmlspecialchars($row['product_name']) . '</td>';
                $html .= '<td>$' . $row['price'] . '</td>';
                $html .= '<td>' . $row['stock'] . '</td>';
                $html .= '<td>' . htmlspecialchars($row['category']) . '</td>';
                $html .= '</tr>';
            }
            $html .= '</table>';
            
        } elseif($report_type == 'supplier') {
            $html .= '<table border="1" cellpadding="5" cellspacing="0">';
            $html .= '<tr><th>ID</th><th>Supplier Name</th><th>Location</th><th>Email</th></tr>';
            
            $stmt = $conn->prepare("SELECT id, supplier_name, supplier_location, email FROM suppliers ORDER BY created_at DESC");
            $stmt->execute();
            
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $html .= '<tr>';
                $html .= '<td>' . $row['id'] . '</td>';
                $html .= '<td>' . htmlspecialchars($row['supplier_name']) . '</td>';
                $html .= '<td>' . htmlspecialchars($row['supplier_location']) . '</td>';
                $html .= '<td>' . htmlspecialchars($row['email']) . '</td>';
                $html .= '</tr>';
            }
            $html .= '</table>';
        }
        
    } catch(PDOException $e) {
        $html .= '<p>Error: ' . $e->getMessage() . '</p>';
    }
    
    $html .= '</body></html>';
    
    // Set headers for PDF display
    header('Content-Type: text/html');
    echo $html;
?>
