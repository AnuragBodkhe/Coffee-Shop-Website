<?php
/**
 * Admin Logout
 * 
 * This file handles admin logout functionality
 */

// Start session
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: admin-login.php");
exit;
?>
