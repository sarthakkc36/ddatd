<?php 
define('ALLOWED_ACCESS', true);
include 'includes/header.php';

// Get services for display
require_once 'includes/Services.php';
$servicesHandler = new Services();
$featuredServices = array_slice($servicesHandler->getAllActiveServices(), 0, 3);
?>

<!-- Hero Section -->
<section class="hero" data-aos="fade">
    <div class="hero-content">
        <h1>Doctors At Your Doorstep</h1>
        <p>Better always home care... Professional medical services delivered by experienced doctors in the comfort of your home in Kathmandu.</p>
        <div class="hero-cta">
            <a href="booking.php" class="btn btn-primary">Get Started</a>
            <a href="services.php" class="btn btn-outline">Our Services</a>
        </div>
    </div>
</section>

<!-- Services Overview -->
<section class="services-overview">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <h2>Our Services</h2>
            <p>Comprehensive healthcare solutions tailored to your needs</p>
        </div>
        <div class="services-grid">
            <!-- Service Card 1 -->
            <div class="service-card" style="opacity: 1;">
                <div class="service-icon">
                    <i class="fas fa-user-nurse"></i>
                </div>
                <h3>Home Nursing</h3>
                <p>Professional nursing care in the comfort of your home.</p>
                <a href="services.php#home-nursing" class="service-link">Learn More <i class="fas fa-arrow-right"></i></a>
            </div>
            <!-- Service Card 2 -->
            <div class="service-card" style="opacity: 1;">
                <div class="service-icon">
                    <i class="fas fa-hand-holding-heart"></i>
                </div>
                <h3>Elderly Assistance</h3>
                <p>Compassionate care and support for seniors.</p>
                <a href="services.php#elderly-assistance" class="service-link">Learn More <i class="fas fa-arrow-right"></i></a>
            </div>
            <!-- Service Card 3 -->
            <div class="service-card" style="opacity: 1;">
                <div class="service-icon">
                    <i class="fas fa-walking"></i>
                </div>
                <h3>Physical Therapy</h3>
                <p>Rehabilitation services for improved mobility and strength.</p>
                <a href="services.php#physical-therapy" class="service-link">Learn More <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="why-choose-us">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <h2>Why Choose Us?</h2>
            <p>Experience the difference with our specialized care approach</p>
        </div>
        <div class="features-grid">
            <div class="feature-card" data-aos="fade-up">
                <i class="fas fa-certificate"></i>
                <h3>Licensed Professionals</h3>
                <p>All our caregivers are certified and extensively trained.</p>
            </div>
            <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
                <i class="fas fa-clock"></i>
                <h3>24/7 Availability</h3>
                <p>Round-the-clock support whenever you need us.</p>
            </div>
            <div class="feature-card" data-aos="fade-up" data-aos-delay="200">
                <i class="fas fa-home"></i>
                <h3>Home Environment</h3>
                <p>Care provided in the comfort of your own home.</p>
            </div>
            <div class="feature-card" data-aos="fade-up" data-aos-delay="300">
                <i class="fas fa-heart"></i>
                <h3>Personalized Care</h3>
                <p>Customized care plans tailored to your needs.</p>
            </div>
        </div>
    </div>
</section>

<!-- Stats Counter -->
<section class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card" data-aos="fade-up">
                <i class="fas fa-calendar-check"></i>
                <span class="stats-number" data-value="20">0</span>
                <p>Years of Experience</p>
            </div>
            <div class="stat-card" data-aos="fade-up" data-aos-delay="100">
                <i class="fas fa-users"></i>
                <span class="stats-number" data-value="5000">0</span>
                <p>Patients Served</p>
            </div>
            <div class="stat-card" data-aos="fade-up" data-aos-delay="200">
                <i class="fas fa-user-md"></i>
                <span class="stats-number" data-value="100">0</span>
                <p>Healthcare Professionals</p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="testimonials">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <h2>What Our Clients Say</h2>
            <p>Real experiences from families we've helped</p>
        </div>
        <div class="testimonials-slider" data-aos="fade-up">
            <!-- Testimonial 1 -->
            <div class="testimonial-card">
            <div class="testimonial-content">
                <p>"The care and attention provided to my father was exceptional. The doctors were professional, caring, and always available when we needed them."</p>
            </div>
            <div class="testimonial-author">
                <img src="images/testimonial-1.jpg" alt="Priya Sharma" loading="lazy">
                <div class="author-info">
                    <h4>Priya Sharma</h4>
                    <p>Daughter of Patient</p>
                </div>
            </div>
            </div>
            <!-- Testimonial 2 -->
            <div class="testimonial-card">
            <div class="testimonial-content">
                <p>"Doctors At Door Step's medical services helped me recover faster than I expected. Their team is highly skilled and motivating."</p>
            </div>
            <div class="testimonial-author">
                <img src="images/testimonial-2.jpg" alt="Raj Thapa" loading="lazy">
                <div class="author-info">
                    <h4>Raj Thapa</h4>
                    <p>Patient</p>
                </div>
            </div>
            </div>
        </div>
    </div>
</section>


<!-- Contact CTA -->
<section class="contact-cta">
    <div class="container">
        <div class="cta-content" data-aos="fade-up">
            <h2>Ready to Get Started?</h2>
            <p>Contact us today to schedule a free consultation and learn more about our services.</p>
            <div class="cta-buttons">
                <a href="contact.php" class="btn btn-primary">Contact Us</a>
                <a href="booking.php" class="btn btn-outline">Book Consultation</a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
