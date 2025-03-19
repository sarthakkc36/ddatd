<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Configuration
$admin_email = "admin@homecare.com";
$log_file = "logs/booking_submissions.log";

// Function to sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to generate booking reference
function generate_booking_reference() {
    return 'BK' . date('Ymd') . strtoupper(substr(uniqid(), -6));
}

// Function to log bookings
function log_booking($data) {
    global $log_file;
    $log_entry = date('Y-m-d H:i:s') . " | " . 
                 "Ref: " . $data['reference'] . " | " .
                 "Name: " . $data['firstName'] . " " . $data['lastName'] . " | " .
                 "Email: " . $data['email'] . " | " .
                 "Service: " . $data['service'] . " | " .
                 "Date: " . $data['preferredDate'] . "\n";

    // Create logs directory if it doesn't exist
    if (!file_exists(dirname($log_file))) {
        mkdir(dirname($log_file), 0755, true);
    }

    file_put_contents($log_file, $log_entry, FILE_APPEND);
}

// Function to send confirmation email
function send_confirmation_email($data) {
    $to = $data['email'];
    $subject = "Booking Confirmation - Reference: " . $data['reference'];
    
    $message = "Dear " . $data['firstName'] . " " . $data['lastName'] . ",\n\n";
    $message .= "Thank you for booking with HomeCare. Here are your booking details:\n\n";
    $message .= "Booking Reference: " . $data['reference'] . "\n";
    $message .= "Service: " . $data['service'] . "\n";
    $message .= "Date: " . $data['preferredDate'] . "\n";
    $message .= "Time: " . $data['preferredTime'] . "\n";
    $message .= "Duration: " . $data['duration'] . "\n\n";
    $message .= "We will contact you shortly to confirm your appointment.\n\n";
    $message .= "Best regards,\nHomeCare Team";

    $headers = "From: HomeCare <noreply@homecare.com>\r\n";
    $headers .= "Reply-To: " . $admin_email . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    return mail($to, $subject, $message, $headers);
}

// Function to send admin notification
function send_admin_notification($data) {
    global $admin_email;
    
    $subject = "New Booking Request - " . $data['reference'];
    
    $message = "New booking request received:\n\n";
    $message .= "Reference: " . $data['reference'] . "\n";
    $message .= "Name: " . $data['firstName'] . " " . $data['lastName'] . "\n";
    $message .= "Email: " . $data['email'] . "\n";
    $message .= "Phone: " . $data['phone'] . "\n";
    $message .= "Service: " . $data['service'] . "\n";
    $message .= "Care Type: " . $data['careType'] . "\n";
    $message .= "Date: " . $data['preferredDate'] . "\n";
    $message .= "Time: " . $data['preferredTime'] . "\n";
    $message .= "Duration: " . $data['duration'] . "\n\n";
    $message .= "Medical Condition:\n" . $data['medicalCondition'] . "\n\n";
    $message .= "Special Requirements:\n" . $data['specialRequirements'] . "\n\n";
    $message .= "Emergency Contact:\n";
    $message .= "Name: " . $data['emergencyName'] . "\n";
    $message .= "Phone: " . $data['emergencyPhone'] . "\n";
    $message .= "Relationship: " . $data['relationship'] . "\n";

    $headers = "From: HomeCare System <system@homecare.com>\r\n";
    $headers .= "Reply-To: " . $data['email'] . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    return mail($admin_email, $subject, $message, $headers);
}

// Process booking submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $response = [
        'success' => false,
        'message' => '',
        'reference' => ''
    ];

    // Validate required fields
    $required_fields = [
        'firstName', 'lastName', 'email', 'phone',
        'service', 'careType', 'preferredDate', 'preferredTime',
        'duration', 'emergencyName', 'emergencyPhone', 'relationship'
    ];

    $missing_fields = [];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $missing_fields[] = ucfirst($field);
        }
    }

    if (!empty($missing_fields)) {
        $response['message'] = "Please fill in the following fields: " . implode(', ', $missing_fields);
        echo json_encode($response);
        exit;
    }

    // Sanitize input
    $booking_data = [
        'reference' => generate_booking_reference(),
        'firstName' => sanitize_input($_POST['firstName']),
        'lastName' => sanitize_input($_POST['lastName']),
        'email' => sanitize_input($_POST['email']),
        'phone' => sanitize_input($_POST['phone']),
        'service' => sanitize_input($_POST['service']),
        'careType' => sanitize_input($_POST['careType']),
        'preferredDate' => sanitize_input($_POST['preferredDate']),
        'preferredTime' => sanitize_input($_POST['preferredTime']),
        'duration' => sanitize_input($_POST['duration']),
        'medicalCondition' => sanitize_input($_POST['medicalCondition'] ?? ''),
        'specialRequirements' => sanitize_input($_POST['specialRequirements'] ?? ''),
        'emergencyName' => sanitize_input($_POST['emergencyName']),
        'emergencyPhone' => sanitize_input($_POST['emergencyPhone']),
        'relationship' => sanitize_input($_POST['relationship'])
    ];

    // Validate email
    if (!filter_var($booking_data['email'], FILTER_VALIDATE_EMAIL)) {
        $response['message'] = "Please enter a valid email address.";
        echo json_encode($response);
        exit;
    }

    try {
        // Log booking
        log_booking($booking_data);

        // Send confirmation email
        if (!send_confirmation_email($booking_data)) {
            throw new Exception("Failed to send confirmation email");
        }

        // Send admin notification
        if (!send_admin_notification($booking_data)) {
            throw new Exception("Failed to send admin notification");
        }

        $response['success'] = true;
        $response['message'] = "Your booking has been successfully submitted.";
        $response['reference'] = $booking_data['reference'];

    } catch (Exception $e) {
        $response['message'] = "An error occurred while processing your booking. Please try again later.";
        error_log("Booking error: " . $e->getMessage());
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// If accessed directly without POST data
header("Location: booking.php");
exit;
?>
