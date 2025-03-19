<?php
// Prevent direct access to this file
if (!defined('ALLOWED_ACCESS')) {
    header("HTTP/1.0 403 Forbidden");
    exit('Direct access forbidden.');
}

// Debug mode (set to false in production)
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// Site Configuration
define('SITE_NAME', 'HomeCare');
define('SITE_URL', 'https://www.yourdomain.com/homecare');
define('ADMIN_EMAIL', 'admin@homecare.com');
define('SUPPORT_EMAIL', 'support@homecare.com');
define('NOREPLY_EMAIL', 'noreply@homecare.com');

// Time zone
date_default_timezone_set('Asia/Kathmandu');

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1);

// Security settings
define('CSRF_TOKEN_TIME', 3600); // 1 hour
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_TIMEOUT', 900); // 15 minutes

// reCAPTCHA settings (replace with actual keys in production)
define('RECAPTCHA_SITE_KEY', 'your-recaptcha-site-key');
define('RECAPTCHA_SECRET_KEY', 'your-recaptcha-secret-key');

// Database configuration (if needed in future)
define('DB_HOST', 'localhost');
define('DB_NAME', 'homecare_db');
define('DB_USER', 'homecare_user');
define('DB_PASS', 'strong_password_here');

// File upload settings
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_FILE_TYPES', ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx']);
define('UPLOAD_PATH', $_SERVER['DOCUMENT_ROOT'] . '/homecare/uploads/');

// Booking settings
define('MIN_BOOKING_NOTICE', 24); // Hours
define('MAX_BOOKING_ADVANCE', 90); // Days
define('DEFAULT_BOOKING_DURATION', 2); // Hours

// Service types
$SERVICE_TYPES = [
    'home-nursing' => 'Home Nursing Care',
    'elderly-assistance' => 'Elderly Assistance',
    'physical-therapy' => 'Physical Therapy',
    'medical-support' => '24/7 Medical Support',
    'companionship' => 'Companionship Care'
];

// Contact form settings
define('MAX_MESSAGE_LENGTH', 1000);
define('CONTACT_FORM_TIMEOUT', 60); // Seconds between submissions

// Error logging
define('ERROR_LOG_FILE', $_SERVER['DOCUMENT_ROOT'] . '/homecare/logs/error.log');
define('ACCESS_LOG_FILE', $_SERVER['DOCUMENT_ROOT'] . '/homecare/logs/access.log');

// Social media links
$SOCIAL_LINKS = [
    'facebook' => 'https://facebook.com/homecare',
    'twitter' => 'https://twitter.com/homecare',
    'linkedin' => 'https://linkedin.com/company/homecare',
    'instagram' => 'https://instagram.com/homecare'
];

// Utility functions
function sanitize_output($buffer) {
    $search = [
        '/\>[^\S ]+/s',     // Remove whitespaces after tags
        '/[^\S ]+\</s',     // Remove whitespaces before tags
        '/(\s)+/s',         // Shorten multiple whitespace sequences
        '/<!--(.|\s)*?-->/' // Remove HTML comments
    ];

    $replace = ['>', '<', '\\1', ''];

    return preg_replace($search, $replace, $buffer);
}

function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    return $_SESSION['csrf_token'];
}

function validate_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || 
        !isset($_SESSION['csrf_token_time']) ||
        $token !== $_SESSION['csrf_token']) {
        return false;
    }
    
    if (time() - $_SESSION['csrf_token_time'] > CSRF_TOKEN_TIME) {
        return false;
    }
    
    return true;
}

function log_error($message) {
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] $message\n";
    error_log($log_entry, 3, ERROR_LOG_FILE);
}

// Initialize session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
