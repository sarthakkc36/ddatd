</main>
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <!-- Quick Links -->
                <div class="footer-section" data-aos="fade-up">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="services.php">Our Services</a></li>
                        <li><a href="pricing.php">Pricing</a></li>
                        <li><a href="contact.php">Contact Us</a></li>
                        <li><a href="privacy.php">Privacy Policy</a></li>
                        <li><a href="terms.php">Terms of Service</a></li>
                    </ul>
                </div>

                <!-- Services -->
                <div class="footer-section" data-aos="fade-up" data-aos-delay="100">
                    <h3>Our Services</h3>
                    <ul>
                        <li><a href="services.php#home-nursing">Home Medical Care</a></li>
                        <li><a href="services.php#elderly-assistance">Elderly Assistance</a></li>
                        <li><a href="services.php#physical-therapy">Physical Therapy</a></li>
                        <li><a href="services.php#medical-support">24/7 Doctor Support</a></li>
                        <li><a href="services.php#companionship">Companionship Care</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="footer-section" data-aos="fade-up" data-aos-delay="200">
                    <h3>Contact Us</h3>
                    <ul class="contact-info">
                        <li><i class="fas fa-phone"></i> <?php echo htmlspecialchars($settingsHandler->get('contact_phone', '+977 986-0102404')); ?></li>
                        <li><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($settingsHandler->get('contact_email', 'doctorsatdoorstep@gmail.com')); ?></li>
                        <li><i class="fas fa-location-dot"></i> <?php echo htmlspecialchars($settingsHandler->get('address', 'khursanitar marg, Kathmandu, Nepal')); ?></li>
                    </ul>
                </div>

                <!-- Newsletter -->
                <div class="footer-section" data-aos="fade-up" data-aos-delay="300">
                    <h3>Newsletter</h3>
                    <p>Subscribe to our newsletter for healthcare tips and updates.</p>
                    <form class="newsletter-form" id="newsletterForm">
                        <input type="email" placeholder="Enter your email" required>
                        <button type="submit"><i class="fas fa-paper-plane"></i></button>
                    </form>
                    <!-- Social Media Icons -->
                    <div class="social-icons">
                        <?php
                        // Get social media links from settings
                        $facebook = $settingsHandler->get('facebook', '');
                        $twitter = $settingsHandler->get('twitter', '');
                        $instagram = $settingsHandler->get('instagram', '');
                        $linkedin = $settingsHandler->get('linkedin', '');
                        $youtube = $settingsHandler->get('youtube', '');
                        
                        if (!empty($facebook)): 
                        ?>
                        <a href="<?php echo htmlspecialchars($facebook); ?>" class="social-icon" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <?php endif; 
                        if (!empty($twitter)): 
                        ?>
                        <a href="<?php echo htmlspecialchars($twitter); ?>" class="social-icon" target="_blank"><i class="fab fa-twitter"></i></a>
                        <?php endif; 
                        if (!empty($instagram)): 
                        ?>
                        <a href="<?php echo htmlspecialchars($instagram); ?>" class="social-icon" target="_blank"><i class="fab fa-instagram"></i></a>
                        <?php endif; 
                        if (!empty($linkedin)): 
                        ?>
                        <a href="<?php echo htmlspecialchars($linkedin); ?>" class="social-icon" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                        <?php endif; 
                        if (!empty($youtube)): 
                        ?>
                        <a href="<?php echo htmlspecialchars($youtube); ?>" class="social-icon" target="_blank"><i class="fab fa-youtube"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($settingsHandler->get('site_name', 'Doctors At Door Step')); ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <!-- Swiper JS -->
    <script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
    <script src="js/script.js"></script>
</body>
</html>