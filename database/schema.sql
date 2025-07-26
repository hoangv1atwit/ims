-- PostgreSQL Schema for Inventory Management System

-- Users table
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    permissions TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Suppliers table
CREATE TABLE suppliers (
    id SERIAL PRIMARY KEY,
    supplier_name VARCHAR(255) NOT NULL,
    supplier_location VARCHAR(255),
    email VARCHAR(255),
    phone VARCHAR(20),
    created_by INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Products table
CREATE TABLE products (
    id SERIAL PRIMARY KEY,
    product_name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INTEGER DEFAULT 0,
    category VARCHAR(100),
    size VARCHAR(50),
    color VARCHAR(50),
    brand VARCHAR(100),
    img VARCHAR(255),
    created_by INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Product-Supplier relationship table
CREATE TABLE productsuppliers (
    id SERIAL PRIMARY KEY,
    product INTEGER,
    supplier INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (supplier) REFERENCES suppliers(id) ON DELETE CASCADE
);

-- Purchase Orders table
CREATE TABLE order_product (
    id SERIAL PRIMARY KEY,
    product INTEGER NOT NULL,
    supplier INTEGER NOT NULL,
    quantity_ordered INTEGER NOT NULL,
    quantity_received INTEGER DEFAULT 0,
    batch VARCHAR(50),
    status VARCHAR(20) DEFAULT 'pending' CHECK (status IN ('pending', 'incomplete', 'complete')),
    requested_by VARCHAR(255),
    created_by INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product) REFERENCES products(id),
    FOREIGN KEY (supplier) REFERENCES suppliers(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Delivery History table
CREATE TABLE delivery_history (
    id SERIAL PRIMARY KEY,
    order_product_id INTEGER,
    qty_received INTEGER NOT NULL,
    date_received TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes TEXT,
    FOREIGN KEY (order_product_id) REFERENCES order_product(id)
);

-- Requests table
CREATE TABLE requests (
    id SERIAL PRIMARY KEY,
    requested_by VARCHAR(150),
    product_id INTEGER NOT NULL,
    quantity INTEGER,
    date DATE,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Insert default admin user
INSERT INTO users (first_name, last_name, email, password, permissions) 
VALUES (
    'Admin', 
    'User', 
    'admin@inventory.com', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
    'dashboard_view,report_view,po_view,po_create,po_edit,product_view,product_create,product_edit,product_delete,supplier_view,supplier_create,supplier_edit,supplier_delete,user_view,user_create,user_edit,user_delete,pos'
);

-- Insert sample data
INSERT INTO suppliers (supplier_name, supplier_location, email, phone, created_by) VALUES
('Fashion Hub Ltd', 'New York, NY', 'contact@fashionhub.com', '555-0101', 1),
('Textile World Co', 'Los Angeles, CA', 'orders@textileworld.com', '555-0102', 1),
('Apparel Solutions', 'Chicago, IL', 'info@apparelsolutions.com', '555-0103', 1);

INSERT INTO products (product_name, description, price, stock, category, size, color, brand, created_by) VALUES
('Cotton T-Shirt', 'Premium quality cotton t-shirt', 19.99, 50, 'Tops', 'M', 'Blue', 'BasicWear', 1),
('Denim Jeans', 'Classic fit denim jeans', 49.99, 30, 'Bottoms', 'L', 'Blue', 'DenimCo', 1),
('Summer Dress', 'Floral print summer dress', 39.99, 25, 'Dresses', 'S', 'Red', 'SummerStyle', 1),
('Leather Jacket', 'Genuine leather jacket', 199.99, 10, 'Outerwear', 'L', 'Black', 'LeatherPro', 1),
('Sneakers', 'Comfortable running sneakers', 79.99, 40, 'Footwear', '9', 'White', 'RunFast', 1);

-- Link products to suppliers
INSERT INTO productsuppliers (product, supplier) VALUES
(1, 1), (1, 2), (2, 1), (3, 2), (4, 3), (5, 1);
