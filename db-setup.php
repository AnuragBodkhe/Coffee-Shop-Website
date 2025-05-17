<?php
/**
 * Database Setup Script
 * 
 * This file creates the necessary database tables for the Keofi Coffee Shop website
 */

// Include database configuration
require_once 'config.php';

// Initialize response
$response = [
    'success' => true,
    'messages' => []
];

// Function to create tables
function createTables($conn) {
    global $response;
    
    // Create bookings table
    $sql_bookings = "CREATE TABLE IF NOT EXISTS `bookings` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(100) NOT NULL,
        `email` VARCHAR(100) NOT NULL,
        `phone` VARCHAR(20) NOT NULL,
        `booking_date` DATE NOT NULL,
        `booking_time` TIME NOT NULL,
        `guests` INT(11) NOT NULL,
        `message` TEXT,
        `status` ENUM('pending', 'confirmed', 'cancelled') NOT NULL DEFAULT 'pending',
        `created_at` DATETIME NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    if (mysqli_query($conn, $sql_bookings)) {
        $response['messages'][] = "Bookings table created successfully";
    } else {
        $response['success'] = false;
        $response['messages'][] = "Error creating bookings table: " . mysqli_error($conn);
    }
    
    // Create contacts table
    $sql_contacts = "CREATE TABLE IF NOT EXISTS `contacts` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(100) NOT NULL,
        `email` VARCHAR(100) NOT NULL,
        `subject` VARCHAR(255),
        `message` TEXT NOT NULL,
        `created_at` DATETIME NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    if (mysqli_query($conn, $sql_contacts)) {
        $response['messages'][] = "Contacts table created successfully";
    } else {
        $response['success'] = false;
        $response['messages'][] = "Error creating contacts table: " . mysqli_error($conn);
    }
    
    // Create menu_items table
    $sql_menu = "CREATE TABLE IF NOT EXISTS `menu_items` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(100) NOT NULL,
        `description` TEXT,
        `price` DECIMAL(10,2) NOT NULL,
        `category` ENUM('coffee', 'food', 'dessert') NOT NULL DEFAULT 'coffee',
        `image` VARCHAR(255),
        `badge` VARCHAR(50),
        `featured` TINYINT(1) NOT NULL DEFAULT 0,
        `active` TINYINT(1) NOT NULL DEFAULT 1,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    if (mysqli_query($conn, $sql_menu)) {
        $response['messages'][] = "Menu items table created successfully";
    } else {
        $response['success'] = false;
        $response['messages'][] = "Error creating menu items table: " . mysqli_error($conn);
    }
    
    // Create users table for admin access
    $sql_users = "CREATE TABLE IF NOT EXISTS `users` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `username` VARCHAR(50) NOT NULL,
        `password` VARCHAR(255) NOT NULL,
        `email` VARCHAR(100) NOT NULL,
        `role` ENUM('admin', 'staff') NOT NULL DEFAULT 'staff',
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `username` (`username`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    if (mysqli_query($conn, $sql_users)) {
        $response['messages'][] = "Users table created successfully";
    } else {
        $response['success'] = false;
        $response['messages'][] = "Error creating users table: " . mysqli_error($conn);
    }
}

// Function to insert sample data
function insertSampleData($conn) {
    global $response;
    
    // Check if menu_items table already has data
    $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM menu_items");
    $row = mysqli_fetch_assoc($result);
    
    if ($row['count'] > 0) {
        $response['messages'][] = "Sample menu items already exist, skipping insertion";
        return;
    }
    
    // Sample menu items
    $menu_items = [
        [
            'name' => 'Americano Coffee',
            'description' => '2/3 espresso, 1/3 streamed milk',
            'price' => 4.90,
            'category' => 'coffee',
            'image' => 'menu-1.jpg',
            'badge' => 'Hot',
            'featured' => 1
        ],
        [
            'name' => 'Espresso Coffee',
            'description' => 'Barista Pouring Syrup',
            'price' => 3.50,
            'category' => 'coffee',
            'image' => 'menu-2.jpg',
            'badge' => '',
            'featured' => 1
        ],
        [
            'name' => 'Cold Coffee',
            'description' => 'Iced coffee with cream',
            'price' => 6.00,
            'category' => 'coffee',
            'image' => 'menu-3.jpg',
            'badge' => 'Cold',
            'featured' => 1
        ],
        [
            'name' => 'Cappuccino Arabica',
            'description' => 'Espresso with steamed milk',
            'price' => 2.80,
            'category' => 'coffee',
            'image' => 'menu-4.jpg',
            'badge' => '',
            'featured' => 1
        ],
        [
            'name' => 'Milk Cream Coffee',
            'description' => 'Coffee with milk cream',
            'price' => 7.50,
            'category' => 'coffee',
            'image' => 'menu-5.jpg',
            'badge' => 'Hot',
            'featured' => 1
        ],
        [
            'name' => 'Mocha Coffee',
            'description' => 'Espresso with chocolate and milk',
            'price' => 5.20,
            'category' => 'coffee',
            'image' => 'menu-6.jpg',
            'badge' => '',
            'featured' => 1
        ],
        [
            'name' => 'Chocolate Cake',
            'description' => 'Rich chocolate cake with ganache',
            'price' => 6.50,
            'category' => 'dessert',
            'image' => '',
            'badge' => 'New',
            'featured' => 0
        ],
        [
            'name' => 'Croissant',
            'description' => 'Buttery, flaky pastry',
            'price' => 3.20,
            'category' => 'food',
            'image' => '',
            'badge' => '',
            'featured' => 0
        ]
    ];
    
    // Insert menu items
    $success = true;
    foreach ($menu_items as $item) {
        $sql = "INSERT INTO menu_items (name, description, price, category, image, badge, featured, created_at) 
                VALUES (
                    '" . mysqli_real_escape_string($conn, $item['name']) . "',
                    '" . mysqli_real_escape_string($conn, $item['description']) . "',
                    " . (float)$item['price'] . ",
                    '" . mysqli_real_escape_string($conn, $item['category']) . "',
                    '" . mysqli_real_escape_string($conn, $item['image']) . "',
                    '" . mysqli_real_escape_string($conn, $item['badge']) . "',
                    " . (int)$item['featured'] . ",
                    NOW()
                )";
        
        if (!mysqli_query($conn, $sql)) {
            $success = false;
            $response['messages'][] = "Error inserting menu item: " . mysqli_error($conn);
        }
    }
    
    if ($success) {
        $response['messages'][] = "Sample menu items inserted successfully";
    }
    
    // Create default admin user
    $check_admin = mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE username = 'admin'");
    $admin_exists = mysqli_fetch_assoc($check_admin);
    
    if ($admin_exists['count'] == 0) {
        // Create a default admin user (password: admin123)
        $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, password, email, role, created_at) 
                VALUES ('admin', '$hashed_password', 'admin@keofi.com', 'admin', NOW())";
        
        if (mysqli_query($conn, $sql)) {
            $response['messages'][] = "Default admin user created (Username: admin, Password: admin123)";
        } else {
            $response['success'] = false;
            $response['messages'][] = "Error creating admin user: " . mysqli_error($conn);
        }
    } else {
        $response['messages'][] = "Admin user already exists";
    }
}

// Check if the database exists
$db_exists = mysqli_query($conn, "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '" . DB_NAME . "'");

if (!$db_exists) {
    // Create the database
    $sql = "CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
    
    if (mysqli_query($conn, $sql)) {
        $response['messages'][] = "Database created successfully";
        
        // Select the database
        mysqli_select_db($conn, DB_NAME);
    } else {
        $response['success'] = false;
        $response['messages'][] = "Error creating database: " . mysqli_error($conn);
    }
}

// Create tables
createTables($conn);

// Insert sample data
insertSampleData($conn);

// Close connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - Keofi Coffee Shop</title>
    <link rel="shortcut icon" href="./favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="./assets/css/style.css">
    <style>
        .setup-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background-color: var(--rich-black-fogra-39);
            border-radius: var(--radius-5);
            color: var(--white);
        }
        
        .setup-container h1 {
            color: var(--camel);
            margin-bottom: 20px;
            font-family: var(--ff-oswald);
        }
        
        .message-list {
            margin: 20px 0;
            padding: 0;
        }
        
        .message-item {
            padding: 10px;
            margin-bottom: 5px;
            border-radius: var(--radius-5);
            list-style: none;
        }
        
        .message-success {
            background-color: rgba(0, 128, 0, 0.2);
            color: #4caf50;
        }
        
        .message-error {
            background-color: rgba(255, 0, 0, 0.2);
            color: #f44336;
        }
        
        .setup-footer {
            margin-top: 30px;
            display: flex;
            gap: 15px;
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <h1>Keofi Coffee Shop - Database Setup</h1>
        
        <div class="setup-status">
            <h2>Setup <?php echo $response['success'] ? 'Completed' : 'Failed'; ?></h2>
            
            <ul class="message-list">
                <?php foreach ($response['messages'] as $message): ?>
                    <li class="message-item <?php echo $response['success'] ? 'message-success' : 'message-error'; ?>">
                        <?php echo $message; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        
        <div class="setup-footer">
            <a href="index.php" class="btn btn-primary">Go to Homepage</a>
            
            <?php if ($response['success']): ?>
                <a href="admin/login.php" class="btn btn-secondary">Go to Admin Panel</a>
            <?php else: ?>
                <a href="db-setup.php" class="btn btn-secondary">Try Again</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
