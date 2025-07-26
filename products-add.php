<?php
    session_start();
    if(!isset($_SESSION['user'])) header('location: login.php');
    
    $_SESSION['table'] = 'products';
    $_SESSION['redirect_to'] = 'products-add.php';
    
    $user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Product - Inventory Management System</title>
    <?php include('partials/app-header-scripts.php'); ?>
</head>
<body>
    <div id="dashboardMainContainer">
        <?php include('partials/app-sidebar.php') ?>
        <div class="dasboard_content_container" id="dasboard_content_container">
            <?php include('partials/app-topnav.php') ?>
            <div class="dashboard_content">
                <?php if(in_array('product_create', $user['permissions'])) { ?>
                <div class="dashboard_content_main">
                    <div class="row">
                        <div class="column column-12">
                            <h1 class="section_header"><i class="fa fa-plus"></i> Add Product</h1>
                            <div id="productAddFormContainer">
                                <form action="database/add-product-direct.php" method="POST" class="appForm" enctype="multipart/form-data">
                                    <div class="appFormInputContainer">
                                        <label for="product_name">Product Name</label>
                                        <input type="text" class="appFormInput" id="product_name" name="product_name" required />
                                    </div>
                                    <div class="appFormInputContainer">
                                        <label for="description">Description</label>
                                        <textarea class="appFormInput" id="description" name="description" rows="3"></textarea>
                                    </div>
                                    <div class="appFormInputContainer">
                                        <label for="price">Price</label>
                                        <input type="number" step="0.01" class="appFormInput" id="price" name="price" required />
                                    </div>
                                    <div class="appFormInputContainer">
                                        <label for="stock">Stock Quantity</label>
                                        <input type="number" class="appFormInput" id="stock" name="stock" required />
                                    </div>
                                    <div class="appFormInputContainer">
                                        <label for="category">Category</label>
                                        <select class="appFormInput" id="category" name="category">
                                            <option value="">Select Category</option>
                                            <option value="Tops">Tops</option>
                                            <option value="Bottoms">Bottoms</option>
                                            <option value="Dresses">Dresses</option>
                                            <option value="Outerwear">Outerwear</option>
                                            <option value="Footwear">Footwear</option>
                                            <option value="Accessories">Accessories</option>
                                        </select>
                                    </div>
                                    <div class="appFormInputContainer">
                                        <label for="size">Size</label>
                                        <select class="appFormInput" id="size" name="size">
                                            <option value="">Select Size</option>
                                            <option value="XS">XS</option>
                                            <option value="S">S</option>
                                            <option value="M">M</option>
                                            <option value="L">L</option>
                                            <option value="XL">XL</option>
                                            <option value="XXL">XXL</option>
                                        </select>
                                    </div>
                                    <div class="appFormInputContainer">
                                        <label for="color">Color</label>
                                        <input type="text" class="appFormInput" id="color" name="color" />
                                    </div>
                                    <div class="appFormInputContainer">
                                        <label for="brand">Brand</label>
                                        <input type="text" class="appFormInput" id="brand" name="brand" />
                                    </div>
                                    <div class="appFormInputContainer">
                                        <label for="img">Product Image</label>
                                        <input type="file" class="appFormInput" id="img" name="img" accept="image/*" />
                                    </div>
                                    <button type="submit" class="appBtn" name="submit"><i class="fa fa-plus"></i> Add Product</button>
                                </form>
                                
                                <?php 
                                    if(isset($_SESSION['response'])){
                                        $response_message = $_SESSION['response']['message'];
                                        $is_success = $_SESSION['response']['success'];
                                ?>
                                    <div class="responseMessage">
                                        <p class="responseMessage <?= $is_success ? 'responseMessage__success' : 'responseMessage__error' ?>">
                                            <?= $response_message ?>
                                        </p>
                                    </div>
                                <?php unset($_SESSION['response']); } ?>
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
        // Debug form submission
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('.appForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    console.log('Form submission detected');
                    const formData = new FormData(form);
                    console.log('Form data entries:');
                    for (let [key, value] of formData.entries()) {
                        console.log(key + ': ' + value);
                    }
                });
            }
        });
    </script>
</body>
</html>
