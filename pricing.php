<?php include 'includes/header.php'; ?>

<!-- Hero Banner -->
<section class="page-hero pricing-hero" data-aos="fade-up">
    <div class="container">
        <h1>Find the Best Plan for Your Loved One</h1>
        <p>Transparent pricing with flexible care options tailored to your needs</p>
    </div>
</section>

<!-- Pricing Plans -->
<section class="pricing-plans" data-aos="fade-up">
    <div class="container">
        <div class="pricing-grid">
            <!-- Basic Plan -->
            <div class="pricing-plan" data-aos="fade-up">
                <div class="plan-header">
                    <h2>Basic</h2>
                    <div class="plan-name">Essential Care</div>
                    <div class="plan-price">
                        <span class="currency">$</span>
                        <span class="amount">49</span>
                        <span class="period">/hour</span>
                    </div>
                    <p class="plan-description">Perfect for basic assistance and companionship needs</p>
                </div>
                <ul class="plan-features">
                    <li><i class="fas fa-check"></i> Basic health monitoring</li>
                    <li><i class="fas fa-check"></i> Medication reminders</li>
                    <li><i class="fas fa-check"></i> Light housekeeping</li>
                    <li><i class="fas fa-check"></i> Companionship services</li>
                    <li><i class="fas fa-check"></i> Basic personal care</li>
                </ul>
                <a href="booking.php?plan=basic" class="btn btn-outline">Choose Basic Plan</a>
            </div>

            <!-- Standard Plan -->
            <div class="pricing-plan recommended" data-aos="fade-up">
                <div class="plan-badge">Most Popular</div>
                <div class="plan-header">
                    <h2>Standard</h2>
                    <div class="plan-name">Personalized Care</div>
                    <div class="plan-price">
                        <span class="currency">$</span>
                        <span class="amount">79</span>
                        <span class="period">/hour</span>
                    </div>
                    <p class="plan-description">Comprehensive care with personalized attention</p>
                </div>
                <ul class="plan-features">
                    <li><i class="fas fa-check"></i> All Basic features, plus:</li>
                    <li><i class="fas fa-check"></i> Skilled nursing care</li>
                    <li><i class="fas fa-check"></i> Physical therapy sessions</li>
                    <li><i class="fas fa-check"></i> Medication management</li>
                    <li><i class="fas fa-check"></i> Personal care assistance</li>
                    <li><i class="fas fa-check"></i> Transportation services</li>
                </ul>
                <a href="booking.php?plan=standard" class="btn btn-primary">Choose Standard Plan</a>
            </div>

            <!-- Premium Plan -->
            <div class="pricing-plan" data-aos="fade-up">
                <div class="plan-header">
                    <h2>Premium</h2>
                    <div class="plan-name">24/7 Assistance</div>
                    <div class="plan-price">
                        <span class="currency">$</span>
                        <span class="amount">109</span>
                        <span class="period">/hour</span>
                    </div>
                    <p class="plan-description">Complete care with round-the-clock support</p>
                </div>
                <ul class="plan-features">
                    <li><i class="fas fa-check"></i> All Standard features, plus:</li>
                    <li><i class="fas fa-check"></i> 24/7 dedicated care</li>
                    <li><i class="fas fa-check"></i> Advanced medical monitoring</li>
                    <li><i class="fas fa-check"></i> Emergency response system</li>
                    <li><i class="fas fa-check"></i> Care coordination</li>
                    <li><i class="fas fa-check"></i> Specialized therapy services</li>
                    <li><i class="fas fa-check"></i> Family care consulting</li>
                </ul>
                <a href="booking.php?plan=premium" class="btn btn-outline">Choose Premium Plan</a>
            </div>
        </div>
    </div>
</section>

<!-- Comparison Chart -->
<section class="comparison-section" data-aos="fade-up">
    <div class="container">
        <h2>Detailed Comparison</h2>
        <div class="comparison-table-wrapper">
            <table class="comparison-table">
                <thead>
                    <tr>
                        <th>Features</th>
                        <th>Basic</th>
                        <th>Standard</th>
                        <th>Premium</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Health Monitoring</td>
                        <td>Basic</td>
                        <td>Advanced</td>
                        <td>Comprehensive</td>
                    </tr>
                    <tr>
                        <td>Care Hours</td>
                        <td>Flexible</td>
                        <td>Extended</td>
                        <td>24/7</td>
                    </tr>
                    <tr>
                        <td>Personal Care</td>
                        <td>Basic</td>
                        <td>Full Service</td>
                        <td>Premium Service</td>
                    </tr>
                    <tr>
                        <td>Medication Management</td>
                        <td>Reminders Only</td>
                        <td>Full Management</td>
                        <td>Advanced Management</td>
                    </tr>
                    <tr>
                        <td>Transportation</td>
                        <td><i class="fas fa-times"></i></td>
                        <td><i class="fas fa-check"></i></td>
                        <td><i class="fas fa-check"></i></td>
                    </tr>
                    <tr>
                        <td>Therapy Services</td>
                        <td><i class="fas fa-times"></i></td>
                        <td>Basic</td>
                        <td>Advanced</td>
                    </tr>
                    <tr>
                        <td>Emergency Response</td>
                        <td><i class="fas fa-times"></i></td>
                        <td><i class="fas fa-check"></i></td>
                        <td>Premium</td>
                    </tr>
                    <tr>
                        <td>Care Coordination</td>
                        <td><i class="fas fa-times"></i></td>
                        <td>Basic</td>
                        <td>Advanced</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- Pricing FAQ -->
<section class="pricing-faq" data-aos="fade-up">
    <div class="container">
        <h2>Common Questions About Pricing</h2>
        <div class="faq-accordion">
            <div class="faq-item" data-aos="fade-up">
                <div class="faq-question">
                    <h3>Are your services covered by insurance?</h3>
                    <span class="faq-toggle"><i class="fas fa-plus"></i></span>
                </div>
                <div class="faq-answer">
                    <p>Many of our services are covered by long-term care insurance and Medicare. We can help you understand your coverage and work directly with your insurance provider.</p>
                </div>
            </div>

            <div class="faq-item" data-aos="fade-up">
                <div class="faq-question">
                    <h3>Can I change plans if my needs change?</h3>
                    <span class="faq-toggle"><i class="fas fa-plus"></i></span>
                </div>
                <div class="faq-answer">
                    <p>Yes, you can upgrade or modify your care plan at any time. We understand that care needs may change, and we're flexible in accommodating those changes.</p>
                </div>
            </div>

            <div class="faq-item" data-aos="fade-up">
                <div class="faq-question">
                    <h3>Is there a minimum commitment period?</h3>
                    <span class="faq-toggle"><i class="fas fa-plus"></i></span>
                </div>
                <div class="faq-answer">
                    <p>No, we don't require long-term commitments. Our services are flexible and can be adjusted based on your needs, whether short-term or long-term care is required.</p>
                </div>
            </div>

            <div class="faq-item" data-aos="fade-up">
                <div class="faq-question">
                    <h3>Are there any additional costs?</h3>
                    <span class="faq-toggle"><i class="fas fa-plus"></i></span>
                </div>
                <div class="faq-answer">
                    <p>Our pricing is transparent and inclusive. Any additional services or special requirements will be discussed and agreed upon before care begins. There are no hidden fees.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Pricing CTA -->
<section class="pricing-cta" data-aos="fade-up">
    <div class="container">
        <div class="cta-content">
            <h2>Need Help Choosing a Plan?</h2>
            <p>Our care coordinators are here to help you find the perfect care solution for your needs.</p>
            <div class="cta-buttons">
                <a href="contact.php" class="btn btn-primary">Speak to a Care Coordinator</a>
                <a href="booking.php" class="btn btn-outline">Schedule a Consultation</a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
