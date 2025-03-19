<?php include 'includes/header.php'; ?>

<!-- Hero Banner -->
<section class="page-hero contact-hero" data-aos="fade-up">
    <div class="container">
        <h1>Let's Talk – We're Here to Help</h1>
        <p>Reach out to us for any questions about our healthcare services</p>
    </div>
</section>

<!-- Contact Section -->
<section class="contact-section" data-aos="fade-up">
    <div class="container">
        <div class="contact-grid">
            <!-- Contact Form -->
            <div class="contact-form-wrapper" data-aos="fade-right">
                <h2>Send Us a Message</h2>
                <p>Fill out the form below and we'll get back to you as soon as possible.</p>
                
                <form id="contactForm" class="contact-form" action="process_contact.php" method="POST">
                    <div class="form-group">
                        <label for="name">Full Name *</label>
                        <input type="text" id="name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number *</label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>

                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject">
                    </div>

                    <div class="form-group">
                        <label for="message">Your Message *</label>
                        <textarea id="message" name="message" rows="5" required></textarea>
                    </div>

                    <div class="form-group">
                        <div class="g-recaptcha" data-sitekey="YOUR_RECAPTCHA_SITE_KEY"></div>
                    </div>

                    <button type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>

            <!-- Contact Information -->
            <div class="contact-info-wrapper" data-aos="fade-left">
                <div class="contact-info-box">
                    <h2>Contact Information</h2>
                    <p>Feel free to reach out through any of the following ways:</p>
                    
                    <div class="contact-details">
                        <div class="contact-item">
                            <i class="fas fa-phone"></i>
                            <div class="contact-text">
                                <h3>Phone</h3>
                                <p><a href="tel:+15551234567">(555) 123-4567</a></p>
                                <p class="text-muted">Available 24/7 for emergencies</p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <div class="contact-text">
                                <h3>Email</h3>
                                <p><a href="mailto:info@homecare.com">info@homecare.com</a></p>
                                <p class="text-muted">We'll respond within 24 hours</p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <i class="fas fa-location-dot"></i>
                            <div class="contact-text">
                                <h3>Office Location</h3>
                                <p>123 Healthcare Avenue</p>
                                <p>Medical District, MD 12345</p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <i class="fas fa-clock"></i>
                            <div class="contact-text">
                                <h3>Office Hours</h3>
                                <p>Monday - Friday: 9:00 AM - 6:00 PM</p>
                                <p>Saturday: 9:00 AM - 2:00 PM</p>
                                <p>Sunday: Closed</p>
                            </div>
                        </div>
                    </div>

                    <!-- Social Media Links -->
                    <div class="contact-social">
                        <h3>Follow Us</h3>
                        <div class="social-links">
                            <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="map-section" data-aos="fade-up">
    <div class="map-wrapper">
        <iframe 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d12345.67890!2d-73.123456!3d40.123456!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDDCsDA3JzM0LjQiTiA3M8KwMDcnMzQuNCJX!5e0!3m2!1sen!2sus!4v1234567890"
            width="100%" 
            height="450" 
            style="border:0;" 
            allowfullscreen="" 
            loading="lazy" 
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
    </div>
</section>

<!-- FAQ Section -->
<section class="contact-faq" data-aos="fade-up">
    <div class="container">
        <h2>Common Questions</h2>
        <div class="faq-accordion">
            <div class="faq-item" data-aos="fade-up">
                <div class="faq-question">
                    <h3>How quickly will you respond to my inquiry?</h3>
                    <span class="faq-toggle"><i class="fas fa-plus"></i></span>
                </div>
                <div class="faq-answer">
                    <p>We aim to respond to all inquiries within 24 hours during business days. For urgent matters, please call our emergency hotline.</p>
                </div>
            </div>

            <div class="faq-item" data-aos="fade-up">
                <div class="faq-question">
                    <h3>Can I schedule a consultation before committing?</h3>
                    <span class="faq-toggle"><i class="fas fa-plus"></i></span>
                </div>
                <div class="faq-answer">
                    <p>Yes, we offer free initial consultations to discuss your needs and how we can help. You can schedule one through our booking page or by calling us directly.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Emergency Contact -->
<section class="emergency-contact" data-aos="fade-up">
    <div class="container">
        <div class="emergency-content">
            <div class="emergency-icon">
                <i class="fas fa-phone-volume"></i>
            </div>
            <div class="emergency-text">
                <h2>24/7 Emergency Contact</h2>
                <p>For urgent medical assistance or care needs</p>
                <a href="tel:+15551234567" class="emergency-phone">(555) 123-4567</a>
            </div>
        </div>
    </div>
</section>

<!-- JavaScript for form validation -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script>
document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Basic form validation
    let name = document.getElementById('name').value;
    let email = document.getElementById('email').value;
    let phone = document.getElementById('phone').value;
    let message = document.getElementById('message').value;
    
    if (!name || !email || !phone || !message) {
        alert('Please fill in all required fields.');
        return;
    }
    
    // Email validation
    let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        alert('Please enter a valid email address.');
        return;
    }
    
    // Phone validation
    let phoneRegex = /^\+?[\d\s-]{10,}$/;
    if (!phoneRegex.test(phone)) {
        alert('Please enter a valid phone number.');
        return;
    }
    
    // If validation passes, submit the form
    this.submit();
});
</script>

<?php include 'includes/footer.php'; ?>
