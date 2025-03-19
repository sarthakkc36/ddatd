<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Configuration
$admin_email = "admin@homecare.com";
$log_file = "logs/contact_submissions.log";

// Function to validate reCAPTCHA
function verifyRecaptcha($recaptcha_response) {
    $secret_key = "YOUR_RECAPTCHA_SECRET_KEY";
    $url = "https://www.google.com/recaptcha/api/siteverify";
    $data = [
        'secret' => $secret_key,
        'response' => $recaptcha_response
    ];

    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    $result = json_decode($response);

    return $result->success;
}

// Function to sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to log submissions
function log_submission($data) {
    global $log_file;
    $log_entry = date('Y-m-d H:i:s') . " | " . 
                 "Name: " . $data['name'] . " | " .
                 "Email: " . $data['email'] . " | " .
                 "Phone: " . $data['phone'] . " | " .
                 "Subject: " . $data['subject'] . " | " .
                 "Message: " . str_replace("\n", " ", $data['message']) . "\n";

    // Create logs directory if it doesn't exist
    if (!file_exists(dirname($log_file))) {
        mkdir(dirname($log_file), 0755, true);
    }

    file_put_contents($log_file, $log_entry, FILE_APPEND);
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $response = [
        'success' => false,
        'message' => ''
    ];

    // Validate required fields
    $required_fields = ['name', 'email', 'phone', 'message'];
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
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $phone = sanitize_input($_POST['phone']);
    $subject = sanitize_input($_POST['subject'] ?? 'New Contact Form Submission');
    $message = sanitize_input($_POST['message']);

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = "Please enter a valid email address.";
        echo json_encode($response);
        exit;
    }

    // Verify reCAPTCHA
    if (isset($_POST['g-recaptcha-response'])) {
        $recaptcha_response = $_POST['g-recaptcha-response'];
        if (!verifyRecaptcha($recaptcha_response)) {
            $response['message'] = "reCAPTCHA verification failed. Please try again.";
            echo json_encode($response);
            exit;
        }
    } else {
        $response['message'] = "Please complete the reCAPTCHA verification.";
        echo json_encode($response);
        exit;
    }

    // Prepare email content
    $email_content = "New Contact Form Submission\n\n";
    $email_content .= "Name: " . $name . "\n";
    $email_content .= "Email: " . $email . "\n";
    $email_content .= "Phone: " . $phone . "\n";
    $email_content .= "Subject: " . $subject . "\n\n";
    $email_content .= "Message:\n" . $message . "\n";

    // Email headers
    $headers = "From: " . $email . "\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Send email
    try {
        if (mail($admin_email, "New Contact Form Submission: " . $subject, $email_content, $headers)) {
            // Log successful submission
            log_submission([
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'subject' => $subject,
                'message' => $message
            ]);

            $response['success'] = true;
            $response['message'] = "Thank you for your message. We'll get back to you soon!";
        } else {
            throw new Exception("Failed to send email");
        }
    } catch (Exception $e) {
        $response['message'] = "Sorry, there was an error sending your message. Please try again later.";
        error_log("Contact form error: " . $e->getMessage());
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// If accessed directly without POST data
header("Location: contact.php");
exit;
?>
