<?php
    $current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="dashboard_sidebar">
    <div class="sidebar_header">
        <h3><i class="fa fa-boxes"></i> IMS</h3>
    </div>
    
    <div class="sidebar_user">
        <div class="user_info">
            <strong><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></strong>
            <span><?= htmlspecialchars($user['email']) ?></span>
        </div>
    </div>
    
    <nav class="sidebar_nav">
        <ul>
            <li class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>">
                <a href="dashboard.php">
                    <i class="fa fa-dashboard"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <?php if(in_array('product_view', $user['permissions']) || in_array('product_create', $user['permissions'])): ?>
            <li class="nav-header">Products</li>
            <?php if(in_array('product_view', $user['permissions'])): ?>
            <li class="<?= $current_page == 'products-view.php' ? 'active' : '' ?>">
                <a href="products-view.php">
                    <i class="fa fa-cube"></i>
                    <span>View Products</span>
                </a>
            </li>
            <?php endif; ?>
            <?php if(in_array('product_create', $user['permissions'])): ?>
            <li class="<?= $current_page == 'products-add.php' ? 'active' : '' ?>">
                <a href="products-add.php">
                    <i class="fa fa-plus"></i>
                    <span>Add Product</span>
                </a>
            </li>
            <?php endif; ?>
            <?php endif; ?>
            
            <?php if(in_array('supplier_view', $user['permissions']) || in_array('supplier_create', $user['permissions'])): ?>
            <li class="nav-header">Suppliers</li>
            <?php if(in_array('supplier_view', $user['permissions'])): ?>
            <li class="<?= $current_page == 'supplier-view.php' ? 'active' : '' ?>">
                <a href="supplier-view.php">
                    <i class="fa fa-truck"></i>
                    <span>View Suppliers</span>
                </a>
            </li>
            <?php endif; ?>
            <?php if(in_array('supplier_create', $user['permissions'])): ?>
            <li class="<?= $current_page == 'supplier-add.php' ? 'active' : '' ?>">
                <a href="supplier-add.php">
                    <i class="fa fa-plus"></i>
                    <span>Add Supplier</span>
                </a>
            </li>
            <?php endif; ?>
            <?php endif; ?>
            
            <?php if(in_array('po_view', $user['permissions']) || in_array('po_create', $user['permissions'])): ?>
            <li class="nav-header">Purchase Orders</li>
            <?php if(in_array('po_view', $user['permissions'])): ?>
            <li class="<?= $current_page == 'view-order.php' ? 'active' : '' ?>">
                <a href="view-order.php">
                    <i class="fa fa-list"></i>
                    <span>View Orders</span>
                </a>
            </li>
            <?php endif; ?>
            <?php if(in_array('po_create', $user['permissions'])): ?>
            <li class="<?= $current_page == 'create-order.php' ? 'active' : '' ?>">
                <a href="create-order.php">
                    <i class="fa fa-plus"></i>
                    <span>Create Order</span>
                </a>
            </li>
            <?php endif; ?>
            <?php endif; ?>
            
            <?php if(in_array('user_view', $user['permissions']) || in_array('user_create', $user['permissions'])): ?>
            <li class="nav-header">Users</li>
            <?php if(in_array('user_view', $user['permissions'])): ?>
            <li class="<?= $current_page == 'users-view.php' ? 'active' : '' ?>">
                <a href="users-view.php">
                    <i class="fa fa-users"></i>
                    <span>View Users</span>
                </a>
            </li>
            <?php endif; ?>
            <?php if(in_array('user_create', $user['permissions'])): ?>
            <li class="<?= $current_page == 'users-add.php' ? 'active' : '' ?>">
                <a href="users-add.php">
                    <i class="fa fa-plus"></i>
                    <span>Add User</span>
                </a>
            </li>
            <?php endif; ?>
            <?php endif; ?>
            
            <?php if(in_array('report_view', $user['permissions'])): ?>
            <li class="nav-header">Reports</li>
            <li class="<?= $current_page == 'report.php' ? 'active' : '' ?>">
                <a href="report.php">
                    <i class="fa fa-file-text"></i>
                    <span>Reports</span>
                </a>
            </li>
            <?php endif; ?>
            
            <?php if(in_array('user_edit', $user['permissions'])): ?>
            <li class="nav-header">System</li>
            <li class="<?= $current_page == 'settings.php' ? 'active' : '' ?>">
                <a href="settings.php">
                    <i class="fa fa-cog"></i>
                    <span>Settings</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>
