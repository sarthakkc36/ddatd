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

<?php
// Get testimonials data
require_once 'includes/Testimonials.php';
$testimonialHandler = new Testimonials();
$testimonials = $testimonialHandler->getAllActiveTestimonials();
?>

<!-- Testimonials Section -->
<section class="testimonials">
    <div class="container">
        <div class="section-header">
            <h2>What Our Clients Say</h2>
            <p>Real experiences from families we've helped</p>
        </div>
        
        <div class="testimonials-slider">
            <!-- Swiper Container -->
            <div class="swiper-container testimonials-swiper">
                <div class="swiper-wrapper">
                    <?php if (empty($testimonials)): ?>
                        <!-- Fallback if no testimonials -->
                        <div class="swiper-slide">
                            <div class="testimonial-card">
                                <div class="testimonial-content">
                                    <p>"The care and attention provided by the doctors was exceptional. They made my recovery process so much more comfortable in my own home."</p>
                                </div>
                                <div class="testimonial-author">
                                    <div class="testimonial-author-image no-image">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="author-info">
                                        <h4>Raj Thapa</h4>
                                        <p>Patient</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($testimonials as $testimonial): ?>
                            <div class="swiper-slide">
                                <div class="testimonial-card">
                                    <div class="testimonial-content">
                                        <p>"<?php echo htmlspecialchars($testimonial['content']); ?>"</p>
                                    </div>
                                    <div class="testimonial-author">
                                        <!-- Fixed image path handling -->
                                        <?php if (!empty($testimonial['photo_path'])): ?>
                                            <!-- Correctly referencing the image path from the root -->
                                            <div class="testimonial-author-image" style="background-image: url('<?php echo htmlspecialchars($testimonial['photo_path']); ?>'); background-size: cover; background-position: center;"></div>
                                        <?php else: ?>
                                            <div class="testimonial-author-image no-image">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div class="author-info">
                                            <h4><?php echo htmlspecialchars($testimonial['name']); ?></h4>
                                            <p><?php echo htmlspecialchars($testimonial['position']); ?></p>
                                            <?php if ($testimonial['rating'] > 0): ?>
                                            <div class="testimonial-rating">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="fas fa-star<?php echo $i <= $testimonial['rating'] ? '' : '-o'; ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <!-- Add Pagination -->
                <div class="swiper-pagination"></div>
            </div>
            
            <!-- Add Navigation -->
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
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
<!-- Add this right before the closing body tag in index.php -->
<script>
// Testimonials Slider Initialization
document.addEventListener('DOMContentLoaded', function() {
    // Initialize testimonials slider
    if (document.querySelector('.testimonials-swiper')) {
        // Check if Swiper is already loaded
        if (typeof Swiper !== 'undefined') {
            initTestimonialsSlider();
        } else {
            // If Swiper isn't loaded yet, wait for a moment and try again
            setTimeout(function() {
                if (typeof Swiper !== 'undefined') {
                    initTestimonialsSlider();
                } else {
                    console.error('Swiper library not loaded properly');
                }
            }, 1000);
        }
    }
});

function initTestimonialsSlider() {
    // Simple configuration without complex animations
    var testimonialSwiper = new Swiper('.testimonials-swiper', {
        // Optional parameters
        slidesPerView: 1,
        spaceBetween: 30,
        loop: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        // Responsive breakpoints
        breakpoints: {
            // when window width is >= 768px
            768: {
                slidesPerView: 2,
                spaceBetween: 20
            },
            // when window width is >= 1024px
            1024: {
                slidesPerView: 2,
                spaceBetween: 30
            }
        },
        // Navigation arrows
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        // Pagination
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        }
    });
}
</script>
<?php include 'includes/footer.php'; ?>
