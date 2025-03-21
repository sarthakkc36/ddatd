<?php
if (!defined('ALLOWED_ACCESS')) {
    define('ALLOWED_ACCESS', true);
}
require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctors At Door Step - Professional Healthcare Services</title>
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
                <img src="images/logo.png" alt="Doctors At Door Step"width="90px" height="80px"  />
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
                    <li><a href="contact.php" class="nav-link">Contact</a></li>
                    <li><a href="booking.php" class="nav-link cta-button">Book Now</a></li>
                </ul>
            </nav>
        </div>
    </header>