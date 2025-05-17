<?php
/**
 * Admin Dashboard
 * 
 * This file displays the admin dashboard and provides access to various management functions
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

// Get counts for dashboard
$booking_count = 0;
$menu_count = 0;
$contact_count = 0;

// Count bookings
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM bookings");
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $booking_count = $row['count'];
}

// Count menu items
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM menu_items");
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $menu_count = $row['count'];
}

// Count contact messages
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM contacts");
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $contact_count = $row['count'];
}

// Get recent bookings
$recent_bookings = [];
$result = mysqli_query($conn, "SELECT * FROM bookings ORDER BY created_at DESC LIMIT 5");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $recent_bookings[] = $row;
    }
}

// Get recent contacts
$recent_contacts = [];
$result = mysqli_query($conn, "SELECT * FROM contacts ORDER BY created_at DESC LIMIT 5");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $recent_contacts[] = $row;
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
    <title>Admin Dashboard - Keofi Coffee Shop</title>
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
        
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .card {
            background-color: var(--rich-black-fogra-39);
            border-radius: var(--radius-5);
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .card-title {
            font-family: var(--ff-oswald);
            font-size: 1.6rem;
            color: var(--white);
        }
        
        .card-icon {
            font-size: 30px;
            color: var(--camel);
        }
        
        .card-value {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--white);
        }
        
        .card-link {
            display: block;
            text-align: right;
            margin-top: 10px;
            color: var(--camel);
            text-decoration: none;
        }
        
        .card-link:hover {
            text-decoration: underline;
        }
        
        .recent-section {
            margin-top: 30px;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .section-title {
            font-family: var(--ff-oswald);
            font-size: 1.8rem;
            color: var(--white);
        }
        
        .view-all {
            color: var(--camel);
            text-decoration: none;
        }
        
        .view-all:hover {
            text-decoration: underline;
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
        
        .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.9rem;
            display: inline-block;
        }
        
        .status-pending {
            background-color: rgba(255, 193, 7, 0.2);
            color: #ffc107;
        }
        
        .status-confirmed {
            background-color: rgba(40, 167, 69, 0.2);
            color: #28a745;
        }
        
        .status-cancelled {
            background-color: rgba(220, 53, 69, 0.2);
            color: #dc3545;
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
        
        .empty-message {
            padding: 20px;
            text-align: center;
            color: var(--white_a60);
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
                <a href="admin-dashboard.php" class="menu-item active">
                    <ion-icon name="grid-outline"></ion-icon>
                    Dashboard
                </a>
                <a href="admin-bookings.php" class="menu-item">
                    <ion-icon name="calendar-outline"></ion-icon>
                    Bookings
                </a>
                <a href="admin-menu.php" class="menu-item">
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
                    Dashboard
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
                <h1>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</h1>
                <p>Here's an overview of your coffee shop website.</p>
                
                <!-- Dashboard Cards -->
                <div class="dashboard-cards">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">Bookings</h2>
                            <ion-icon name="calendar-outline" class="card-icon"></ion-icon>
                        </div>
                        <div class="card-value"><?php echo $booking_count; ?></div>
                        <a href="admin-bookings.php" class="card-link">View All</a>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">Menu Items</h2>
                            <ion-icon name="restaurant-outline" class="card-icon"></ion-icon>
                        </div>
                        <div class="card-value"><?php echo $menu_count; ?></div>
                        <a href="admin-menu.php" class="card-link">View All</a>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title">Messages</h2>
                            <ion-icon name="mail-outline" class="card-icon"></ion-icon>
                        </div>
                        <div class="card-value"><?php echo $contact_count; ?></div>
                        <a href="admin-contacts.php" class="card-link">View All</a>
                    </div>
                </div>
                
                <!-- Recent Bookings -->
                <div class="recent-section">
                    <div class="section-header">
                        <h2 class="section-title">Recent Bookings</h2>
                        <a href="admin-bookings.php" class="view-all">View All</a>
                    </div>
                    
                    <div class="card">
                        <div class="table-container">
                            <?php if (empty($recent_bookings)): ?>
                                <div class="empty-message">No bookings found.</div>
                            <?php else: ?>
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Guests</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_bookings as $booking): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($booking['name']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['booking_date']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['booking_time']); ?></td>
                                                <td><?php echo htmlspecialchars($booking['guests']); ?></td>
                                                <td>
                                                    <span class="status status-<?php echo $booking['status']; ?>">
                                                        <?php echo ucfirst($booking['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="admin-booking-view.php?id=<?php echo $booking['id']; ?>" class="action-btn btn-view">View</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Messages -->
                <div class="recent-section">
                    <div class="section-header">
                        <h2 class="section-title">Recent Messages</h2>
                        <a href="admin-contacts.php" class="view-all">View All</a>
                    </div>
                    
                    <div class="card">
                        <div class="table-container">
                            <?php if (empty($recent_contacts)): ?>
                                <div class="empty-message">No messages found.</div>
                            <?php else: ?>
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Subject</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_contacts as $contact): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($contact['name']); ?></td>
                                                <td><?php echo htmlspecialchars($contact['email']); ?></td>
                                                <td><?php echo htmlspecialchars($contact['subject']); ?></td>
                                                <td><?php echo date('M d, Y', strtotime($contact['created_at'])); ?></td>
                                                <td>
                                                    <a href="admin-contact-view.php?id=<?php echo $contact['id']; ?>" class="action-btn btn-view">View</a>
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
    </script>
</body>
</html>
