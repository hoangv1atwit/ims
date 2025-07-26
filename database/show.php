<?php
    include('connection.php');
    
    try {
        $stmt = $conn->prepare("SELECT * FROM $show_table ORDER BY created_at DESC");
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $results;
        
    } catch(PDOException $e) {
        return [];
    }
?>
