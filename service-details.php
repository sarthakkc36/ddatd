<?php
require_once 'includes/Services.php';

// Get service ID from URL
$serviceId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Initialize Services handler
$servicesHandler = new Services();

// Get service details
$serviceData = $servicesHandler->getServiceById($serviceId);

// Redirect to services page if service not found
if (!$serviceData) {
    header('Location: services.php');
    exit();
}

// Parse features and benefits from description
$description_parts = explode("\n", $serviceData['description']);
$main_description = array_shift($description_parts);

// Default features and benefits if not specified in description
$features = [
    'Professional medical care by qualified doctors',
    '24/7 availability for medical needs',
    'Personalized care plans',
    'Regular health monitoring',
    'Emergency response services',
    'Comprehensive medical support'
];

$benefits = [
    'Care in comfortable home environment',
    'Reduced hospital visits',
    'Better recovery outcomes',
    'Peace of mind for family',
    'Cost-effective healthcare solution'
];
?>

<?php include 'includes/header.php'; ?>

<!-- Service Hero -->
<section class="page-hero service-hero" data-aos="fade-up" style="background-image: linear-gradient(rgba(26, 43, 60, 0.8), rgba(26, 43, 60, 0.8)), url('images/<?php echo $serviceData['hero_image']; ?>');">
    <div class="container">
        <h1><?php echo $serviceData['title']; ?></h1>
        <p><?php echo $serviceData['description']; ?></p>
    </div>
</section>

<!-- Service Details -->
<section class="service-details-section" data-aos="fade-up">
    <div class="container">
        <div class="service-details-grid">
            <!-- Main Content -->
            <div class="service-main-content">
                <h2>Service Features</h2>
                <ul class="service-features">
                    <?php foreach ($serviceData['features'] as $feature): ?>
                        <li data-aos="fade-up">
                            <i class="fas fa-check-circle"></i>
                            <span><?php echo $feature; ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <h2>Key Benefits</h2>
                <ul class="service-benefits">
                    <?php foreach ($serviceData['benefits'] as $benefit): ?>
                        <li data-aos="fade-up">
                            <i class="fas fa-star"></i>
                            <span><?php echo $benefit; ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Sidebar -->
            <div class="service-sidebar">
                <div class="pricing-box" data-aos="fade-left">
                    <h3>Service Details</h3>
                    <div class="price-details">
                        <div class="price-item">
                            <span class="label">Base Price:</span>
                            <span class="value"><?php echo $servicesHandler->formatPrice($serviceData['price']); ?></span>
                        </div>
                        <div class="price-item">
                            <span class="label">Duration:</span>
                            <span class="value"><?php echo $servicesHandler->formatDuration($serviceData['duration']); ?></span>
                        </div>
                    </div>
                    <p class="pricing-note">* Final price may vary based on specific care requirements and duration</p>
                    <a href="booking.php?id=<?php echo $serviceData['id']; ?>" class="btn btn-primary">Book This Service</a>
                </div>

                <div class="contact-box" data-aos="fade-left">
                    <h3>Need More Information?</h3>
                    <p>Speak with our care coordinator to learn more about this service.</p>
                    <a href="tel:+97701456789" class="phone-link">
                        <i class="fas fa-phone"></i>
                        +977 986-0102404
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Service Testimonials -->
<section class="service-testimonials" data-aos="fade-up">
    <div class="container">
        <h2>What Our Clients Say</h2>
        <div class="testimonials-grid">
            <!-- Testimonial 1 -->
            <div class="testimonial-card" data-aos="fade-up">
                <div class="testimonial-content">
                    <p>"The level of care and attention provided by the doctors was exceptional. They made my recovery process so much more comfortable in my own home."</p>
                </div>
                <div class="testimonial-author">
                    <img src="images/testimonial-1.jpg" alt="Raj Thapa" loading="lazy">
                    <div class="author-info">
                        <h4>Raj Thapa</h4>
                        <p>Patient</p>
                    </div>
                </div>
            </div>

            <!-- Testimonial 2 -->
            <div class="testimonial-card" data-aos="fade-up" data-aos-delay="100">
                <div class="testimonial-content">
                    <p>"Professional, compassionate, and always available when needed. I couldn't have asked for better medical care for my father in Kathmandu."</p>
                </div>
                <div class="testimonial-author">
                    <img src="images/testimonial-2.jpg" alt="Priya Sharma" loading="lazy">
                    <div class="author-info">
                        <h4>Priya Sharma</h4>
                        <p>Family Member</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="service-faq" data-aos="fade-up">
    <div class="container">
        <h2>Frequently Asked Questions</h2>
        <div class="faq-accordion">
            <div class="faq-item" data-aos="fade-up">
                <div class="faq-question">
                    <h3>What qualifications do your doctors have?</h3>
                    <span class="faq-toggle"><i class="fas fa-plus"></i></span>
                </div>
                <div class="faq-answer">
                    <p>All our doctors are licensed medical professionals with extensive experience in their respective fields. They undergo regular training and certification updates to maintain the highest standards of medical care.</p>
                </div>
            </div>

            <div class="faq-item" data-aos="fade-up">
                <div class="faq-question">
                    <h3>How quickly can you start providing care?</h3>
                    <span class="faq-toggle"><i class="fas fa-plus"></i></span>
                </div>
                <div class="faq-answer">
                    <p>We can typically begin services within 24-48 hours of initial contact, depending on your specific needs and care requirements. Emergency services can be arranged more quickly when needed.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Booking CTA -->
<section class="booking-cta" data-aos="fade-up">
    <div class="container">
        <div class="cta-content">
            <h2>Ready to Start Your Care Journey?</h2>
            <p>Book a consultation today to discuss your specific care needs and create a personalized care plan.</p>
            <div class="cta-buttons">
                <a href="booking.php?id=<?php echo $serviceData['id']; ?>" class="btn btn-primary">Book This Service</a>
                <a href="contact.php" class="btn btn-outline">Contact Us</a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
