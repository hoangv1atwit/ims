<?php
    session_start();
    if(!isset($_SESSION['user'])) header('location: login.php');
    
    $user = $_SESSION['user'];
    
    // Get products and suppliers
    include('database/connection.php');
    
    $show_table = 'products';
    $products = include('database/show.php');
    
    $show_table = 'suppliers';
    $suppliers = include('database/show.php');
    
    // Handle form submission
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        try {
            $batch_id = 'BATCH_' . time();
            $requested_by = $_POST['requested_by'];
            $products_data = $_POST['products'];
            
            foreach($products_data as $product_data) {
                $product_id = $product_data['product_id'];
                $supplier_id = $product_data['supplier_id'];
                $quantity = $product_data['quantity'];
                
                $stmt = $conn->prepare("INSERT INTO order_product (product, supplier, quantity_ordered, batch, requested_by, created_by) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$product_id, $supplier_id, $quantity, $batch_id, $requested_by, $user['id']]);
            }
            
            $_SESSION['response'] = [
                'success' => true,
                'message' => 'Purchase order created successfully! Batch ID: ' . $batch_id
            ];
            
        } catch(PDOException $e) {
            $_SESSION['response'] = [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Purchase Order - Inventory Management System</title>
    <?php include('partials/app-header-scripts.php'); ?>
</head>
<body>
    <div id="dashboardMainContainer">
        <?php include('partials/app-sidebar.php') ?>
        <div class="dasboard_content_container" id="dasboard_content_container">
            <?php include('partials/app-topnav.php') ?>
            <div class="dashboard_content">
                <?php if(in_array('po_create', $user['permissions'])) { ?>
                <div class="dashboard_content_main">
                    <div class="row">
                        <div class="column column-12">
                            <h1 class="section_header"><i class="fa fa-plus"></i> Create Purchase Order</h1>
                            <div id="orderCreateFormContainer">
                                <form method="POST" class="appForm">
                                    <div class="appFormInputContainer">
                                        <label for="requested_by">Requested By</label>
                                        <input type="text" class="appFormInput" id="requested_by" name="requested_by" required />
                                    </div>
                                    
                                    <div id="productsContainer">
                                        <h3>Products</h3>
                                        <div class="product-row">
                                            <div class="row">
                                                <div class="column column-4">
                                                    <select name="products[0][product_id]" class="appFormInput" required>
                                                        <option value="">Select Product</option>
                                                        <?php foreach($products as $product): ?>
                                                            <option value="<?= $product['id'] ?>"><?= htmlspecialchars($product['product_name']) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="column column-4">
                                                    <select name="products[0][supplier_id]" class="appFormInput" required>
                                                        <option value="">Select Supplier</option>
                                                        <?php foreach($suppliers as $supplier): ?>
                                                            <option value="<?= $supplier['id'] ?>"><?= htmlspecialchars($supplier['supplier_name']) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="column column-3">
                                                    <input type="number" name="products[0][quantity]" class="appFormInput" placeholder="Quantity" required min="1" />
                                                </div>
                                                <div class="column column-1">
                                                    <button type="button" class="appBtn removeProduct" disabled>Remove</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <button type="button" class="appBtn" id="addProduct">Add Another Product</button>
                                    <button type="submit" class="appBtn"><i class="fa fa-plus"></i> Create Order</button>
                                </form>
                                
                                <?php 
                                    if(isset($_SESSION['response'])){
                                        $response_message = $_SESSION['response']['message'];
                                        $is_success = $_SESSION['response']['success'];
                                ?>
                                    <div class="responseMessage">
                                        <p class="responseMessage <?= $is_success ? 'responseMessage__success' : 'responseMessage__error' ?>">
                                            <?= $response_message ?>
                                        </p>
                                    </div>
                                <?php unset($_SESSION['response']); } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } else { ?>
                    <div id="errorMessage">Access denied.</div>
                <?php } ?>
            </div>
        </div>
    </div>
    
    <?php include('partials/app-scripts.php'); ?>
    
    <script>
        let productRowIndex = 1;
        
        document.getElementById('addProduct').addEventListener('click', function(){
            const container = document.getElementById('productsContainer');
            const newRow = document.createElement('div');
            newRow.className = 'product-row';
            
            newRow.innerHTML = `
                <div class="row">
                    <div class="column column-4">
                        <select name="products[${productRowIndex}][product_id]" class="appFormInput" required>
                            <option value="">Select Product</option>
                            <?php foreach($products as $product): ?>
                                <option value="<?= $product['id'] ?>"><?= htmlspecialchars($product['product_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="column column-4">
                        <select name="products[${productRowIndex}][supplier_id]" class="appFormInput" required>
                            <option value="">Select Supplier</option>
                            <?php foreach($suppliers as $supplier): ?>
                                <option value="<?= $supplier['id'] ?>"><?= htmlspecialchars($supplier['supplier_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="column column-3">
                        <input type="number" name="products[${productRowIndex}][quantity]" class="appFormInput" placeholder="Quantity" required min="1" />
                    </div>
                    <div class="column column-1">
                        <button type="button" class="appBtn removeProduct">Remove</button>
                    </div>
                </div>
            `;
            
            container.appendChild(newRow);
            productRowIndex++;
            
            // Update remove button states
            updateRemoveButtons();
        });
        
        document.addEventListener('click', function(e){
            if(e.target.classList.contains('removeProduct')){
                e.target.closest('.product-row').remove();
                updateRemoveButtons();
            }
        });
        
        function updateRemoveButtons(){
            const removeButtons = document.querySelectorAll('.removeProduct');
            removeButtons.forEach(button => {
                button.disabled = removeButtons.length === 1;
            });
        }
    </script>
</body>
</html>
