<?php
    session_start();
    if(!isset($_SESSION['user'])) header('location: login.php');
    
    $user = $_SESSION['user'];
    
    // Get dashboard statistics
    include('database/connection.php');
    
    // Get total products
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM products");
    $stmt->execute();
    $total_products = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get total suppliers
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM suppliers");
    $stmt->execute();
    $total_suppliers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get total users
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM users");
    $stmt->execute();
    $total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get pending orders
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM order_product WHERE status = 'pending'");
    $stmt->execute();
    $pending_orders = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get low stock products with customizable threshold
    $low_stock_threshold = 10; // This can be made configurable
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM products WHERE stock < ?");
    $stmt->execute([$low_stock_threshold]);
    $low_stock_products = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get stock-out products
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM products WHERE stock = 0");
    $stmt->execute();
    $stock_out_products = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get current month revenue data
    $stmt = $conn->prepare("
        SELECT 
            DATE(op.created_at) as order_date,
            SUM(op.quantity_received * p.price) as daily_revenue
        FROM order_product op
        JOIN products p ON op.product = p.id
        WHERE DATE_TRUNC('month', op.created_at) = DATE_TRUNC('month', CURRENT_DATE)
        AND op.status = 'complete'
        GROUP BY DATE(op.created_at)
        ORDER BY order_date
    ");
    $stmt->execute();
    $revenue_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get total current month revenue
    $stmt = $conn->prepare("
        SELECT SUM(op.quantity_received * p.price) as total_revenue
        FROM order_product op
        JOIN products p ON op.product = p.id
        WHERE DATE_TRUNC('month', op.created_at) = DATE_TRUNC('month', CURRENT_DATE)
        AND op.status = 'complete'
    ");
    $stmt->execute();
    $total_revenue = $stmt->fetch(PDO::FETCH_ASSOC)['total_revenue'] ?? 0;
    
    // Get overstock items (items with high stock and low turnover)
    $stmt = $conn->prepare("
        SELECT p.*, 
               COALESCE(SUM(op.quantity_received), 0) as total_received,
               EXTRACT(DAYS FROM (CURRENT_DATE - MAX(op.created_at))) as days_since_last_order
        FROM products p
        LEFT JOIN order_product op ON p.id = op.product
        WHERE p.stock > 50 
        GROUP BY p.id
        HAVING COALESCE(SUM(op.quantity_received), 0) < 10 
           OR MAX(op.created_at) < CURRENT_DATE - INTERVAL '30 days'
           OR MAX(op.created_at) IS NULL
        ORDER BY p.stock DESC
        LIMIT 10
    ");
    $stmt->execute();
    $overstock_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get low stock items with details
    $stmt = $conn->prepare("
        SELECT p.*, s.supplier_name
        FROM products p
        LEFT JOIN productsuppliers ps ON p.id = ps.product
        LEFT JOIN suppliers s ON ps.supplier = s.id
        WHERE p.stock < ?
        ORDER BY p.stock ASC
        LIMIT 10
    ");
    $stmt->execute([$low_stock_threshold]);
    $low_stock_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get recent orders
    $stmt = $conn->prepare("
        SELECT op.*, p.product_name, s.supplier_name, u.first_name, u.last_name
        FROM order_product op
        JOIN products p ON op.product = p.id
        JOIN suppliers s ON op.supplier = s.id
        JOIN users u ON op.created_by = u.id
        ORDER BY op.created_at DESC
        LIMIT 5
    ");
    $stmt->execute();
    $recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Inventory Management System</title>
    <?php include('partials/app-header-scripts.php'); ?>
</head>
<body>
    <div id="dashboardMainContainer">
        <?php include('partials/app-sidebar.php') ?>
        <div class="dashboard_content_container" id="dashboard_content_container">
            <?php include('partials/app-topnav.php') ?>

            <?php if(in_array('dashboard_view', $user['permissions'])) { ?>
            <div class="dashboard_content">
                <div class="dashboard_content_main">
                    <div class="row">
                        <div class="column column-12">
                            <h1 class="section_header">
                                <i class="fa fa-dashboard"></i> <span data-translate="Dashboard Overview">Dashboard Overview</span>
                            </h1>
                        </div>
                    </div>
                    
                    <!-- Statistics Cards -->
                    <div class="row">
                        <div class="column column-3">
                            <div class="dashboard_card">
                                <div class="card_header">
                                    <i class="fa fa-cube"></i>
                                    <h3>Total Products</h3>
                                </div>
                                <div class="card_body">
                                    <span class="stat_number" data-stat="total_products" data-no-translate><?= $total_products ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="column column-3">
                            <div class="dashboard_card">
                                <div class="card_header">
                                    <i class="fa fa-dollar"></i>
                                    <h3>Monthly Revenue</h3>
                                </div>
                                <div class="card_body">
                                    <span class="stat_number revenue" data-stat="total_revenue" data-no-translate>$<?= number_format($total_revenue, 2) ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="column column-3">
                            <div class="dashboard_card warning">
                                <div class="card_header">
                                    <i class="fa fa-exclamation-triangle"></i>
                                    <h3>Low Stock Items</h3>
                                </div>
                                <div class="card_body">
                                    <span class="stat_number" data-stat="low_stock_products" data-no-translate><?= $low_stock_products ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="column column-3">
                            <div class="dashboard_card danger">
                                <div class="card_header">
                                    <i class="fa fa-times-circle"></i>
                                    <h3>Stock-Out Items</h3>
                                </div>
                                <div class="card_body">
                                    <span class="stat_number" data-stat="stock_out_products" data-no-translate><?= $stock_out_products ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Real-time Alerts -->
                    <?php if($stock_out_products > 0): ?>
                    <div class="row">
                        <div class="column column-12">
                            <div class="alert alert-danger pulse">
                                <i class="fa fa-times-circle"></i>
                                <strong>URGENT - Stock-Out Alert:</strong> <?= $stock_out_products ?> products are completely out of stock and need immediate attention!
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if($low_stock_products > 0): ?>
                    <div class="row">
                        <div class="column column-12">
                            <div class="alert alert-warning">
                                <i class="fa fa-exclamation-triangle"></i>
                                <strong>Low Stock Alert:</strong> <?= $low_stock_products ?> products are running low on stock.
                                <a href="products-view.php">View Products</a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Revenue Chart -->
                    <div class="row">
                        <div class="column column-12">
                            <div class="dashboard_card">
                                <div class="card_header">
                                    <i class="fa fa-line-chart"></i>
                                    <h3>Monthly Revenue Trends</h3>
                                    <span class="chart-period">Current Month</span>
                                </div>
                                <div class="card_body">
                                    <canvas id="revenueChart" width="400" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Inventory Management Sections -->
                    <div class="row">
                        <div class="column column-6">
                            <div class="dashboard_card">
                                <div class="card_header">
                                    <i class="fa fa-exclamation-triangle"></i>
                                    <h3>Low Stock Items</h3>
                                    <span class="threshold-indicator">Threshold: <?= $low_stock_threshold ?> units</span>
                                </div>
                                <div class="card_body">
                                    <?php if(!empty($low_stock_items)): ?>
                                    <div class="inventory-alerts">
                                        <?php foreach($low_stock_items as $item): ?>
                                        <div class="alert-item low-stock">
                                            <div class="item-info">
                                                <strong><?= htmlspecialchars($item['product_name']) ?></strong>
                                                <span class="category"><?= htmlspecialchars($item['category']) ?></span>
                                            </div>
                                            <div class="stock-level">
                                                <span class="stock-count <?= $item['stock'] == 0 ? 'zero' : 'low' ?>">
                                                    <?= $item['stock'] ?> units
                                                </span>
                                                <span class="supplier"><?= htmlspecialchars($item['supplier_name'] ?? 'No Supplier') ?></span>
                                            </div>
                                            <div class="action-buttons">
                                                <button class="btn btn-sm btn-primary reorder-btn" data-product-id="<?= $item['id'] ?>">
                                                    <i class="fa fa-plus"></i> Reorder
                                                </button>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php else: ?>
                                    <div class="no-alerts">
                                        <i class="fa fa-check-circle"></i>
                                        <p>All items are well-stocked!</p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="column column-6">
                            <div class="dashboard_card">
                                <div class="card_header">
                                    <i class="fa fa-archive"></i>
                                    <h3>Overstock Items</h3>
                                    <span class="recommendation">Automated Recommendations</span>
                                </div>
                                <div class="card_body">
                                    <?php if(!empty($overstock_items)): ?>
                                    <div class="inventory-alerts">
                                        <?php foreach($overstock_items as $item): ?>
                                        <div class="alert-item overstock">
                                            <div class="item-info">
                                                <strong><?= htmlspecialchars($item['product_name']) ?></strong>
                                                <span class="category"><?= htmlspecialchars($item['category']) ?></span>
                                            </div>
                                            <div class="stock-level">
                                                <span class="stock-count high"><?= $item['stock'] ?> units</span>
                                                <span class="turnover-info">
                                                    Last order: <?= $item['days_since_last_order'] ?? 'Never' ?> days ago
                                                </span>
                                            </div>
                                            <div class="recommendations">
                                                <div class="recommendation-item">
                                                    <i class="fa fa-arrow-down"></i>
                                                    <span>Reduce by 30%</span>
                                                </div>
                                                <div class="recommendation-item">
                                                    <i class="fa fa-tag"></i>
                                                    <span>Consider discount</span>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php else: ?>
                                    <div class="no-alerts">
                                        <i class="fa fa-balance-scale"></i>
                                        <p>Inventory levels are optimized!</p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Orders -->
                    <div class="row">
                        <div class="column column-12">
                            <div class="dashboard_card">
                                <div class="card_header">
                                    <i class="fa fa-list"></i>
                                    <h3>Recent Orders</h3>
                                </div>
                                <div class="card_body">
                                    <?php if(!empty($recent_orders)): ?>
                                    <table class="recent_orders_table">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Supplier</th>
                                                <th>Quantity</th>
                                                <th>Status</th>
                                                <th>Ordered By</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($recent_orders as $order): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($order['product_name']) ?></td>
                                                <td><?= htmlspecialchars($order['supplier_name']) ?></td>
                                                <td><?= $order['quantity_ordered'] ?></td>
                                                <td><span class="status-badge status-<?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span></td>
                                                <td><?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></td>
                                                <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <?php else: ?>
                                    <p class="no-data">No recent orders found.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php } else { ?>
                <div id="errorMessage">Access denied.</div>
            <?php } ?>
        </div>
    </div>
    
    <?php include('partials/app-scripts.php'); ?>
    
    <!-- Chart.js for interactive charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Revenue data from PHP
        const revenueData = <?= json_encode($revenue_data) ?>;
        
        // Prepare chart data
        const chartLabels = revenueData.map(item => {
            const date = new Date(item.order_date);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        });
        
        const chartData = revenueData.map(item => parseFloat(item.daily_revenue) || 0);
        
        // Create revenue chart
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Daily Revenue',
                    data: chartData,
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#3498db',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                elements: {
                    point: {
                        hoverRadius: 8
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
        
        // Real-time updates every 30 seconds
        setInterval(function() {
            updateDashboardData();
        }, 30000);
        
        // Function to update dashboard data
        function updateDashboardData() {
            fetch('dashboard-data.php')
                .then(response => response.json())
                .then(data => {
                    // Update statistics cards
                    updateStatisticsCards(data);
                    
                    // Update alerts
                    updateAlerts(data);
                    
                    // Update chart if new data available
                    if (data.revenue_data) {
                        updateRevenueChart(data.revenue_data);
                    }
                })
                .catch(error => {
                    console.error('Error updating dashboard data:', error);
                });
        }
        
        // Update statistics cards
        function updateStatisticsCards(data) {
            if (data.stats) {
                const stats = data.stats;
                
                // Update each statistic
                const elements = {
                    'total_products': stats.total_products,
                    'total_revenue': '$' + parseFloat(stats.total_revenue || 0).toLocaleString('en-US', {minimumFractionDigits: 2}),
                    'low_stock_products': stats.low_stock_products,
                    'stock_out_products': stats.stock_out_products
                };
                
                Object.keys(elements).forEach(key => {
                    const element = document.querySelector(`[data-stat="${key}"]`);
                    if (element) {
                        element.textContent = elements[key];
                    }
                });
            }
        }
        
        // Update alerts
        function updateAlerts(data) {
            // Show/hide stock-out alerts
            const stockOutAlert = document.querySelector('.alert-danger');
            if (data.stats && data.stats.stock_out_products > 0) {
                if (stockOutAlert) {
                    stockOutAlert.style.display = 'block';
                    stockOutAlert.innerHTML = `
                        <i class="fa fa-times-circle"></i>
                        <strong>URGENT - Stock-Out Alert:</strong> ${data.stats.stock_out_products} products are completely out of stock and need immediate attention!
                    `;
                }
            } else if (stockOutAlert) {
                stockOutAlert.style.display = 'none';
            }
        }
        
        // Update revenue chart
        function updateRevenueChart(newData) {
            const newLabels = newData.map(item => {
                const date = new Date(item.order_date);
                return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
            });
            
            const newChartData = newData.map(item => parseFloat(item.daily_revenue) || 0);
            
            revenueChart.data.labels = newLabels;
            revenueChart.data.datasets[0].data = newChartData;
            revenueChart.update();
        }
        
        // Handle reorder button clicks
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('reorder-btn') || e.target.closest('.reorder-btn')) {
                const button = e.target.closest('.reorder-btn');
                const productId = button.dataset.productId;
                
                // Redirect to create order page with product pre-selected
                window.location.href = `create-order.php?product_id=${productId}`;
            }
        });
        
        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            // Add pulsing animation to urgent alerts
            const urgentAlerts = document.querySelectorAll('.alert-danger.pulse');
            urgentAlerts.forEach(alert => {
                alert.style.animation = 'pulse 2s infinite';
            });
        });
    </script>
    
    <style>
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }
    </style>
</body>
</html>
