<?php
    session_start();
    include('connection.php');
    
    if(!isset($_SESSION['user'])) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit();
    }
    
    $response = ['success' => false, 'message' => ''];
    
    try {
        $table = $_POST['table'];
        $id = $_POST['id'];
        
        if($table == 'users') {
            // Check if user has created products, suppliers or orders
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM products WHERE created_by = ?");
            $stmt->execute([$id]);
            $productCount = $stmt->fetch()['count'];
            
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM suppliers WHERE created_by = ?");
            $stmt->execute([$id]);
            $supplierCount = $stmt->fetch()['count'];
            
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM order_product WHERE created_by = ?");
            $stmt->execute([$id]);
            $orderCount = $stmt->fetch()['count'];
            
            if($productCount > 0 || $supplierCount > 0 || $orderCount > 0) {
                $response['message'] = 'Cannot delete user: User has created ' . 
                    ($productCount > 0 ? $productCount . ' products, ' : '') .
                    ($supplierCount > 0 ? $supplierCount . ' suppliers, ' : '') .
                    ($orderCount > 0 ? $orderCount . ' orders' : '');
            } else {
                $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$id]);
                $response['success'] = true;
                $response['message'] = 'User deleted successfully!';
            }
            
        } elseif($table == 'suppliers') {
            // Check if supplier is referenced in orders or product-supplier relationships
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM order_product WHERE supplier = ?");
            $stmt->execute([$id]);
            $orderCount = $stmt->fetch()['count'];
            
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM productsuppliers WHERE supplier = ?");
            $stmt->execute([$id]);
            $productSupplierCount = $stmt->fetch()['count'];
            
            // Remove debug logging for production use
            
            if($orderCount > 0 || $productSupplierCount > 0) {
                $response['message'] = 'Cannot delete supplier: Supplier is referenced in ' . 
                    ($orderCount > 0 ? $orderCount . ' orders' : '') .
                    ($orderCount > 0 && $productSupplierCount > 0 ? ' and ' : '') .
                    ($productSupplierCount > 0 ? $productSupplierCount . ' product relationships' : '');
            } else {
                $stmt = $conn->prepare("DELETE FROM suppliers WHERE id = ?");
                $stmt->execute([$id]);
                $response['success'] = true;
                $response['message'] = 'Supplier deleted successfully!';
            }
            
        } elseif($table == 'products') {
            // Check if product is referenced in orders, requests, or product-supplier relationships
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM order_product WHERE product = ?");
            $stmt->execute([$id]);
            $orderCount = $stmt->fetch()['count'];
            
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM requests WHERE product_id = ?");
            $stmt->execute([$id]);
            $requestCount = $stmt->fetch()['count'];
            
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM productsuppliers WHERE product = ?");
            $stmt->execute([$id]);
            $productSupplierCount = $stmt->fetch()['count'];
            
            if($orderCount > 0 || $requestCount > 0 || $productSupplierCount > 0) {
                $response['message'] = 'Cannot delete product: Product is referenced in ' . 
                    ($orderCount > 0 ? $orderCount . ' orders' : '') .
                    ($orderCount > 0 && ($requestCount > 0 || $productSupplierCount > 0) ? ', ' : '') .
                    ($requestCount > 0 ? $requestCount . ' requests' : '') .
                    ($requestCount > 0 && $productSupplierCount > 0 ? ', and ' : '') .
                    ($productSupplierCount > 0 ? $productSupplierCount . ' supplier relationships' : '');
            } else {
                $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
                $stmt->execute([$id]);
                $response['success'] = true;
                $response['message'] = 'Product deleted successfully!';
            }
        }
        
    } catch(PDOException $e) {
        $response['message'] = 'Database Error: ' . $e->getMessage();
    } catch(Exception $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }
    
    echo json_encode($response);
?>
