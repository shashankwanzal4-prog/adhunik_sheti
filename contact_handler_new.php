<?php
/**
 * Contact Form Handler
 * Process contact form submissions and save to database
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Enable output buffering
ob_start();

// Include database configuration
require_once 'config/db.php';

// Start session
session_start();

// Initialize response
$response = [
    'success' => false,
    'message' => ''
];

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Validation
    $errors = [];
    
    if (empty($name)) {
        $errors[] = 'Full name is required';
    }
    
    if (empty($phone)) {
        $errors[] = 'Phone number is required';
    } elseif (!preg_match('/^[0-9]{10}$/', preg_replace('/[^0-9]/', '', $phone))) {
        $errors[] = 'Please enter a valid 10-digit phone number';
    }
    
    if (empty($message)) {
        $errors[] = 'Message is required';
    }
    
    // If no errors, save to database
    if (empty($errors)) {
        // Clean phone number (remove non-numeric characters)
        $clean_phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Insert into database
        $stmt = $db->prepare("INSERT INTO contact_submissions (name, phone, email, subject, message, status, created_at) VALUES (?, ?, ?, ?, ?, 'new', NOW())");
        $stmt->bind_param("sssss", $name, $clean_phone, $email, $subject, $message);
        
        if ($stmt->execute()) {
            $submission_id = $db->insert_id();
            
            // Send email notification
            $email_sent = sendEmailNotification($name, $clean_phone, $email, $subject, $message, $submission_id);
            
            $response['success'] = true;
            $response['message'] = 'Thank you for contacting us! We will get back to you soon.';
            
            // Store WhatsApp link for redirect
            $whatsapp_message = "New Contact Form Submission:\n\nName: $name\nPhone: $clean_phone\nEmail: $email\nSubject: $subject\nMessage: $message\n\nID: $submission_id";
            $_SESSION['whatsapp_notification'] = "https://wa.me/919588676848?text=" . urlencode($whatsapp_message);
            
        } else {
            $response['message'] = 'Failed to submit form. Please try again.';
        }
    } else {
        $response['message'] = implode(', ', $errors);
    }
} else {
    $response['message'] = 'Invalid request method';
}

// For regular form submissions, redirect back with message
if ($response['success']) {
    $_SESSION['contact_success'] = $response['message'];
    header('Location: index.php?contact=success#contact');
} else {
    $_SESSION['contact_error'] = $response['message'];
    header('Location: index.php?contact=error#contact');
}
exit;

/**
 * Send email notification for contact form submission
 */
function sendEmailNotification($name, $phone, $email, $subject, $message, $submission_id) {
    $to = 'shashankwanzal4@gmail.com'; // Admin email
    $email_subject = 'New Contact Form Submission #' . $submission_id . ' - Adhunik Krushi Bhandar';
    
    $headers = "From: noreply@adhunikkrushi.com\r\n";
    $headers .= "Reply-To: " . ($email ?: 'noreply@adhunikkrushi.com') . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    $email_body = "NEW CONTACT FORM SUBMISSION\n\n";
    $email_body .= "Submission ID: #{$submission_id}\n";
    $email_body .= "Name: {$name}\n";
    $email_body .= "Phone: {$phone}\n";
    $email_body .= "Email: " . ($email ?: 'Not provided') . "\n";
    $email_body .= "Subject: " . ($subject ?: 'Not specified') . "\n";
    $email_body .= "Message:\n{$message}\n\n";
    $email_body .= "Submitted at: " . date('Y-m-d H:i:s') . "\n";
    $email_body .= "View in admin panel: http://localhost/Adhunik_Sheti/admin/contacts.php\n";
    
    // Send email (may not work on local XAMPP without SMTP configuration)
    @mail($to, $email_subject, $email_body, $headers);
    
    return true;
}
?>
