# Inventory Management System

A comprehensive web-based inventory management system for clothing stores with multi-language support.

## Features

- **Product Management**: Add, edit, delete, and view products with image support
- **Supplier Management**: Manage supplier information and relationships
- **User Management**: Role-based permissions and user authentication
- **Multi-language Support**: English, Somali, Vietnamese, Chinese with Google Translate API
- **Dashboard**: Real-time inventory statistics and low stock alerts
- **Reports**: Generate CSV and PDF reports
- **Responsive Design**: Works on desktop and mobile devices

## Technologies Used

- **Backend**: PHP 7.4+
- **Database**: MySQL/PostgreSQL
- **Frontend**: HTML5, CSS3, JavaScript (jQuery)
- **UI Framework**: Bootstrap 3
- **Translation**: Google Translate API integration

## Installation

### Requirements
- PHP 7.4 or higher
- MySQL 5.7+ or PostgreSQL 10+
- Web server (Apache/Nginx)
- Google Translate API key (optional, for translations)

### Setup Instructions

1. **Database Setup**
   ```sql
   -- Import the database schema
   mysql -u your_username -p your_database < database/schema.sql
   ```

2. **Configuration**
   - Copy `database/connection.php.example` to `database/connection.php`
   - Update database credentials in `database/connection.php`
   - Set up Google Translate API key in `translate-text.php` (if using translation features)

3. **File Permissions**
   ```bash
   chmod 755 uploads/
   chmod 644 uploads/*
   ```

4. **Default Login**
   - Username: admin
   - Password: admin123
   (Change these after first login)

## File Structure

```
inventory_system/
├── css/                    # Stylesheets
│   ├── style.css          # Main styles
│   └── login.css          # Login page styles
├── js/                     # JavaScript files
│   ├── script.js          # Main application scripts
│   └── translator.js      # Translation functionality
├── database/              # Database scripts
│   ├── connection.php     # Database connection
│   ├── schema.sql         # Database schema
│   ├── *.php             # CRUD operations
├── partials/              # Reusable components
│   ├── app-header-scripts.php
│   ├── app-sidebar.php
│   └── app-topnav.php
├── uploads/               # Product images
├── *.php                 # Main application pages
└── README.md             # This file
```

## Key Features Explained

### Multi-language Support
- Automatic translation using Google Translate API
- Preserves functionality while translating content
- Protected action buttons and data from translation interference

### CRUD Operations
- **Products**: Full product lifecycle management
- **Suppliers**: Supplier information and product relationships
- **Users**: User management with role-based permissions

### Security Features
- Session-based authentication
- SQL injection protection with prepared statements
- XSS prevention with output escaping
- Role-based access control

## Usage

1. **Login**: Access the system using your credentials
2. **Dashboard**: View inventory overview and statistics
3. **Products**: Manage your product inventory
4. **Suppliers**: Add and manage supplier information
5. **Users**: Create and manage user accounts (admin only)
6. **Reports**: Generate inventory reports
7. **Language**: Switch between supported languages using the dropdown

## API Endpoints

The system includes RESTful endpoints for:
- Product CRUD operations
- Supplier management
- User management
- Report generation

## License

This project is licensed under the MIT License.

## Support

For support and questions, please contact the development team.
