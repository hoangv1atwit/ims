<?php
    session_start();
    if(!isset($_SESSION['user'])) header('location: login.php');
    
    $user = $_SESSION['user'];
    
    $show_table = 'products';
    $products = include('database/show.php');
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Products - Inventory Management System</title>
    <?php include('partials/app-header-scripts.php'); ?>
</head>
<body>
    <div id="dashboardMainContainer">
        <?php include('partials/app-sidebar.php') ?>
        <div class="dasboard_content_container" id="dasboard_content_container">
            <?php include('partials/app-topnav.php') ?>
            <div class="dashboard_content">
                <?php if(in_array('product_view', $user['permissions'])) { ?>
                <div class="dashboard_content_main">
                    <div class="row">
                        <div class="column column-12">
                            <h1 class="section_header"><i class="fa fa-list"></i> Products</h1>
                            <div class="section_content">
                                <div class="products">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Image</th>
                                                <th>Product Name</th>
                                                <th>Price</th>
                                                <th>Stock</th>
                                                <th>Category</th>
                                                <th>Size</th>
                                                <th>Color</th>
                                                <th>Brand</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($products as $index => $product){ ?>
                                                <tr class="<?= $product['stock'] < 10 ? 'low-stock' : '' ?>">
                                                    <td><?= $index + 1 ?></td>
                                                    <td>
                                                        <?php if($product['img']): ?>
                                                            <img src="uploads/<?= $product['img'] ?>" alt="<?= htmlspecialchars($product['product_name']) ?>" class="product-thumbnail">
                                                        <?php else: ?>
                                                            <div class="no-image">No Image</div>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= htmlspecialchars($product['product_name']) ?></td>
                                                    <td>$<?= $product['price'] ?></td>
                                                    <td>
                                                        <span class="stock-badge <?= $product['stock'] < 10 ? 'low-stock' : '' ?>">
                                                            <?= $product['stock'] ?>
                                                        </span>
                                                    </td>
                                                    <td><?= htmlspecialchars($product['category']) ?></td>
                                                    <td><?= htmlspecialchars($product['size']) ?></td>
                                                    <td><?= htmlspecialchars($product['color']) ?></td>
                                                    <td><?= htmlspecialchars($product['brand']) ?></td>
                                                    <td data-no-translate="true">
                                                        <a href="" class="<?= in_array('product_edit', $user['permissions']) ? 'updateProduct' : 'accessDeniedErr' ?>" 
                                                           data-id="<?= $product['id'] ?>" data-no-translate="true">
                                                            <i class="fa fa-pencil"></i> <span data-translatable="true">Edit</span>
                                                        </a> |
                                                        <a href="" class="<?= in_array('product_delete', $user['permissions']) ? 'deleteProduct' : 'accessDeniedErr' ?>" 
                                                           data-id="<?= $product['id'] ?>" data-name="<?= htmlspecialchars($product['product_name']) ?>" data-no-translate="true">
                                                            <i class="fa fa-trash"></i> <span data-translatable="true">Delete</span>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                    <p class="productCount"><?= count($products) ?> products</p>
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
    </div>
    
    <?php include('partials/app-scripts.php'); ?>
    
    <script>
        function script(){
            var vm = this;
            
            this.registerEvents = function(){
                document.addEventListener('click', function(e){
                    targetElement = e.target;
                    classList = targetElement.classList;
                    
                    if(classList.contains('deleteProduct')){
                        e.preventDefault();
                        
                        productId = targetElement.dataset.id;
                        productName = targetElement.dataset.name;
                        
                        BootstrapDialog.confirm({
                            type: BootstrapDialog.TYPE_DANGER,
                            title: 'Delete Product',
                            message: 'Are you sure to delete <strong>'+ productName +'</strong>?',
                            callback: function(isDelete){
                                if(isDelete){
                                    $.ajax({
                                        method: 'POST',
                                        data: {
                                            id: productId,
                                            table: 'products'
                                        },
                                        url: 'database/delete.php',
                                        dataType: 'json',
                                        success: function(data){
                                            message = data.success ? 
                                                productName + ' successfully deleted!' : data.message || 'Error processing your request!';
                                            
                                            BootstrapDialog.alert({
                                                type: data.success ? BootstrapDialog.TYPE_SUCCESS : BootstrapDialog.TYPE_DANGER,
                                                message: message,
                                                callback: function(){
                                                    if(data.success) location.reload();
                                                }
                                            });
                                        }
                                    });
                                }
                            }
                        });
                    }
                });
            },
            
            this.initialize = function(){
                this.registerEvents();
            }
        }
        
        var script = new script;
        script.initialize();
    </script>
</body>
</html>
