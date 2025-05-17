<?php
/**
 * Keofi Coffee Shop - Home Page
 * 
 * This is the main entry point for the Keofi Coffee Shop website
 */

// Include database configuration
require_once 'config.php';

// Function to get featured menu items
function getFeaturedMenuItems($limit = 6) {
    global $conn;
    
    $sql = "SELECT * FROM menu_items WHERE featured = 1 AND active = 1 ORDER BY id DESC LIMIT $limit";
    $result = mysqli_query($conn, $sql);
    
    $items = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $items[] = $row;
        }
    }
    
    return $items;
}

// Try to get featured menu items, but don't fail if table doesn't exist yet
try {
    $featuredItems = getFeaturedMenuItems();
} catch (Exception $e) {
    $featuredItems = [];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- 
    - primary meta tags
  -->
  <title>Keofi - Great coffee good vibes</title>
  <meta name="title" content="Keofi - Great coffee good vibes">
  <meta name="description" content="This is a coffee shop html template made by codewithsadee">

  <!-- 
    - favicon
  -->
  <link rel="shortcut icon" href="./favicon.svg" type="image/svg+xml">

  <!-- 
    - custom css link
  -->
  <link rel="stylesheet" href="./assets/css/style.css">

  <!-- 
    - google font link
  -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Merienda&family=Oswald:wght@300;400;500&family=Roboto:wght@400;500;700&display=swap"
    rel="stylesheet">

  <!-- 
    - preload images
  -->
  <link rel="preload" as="image" href="./assets/images/hero-banner.jpg">

  <!-- 
    - custom styles for image fixes and booking form
  -->
  <style>
    .img-holder {
      position: relative;
      aspect-ratio: var(--width) / var(--height);
      background-color: var(--light-gray);
      overflow: hidden;
      border-radius: var(--radius-circle);
    }
    
    .img-cover {
      width: 100%;
      height: 100%;
      object-fit: cover;
      object-position: center;
      position: absolute;
      top: 0;
      left: 0;
    }
    
    .about-card {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    
    .about-card .card-banner {
      flex-shrink: 0;
    }

    /* Booking Modal Styles */
    .booking-modal {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.8);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 1000;
      opacity: 0;
      visibility: hidden;
      transition: all 0.3s ease;
    }

    .booking-modal.active {
      opacity: 1;
      visibility: visible;
    }

    .booking-form {
      background-color: var(--rich-black-fogra-39);
      border: 2px solid var(--camel);
      border-radius: 10px;
      padding: 30px;
      width: 90%;
      max-width: 500px;
      position: relative;
      transform: translateY(-50px);
      transition: all 0.4s ease;
    }

    .booking-modal.active .booking-form {
      transform: translateY(0);
    }

    .booking-form h2 {
      color: var(--white);
      font-family: var(--ff-oswald);
      margin-bottom: 20px;
      text-align: center;
      font-size: 2.4rem;
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

    .booking-form .btn {
      width: 100%;
      justify-content: center;
      margin-top: 10px;
      font-size: 1.6rem;
      padding: 15px;
    }

    .close-modal {
      position: absolute;
      top: 15px;
      right: 15px;
      background: none;
      border: none;
      font-size: 24px;
      color: var(--white);
      cursor: pointer;
    }

    .form-message {
      text-align: center;
      margin-top: 15px;
      padding: 10px;
      border-radius: 5px;
      display: none;
    }

    .form-message.success {
      background-color: rgba(0, 128, 0, 0.2);
      color: #4caf50;
      display: block;
    }

    .form-message.error {
      background-color: rgba(255, 0, 0, 0.2);
      color: #f44336;
      display: block;
    }
    
    /* Developer Modal Styles */
    .developer-modal {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.8);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 1000;
      opacity: 0;
      visibility: hidden;
      transition: all 0.3s ease;
    }
    
    .developer-modal.active {
      opacity: 1;
      visibility: visible;
    }
    
    .developer-options {
      background-color: var(--rich-black-fogra-39);
      border: 2px solid var(--camel);
      border-radius: 10px;
      padding: 30px;
      width: 90%;
      max-width: 500px;
      position: relative;
      transform: translateY(-50px);
      transition: all 0.4s ease;
    }
    
    .developer-modal.active .developer-options {
      transform: translateY(0);
    }
  </style>
</head>

<body>
  <?php include 'header.php'; ?>

  <main>
    <article>
      <!-- 
        - #HERO
      -->
      <section class="section hero has-bg-image" id="home" aria-labelledby="hero-label"
        style="background-image: url('./assets/images/hero-banner.jpg')">
        <div class="container">
          <h1 class="h1 hero-title" id="hero-label" data-reveal>
            Welcome to the keofi
          </h1>

          <div class="wrapper" data-reveal>
            <h2 class="h2 section-title">
              Great Coffee Good Vibes
            </h2>

            <a href="#menu" class="btn btn-primary">
              <span class="span">Explore more</span>

              <ion-icon name="chevron-forward" aria-hidden="true"></ion-icon>
            </a>

            <a href="#" class="btn btn-secondary">
              <span class="span">Get delivery</span>

              <ion-icon name="chevron-forward" aria-hidden="true"></ion-icon>
            </a>
          </div>
        </div>
      </section>

      <!-- 
        - #ABOUT
      -->
      <section class="section about" id="about" aria-labelledby="about-label">
        <div class="container">
          <figure class="about-banner" data-reveal="left">
            <img src="./assets/images/about-banner.png" width="680" height="700" loading="lazy" alt="about banner"
              class="w-100">
          </figure>

          <div class="about-content" data-reveal="right">
            <p class="section-subtitle" id="about-label">About Us</p>

            <h2 class="section-title">Organic & Fresh Coffee Provider Center</h2>

            <p class="section-text">
              Sed ut perspiciatis unde omnis iste natus error voluptate accusantium doloremque laudantium, totam rem
              aperiam eaque ipsa quae abillo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.
              Nemo enim ipsluptatem quia voluptas sit aspernatur aut odit aut fugit sed quia consequuntur magni dolores
              eos qui ratione
            </p>

            <div class="about-card">
              <figure class="card-banner">
                <img src="./assets/images/about-img.png" width="100" height="100" loading="lazy" alt="john doe">
              </figure>

              <div class="card-content">
                <h3 class="h3 card-title">Quis autem vel eum iure reprehenderit in ealuptate velit esse molestiae</h3>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- 
        - #SERVICE
      -->
      <section class="section service" aria-label="service">
        <div class="container">
          <div class="service-list">
            <div class="service-item" data-reveal>
              <div class="service-card">
                <div class="card-icon">
                  <img src="./assets/images/services/service-1.jpg" width="300" height="300" loading="lazy"
                    alt="Restaurant Menu" class="w-100">
                </div>

                <div class="card-content">
                  <ion-icon name="restaurant-outline" aria-hidden="true"></ion-icon>

                  <h3 class="h3 card-title">Restaurant Menu</h3>
                </div>
              </div>
            </div>

            <div class="service-item" data-reveal>
              <div class="service-card">
                <div class="card-icon">
                  <img src="./assets/images/services/service-2.jpg" width="300" height="300" loading="lazy"
                    alt="Coffee Menu" class="w-100">
                </div>

                <div class="card-content">
                  <ion-icon name="cafe-outline" aria-hidden="true"></ion-icon>

                  <h3 class="h3 card-title">Coffee Menu</h3>
                </div>
              </div>
            </div>

            <div class="service-item" data-reveal>
              <div class="service-card">
                <div class="card-icon">
                  <img src="./assets/images/services/service-3.jpg" width="300" height="300" loading="lazy"
                    alt="Food Services" class="w-100">
                </div>

                <div class="card-content">
                  <ion-icon name="wine-outline" aria-hidden="true"></ion-icon>

                  <h3 class="h3 card-title">Food Services</h3>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- 
        - #MENU
      -->
      <section class="section menu has-bg-image" id="menu" aria-labelledby="menu-label"
        style="background-image: url('./assets/images/menu-bg.jpg')">
        <div class="container">
          <p class="section-subtitle text-center" id="menu-label">Choose Best Coffee</p>

          <h2 class="section-title text-center">
            Keofi Popular Coffee Menu
          </h2>

          <div class="menu-tab-container">
            <a href="menu.php?category=coffee" class="btn btn-primary">View Full Menu</a>
          </div>

          <div class="grid-list">
            <?php if (empty($featuredItems)): ?>
              <!-- Fallback static menu items if database is not set up yet -->
              <div class="menu-card">
                <figure class="card-banner img-holder" style="--width: 100; --height: 100;">
                  <img src="./assets/images/menu/menu-1.jpg" width="100" height="100" loading="lazy" alt="Americano Coffee" class="img-cover">
                </figure>

                <div>
                  <div class="title-wrapper">
                    <h3 class="title-3">
                      <a href="#" class="card-title">Americano Coffee</a>
                    </h3>

                    <span class="badge">Hot</span>

                    <span class="span title-2">$4.9</span>
                  </div>

                  <p class="card-text">
                    2/3 espresso, 1/3 streamed milk
                  </p>
                </div>
              </div>

              <div class="menu-card">
                <figure class="card-banner img-holder" style="--width: 100; --height: 100;">
                  <img src="./assets/images/menu/menu-2.jpg" width="100" height="100" loading="lazy" alt="Espresso Coffee" class="img-cover">
                </figure>

                <div>
                  <div class="title-wrapper">
                    <h3 class="title-3">
                      <a href="#" class="card-title">Espresso Coffee</a>
                    </h3>

                    <span class="span title-2">$3.5</span>
                  </div>

                  <p class="card-text">
                    Barista Pouring Syrup
                  </p>
                </div>
              </div>

              <div class="menu-card">
                <figure class="card-banner img-holder" style="--width: 100; --height: 100;">
                  <img src="./assets/images/menu/menu-3.jpg" width="100" height="100" loading="lazy" alt="Cold - Coffee" class="img-cover">
                </figure>

                <div>
                  <div class="title-wrapper">
                    <h3 class="title-3">
                      <a href="#" class="card-title">Cold - Coffee</a>
                    </h3>

                    <span class="badge">Cold</span>

                    <span class="span title-2">$6.0</span>
                  </div>

                  <p class="card-text">
                    Iced coffee with cream
                  </p>
                </div>
              </div>

              <div class="menu-card">
                <figure class="card-banner img-holder" style="--width: 100; --height: 100;">
                  <img src="./assets/images/menu/menu-4.jpg" width="100" height="100" loading="lazy" alt="Cappuccino Arabica" class="img-cover">
                </figure>

                <div>
                  <div class="title-wrapper">
                    <h3 class="title-3">
                      <a href="#" class="card-title">Cappuccino Arabica</a>
                    </h3>

                    <span class="span title-2">$2.8</span>
                  </div>

                  <p class="card-text">
                    Espresso with steamed milk
                  </p>
                </div>
              </div>

              <div class="menu-card">
                <figure class="card-banner img-holder" style="--width: 100; --height: 100;">
                  <img src="./assets/images/menu/menu-5.jpg" width="100" height="100" loading="lazy" alt="Milk Cream Coffee" class="img-cover">
                </figure>

                <div>
                  <div class="title-wrapper">
                    <h3 class="title-3">
                      <a href="#" class="card-title">Milk Cream Coffee</a>
                    </h3>

                    <span class="badge">Hot</span>

                    <span class="span title-2">$7.5</span>
                  </div>

                  <p class="card-text">
                    Coffee with milk cream
                  </p>
                </div>
              </div>

              <div class="menu-card">
                <figure class="card-banner img-holder" style="--width: 100; --height: 100;">
                  <img src="./assets/images/menu/menu-6.jpg" width="100" height="100" loading="lazy" alt="Mocha Coffee" class="img-cover">
                </figure>

                <div>
                  <div class="title-wrapper">
                    <h3 class="title-3">
                      <a href="#" class="card-title">Mocha Coffee</a>
                    </h3>

                    <span class="span title-2">$5.2</span>
                  </div>

                  <p class="card-text">
                    Espresso with chocolate and milk
                  </p>
                </div>
              </div>
            <?php else: ?>
              <!-- Dynamic menu items from database -->
              <?php foreach ($featuredItems as $item): ?>
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
                      
                      <?php if (!empty($item['badge'])): ?>
                        <span class="badge"><?php echo htmlspecialchars($item['badge']); ?></span>
                      <?php endif; ?>
                      
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

      <!-- 
        - #FEATURE
      -->
      <section class="section feature" aria-label="feature">
        <div class="container">
          <figure class="feature-banner" data-reveal="left">
            <img src="./assets/images/feature-banner.jpg" width="680" height="700" loading="lazy" alt="feature banner"
              class="w-100">
          </figure>

          <div class="feature-content" data-reveal="right">
            <p class="section-subtitle">Why Choose Us</p>

            <h2 class="section-title">New London Coffee Founded For Extraordinary Test</h2>

            <p class="section-text">
              Sed ut perspiciatis unde omnis iste natus error voluptate accusantium doloremque laudantium, totam rem
              aperiam eaque ipsa quae abillo inventore veritatis
            </p>

            <div class="feature-wrapper">
              <div class="feature-card">
                <div class="card-icon">
                  <img src="./assets/images/features/feature-icon-1.png" width="70" height="70" loading="lazy"
                    alt="feature icon">
                </div>

                <div>
                  <h3 class="h3 card-title">Natural Coffee Beans</h3>

                  <p class="card-text">
                    Sed ut perspiciatis unde omnis iste natus error voluptate accusantium doloremque
                  </p>
                </div>
              </div>

              <div class="feature-card">
                <div class="card-icon">
                  <img src="./assets/images/features/feature-icon-2.png" width="70" height="70" loading="lazy"
                    alt="feature icon">
                </div>

                <div>
                  <h3 class="h3 card-title">100% ISO Certification</h3>

                  <p class="card-text">
                    Sed ut perspiciatis unde omnis iste natus error voluptate accusantium doloremque
                  </p>
                </div>
              </div>
            </div>

            <a href="#" class="btn btn-primary">
              <span class="span">Explore More</span>

              <ion-icon name="chevron-forward" aria-hidden="true"></ion-icon>
            </a>
          </div>
        </div>
      </section>

      <!-- 
        - #CTA
      -->
      <section class="section cta has-bg-image" aria-label="cta" style="background-image: url('./assets/images/hero-banner.jpg')">
        <div class="container">
          <div data-reveal>
            <p class="section-subtitle">Need A Table On Coffee House</p>

            <h2 class="section-title">Booking Table For Your & Family Members</h2>
          </div>

          <a href="#" class="btn btn-primary bookTableBtn" data-reveal>
            <span class="span">Booking Table</span>

            <ion-icon name="chevron-forward" aria-hidden="true"></ion-icon>
          </a>
        </div>
      </section>
    </article>
  </main>

  <?php include 'footer.php'; ?>

  <!-- Developer Modal -->
  <div class="developer-modal" id="developerModal">
    <div class="developer-options">
      <button class="close-modal" id="closeDevModal">×</button>
      <h2>Developer Options</h2>
      <div class="form-group">
        <a href="db-setup.php" class="btn btn-primary">Setup Database</a>
      </div>
      <div class="form-group">
        <a href="admin/" class="btn btn-primary">Admin Panel</a>
      </div>
    </div>
  </div>

  <!-- 
    - custom js link
  -->
  <script src="./assets/js/script.js"></script>

  <!-- 
    - ionicon link
  -->
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Booking Modal Elements
      const bookingModal = document.getElementById('bookingModal');
      const bookTableBtn = document.getElementById('bookTableBtn');
      const closeModal = document.getElementById('closeModal');
      const tableBookingForm = document.getElementById('tableBookingForm');
      const formMessage = document.getElementById('formMessage');
      
      // Developer Modal Elements
      const developerModal = document.getElementById('developerModal');
      const devOptionsBtn = document.getElementById('devOptionsBtn');
      const closeDevModal = document.getElementById('closeDevModal');
      
      // Get all buttons with the bookTableBtn class
      const allBookButtons = document.querySelectorAll('.bookTableBtn, #bookTableBtn');
      
      // Add click event to all booking buttons
      allBookButtons.forEach(button => {
        button.addEventListener('click', function(e) {
          e.preventDefault();
          bookingModal.classList.add('active');
          document.body.style.overflow = 'hidden'; // Prevent scrolling when modal is open
        });
      });
      
      // Close booking modal when clicking the close button
      if (closeModal) {
        closeModal.addEventListener('click', function() {
          bookingModal.classList.remove('active');
          document.body.style.overflow = ''; // Re-enable scrolling
          formMessage.className = 'form-message'; // Reset message
          formMessage.textContent = '';
        });
      }
      
      // Close booking modal when clicking outside the form
      if (bookingModal) {
        bookingModal.addEventListener('click', function(e) {
          if (e.target === bookingModal) {
            bookingModal.classList.remove('active');
            document.body.style.overflow = ''; // Re-enable scrolling
            formMessage.className = 'form-message'; // Reset message
            formMessage.textContent = '';
          }
        });
      }
      
      // Developer Options Button Click Event
      if (devOptionsBtn) {
        devOptionsBtn.addEventListener('click', function(e) {
          e.preventDefault();
          developerModal.classList.add('active');
          document.body.style.overflow = 'hidden'; // Prevent scrolling when modal is open
        });
      }
      
      // Close developer modal when clicking the close button
      if (closeDevModal) {
        closeDevModal.addEventListener('click', function() {
          developerModal.classList.remove('active');
          document.body.style.overflow = ''; // Re-enable scrolling
        });
      }
      
      // Close developer modal when clicking outside the form
      if (developerModal) {
        developerModal.addEventListener('click', function(e) {
          if (e.target === developerModal) {
            developerModal.classList.remove('active');
            document.body.style.overflow = ''; // Re-enable scrolling
          }
        });
      }
    });
  </script>
</body>

</html>
