<?php
/**
 * Header Template
 * 
 * This file contains the header section that can be included in all pages
 */
?>
<!-- 
  - #HEADER
-->
<header class="header">
  <div class="header-top">
    <div class="container">
      <p class="header-top-text">
        <ion-icon name="time-outline" aria-hidden="true"></ion-icon>
        <span class="span">Opening Hours :</span> 08:00 Am - 09:00 Pm
      </p>

      <ul class="social-list">
        <li>
          <a href="#" class="social-link">
            <ion-icon name="logo-facebook"></ion-icon>
          </a>
        </li>
        <li>
          <a href="#" class="social-link">
            <ion-icon name="logo-twitter"></ion-icon>
          </a>
        </li>
        <li>
          <a href="#" class="social-link">
            <ion-icon name="logo-instagram"></ion-icon>
          </a>
        </li>
        <li>
          <a href="#" class="social-link">
            <ion-icon name="logo-youtube"></ion-icon>
          </a>
        </li>
      </ul>

      <p class="header-top-text">
        <ion-icon name="location-outline" aria-hidden="true"></ion-icon>
        <span class="span">Location :</span> MIT ADT UNIVERSITY PUNE
      </p>
    </div>
  </div>

  <div class="header-bottom" data-header>
    <div class="container">
      <a href="index.php" class="logo">
        <img src="./assets/images/logo.svg" width="216" height="80" alt="keofi home" class="w-100">
      </a>

      <nav class="navbar" data-navbar>
        <ul class="navbar-list">
          <li>
            <a href="index.php" class="navbar-link" data-nav-link>Home</a>
          </li>
          <li>
            <a href="index.php#about" class="navbar-link" data-nav-link>About</a>
          </li>
          <li>
            <a href="menu.php" class="navbar-link" data-nav-link>Menu</a>
          </li>
          <li>
            <a href="blog.php" class="navbar-link" data-nav-link>Blog</a>
          </li>
          <li>
            <a href="contact.php" class="navbar-link" data-nav-link>Contacts</a>
          </li>
        </ul>
      </nav>

      <a href="#" class="btn btn-primary bookTableBtn">Book A Table</a>

      <button class="nav-open-btn" aria-label="open menu" data-nav-toggler>
        <span class="span"></span>
        <span class="span"></span>
      </button>
    </div>
  </div>
</header>

<!-- Booking Modal -->
<div class="booking-modal" id="bookingModal">
  <div class="booking-form">
    <button class="close-modal" id="closeModal">×</button>
    <h2>Book A Table</h2>
    <form id="tableBookingForm" action="booking.php" method="post">
      <div class="form-group">
        <label for="name">Your Name</label>
        <input type="text" id="name" name="name" class="form-control" required>
      </div>
      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" class="form-control" required>
      </div>
      <div class="form-group">
        <label for="phone">Phone Number</label>
        <input type="tel" id="phone" name="phone" class="form-control" required>
      </div>
      <div class="form-group">
        <label for="date">Date</label>
        <input type="date" id="date" name="date" class="form-control" required>
      </div>
      <div class="form-group">
        <label for="time">Time</label>
        <input type="time" id="time" name="time" class="form-control" required>
      </div>
      <div class="form-group">
        <label for="guests">Number of Guests</label>
        <select id="guests" name="guests" class="form-control" required>
          <option value="">Select</option>
          <option value="1">1 Person</option>
          <option value="2">2 People</option>
          <option value="3">3 People</option>
          <option value="4">4 People</option>
          <option value="5">5 People</option>
          <option value="6">6+ People</option>
        </select>
      </div>
      <div class="form-group">
        <label for="message">Special Requests (Optional)</label>
        <textarea id="message" name="message" class="form-control" rows="3"></textarea>
      </div>
      <button type="submit" class="btn btn-primary">Book Now</button>
      <div id="formMessage" class="form-message"></div>
    </form>
  </div>
</div>
