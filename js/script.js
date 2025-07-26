// Global JavaScript functions for the Inventory Management System

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips if Bootstrap is loaded
    if (typeof $().tooltip === 'function') {
        $('[data-toggle="tooltip"]').tooltip();
    }
    
    // Initialize any custom components
    initializeCustomComponents();
});

// Initialize custom components
function initializeCustomComponents() {
    // Auto-hide response messages after 5 seconds
    setTimeout(function() {
        const responseMessages = document.querySelectorAll('.responseMessage');
        responseMessages.forEach(function(message) {
            message.style.transition = 'opacity 0.5s ease';
            message.style.opacity = '0';
            setTimeout(function() {
                message.style.display = 'none';
            }, 500);
        });
    }, 5000);
    
    // Add click handler for access denied errors
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('accessDeniedErr')) {
            e.preventDefault();
            BootstrapDialog.alert({
                type: BootstrapDialog.TYPE_DANGER,
                title: 'Access Denied',
                message: 'You do not have permission to perform this action.'
            });
        }
    });
}

// Utility functions
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

function formatDateTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Form validation helpers
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function validateRequired(value) {
    return value && value.trim().length > 0;
}

function validateNumber(value) {
    return !isNaN(value) && value > 0;
}

// AJAX helper functions
function makeAjaxRequest(url, data, method = 'POST') {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: url,
            method: method,
            data: data,
            dataType: 'json',
            success: function(response) {
                resolve(response);
            },
            error: function(xhr, status, error) {
                reject({
                    status: status,
                    error: error,
                    message: 'An error occurred while processing your request.'
                });
            }
        });
    });
}

// Show loading indicator
function showLoading(element) {
    if (element) {
        element.classList.add('loading');
        element.disabled = true;
    }
}

// Hide loading indicator
function hideLoading(element) {
    if (element) {
        element.classList.remove('loading');
        element.disabled = false;
    }
}

// Show success message
function showSuccess(message, callback) {
    BootstrapDialog.alert({
        type: BootstrapDialog.TYPE_SUCCESS,
        title: 'Success',
        message: message,
        callback: callback
    });
}

// Show error message
function showError(message, callback) {
    BootstrapDialog.alert({
        type: BootstrapDialog.TYPE_DANGER,
        title: 'Error',
        message: message,
        callback: callback
    });
}

// Show confirmation dialog
function showConfirmation(message, callback) {
    BootstrapDialog.confirm({
        type: BootstrapDialog.TYPE_WARNING,
        title: 'Confirmation',
        message: message,
        callback: callback
    });
}

// File upload preview
function previewImage(input, previewElement) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewElement.src = e.target.result;
            previewElement.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Table search functionality
function initializeTableSearch(tableId, searchInputId) {
    const searchInput = document.getElementById(searchInputId);
    const table = document.getElementById(tableId);
    
    if (searchInput && table) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = table.querySelectorAll('tbody tr');
            
            rows.forEach(function(row) {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
}

// Export table to CSV
function exportTableToCSV(tableId, filename) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    let csv = [];
    const rows = table.querySelectorAll('tr');
    
    rows.forEach(function(row) {
        const cols = row.querySelectorAll('td, th');
        const rowData = [];
        
        cols.forEach(function(col) {
            rowData.push('"' + col.textContent.replace(/"/g, '""') + '"');
        });
        
        csv.push(rowData.join(','));
    });
    
    // Download CSV
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename + '.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}

// Local storage helpers
function saveToLocalStorage(key, data) {
    try {
        localStorage.setItem(key, JSON.stringify(data));
        return true;
    } catch (e) {
        console.error('Error saving to localStorage:', e);
        return false;
    }
}

function getFromLocalStorage(key) {
    try {
        const data = localStorage.getItem(key);
        return data ? JSON.parse(data) : null;
    } catch (e) {
        console.error('Error reading from localStorage:', e);
        return null;
    }
}

function removeFromLocalStorage(key) {
    try {
        localStorage.removeItem(key);
        return true;
    } catch (e) {
        console.error('Error removing from localStorage:', e);
        return false;
    }
}

// Form serialization helper
function serializeForm(form) {
    const formData = new FormData(form);
    const data = {};
    
    for (let [key, value] of formData.entries()) {
        if (data[key]) {
            if (Array.isArray(data[key])) {
                data[key].push(value);
            } else {
                data[key] = [data[key], value];
            }
        } else {
            data[key] = value;
        }
    }
    
    return data;
}

// Debounce function for search inputs
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// CRUD Operation Handlers
$(document).ready(function() {
    
    // Handle Product Edit
    $(document).on('click', '.updateProduct', function(e) {
        e.preventDefault();
        
        if ($(this).hasClass('accessDeniedErr')) {
            return;
        }
        
        const productId = $(this).data('id');
        
        // Get product data via AJAX
        $.ajax({
            url: 'database/get-product.php',
            method: 'POST',
            data: { id: productId },
            dataType: 'json',
            success: function(product) {
                // Create edit modal
                const modalHTML = `
                    <div class="modal fade" id="editProductModal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Edit Product</h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <form id="updateProductForm" enctype="multipart/form-data">
                                        <input type="hidden" id="edit_product_id" name="id" value="${product.id}">
                                        
                                        <div class="appFormInputContainer">
                                            <label for="edit_product_name">Product Name</label>
                                            <input type="text" class="appFormInput" id="edit_product_name" name="product_name" value="${product.product_name}" required>
                                        </div>
                                        
                                        <div class="appFormInputContainer">
                                            <label for="edit_description">Description</label>
                                            <textarea class="appFormInput" id="edit_description" name="description" rows="3">${product.description || ''}</textarea>
                                        </div>
                                        
                                        <div class="appFormInputContainer">
                                            <label for="edit_price">Price</label>
                                            <input type="number" step="0.01" class="appFormInput" id="edit_price" name="price" value="${product.price}" required>
                                        </div>
                                        
                                        <div class="appFormInputContainer">
                                            <label for="edit_stock">Stock Quantity</label>
                                            <input type="number" class="appFormInput" id="edit_stock" name="stock" value="${product.stock}" required>
                                        </div>
                                        
                                        <div class="appFormInputContainer">
                                            <label for="edit_category">Category</label>
                                            <select class="appFormInput" id="edit_category" name="category">
                                                <option value="">Select Category</option>
                                                <option value="Tops" ${product.category === 'Tops' ? 'selected' : ''}>Tops</option>
                                                <option value="Bottoms" ${product.category === 'Bottoms' ? 'selected' : ''}>Bottoms</option>
                                                <option value="Dresses" ${product.category === 'Dresses' ? 'selected' : ''}>Dresses</option>
                                                <option value="Outerwear" ${product.category === 'Outerwear' ? 'selected' : ''}>Outerwear</option>
                                                <option value="Footwear" ${product.category === 'Footwear' ? 'selected' : ''}>Footwear</option>
                                                <option value="Accessories" ${product.category === 'Accessories' ? 'selected' : ''}>Accessories</option>
                                            </select>
                                        </div>
                                        
                                        <div class="appFormInputContainer">
                                            <label for="edit_size">Size</label>
                                            <select class="appFormInput" id="edit_size" name="size">
                                                <option value="">Select Size</option>
                                                <option value="XS" ${product.size === 'XS' ? 'selected' : ''}>XS</option>
                                                <option value="S" ${product.size === 'S' ? 'selected' : ''}>S</option>
                                                <option value="M" ${product.size === 'M' ? 'selected' : ''}>M</option>
                                                <option value="L" ${product.size === 'L' ? 'selected' : ''}>L</option>
                                                <option value="XL" ${product.size === 'XL' ? 'selected' : ''}>XL</option>
                                                <option value="XXL" ${product.size === 'XXL' ? 'selected' : ''}>XXL</option>
                                            </select>
                                        </div>
                                        
                                        <div class="appFormInputContainer">
                                            <label for="edit_color">Color</label>
                                            <input type="text" class="appFormInput" id="edit_color" name="color" value="${product.color || ''}">
                                        </div>
                                        
                                        <div class="appFormInputContainer">
                                            <label for="edit_brand">Brand</label>
                                            <input type="text" class="appFormInput" id="edit_brand" name="brand" value="${product.brand || ''}">
                                        </div>
                                        
                                        <div class="appFormInputContainer">
                                            <label for="edit_img">Product Image</label>
                                            <input type="file" class="appFormInput" id="edit_img" name="img" accept="image/*">
                                            ${product.img ? `<p>Current image: ${product.img}</p>` : ''}
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary" id="saveProductChanges">Save Changes</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                // Remove existing modal if any
                $('#editProductModal').remove();
                
                // Add modal to page
                $('body').append(modalHTML);
                
                // Show modal
                $('#editProductModal').modal('show');
            },
            error: function() {
                showError('Error loading product data');
            }
        });
    });
    
    // Handle Product Update Form Submission
    $(document).on('click', '#saveProductChanges', function() {
        console.log('Save button clicked');
        
        const form = document.getElementById('updateProductForm');
        if (!form) {
            console.error('Form not found!');
            showError('Form not found');
            return;
        }
        
        console.log('Form found:', form);
        
        // Create FormData and manually add all required fields
        const formData = new FormData();
        
        // Add all form fields manually to ensure they're included
        const formFields = form.querySelectorAll('input, select, textarea');
        console.log('Found form fields:', formFields.length);
        
        formFields.forEach(field => {
            if (field.type === 'file') {
                if (field.files.length > 0) {
                    formData.append(field.name, field.files[0]);
                    console.log('Added file:', field.name, field.files[0]);
                }
            } else {
                formData.append(field.name, field.value);
                console.log('Added field:', field.name, field.value);
            }
        });
        
        // Ensure ID field is included (critical for update)
        const idField = document.getElementById('edit_product_id');
        if (idField && idField.value) {
            formData.set('id', idField.value);
            console.log('Explicitly added ID:', idField.value);
        } else {
            console.error('ID field not found or empty!');
            showError('Product ID is missing. Please try again.');
            return;
        }
        
        // Validate required fields
        const productName = formData.get('product_name');
        const price = formData.get('price');
        const stock = formData.get('stock');
        
        console.log('Validation - Product Name:', productName);
        console.log('Validation - Price:', price);
        console.log('Validation - Stock:', stock);
        
        if (!productName || productName.trim() === '') {
            showError('Product name is required.');
            return;
        }
        
        if (!price || isNaN(price) || parseFloat(price) <= 0) {
            showError('Valid price is required.');
            return;
        }
        
        if (!stock || isNaN(stock) || parseInt(stock) < 0) {
            showError('Valid stock quantity is required.');
            return;
        }
        
        // Debug: Log final form data
        console.log('Final form data being sent:');
        for (let [key, value] of formData.entries()) {
            console.log('  ', key, '=', value);
        }
        
        $.ajax({
            url: 'database/update-product-simple.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('Raw server response:', response);
                
                // Try to parse JSON response
                let parsedResponse;
                try {
                    parsedResponse = typeof response === 'string' ? JSON.parse(response) : response;
                } catch (e) {
                    console.error('Failed to parse response as JSON:', response);
                    showError('Server response error: ' + response);
                    return;
                }
                
                console.log('Parsed response:', parsedResponse);
                
                if (parsedResponse.success) {
                    showSuccess(parsedResponse.message, function() {
                        $('#editProductModal').modal('hide');
                        location.reload(); // Refresh page to show updated data
                    });
                } else {
                    showError(parsedResponse.message || 'Unknown error occurred');
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error Details:');
                console.log('Status:', status);
                console.log('Error:', error);
                console.log('Response Text:', xhr.responseText);
                console.log('Status Code:', xhr.status);
                
                let errorMessage = 'Error updating product';
                if (xhr.responseText) {
                    errorMessage += ': ' + xhr.responseText;
                } else if (error) {
                    errorMessage += ': ' + error;
                }
                
                showError(errorMessage);
            }
        });
    });
    
    // Handle Product Delete
    $(document).on('click', '.deleteProduct', function(e) {
        e.preventDefault();
        
        if ($(this).hasClass('accessDeniedErr')) {
            return;
        }
        
        const productId = $(this).data('id');
        const productName = $(this).data('name');
        
        // Show confirmation dialog
        showConfirmation(
            `Are you sure you want to delete "${productName}"? This action cannot be undone.`,
            function(confirmed) {
                if (confirmed) {
                    // Send delete request
                    $.ajax({
                        url: 'database/delete.php',
                        method: 'POST',
                        data: {
                            table: 'products',
                            id: productId
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                showSuccess(response.message, function() {
                                    location.reload(); // Refresh page
                                });
                            } else {
                                showError(response.message);
                            }
                        },
                        error: function() {
                            showError('Error deleting product');
                        }
                    });
                }
            }
        );
    });
    
    // Similar handlers for Suppliers
    $(document).on('click', '.updateSupplier', function(e) {
        e.preventDefault();
        
        if ($(this).hasClass('accessDeniedErr')) {
            return;
        }
        
        const supplierId = $(this).data('sid') || $(this).data('id');
        
        $.ajax({
            url: 'database/get-supplier.php',
            method: 'POST',
            data: { id: supplierId },
            dataType: 'json',
            success: function(supplier) {
                const modalHTML = `
                    <div class="modal fade" id="editSupplierModal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Edit Supplier</h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <form id="updateSupplierForm">
                                        <input type="hidden" name="id" value="${supplier.id}">
                                        <input type="hidden" name="table" value="suppliers">
                                        
                                        <div class="appFormInputContainer">
                                            <label for="edit_supplier_name">Supplier Name</label>
                                            <input type="text" class="appFormInput" name="supplier_name" value="${supplier.supplier_name}" required>
                                        </div>
                                        
                                        <div class="appFormInputContainer">
                                            <label for="edit_supplier_location">Location</label>
                                            <input type="text" class="appFormInput" name="supplier_location" value="${supplier.supplier_location}" required>
                                        </div>
                                        
                                        <div class="appFormInputContainer">
                                            <label for="edit_email">Email</label>
                                            <input type="email" class="appFormInput" name="email" value="${supplier.email || ''}" required>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="button" class="btn btn-primary" id="saveSupplierChanges">Save Changes</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                $('#editSupplierModal').remove();
                $('body').append(modalHTML);
                $('#editSupplierModal').modal('show');
            },
            error: function() {
                showError('Error loading supplier data');
            }
        });
    });
    
    // Handle Supplier Update
    $(document).on('click', '#saveSupplierChanges', function() {
        const form = document.getElementById('updateSupplierForm');
        const formData = new FormData(form);
        
        $.ajax({
            url: 'database/update-supplier.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showSuccess(response.message, function() {
                        $('#editSupplierModal').modal('hide');
                        location.reload();
                    });
                } else {
                    showError(response.message);
                }
            },
            error: function() {
                showError('Error updating supplier');
            }
        });
    });
    
    // Handle Supplier Delete
    $(document).on('click', '.deleteSupplier', function(e) {
        e.preventDefault();
        
        if ($(this).hasClass('accessDeniedErr')) {
            return;
        }
        
        const supplierId = $(this).data('sid') || $(this).data('id');
        const supplierName = $(this).data('name');
        
        showConfirmation(
            `Are you sure you want to delete supplier "${supplierName}"?`,
            function(confirmed) {
                if (confirmed) {
                    $.ajax({
                        url: 'database/delete.php',
                        method: 'POST',
                        data: {
                            table: 'suppliers',
                            id: supplierId
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                showSuccess(response.message, function() {
                                    location.reload();
                                });
                            } else {
                                showError(response.message);
                            }
                        }
                    });
                }
            }
        );
    });
});

// Number formatting
function formatNumber(num) {
    return new Intl.NumberFormat().format(num);
}

// Auto-resize textarea
function autoResizeTextarea(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = textarea.scrollHeight + 'px';
}

// Initialize auto-resize for all textareas
document.addEventListener('DOMContentLoaded', function() {
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(function(textarea) {
        textarea.addEventListener('input', function() {
            autoResizeTextarea(this);
        });
    });
});

// Print functionality
function printTable(tableId) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
        <head>
            <title>Print Table</title>
            <style>
                table { border-collapse: collapse; width: 100%; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
                @media print {
                    body { margin: 0; }
                    table { page-break-inside: auto; }
                    tr { page-break-inside: avoid; page-break-after: auto; }
                }
            </style>
        </head>
        <body>
            ${table.outerHTML}
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}
