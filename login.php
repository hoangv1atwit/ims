<?php
    session_start();
    
    // If user is already logged in, redirect to dashboard
    if(isset($_SESSION['user'])) {
        header('Location: dashboard.php');
        exit();
    }
    
    $login_error = '';
    
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        include('database/connection.php');
        
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        // Validate input
        if(empty($email) || empty($password)) {
            $login_error = 'Please fill in all fields.';
        } else {
            // Check user credentials
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($user && password_verify($password, $user['password'])) {
                // Convert permissions string to array
                $permissions = !empty($user['permissions']) ? explode(',', $user['permissions']) : [];
                $user['permissions'] = $permissions;
                
                $_SESSION['user'] = $user;
                header('Location: dashboard.php');
                exit();
            } else {
                $login_error = 'Invalid email or password.';
            }
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Inventory Management System</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <i class="fas fa-boxes"></i>
                <h2>Inventory Management System</h2>
                <p>Please sign in to continue</p>
            </div>
            
            <form method="POST" action="login.php" class="login-form">
                <?php if($login_error): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?= htmlspecialchars($login_error) ?>
                    </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" placeholder="Email Address" required>
                </div>
                
                <div class="form-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                
                <button type="submit" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    Sign In
                </button>
            </form>
            
            <div class="login-footer">
                <p>Demo Account: admin@inventory.com / password123</p>
            </div>
        </div>
    </div>
</body>
</html>
