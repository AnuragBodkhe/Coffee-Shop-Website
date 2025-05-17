<?php
/**
 * Admin Menu Management
 * 
 * This file allows administrators to manage menu items
 */

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin-login.php");
    exit;
}

// Include database configuration
require_once 'config.php';

// Initialize variables
$success_message = $error_message = "";

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Get image filename before deleting
    $result = mysqli_query($conn, "SELECT image FROM menu_items WHERE id = $id");
    $image = "";
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $image = $row['image'];
    }
    
    // Delete the menu item
    $sql = "DELETE FROM menu_items WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        // Delete image file if it exists
        if (!empty($image) && file_exists("./assets/images/menu/" . $image)) {
            unlink("./assets/images/menu/" . $image);
        }
        $success_message = "Menu item deleted successfully!";
    } else {
        $error_message = "Error deleting menu item: " . mysqli_error($conn);
    }
}

// Get all menu items
$menu_items = [];
$sql = "SELECT * FROM menu_items ORDER BY category, name";
$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $menu_items[] = $row;
    }
}

// Close connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Management - Keofi Coffee Shop</title>
    <link rel="shortcut icon" href="./favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merienda&family=Oswald:wght@300;400;500&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 250px;
            --header-height: 70px;
        }
        
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--rich-black-fogra-39);
            color: var(--white);
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 10;
        }
        
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid var(--white_a10);
        }
        
        .sidebar-header img {
            max-width: 150px;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .menu-item {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            color: var(--white_a60);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .menu-item:hover, .menu-item.active {
            background-color: var(--camel);
            color: var(--white);
        }
        
        .menu-item ion-icon {
            margin-right: 10px;
            font-size: 20px;
        }
        
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            background-color: var(--rich-black-fogra-29);
            color: var(--white);
        }
        
        .header {
            height: var(--header-height);
            background-color: var(--rich-black-fogra-39);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
            position: fixed;
            top: 0;
            right: 0;
            left: var(--sidebar-width);
            z-index: 5;
            border-bottom: 1px solid var(--white_a10);
        }
        
        .header-title {
            font-family: var(--ff-oswald);
            font-size: 1.8rem;
        }
        
        .user-dropdown {
            position: relative;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: var(--radius-5);
            transition: all 0.3s ease;
        }
        
        .user-info:hover {
            background-color: var(--rich-black-fogra-29);
        }
        
        .user-name {
            margin-right: 10px;
        }
        
        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background-color: var(--rich-black-fogra-39);
            border-radius: var(--radius-5);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            min-width: 150px;
            display: none;
            z-index: 10;
        }
        
        .dropdown-menu.show {
            display: block;
        }
        
        .dropdown-item {
            padding: 10px 15px;
            display: block;
            color: var(--white);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .dropdown-item:hover {
            background-color: var(--camel);
        }
        
        .content {
            padding: 90px 20px 20px;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: var(--radius-5);
        }
        
        .alert-success {
            background-color: rgba(40, 167, 69, 0.2);
            color: #28a745;
        }
        
        .alert-danger {
            background-color: rgba(220, 53, 69, 0.2);
            color: #dc3545;
        }
        
        .card {
            background-color: var(--rich-black-fogra-39);
            border-radius: var(--radius-5);
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        
        .table-container {
            overflow-x: auto;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table th, .data-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--white_a10);
        }
        
        .data-table th {
            background-color: var(--rich-black-fogra-39);
            color: var(--camel);
            font-weight: 500;
        }
        
        .data-table tbody tr {
            transition: all 0.3s ease;
        }
        
        .data-table tbody tr:hover {
            background-color: var(--rich-black-fogra-39);
        }
        
        .badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.9rem;
            display: inline-block;
        }
        
        .badge-coffee {
            background-color: rgba(255, 193, 7, 0.2);
            color: #ffc107;
        }
        
        .badge-food {
            background-color: rgba(40, 167, 69, 0.2);
            color: #28a745;
        }
        
        .badge-dessert {
            background-color: rgba(111, 66, 193, 0.2);
            color: #6f42c1;
        }
        
        .featured-badge {
            background-color: rgba(23, 162, 184, 0.2);
            color: #17a2b8;
        }
        
        .action-btn {
            padding: 5px 10px;
            border-radius: var(--radius-5);
            color: var(--white);
            text-decoration: none;
            margin-right: 5px;
            font-size: 0.9rem;
            display: inline-block;
        }
        
        .btn-view {
            background-color: var(--camel);
        }
        
        .btn-edit {
            background-color: #17a2b8;
        }
        
        .btn-delete {
            background-color: #dc3545;
        }
        
        .menu-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }
        
        .empty-message {
            padding: 20px;
            text-align: center;
            color: var(--white_a60);
        }
        
        .filter-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .filter-btn {
            padding: 8px 15px;
            border-radius: var(--radius-5);
            background-color: var(--rich-black-fogra-39);
            color: var(--white);
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .filter-btn.active, .filter-btn:hover {
            background-color: var(--camel);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                transition: all 0.3s ease;
            }
            
            .sidebar.show {
                width: var(--sidebar-width);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .header {
                left: 0;
            }
            
            .toggle-sidebar {
                display: block;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <img src="./assets/images/logo.svg" alt="Keofi Coffee Shop">
            </div>
            
            <div class="sidebar-menu">
                <a href="admin-dashboard.php" class="menu-item">
                    <ion-icon name="grid-outline"></ion-icon>
                    Dashboard
                </a>
                <a href="admin-bookings.php" class="menu-item">
                    <ion-icon name="calendar-outline"></ion-icon>
                    Bookings
                </a>
                <a href="admin-menu.php" class="menu-item active">
                    <ion-icon name="restaurant-outline"></ion-icon>
                    Menu Items
                </a>
                <a href="admin-contacts.php" class="menu-item">
                    <ion-icon name="mail-outline"></ion-icon>
                    Contact Messages
                </a>
                <a href="admin-users.php" class="menu-item">
                    <ion-icon name="people-outline"></ion-icon>
                    Users
                </a>
                <a href="admin-settings.php" class="menu-item">
                    <ion-icon name="settings-outline"></ion-icon>
                    Settings
                </a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="header-title">
                    Menu Management
                </div>
                
                <div class="user-dropdown">
                    <div class="user-info" id="userDropdown">
                        <span class="user-name"><?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                        <ion-icon name="chevron-down-outline"></ion-icon>
                    </div>
                    
                    <div class="dropdown-menu" id="dropdownMenu">
                        <a href="admin-profile.php" class="dropdown-item">Profile</a>
                        <a href="admin-logout.php" class="dropdown-item">Logout</a>
                    </div>
                </div>
            </div>
            
            <!-- Content -->
            <div class="content">
                <div class="page-header">
                    <h1>Menu Items</h1>
                    <a href="admin-menu-add.php" class="btn btn-primary">Add New Item</a>
                </div>
                
                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php endif; ?>
                
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="filter-bar">
                        <button class="filter-btn active" data-filter="all">All</button>
                        <button class="filter-btn" data-filter="coffee">Coffee</button>
                        <button class="filter-btn" data-filter="food">Food</button>
                        <button class="filter-btn" data-filter="dessert">Dessert</button>
                        <button class="filter-btn" data-filter="featured">Featured</button>
                    </div>
                    
                    <div class="table-container">
                        <?php if (empty($menu_items)): ?>
                            <div class="empty-message">No menu items found. <a href="admin-menu-add.php">Add your first menu item</a>.</div>
                        <?php else: ?>
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Featured</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($menu_items as $item): ?>
                                        <tr class="menu-item-row" data-category="<?php echo $item['category']; ?>" data-featured="<?php echo $item['featured']; ?>">
                                            <td>
                                                <?php if (!empty($item['image'])): ?>
                                                    <img src="./assets/images/menu/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="menu-image">
                                                <?php else: ?>
                                                    <div style="width: 60px; height: 60px; background-color: #444; border-radius: 5px; display: flex; align-items: center; justify-content: center;">
                                                        <ion-icon name="image-outline" style="font-size: 24px;"></ion-icon>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                                            <td>
                                                <span class="badge badge-<?php echo $item['category']; ?>">
                                                    <?php echo ucfirst($item['category']); ?>
                                                </span>
                                            </td>
                                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                                            <td>
                                                <?php if ($item['featured']): ?>
                                                    <span class="badge featured-badge">Featured</span>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="admin-menu-edit.php?id=<?php echo $item['id']; ?>" class="action-btn btn-edit">Edit</a>
                                                <a href="admin-menu.php?action=delete&id=<?php echo $item['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Are you sure you want to delete this item?')">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    
    <script>
        // User dropdown toggle
        const userDropdown = document.getElementById('userDropdown');
        const dropdownMenu = document.getElementById('dropdownMenu');
        
        userDropdown.addEventListener('click', function() {
            dropdownMenu.classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        window.addEventListener('click', function(event) {
            if (!event.target.matches('#userDropdown') && !event.target.closest('#userDropdown')) {
                if (dropdownMenu.classList.contains('show')) {
                    dropdownMenu.classList.remove('show');
                }
            }
        });
        
        // Filter functionality
        const filterButtons = document.querySelectorAll('.filter-btn');
        const menuItems = document.querySelectorAll('.menu-item-row');
        
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons
                filterButtons.forEach(btn => btn.classList.remove('active'));
                
                // Add active class to clicked button
                this.classList.add('active');
                
                const filter = this.getAttribute('data-filter');
                
                menuItems.forEach(item => {
                    if (filter === 'all') {
                        item.style.display = '';
                    } else if (filter === 'featured') {
                        if (item.getAttribute('data-featured') === '1') {
                            item.style.display = '';
                        } else {
                            item.style.display = 'none';
                        }
                    } else {
                        if (item.getAttribute('data-category') === filter) {
                            item.style.display = '';
                        } else {
                            item.style.display = 'none';
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>
