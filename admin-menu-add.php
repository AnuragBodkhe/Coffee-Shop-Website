<?php
/**
 * Admin Add Menu Item
 * 
 * This file allows administrators to add new menu items
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
$name = $description = $price = $category = $badge = "";
$featured = 0;
$name_err = $price_err = $category_err = $image_err = "";
$success_message = $error_message = "";

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validate name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter the menu item name.";
    } else {
        $name = trim($_POST["name"]);
    }
    
    // Get description (optional)
    $description = trim($_POST["description"]);
    
    // Validate price
    if (empty(trim($_POST["price"]))) {
        $price_err = "Please enter the price.";
    } elseif (!is_numeric($_POST["price"]) || floatval($_POST["price"]) <= 0) {
        $price_err = "Please enter a valid price.";
    } else {
        $price = floatval($_POST["price"]);
    }
    
    // Validate category
    if (empty($_POST["category"])) {
        $category_err = "Please select a category.";
    } else {
        $category = $_POST["category"];
    }
    
    // Get badge (optional)
    $badge = trim($_POST["badge"]);
    
    // Get featured status
    $featured = isset($_POST["featured"]) ? 1 : 0;
    
    // Process image upload if present
    $image_name = "";
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $allowed_types = ["image/jpeg", "image/jpg", "image/png", "image/gif"];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        if (!in_array($_FILES["image"]["type"], $allowed_types)) {
            $image_err = "Only JPG, JPEG, PNG, and GIF files are allowed.";
        } elseif ($_FILES["image"]["size"] > $max_size) {
            $image_err = "Image size should not exceed 2MB.";
        } else {
            // Generate unique filename
            $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
            $image_name = uniqid() . "." . $file_extension;
            $upload_dir = "./assets/images/menu/";
            
            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $upload_path = $upload_dir . $image_name;
            
            // Move uploaded file
            if (!move_uploaded_file($_FILES["image"]["tmp_name"], $upload_path)) {
                $image_err = "Failed to upload image. Please try again.";
                $image_name = "";
            }
        }
    }
    
    // Check for errors before inserting into database
    if (empty($name_err) && empty($price_err) && empty($category_err) && empty($image_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO menu_items (name, description, price, category, image, badge, featured, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssdssis", $name, $description, $price, $category, $image_name, $badge, $featured);
            
            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                $success_message = "Menu item added successfully!";
                
                // Clear form data
                $name = $description = $price = $category = $badge = "";
                $featured = 0;
            } else {
                $error_message = "Something went wrong. Please try again later.";
            }
            
            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Menu Item - Keofi Coffee Shop</title>
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
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            color: var(--white);
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            background-color: var(--rich-black-fogra-29);
            border: 1px solid var(--white_a10);
            border-radius: 5px;
            color: var(--white);
            font-family: var(--ff-roboto);
        }
        
        .form-control:focus {
            border-color: var(--camel);
            outline: none;
        }
        
        .invalid-feedback {
            color: #f44336;
            font-size: 14px;
            margin-top: 5px;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
        }
        
        .checkbox-group input[type="checkbox"] {
            margin-right: 10px;
        }
        
        .form-text {
            font-size: 14px;
            color: var(--white_a60);
            margin-top: 5px;
        }
        
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }
        
        .preview-image {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
            border-radius: 5px;
            display: none;
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
                    Add Menu Item
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
                    <h1>Add New Menu Item</h1>
                    <a href="admin-menu.php" class="btn btn-secondary">Back to Menu List</a>
                </div>
                
                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php endif; ?>
                
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>
                
                <div class="card">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="name">Item Name *</label>
                            <input type="text" name="name" id="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>" required>
                            <?php if (!empty($name_err)): ?>
                                <span class="invalid-feedback"><?php echo $name_err; ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="3"><?php echo $description; ?></textarea>
                            <div class="form-text">Brief description of the menu item.</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="price">Price ($) *</label>
                            <input type="number" name="price" id="price" class="form-control <?php echo (!empty($price_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $price; ?>" step="0.01" min="0.01" required>
                            <?php if (!empty($price_err)): ?>
                                <span class="invalid-feedback"><?php echo $price_err; ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="category">Category *</label>
                            <select name="category" id="category" class="form-control <?php echo (!empty($category_err)) ? 'is-invalid' : ''; ?>" required>
                                <option value="">Select Category</option>
                                <option value="coffee" <?php echo ($category == "coffee") ? "selected" : ""; ?>>Coffee</option>
                                <option value="food" <?php echo ($category == "food") ? "selected" : ""; ?>>Food</option>
                                <option value="dessert" <?php echo ($category == "dessert") ? "selected" : ""; ?>>Dessert</option>
                            </select>
                            <?php if (!empty($category_err)): ?>
                                <span class="invalid-feedback"><?php echo $category_err; ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="image">Image</label>
                            <input type="file" name="image" id="image" class="form-control <?php echo (!empty($image_err)) ? 'is-invalid' : ''; ?>" accept="image/*" onchange="previewImage(this)">
                            <?php if (!empty($image_err)): ?>
                                <span class="invalid-feedback"><?php echo $image_err; ?></span>
                            <?php endif; ?>
                            <div class="form-text">Recommended size: 300x300 pixels. Max file size: 2MB.</div>
                            <img id="preview" class="preview-image" alt="Image Preview">
                        </div>
                        
                        <div class="form-group">
                            <label for="badge">Badge</label>
                            <input type="text" name="badge" id="badge" class="form-control" value="<?php echo $badge; ?>">
                            <div class="form-text">Optional badge to display (e.g., "Hot", "New", "Spicy").</div>
                        </div>
                        
                        <div class="form-group">
                            <div class="checkbox-group">
                                <input type="checkbox" name="featured" id="featured" <?php echo ($featured) ? "checked" : ""; ?>>
                                <label for="featured">Feature this item on the homepage</label>
                            </div>
                        </div>
                        
                        <div class="btn-group">
                            <button type="submit" class="btn btn-primary">Add Menu Item</button>
                            <a href="admin-menu.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
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
        
        // Image preview functionality
        function previewImage(input) {
            const preview = document.getElementById('preview');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.src = '';
                preview.style.display = 'none';
            }
        }
    </script>
</body>
</html>
