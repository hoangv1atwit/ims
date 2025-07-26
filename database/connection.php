<?php
    // Database configuration - Using PostgreSQL from environment
    $host = getenv('PGHOST') ?: 'localhost';
    $dbname = getenv('PGDATABASE') ?: 'inventory_management';
    $username = getenv('PGUSER') ?: 'root';
    $password = getenv('PGPASSWORD') ?: '';
    $port = getenv('PGPORT') ?: '5432';
    
    try {
        $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
?>
