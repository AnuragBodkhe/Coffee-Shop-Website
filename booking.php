<?php
/**
 * Booking Form Handler
 * 
 * This file processes the booking form submissions
 */

// Include database configuration
require_once 'config.php';

// Initialize response array
$response = [
    'success' => false,
    'message' => ''
];

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get and sanitize form data
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $date = mysqli_real_escape_string($conn, trim($_POST['date']));
    $time = mysqli_real_escape_string($conn, trim($_POST['time']));
    $guests = mysqli_real_escape_string($conn, trim($_POST['guests']));
    $message = mysqli_real_escape_string($conn, trim($_POST['message']));
    
    // Validate required fields
    if (empty($name) || empty($email) || empty($phone) || empty($date) || empty($time) || empty($guests)) {
        $response['message'] = "Please fill all required fields";
    } 
    // Validate email format
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = "Please enter a valid email address";
    }
    // Process valid form submission
    else {
        // SQL query to insert data into bookings table
        $sql = "INSERT INTO bookings (name, email, phone, booking_date, booking_time, guests, message, created_at) 
                VALUES ('$name', '$email', '$phone', '$date', '$time', '$guests', '$message', NOW())";
        
        // Execute query and check if successful
        if (mysqli_query($conn, $sql)) {
            $response['success'] = true;
            $response['message'] = "Thank you, $name! Your table for $guests has been booked for $date at $time. We'll send a confirmation to $email.";
        } else {
            $response['message'] = "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
