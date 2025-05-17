<?php
/**
 * Footer Template
 * 
 * This file contains the footer section that can be included in all pages
 */
?>
<!-- 
  - #FOOTER
-->
<footer class="footer">
  <div class="footer-top section">
    <div class="container">
      <a href="index.php" class="logo">
        <img src="./assets/images/logo.svg" width="216" height="80" alt="Keofi home" class="w-100">
      </a>

      <div class="footer-list">
        <p class="footer-list-title">Working Hours</p>
        <p class="footer-list-text">
          Sunday - Thursday<br>
          08:00 am - 09:00pm
        </p>
        <p class="footer-list-text">
          Only Friday<br>
          03:00 pm - 09:00pm
        </p>
        <p class="footer-list-text">
          Saturday Close
        </p>
      </div>

      <div class="footer-list">
        <p class="footer-list-title">Contact Us</p>
        <div class="contact-item">
          <ion-icon name="location-outline" aria-hidden="true"></ion-icon>
          <address class="contact-link">
            Location :<br>
            MIT ADT UNIVERSITY PUNE
          </address>
        </div>
        <div class="contact-item">
          <ion-icon name="mail-unread-outline" aria-hidden="true"></ion-icon>
          <a href="mailto:keofi@gmail.com" class="contact-link">
            Email Address :<br>
            keofi@gmail.com
          </a>
        </div>
        <div class="contact-item">
          <ion-icon name="call-outline" aria-hidden="true"></ion-icon>
          <a href="tel:+0123456789" class="contact-link">
            Phone Number :<br>
            +012 (345) 678 99
          </a>
        </div>
      </div>

      <div class="footer-list">
        <p class="footer-list-title">Gallery</p>
        <ul class="grid-list">
          <?php
          // You can make this dynamic by fetching from database
          $galleryImages = [
            'gallery-1.jpg',
            'gallery-2.jpg',
            'gallery-3.jpg',
            'gallery-4.jpg',
            'gallery-5.jpg'
          ];
          
          foreach ($galleryImages as $image): ?>
            <li>
              <div class="grid-item">
                <img src="./assets/images/gallery/<?php echo $image; ?>" width="80" height="80" loading="lazy" alt="Gallery" class="img-cover">
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>

  <div class="footer-bottom">
    <div class="container">
      <p class="copyright">
        &copy; <?php echo date('Y'); ?> Keofi. All Rights Reserved.
      </p>
    </div>
  </div>
</footer>

<!-- Custom JavaScript for form handling -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Form submission with AJAX
    const tableBookingForm = document.getElementById('tableBookingForm');
    const formMessage = document.getElementById('formMessage');
    
    if (tableBookingForm) {
      tableBookingForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Create form data object
        const formData = new FormData(this);
        
        // Send AJAX request
        fetch('booking.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          // Display response message
          formMessage.className = data.success ? 'form-message success' : 'form-message error';
          formMessage.textContent = data.message;
          
          // Reset form if successful
          if (data.success) {
            tableBookingForm.reset();
            
            // Auto close after 5 seconds
            setTimeout(() => {
              const bookingModal = document.getElementById('bookingModal');
              bookingModal.classList.remove('active');
              document.body.style.overflow = ''; // Re-enable scrolling
              formMessage.className = 'form-message'; // Reset message
              formMessage.textContent = '';
            }, 5000);
          }
        })
        .catch(error => {
          formMessage.className = 'form-message error';
          formMessage.textContent = 'An error occurred. Please try again later.';
          console.error('Error:', error);
        });
      });
    }
  });
</script>
