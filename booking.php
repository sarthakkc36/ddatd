<?php include 'includes/header.php'; ?>

<!-- Hero Banner -->
<section class="page-hero booking-hero" data-aos="fade-up">
    <div class="container">
        <h1>Schedule Your Care Service</h1>
        <p>Book a consultation or care service with our healthcare professionals</p>
    </div>
</section>

<!-- Booking Form Section -->
<section class="booking-section" data-aos="fade-up">
    <div class="container">
        <div class="booking-grid">
            <!-- Booking Form -->
            <div class="booking-form-wrapper" data-aos="fade-right">
                <form id="bookingForm" class="booking-form" action="process_booking.php" method="POST">
                    <!-- Personal Information -->
                    <div class="form-section">
                        <h2>Personal Information</h2>
                        <div class="form-group">
                            <label for="firstName">First Name *</label>
                            <input type="text" id="firstName" name="firstName" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="lastName">Last Name *</label>
                            <input type="text" id="lastName" name="lastName" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone Number *</label>
                            <input type="tel" id="phone" name="phone" required>
                        </div>
                    </div>

                    <!-- Service Selection -->
                    <div class="form-section">
                        <h2>Service Details</h2>
                        <div class="form-group">
                            <label for="service">Select Service *</label>
                            <select id="service" name="service" required>
                                <option value="">Choose a service...</option>
                                <option value="home-nursing">Home Nursing Care</option>
                                <option value="elderly-assistance">Elderly Assistance</option>
                                <option value="physical-therapy">Physical Therapy</option>
                                <option value="medical-support">24/7 Medical Support</option>
                                <option value="companionship">Companionship Care</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="careType">Type of Care *</label>
                            <select id="careType" name="careType" required>
                                <option value="">Select care type...</option>
                                <option value="one-time">One-time Visit</option>
                                <option value="recurring">Recurring Care</option>
                                <option value="consultation">Initial Consultation</option>
                            </select>
                        </div>
                    </div>

                    <!-- Schedule -->
                    <div class="form-section">
                        <h2>Preferred Schedule</h2>
                        <div class="form-group">
                            <label for="preferredDate">Preferred Date *</label>
                            <input type="date" id="preferredDate" name="preferredDate" required>
                        </div>

                        <div class="form-group">
                            <label for="preferredTime">Preferred Time *</label>
                            <select id="preferredTime" name="preferredTime" required>
                                <option value="">Choose a time...</option>
                                <option value="morning">Morning (9:00 AM - 12:00 PM)</option>
                                <option value="afternoon">Afternoon (12:00 PM - 4:00 PM)</option>
                                <option value="evening">Evening (4:00 PM - 8:00 PM)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="duration">Care Duration *</label>
                            <select id="duration" name="duration" required>
                                <option value="">Select duration...</option>
                                <option value="1-hour">1 Hour</option>
                                <option value="2-hours">2 Hours</option>
                                <option value="4-hours">4 Hours</option>
                                <option value="8-hours">8 Hours</option>
                                <option value="full-day">Full Day</option>
                            </select>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="form-section">
                        <h2>Additional Information</h2>
                        <div class="form-group">
                            <label for="medicalCondition">Medical Condition/Concerns</label>
                            <textarea id="medicalCondition" name="medicalCondition" rows="4"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="specialRequirements">Special Requirements or Preferences</label>
                            <textarea id="specialRequirements" name="specialRequirements" rows="4"></textarea>
                        </div>
                    </div>

                    <!-- Emergency Contact -->
                    <div class="form-section">
                        <h2>Emergency Contact</h2>
                        <div class="form-group">
                            <label for="emergencyName">Emergency Contact Name *</label>
                            <input type="text" id="emergencyName" name="emergencyName" required>
                        </div>

                        <div class="form-group">
                            <label for="emergencyPhone">Emergency Contact Phone *</label>
                            <input type="tel" id="emergencyPhone" name="emergencyPhone" required>
                        </div>

                        <div class="form-group">
                            <label for="relationship">Relationship to Patient *</label>
                            <input type="text" id="relationship" name="relationship" required>
                        </div>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="form-section">
                        <div class="form-group checkbox-group">
                            <input type="checkbox" id="terms" name="terms" required>
                            <label for="terms">I agree to the <a href="terms.php">Terms and Conditions</a> and <a href="privacy.php">Privacy Policy</a></label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Schedule Appointment</button>
                </form>
            </div>

            <!-- Booking Information -->
            <div class="booking-info-wrapper" data-aos="fade-left">
                <div class="booking-info-box">
                    <h2>Booking Information</h2>
                    <div class="info-section">
                        <h3>What to Expect</h3>
                        <ul>
                            <li>Confirmation within 24 hours</li>
                            <li>Free initial consultation</li>
                            <li>Personalized care plan</li>
                            <li>Professional healthcare staff</li>
                        </ul>
                    </div>

                    <div class="info-section">
                        <h3>Required Documents</h3>
                        <ul>
                            <li>Valid ID or passport</li>
                            <li>Medical history (if applicable)</li>
                            <li>Insurance information</li>
                            <li>List of current medications</li>
                        </ul>
                    </div>

                    <div class="info-section">
                        <h3>Need Help?</h3>
                        <p>Contact our care coordinators for assistance:</p>
                        <a href="tel:+15551234567" class="contact-phone">
                            <i class="fas fa-phone"></i>
                            (555) 123-4567
                        </a>
                        <p class="availability">Available Monday - Friday, 9:00 AM - 6:00 PM</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQs -->
<section class="booking-faq" data-aos="fade-up">
    <div class="container">
        <h2>Frequently Asked Questions</h2>
        <div class="faq-accordion">
            <div class="faq-item" data-aos="fade-up">
                <div class="faq-question">
                    <h3>How far in advance should I book?</h3>
                    <span class="faq-toggle"><i class="fas fa-plus"></i></span>
                </div>
                <div class="faq-answer">
                    <p>We recommend booking at least 48-72 hours in advance for regular services. For emergency care needs, we offer expedited booking options.</p>
                </div>
            </div>

            <div class="faq-item" data-aos="fade-up">
                <div class="faq-question">
                    <h3>Can I modify or cancel my booking?</h3>
                    <span class="faq-toggle"><i class="fas fa-plus"></i></span>
                </div>
                <div class="faq-answer">
                    <p>Yes, you can modify or cancel your booking up to 24 hours before the scheduled service time. Please contact our care coordinators for assistance.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Booking Success Modal -->
<div id="bookingSuccessModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <i class="fas fa-check-circle success-icon"></i>
        <h2>Booking Successful!</h2>
        <p>Thank you for scheduling with us. We'll send you a confirmation email shortly with all the details.</p>
        <p class="reference-number">Reference Number: <span id="bookingReference"></span></p>
    </div>
</div>

<!-- JavaScript -->
<script src="js/booking.js"></script>

<?php include 'includes/footer.php'; ?>
