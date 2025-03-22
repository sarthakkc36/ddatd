<?php


// If session hasn't started yet, configure and start it
if (session_status() === PHP_SESSION_NONE) {
    // Session configuration
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
    
    // Start session
    session_start();
}


// Make sure DEBUG_MODE is set to false for production
define('DEBUG_MODE', false);


if (DEBUG_MODE) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// Set timezone
date_default_timezone_set('Asia/Kathmandu');

// Site Configuration
define('SITE_NAME', 'Doctors At Door Step');
define('SITE_URL', 'http://localhost/ddatd');
define('ADMIN_EMAIL', 'admin@ddatd.com');
define('SUPPORT_EMAIL', 'support@ddatd.com');
define('NOREPLY_EMAIL', 'noreply@ddatd.com');

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'ddatd_db');
define('DB_USER', 'root');  // Using root for development
define('DB_PASS', '');      // Empty password for local development

// Security settings
define('CSRF_TOKEN_TIME', 3600); // 1 hour
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_TIMEOUT', 900); // 15 minutes

// File upload settings
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_FILE_TYPES', ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx']);
define('UPLOAD_PATH', $_SERVER['DOCUMENT_ROOT'] . '/ddatd/uploads/');

// Booking settings
define('MIN_BOOKING_NOTICE', 24); // Hours
define('MAX_BOOKING_ADVANCE', 90); // Days
define('DEFAULT_BOOKING_DURATION', 2); // Hours

// Error logging
define('ERROR_LOG_FILE', $_SERVER['DOCUMENT_ROOT'] . '/ddatd/logs/error.log');
define('ACCESS_LOG_FILE', $_SERVER['DOCUMENT_ROOT'] . '/ddatd/logs/access.log');

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
