<?php
    session_start();
    if(!isset($_SESSION['user'])) header('location: login.php');
    
    $user = $_SESSION['user'];
    
    // Handle settings update
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
        include('database/connection.php');
        
        if($_POST['action'] == 'update_thresholds') {
            $low_stock_threshold = (int)$_POST['low_stock_threshold'];
            $overstock_threshold = (int)$_POST['overstock_threshold'];
            $days_since_order = (int)$_POST['days_since_order'];
            
            // For now, we'll store these in session (in production, use database)
            $_SESSION['settings'] = [
                'low_stock_threshold' => $low_stock_threshold,
                'overstock_threshold' => $overstock_threshold,
                'days_since_order' => $days_since_order
            ];
            
            $success_message = "Settings updated successfully!";
        }
    }
    
    // Get current settings
    $settings = $_SESSION['settings'] ?? [
        'low_stock_threshold' => 10,
        'overstock_threshold' => 50,
        'days_since_order' => 30
    ];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Settings - Inventory Management System</title>
    <?php include('partials/app-header-scripts.php'); ?>
</head>
<body>
    <div id="dashboardMainContainer">
        <?php include('partials/app-sidebar.php') ?>
        <div class="dasboard_content_container" id="dasboard_content_container">
            <?php include('partials/app-topnav.php') ?>
            
            <?php if(in_array('user_edit', $user['permissions'])) { ?>
            <div class="dashboard_content">
                <div class="dashboard_content_main">
                    <div class="row">
                        <div class="column column-12">
                            <h1 class="section_header">
                                <i class="fa fa-cog"></i> System Settings
                            </h1>
                        </div>
                    </div>
                    
                    <?php if(isset($success_message)): ?>
                    <div class="row">
                        <div class="column column-12">
                            <div class="alert alert-success">
                                <i class="fa fa-check-circle"></i>
                                <?= $success_message ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="column column-8">
                            <div class="dashboard_card">
                                <div class="card_header">
                                    <i class="fa fa-sliders"></i>
                                    <h3>Inventory Alert Thresholds</h3>
                                </div>
                                <div class="card_body">
                                    <form method="POST" action="settings.php">
                                        <input type="hidden" name="action" value="update_thresholds">
                                        
                                        <div class="form-group">
                                            <label for="low_stock_threshold">Low Stock Threshold</label>
                                            <input type="number" 
                                                   id="low_stock_threshold" 
                                                   name="low_stock_threshold" 
                                                   class="form-control" 
                                                   value="<?= $settings['low_stock_threshold'] ?>" 
                                                   min="1" 
                                                   max="100" 
                                                   required>
                                            <small class="help-text">
                                                Items with stock below this level will trigger low stock alerts
                                            </small>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="overstock_threshold">Overstock Threshold</label>
                                            <input type="number" 
                                                   id="overstock_threshold" 
                                                   name="overstock_threshold" 
                                                   class="form-control" 
                                                   value="<?= $settings['overstock_threshold'] ?>" 
                                                   min="10" 
                                                   max="1000" 
                                                   required>
                                            <small class="help-text">
                                                Items with stock above this level will be considered for overstock analysis
                                            </small>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="days_since_order">Days Since Last Order</label>
                                            <input type="number" 
                                                   id="days_since_order" 
                                                   name="days_since_order" 
                                                   class="form-control" 
                                                   value="<?= $settings['days_since_order'] ?>" 
                                                   min="1" 
                                                   max="365" 
                                                   required>
                                            <small class="help-text">
                                                Items with no orders in this many days will be flagged for review
                                            </small>
                                        </div>
                                        
                                        <div class="form-actions">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa fa-save"></i> Update Settings
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="column column-4">
                            <div class="dashboard_card">
                                <div class="card_header">
                                    <i class="fa fa-info-circle"></i>
                                    <h3>Alert Configuration</h3>
                                </div>
                                <div class="card_body">
                                    <div class="setting-info">
                                        <h4>Current Settings</h4>
                                        <ul class="settings-list">
                                            <li>
                                                <span class="setting-name">Low Stock Threshold:</span>
                                                <span class="setting-value"><?= $settings['low_stock_threshold'] ?> units</span>
                                            </li>
                                            <li>
                                                <span class="setting-name">Overstock Threshold:</span>
                                                <span class="setting-value"><?= $settings['overstock_threshold'] ?> units</span>
                                            </li>
                                            <li>
                                                <span class="setting-name">Days Since Order:</span>
                                                <span class="setting-value"><?= $settings['days_since_order'] ?> days</span>
                                            </li>
                                        </ul>
                                    </div>
                                    
                                    <div class="alert-preview">
                                        <h4>Alert Types</h4>
                                        <div class="alert-type">
                                            <span class="alert-indicator low-stock"></span>
                                            <span>Low Stock Warning</span>
                                        </div>
                                        <div class="alert-type">
                                            <span class="alert-indicator stock-out"></span>
                                            <span>Stock-Out Emergency</span>
                                        </div>
                                        <div class="alert-type">
                                            <span class="alert-indicator overstock"></span>
                                            <span>Overstock Recommendation</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="dashboard_card">
                                <div class="card_header">
                                    <i class="fa fa-bell"></i>
                                    <h3>Notification Settings</h3>
                                </div>
                                <div class="card_body">
                                    <div class="notification-settings">
                                        <div class="notification-item">
                                            <label class="switch">
                                                <input type="checkbox" checked>
                                                <span class="slider"></span>
                                            </label>
                                            <span>Real-time Dashboard Updates</span>
                                        </div>
                                        <div class="notification-item">
                                            <label class="switch">
                                                <input type="checkbox" checked>
                                                <span class="slider"></span>
                                            </label>
                                            <span>Stock-Out Alerts</span>
                                        </div>
                                        <div class="notification-item">
                                            <label class="switch">
                                                <input type="checkbox">
                                                <span class="slider"></span>
                                            </label>
                                            <span>Email Notifications</span>
                                        </div>
                                        <div class="notification-item">
                                            <label class="switch">
                                                <input type="checkbox">
                                                <span class="slider"></span>
                                            </label>
                                            <span>SMS Alerts</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php } else { ?>
                <div class="dashboard_content">
                    <div id="errorMessage">Access denied.</div>
                </div>
            <?php } ?>
        </div>
    </div>
    
    <?php include('partials/app-scripts.php'); ?>
</body>
</html>