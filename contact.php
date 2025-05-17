<?php
/**
 * Contact Form Handler
 * 
 * This file processes contact form submissions
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
    $subject = mysqli_real_escape_string($conn, trim($_POST['subject']));
    $message = mysqli_real_escape_string($conn, trim($_POST['message']));
    
    // Validate required fields
    if (empty($name) || empty($email) || empty($message)) {
        $response['message'] = "Please fill all required fields";
    } 
    // Validate email format
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = "Please enter a valid email address";
    }
    // Process valid form submission
    else {
        // SQL query to insert data into contacts table
        $sql = "INSERT INTO contacts (name, email, subject, message, created_at) 
                VALUES ('$name', '$email', '$subject', '$message', NOW())";
        
        // Execute query and check if successful
        if (mysqli_query($conn, $sql)) {
            $response['success'] = true;
            $response['message'] = "Thank you for contacting us, $name! We'll get back to you soon.";
            
            // Send notification email to admin (optional)
            $to = "admin@keofi.com";
            $subject = "New Contact Form Submission: $subject";
            $email_message = "Name: $name\nEmail: $email\nMessage: $message";
            $headers = "From: $email";
            
            // Uncomment to enable email sending
            // mail($to, $subject, $email_message, $headers);
            
        } else {
            $response['message'] = "Error: " . mysqli_error($conn);
        }
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
