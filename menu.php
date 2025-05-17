<?php
/**
 * Menu Handler
 * 
 * This file retrieves and displays menu items from the database
 */

// Include database configuration
require_once 'config.php';

// Function to get menu items by category
function getMenuItems($category = 'coffee') {
    global $conn;
    
    $category = mysqli_real_escape_string($conn, $category);
    $sql = "SELECT * FROM menu_items WHERE category = '$category' AND active = 1 ORDER BY price ASC";
    $result = mysqli_query($conn, $sql);
    
    $items = [];
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $items[] = $row;
        }
    }
    
    return $items;
}

// Get menu category from request or default to coffee
$category = isset($_GET['category']) ? $_GET['category'] : 'coffee';

// Get menu items
$menuItems = getMenuItems($category);

// Return JSON if requested
if (isset($_GET['format']) && $_GET['format'] == 'json') {
    header('Content-Type: application/json');
    echo json_encode($menuItems);
    exit;
}

// Otherwise, include the menu items in the page
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keofi - Menu</title>
    <link rel="shortcut icon" href="./favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merienda&family=Oswald:wght@300;400;500&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <main>
        <section class="section menu has-bg-image" id="menu" aria-labelledby="menu-label"
            style="background-image: url('./assets/images/menu-bg.jpg')">
            <div class="container">
                <h2 class="section-title text-center" id="menu-label">
                    Choose Best Coffee
                </h2>
                
                <p class="section-text text-center">
                    Keofi Popular Coffee Menu
                </p>
                
                <div class="menu-category-buttons">
                    <a href="?category=coffee" class="btn <?php echo $category == 'coffee' ? 'btn-primary' : 'btn-secondary'; ?>">Coffee</a>
                    <a href="?category=food" class="btn <?php echo $category == 'food' ? 'btn-primary' : 'btn-secondary'; ?>">Food</a>
                    <a href="?category=dessert" class="btn <?php echo $category == 'dessert' ? 'btn-primary' : 'btn-secondary'; ?>">Desserts</a>
                </div>
                
                <div class="grid-list">
                    <?php if (empty($menuItems)): ?>
                        <p class="text-center">No menu items found in this category.</p>
                    <?php else: ?>
                        <?php foreach ($menuItems as $item): ?>
                            <div class="menu-card">
                                <?php if (!empty($item['image'])): ?>
                                <figure class="card-banner img-holder" style="--width: 100; --height: 100;">
                                    <img src="./assets/images/menu/<?php echo htmlspecialchars($item['image']); ?>" width="100" height="100" loading="lazy" alt="<?php echo htmlspecialchars($item['name']); ?>" class="img-cover">
                                </figure>
                                <?php endif; ?>
                                
                                <div>
                                    <div class="title-wrapper">
                                        <h3 class="title-3">
                                            <a href="#" class="card-title"><?php echo htmlspecialchars($item['name']); ?></a>
                                        </h3>
                                        
                                        <span class="badge"><?php echo htmlspecialchars($item['badge']); ?></span>
                                        
                                        <span class="span title-2">$<?php echo number_format($item['price'], 2); ?></span>
                                    </div>
                                    
                                    <p class="card-text">
                                        <?php echo htmlspecialchars($item['description']); ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>
    
    <?php include 'footer.php'; ?>
    
    <script src="./assets/js/script.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</body>
</html>
