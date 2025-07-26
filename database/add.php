<?php
    session_start();
    include('connection.php');
    
    if(!isset($_SESSION['user'])) {
        header('location: ../login.php');
        exit();
    }
    
    $user = $_SESSION['user'];
    $table = $_SESSION['table'];
    $redirect_to = $_SESSION['redirect_to'];
    
    // Handle the case where POST data might be empty due to server config
    $raw_input = file_get_contents('php://input');
    if (empty($_POST) && !empty($raw_input)) {
        // Try to parse the raw input as form data
        parse_str($raw_input, $_POST);
    }

    // Ensure this is a POST request and form was submitted
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $_SESSION['response'] = [
            'success' => false,
            'message' => 'Invalid request method'
        ];
        header("Location: ../$redirect_to");
        exit();
    }

    try {
        if($table == 'users') {
            // Add user
            $first_name = $_POST['first_name'] ?? '';
            $last_name = $_POST['last_name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $permissions = $_POST['permissions'] ?? '';
            
            if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
                $_SESSION['response'] = [
                    'success' => false,
                    'message' => 'Please fill in all required fields'
                ];
                header("Location: ../$redirect_to");
                exit();
            }
            
            $password = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, permissions) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$first_name, $last_name, $email, $password, $permissions]);
            
            $_SESSION['response'] = [
                'success' => true,
                'message' => 'User created successfully!'
            ];
            
        } elseif($table == 'suppliers') {
            // Add supplier
            $supplier_name = $_POST['supplier_name'] ?? '';
            $supplier_location = $_POST['supplier_location'] ?? '';
            $email = $_POST['email'] ?? '';
            
            if (empty($supplier_name) || empty($supplier_location) || empty($email)) {
                $_SESSION['response'] = [
                    'success' => false,
                    'message' => 'Please fill in all required fields'
                ];
                header("Location: ../$redirect_to");
                exit();
            }
            
            $stmt = $conn->prepare("INSERT INTO suppliers (supplier_name, supplier_location, email, created_by) VALUES (?, ?, ?, ?)");
            $stmt->execute([$supplier_name, $supplier_location, $email, $user['id']]);
            
            $_SESSION['response'] = [
                'success' => true,
                'message' => 'Supplier created successfully!'
            ];
            
        } elseif($table == 'products') {
            // Add product with proper validation
            $product_name = $_POST['product_name'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? 0;
            $stock = $_POST['stock'] ?? 0;
            $category = $_POST['category'] ?? '';
            $size = $_POST['size'] ?? '';
            $color = $_POST['color'] ?? '';
            $brand = $_POST['brand'] ?? '';
            
            // Debug and validate required fields
            error_log("Product validation - Name: '$product_name', Price: '$price', Stock: '$stock'");
            error_log("POST data: " . print_r($_POST, true));
            error_log("Raw input: " . $raw_input);
            
            if (empty($product_name) || trim($product_name) === '') {
                $_SESSION['response'] = [
                    'success' => false,
                    'message' => 'Product name is required'
                ];
                header("Location: ../$redirect_to");
                exit();
            }
            
            if (empty($price) || !is_numeric($price) || floatval($price) <= 0) {
                $_SESSION['response'] = [
                    'success' => false,
                    'message' => 'Valid price is required (must be greater than 0)'
                ];
                header("Location: ../$redirect_to");
                exit();
            }
            
            if (empty($stock) || !is_numeric($stock) || intval($stock) < 0) {
                $_SESSION['response'] = [
                    'success' => false,
                    'message' => 'Valid stock quantity is required (must be 0 or greater)'
                ];
                header("Location: ../$redirect_to");
                exit();
            }
            
            // Handle image upload
            $img = '';
            if(isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
                $upload_dir = '../uploads/';
                $file_extension = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION);
                $new_filename = uniqid() . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;
                
                if(move_uploaded_file($_FILES['img']['tmp_name'], $upload_path)) {
                    $img = $new_filename;
                }
            }
            
            $stmt = $conn->prepare("INSERT INTO products (product_name, description, price, stock, category, size, color, brand, img, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$product_name, $description, $price, $stock, $category, $size, $color, $brand, $img, $user['id']]);
            
            $_SESSION['response'] = [
                'success' => true,
                'message' => 'Product created successfully!'
            ];
        }
        
    } catch(PDOException $e) {
        $_SESSION['response'] = [
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ];
    }
    
    header("Location: ../$redirect_to");
    exit();
?>
