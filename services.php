<?php 
require_once 'includes/Services.php';
include 'includes/header.php';

$servicesHandler = new Services();
$services = $servicesHandler->getAllActiveServices();
?>

<!-- Hero Banner -->
<section class="page-hero services-hero" data-aos="fade-up">
    <div class="container">
        <h1>Our Medical Services</h1>
        <p>Comprehensive doorstep medical solutions by qualified doctors tailored to your needs</p>
    </div>
</section>

<!-- Services Grid -->
<section class="services-grid-section" data-aos="fade-up">
    <div class="container">
        <?php if (empty($services)): ?>
            <div class="no-services">
                <p>No services are currently available. Please check back later.</p>
            </div>
        <?php else: ?>
            <?php foreach ($services as $service): ?>
                <div class="service-item" id="service-<?php echo $service['id']; ?>" data-aos="fade-up">
                    <div class="service-image">
                        <img src="images/services/<?php echo htmlspecialchars($service['image']); ?>.jpg" 
                             alt="<?php echo htmlspecialchars($service['title']); ?>" 
                             loading="lazy"
                             onerror="this.src='images/placeholder-service.jpg'">
                    </div>
                    <div class="service-details">
                        <div class="service-icon">
                            <i class="fas <?php echo htmlspecialchars($service['image']); ?>"></i>
                        </div>
                        <h2><?php echo htmlspecialchars($service['title']); ?></h2>
                        <p><?php echo htmlspecialchars($service['description']); ?></p>
                        <div class="service-meta">
                            <p class="service-price"><?php echo $servicesHandler->formatPrice($service['price']); ?></p>
                            <p class="service-duration"><?php echo $servicesHandler->formatDuration($service['duration']); ?></p>
                        </div>
                        <a href="service-details.php?id=<?php echo $service['id']; ?>" class="btn btn-primary">Learn More</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<!-- Service Process -->
<section class="service-process" data-aos="fade-up">
    <div class="container">
            <h2>How It Works</h2>
            <p class="section-subtitle">Our simple process to get you started with the medical care you need</p>
        
        <div class="process-steps">
            <!-- Step 1 -->
            <div class="process-step" data-aos="fade-right">
                <div class="step-circle">1</div>
                <div class="step-content">
                    <h3>Consultation</h3>
                    <p>Schedule a free consultation to discuss your needs and preferences.</p>
                </div>
            </div>
            <!-- Step 2 -->
            <div class="process-step" data-aos="fade-right" data-aos-delay="100">
                <div class="step-circle">2</div>
                <div class="step-content">
                    <h3>Personalized Plan</h3>
                    <p>We develop a customized care plan tailored to your specific requirements.</p>
                </div>
            </div>
            <!-- Step 3 -->
            <div class="process-step" data-aos="fade-right" data-aos-delay="200">
                <div class="step-circle">3</div>
                <div class="step-content">
                    <h3>Care Implementation</h3>
                    <p>Our professional team begins providing the agreed-upon services.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="faq-section" data-aos="fade-up">
    <div class="container">
        <h2>Frequently Asked Questions</h2>
        <p class="section-subtitle">Find answers to common questions about our services</p>
        
        <div class="faq-accordion">
            <div class="faq-item" data-aos="fade-up">
                <div class="faq-question">
                    <h3>What types of medical services do you offer?</h3>
                    <span class="faq-toggle"><i class="fas fa-plus"></i></span>
                </div>
                <div class="faq-answer">
                    <p>We offer a comprehensive range of doorstep medical services including doctor consultations, elderly assistance, physical therapy, 24/7 doctor support, and companionship care. Each service is customized to meet your specific needs in Kathmandu.</p>
                </div>
            </div>

            <div class="faq-item" data-aos="fade-up">
                <div class="faq-question">
                    <h3>How do you select your doctors?</h3>
                    <span class="faq-toggle"><i class="fas fa-plus"></i></span>
                </div>
                <div class="faq-answer">
                    <p>All our doctors undergo thorough background checks, licensing verification, and extensive training. We only select medical professionals who demonstrate both technical expertise and compassionate care abilities.</p>
                </div>
            </div>

            <div class="faq-item" data-aos="fade-up">
                <div class="faq-question">
                    <h3>What are your service hours?</h3>
                    <span class="faq-toggle"><i class="fas fa-plus"></i></span>
                </div>
                <div class="faq-answer">
                    <p>We provide flexible scheduling options, including 24/7 care services. Our care plans can be adjusted to accommodate both short-term and long-term needs, with services available day or night.</p>
                </div>
            </div>

            <div class="faq-item" data-aos="fade-up">
                <div class="faq-question">
                    <h3>Is your service covered by insurance?</h3>
                    <span class="faq-toggle"><i class="fas fa-plus"></i></span>
                </div>
                <div class="faq-answer">
                    <p>Many of our services are covered by insurance plans and Medicare. We can help you understand your coverage options and work with your insurance provider to maximize your benefits.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Booking CTA -->
<section class="booking-cta" data-aos="fade-up">
    <div class="container">
        <div class="cta-content">
            <h2>Ready to Get Started?</h2>
            <p>Book a free consultation to discuss your healthcare needs and create a personalized care plan.</p>
            <div class="cta-buttons">
                <a href="booking.php" class="btn btn-primary">Book Consultation</a>
                <a href="contact.php" class="btn btn-outline">Contact Us</a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
