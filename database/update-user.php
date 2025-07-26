<?php
    session_start();
    include('connection.php');
    
    if(!isset($_SESSION['user'])) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit();
    }
    
    $response = ['success' => false, 'message' => ''];
    
    try {
        $userId = $_POST['userId'];
        $first_name = $_POST['f_name'];
        $last_name = $_POST['l_name'];
        $email = $_POST['email'];
        $permissions = isset($_POST['permissions']) ? $_POST['permissions'] : '';
        
        $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, permissions = ? WHERE id = ?");
        $stmt->execute([$first_name, $last_name, $email, $permissions, $userId]);
        
        $response['success'] = true;
        $response['message'] = 'User updated successfully!';
        
    } catch(PDOException $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }
    
    echo json_encode($response);
?>
