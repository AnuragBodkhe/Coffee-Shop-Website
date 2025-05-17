<?php
/**
 * Admin Bookings Management
 * 
 * This file allows administrators to manage table bookings
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

// Handle status update action
if (isset($_GET['action']) && $_GET['action'] == 'update' && isset($_GET['id']) && isset($_GET['status'])) {
    $id = intval($_GET['id']);
    $status = $_GET['status'];
    
    // Validate status
    if (in_array($status, ['pending', 'confirmed', 'cancelled'])) {
        $sql = "UPDATE bookings SET status = '$status' WHERE id = $id";
        
        if (mysqli_query($conn, $sql)) {
            $success_message = "Booking status updated successfully!";
        } else {
            $error_message = "Error updating booking status: " . mysqli_error($conn);
        }
    } else {
        $error_message = "Invalid status value.";
    }
}

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $sql = "DELETE FROM bookings WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        $success_message = "Booking deleted successfully!";
    } else {
        $error_message = "Error deleting booking: " . mysqli_error($conn);
    }
}

// Get filter values
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$date_filter = isset($_GET['date']) ? $_GET['date'] : '';

// Build query based on filters
$where_clauses = [];
if ($status_filter != 'all') {
    $where_clauses[] = "status = '$status_filter'";
}
if (!empty($date_filter)) {
    $where_clauses[] = "booking_date = '$date_filter'";
}

$where_sql = empty($where_clauses) ? "" : "WHERE " . implode(" AND ", $where_clauses);

// Get all bookings with filters
$bookings = [];
$sql = "SELECT * FROM bookings $where_sql ORDER BY booking_date DESC, booking_time DESC";
$result = mysqli_query($conn, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $bookings[] = $row;
    }
}

// Get booking counts
$total_count = 0;
$pending_count = 0;
$confirmed_count = 0;
$cancelled_count = 0;

$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM bookings");
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $total_count = $row['count'];
}

$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM bookings WHERE status = 'pending'");
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $pending_count = $row['count'];
}

$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM bookings WHERE status = 'confirmed'");
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $confirmed_count = $row['count'];
}

$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM bookings WHERE status = 'cancelled'");
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $cancelled_count = $row['count'];
}

// Close connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings Management - Keofi Coffee Shop</title>
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
        
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            text-align: center;
            padding: 15px;
            border-radius: var(--radius-5);
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card.all {
            background-color: rgba(108, 117, 125, 0.2);
        }
        
        .stat-card.pending {
            background-color: rgba(255, 193, 7, 0.2);
        }
        
        .stat-card.confirmed {
            background-color: rgba(40, 167, 69, 0.2);
        }
        
        .stat-card.cancelled {
            background-color: rgba(220, 53, 69, 0.2);
        }
        
        .stat-value {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stat-card.all .stat-value {
            color: #6c757d;
        }
        
        .stat-card.pending .stat-value {
            color: #ffc107;
        }
        
        .stat-card.confirmed .stat-value {
            color: #28a745;
        }
        
        .stat-card.cancelled .stat-value {
            color: #dc3545;
        }
        
        .stat-label {
            font-size: 1.2rem;
            color: var(--white);
        }
        
        .filter-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
            align-items: center;
        }
        
        .filter-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .filter-label {
            color: var(--white);
            font-weight: 500;
        }
        
        .filter-control {
            padding: 8px 12px;
            background-color: var(--rich-black-fogra-29);
            border: 1px solid var(--white_a10);
            border-radius: var(--radius-5);
            color: var(--white);
        }
        
        .filter-btn {
            padding: 8px 15px;
            background-color: var(--camel);
            color: var(--white);
            border: none;
            border-radius: var(--radius-5);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .filter-btn:hover {
            background-color: var(--white);
            color: var(--camel);
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
        
        .btn-confirm {
            background-color: #28a745;
        }
        
        .btn-cancel {
            background-color: #dc3545;
        }
        
        .btn-delete {
            background-color: #6c757d;
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
            
            .filter-bar {
                flex-direction: column;
                align-items: flex-start;
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
                <a href="admin-bookings.php" class="menu-item active">
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
                    Bookings Management
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
                    <h1>Table Bookings</h1>
                </div>
                
                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php endif; ?>
                
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>
                
                <!-- Booking Stats -->
                <div class="dashboard-cards">
                    <a href="admin-bookings.php" class="stat-card all">
                        <div class="stat-value"><?php echo $total_count; ?></div>
                        <div class="stat-label">All Bookings</div>
                    </a>
                    
                    <a href="admin-bookings.php?status=pending" class="stat-card pending">
                        <div class="stat-value"><?php echo $pending_count; ?></div>
                        <div class="stat-label">Pending</div>
                    </a>
                    
                    <a href="admin-bookings.php?status=confirmed" class="stat-card confirmed">
                        <div class="stat-value"><?php echo $confirmed_count; ?></div>
                        <div class="stat-label">Confirmed</div>
                    </a>
                    
                    <a href="admin-bookings.php?status=cancelled" class="stat-card cancelled">
                        <div class="stat-value"><?php echo $cancelled_count; ?></div>
                        <div class="stat-label">Cancelled</div>
                    </a>
                </div>
                
                <div class="card">
                    <!-- Filter Bar -->
                    <form action="admin-bookings.php" method="get" class="filter-bar">
                        <div class="filter-group">
                            <label for="status" class="filter-label">Status:</label>
                            <select name="status" id="status" class="filter-control">
                                <option value="all" <?php echo ($status_filter == 'all') ? 'selected' : ''; ?>>All</option>
                                <option value="pending" <?php echo ($status_filter == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="confirmed" <?php echo ($status_filter == 'confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                                <option value="cancelled" <?php echo ($status_filter == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="date" class="filter-label">Date:</label>
                            <input type="date" name="date" id="date" class="filter-control" value="<?php echo $date_filter; ?>">
                        </div>
                        
                        <button type="submit" class="filter-btn">Apply Filters</button>
                        <a href="admin-bookings.php" class="filter-btn">Reset</a>
                    </form>
                    
                    <div class="table-container">
                        <?php if (empty($bookings)): ?>
                            <div class="empty-message">No bookings found matching your criteria.</div>
                        <?php else: ?>
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Guests</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bookings as $booking): ?>
                                        <tr>
                                            <td><?php echo $booking['id']; ?></td>
                                            <td><?php echo htmlspecialchars($booking['name']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['email']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['phone']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['booking_date']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['booking_time']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['guests']); ?></td>
                                            <td>
                                                <span class="status status-<?php echo $booking['status']; ?>">
                                                    <?php echo ucfirst($booking['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="admin-booking-view.php?id=<?php echo $booking['id']; ?>" class="action-btn btn-view">View</a>
                                                    
                                                    <?php if ($booking['status'] == 'pending'): ?>
                                                        <a href="admin-bookings.php?action=update&id=<?php echo $booking['id']; ?>&status=confirmed" class="action-btn btn-confirm" onclick="return confirm('Confirm this booking?')">Confirm</a>
                                                        <a href="admin-bookings.php?action=update&id=<?php echo $booking['id']; ?>&status=cancelled" class="action-btn btn-cancel" onclick="return confirm('Cancel this booking?')">Cancel</a>
                                                    <?php elseif ($booking['status'] == 'confirmed'): ?>
                                                        <a href="admin-bookings.php?action=update&id=<?php echo $booking['id']; ?>&status=cancelled" class="action-btn btn-cancel" onclick="return confirm('Cancel this booking?')">Cancel</a>
                                                    <?php elseif ($booking['status'] == 'cancelled'): ?>
                                                        <a href="admin-bookings.php?action=update&id=<?php echo $booking['id']; ?>&status=confirmed" class="action-btn btn-confirm" onclick="return confirm('Reactivate this booking?')">Reactivate</a>
                                                    <?php endif; ?>
                                                    
                                                    <a href="admin-bookings.php?action=delete&id=<?php echo $booking['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Are you sure you want to delete this booking? This action cannot be undone.')">Delete</a>
                                                </div>
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
    </script>
</body>
</html>
