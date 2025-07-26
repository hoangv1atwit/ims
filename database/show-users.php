<?php
    include('connection.php');
    
    try {
        $stmt = $conn->prepare("SELECT * FROM users ORDER BY created_at DESC");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $users;
        
    } catch(PDOException $e) {
        return [];
    }
?>
