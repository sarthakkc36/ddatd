<?php
$service = isset($_GET['service']) ? $_GET['service'] : '';

// Service data array (in a real application, this would come from a database)
$services = [
    'home-nursing' => [
        'title' => 'Home Nursing Care',
        'hero_image' => 'home-nursing-hero.jpg',
        'description' => 'Our professional home nursing care service provides skilled medical care in the comfort of your home.',
        'features' => [
            'Skilled nursing care by registered nurses',
            '24/7 availability for critical care needs',
            'Medication management and administration',
            'Wound care and dressing changes',
            'Post-surgery care and recovery support',
            'Vital signs monitoring and health assessments'
        ],
        'benefits' => [
            'Personalized care in familiar surroundings',
            'Reduced risk of hospital-acquired infections',
            'Greater comfort and independence',
            'Cost-effective compared to hospital stays',
            'Family involvement in care process'
        ],
        'pricing' => [
            'Hourly Rate: $50-75',
            'Daily Rate: $300-450',
            'Weekly Package: $1,800-2,500',
            'Monthly Package: $7,000-9,000'
        ]
    ],
    'elderly-assistance' => [
        'title' => 'Elderly Assistance',
        'hero_image' => 'elderly-assistance-hero.jpg',
        'description' => 'Comprehensive care and support services designed specifically for seniors, enabling them to maintain independence and quality of life.',
        'features' => [
            'Personal care and hygiene assistance',
            'Mobility support and fall prevention',
            'Medication reminders and management',
            'Light housekeeping and meal preparation',
            'Companionship and emotional support',
            'Transportation to appointments'
        ],
        'benefits' => [
            'Enhanced quality of life',
            'Maintained independence',
            'Reduced risk of falls and accidents',
            'Social interaction and engagement',
            'Peace of mind for family members'
        ],
        'pricing' => [
            'Half-Day Care: $120-150',
            'Full-Day Care: $200-250',
            'Weekly Package: $1,200-1,500',
            'Monthly Package: $4,500-5,500'
        ]
    ]
    // Add more services as needed
];

// Get current service data
$serviceData = isset($services[$service]) ? $services[$service] : null;

// Redirect to services page if service not found
if (!$serviceData) {
    header('Location: services.php');
    exit();
}
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
                    <h3>Pricing Options</h3>
                    <ul>
                        <?php foreach ($serviceData['pricing'] as $price): ?>
                            <li><?php echo $price; ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <p class="pricing-note">* Prices may vary based on specific care requirements</p>
                    <a href="booking.php?service=<?php echo $service; ?>" class="btn btn-primary">Book This Service</a>
                </div>

                <div class="contact-box" data-aos="fade-left">
                    <h3>Need More Information?</h3>
                    <p>Speak with our care coordinator to learn more about this service.</p>
                    <a href="tel:+15551234567" class="phone-link">
                        <i class="fas fa-phone"></i>
                        (555) 123-4567
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
                    <p>"The level of care and attention provided by the nursing staff was exceptional. They made my recovery process so much more comfortable."</p>
                </div>
                <div class="testimonial-author">
                    <img src="images/testimonial-1.jpg" alt="John Smith" loading="lazy">
                    <div class="author-info">
                        <h4>John Smith</h4>
                        <p>Patient</p>
                    </div>
                </div>
            </div>

            <!-- Testimonial 2 -->
            <div class="testimonial-card" data-aos="fade-up" data-aos-delay="100">
                <div class="testimonial-content">
                    <p>"Professional, compassionate, and always available when needed. I couldn't have asked for better care for my mother."</p>
                </div>
                <div class="testimonial-author">
                    <img src="images/testimonial-2.jpg" alt="Mary Johnson" loading="lazy">
                    <div class="author-info">
                        <h4>Mary Johnson</h4>
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
                    <h3>What qualifications do your care providers have?</h3>
                    <span class="faq-toggle"><i class="fas fa-plus"></i></span>
                </div>
                <div class="faq-answer">
                    <p>All our care providers are licensed professionals with extensive experience in their respective fields. They undergo regular training and certification updates to maintain the highest standards of care.</p>
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
                <a href="booking.php?service=<?php echo $service; ?>" class="btn btn-primary">Book This Service</a>
                <a href="contact.php" class="btn btn-outline">Contact Us</a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
