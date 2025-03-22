<?php
if (!defined('ALLOWED_ACCESS')) {
    define('ALLOWED_ACCESS', true);
}
require_once __DIR__ . '/config.php';

// Include Settings class
require_once __DIR__ . '/Settings.php';
$settingsHandler = new Settings();

// Get site settings
$siteSettings = [
    'site_name' => $settingsHandler->get('site_name', 'Doctors At Door Step'),
    'site_tagline' => $settingsHandler->get('site_tagline', 'Healthcare at your doorstep'),
    'site_description' => $settingsHandler->get('site_description', ''),
    'logo' => $settingsHandler->get('logo', 'images/logo.png'),
    'favicon' => $settingsHandler->get('favicon', 'images/favicon.ico')
];

// Check if maintenance mode is enabled
$maintenanceMode = $settingsHandler->get('maintenance_mode', '0') === '1';
if ($maintenanceMode && !isset($_SESSION['admin_logged_in'])) {
    // Only display maintenance page to non-admin users
    include 'maintenance.php';
    exit;
}

// Set debug mode based on settings
$debugMode = $settingsHandler->get('debug_mode', '0') === '1';
if ($debugMode) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// Set timezone
$timezone = $settingsHandler->get('timezone', 'Asia/Kathmandu');
date_default_timezone_set($timezone);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($siteSettings['site_name']); ?> - <?php echo htmlspecialchars($siteSettings['site_tagline']); ?></title>
    
    <?php if (!empty($siteSettings['site_description'])): ?>
    <meta name="description" content="<?php echo htmlspecialchars($siteSettings['site_description']); ?>">
    <?php endif; ?>
    
    <!-- Favicon -->
    <?php if (!empty($siteSettings['favicon'])): ?>
    <link rel="icon" href="<?php echo htmlspecialchars($siteSettings['favicon']); ?>" type="image/x-icon">
    <?php endif; ?>
    
    <!-- Fonts & Styles -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Swiper JS for Testimonials -->
    <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/placeholder-images.css">
    
    <?php
    // Get the current page filename
    $current_page = basename($_SERVER['PHP_SELF']);
    
    // Include additional CSS files based on current page
    if ($current_page === 'service-details.php') {
        echo '<link rel="stylesheet" href="css/service-details.css">';
    }
    ?>
</head>
<body>
    <!-- Sticky Header -->
    <header class="header" id="header">
        <div class="container">
            <nav class="nav">
                <a href="index.php" class="logo">
                <?php if (!empty($siteSettings['logo'])): ?>
                <img src="<?php echo htmlspecialchars($siteSettings['logo']); ?>" alt="<?php echo htmlspecialchars($siteSettings['site_name']); ?>" width="90px" height="80px">
                <?php else: ?>
                <h1><?php echo htmlspecialchars($siteSettings['site_name']); ?></h1>
                <?php endif; ?>
                </a>
                
                <div class="nav-toggle" id="navToggle">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>

                <ul class="nav-menu">
                    <li><a href="index.php" class="nav-link">Home</a></li>
                    <li><a href="about.php" class="nav-link">About Us</a></li>
                    <li><a href="services.php" class="nav-link">Services</a></li>
                    <li><a href="pricing.php" class="nav-link">Pricing</a></li>
                    <li><a href="faq.php" class="nav-link">FAQ</a></li>
                    <li><a href="blog.php" class="nav-link">Blog</a></li>
                    <li><a href="contact.php" class="nav-link">Contact</a></li>
                    <li><a href="booking.php" class="nav-link cta-button">Book Now</a></li>
                </ul>
            </nav>
        </div>
    </header>