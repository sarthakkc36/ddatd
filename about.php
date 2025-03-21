<?php 
include 'includes/header.php';
require_once 'includes/Database.php';
require_once 'admin/includes/Team.php';

$db = Database::getInstance();
$pdo = $db->getConnection();
$team = new Team($pdo);
$teamMembers = $team->getAllActiveMembers();
?>

<!-- Hero Banner -->
<section class="page-hero about-hero" data-aos="fade-up">
    <div class="container">
        <h1>Better Always Home Care</h1>
        <p>Dedicated to providing exceptional healthcare with doctors at your doorstep</p>
    </div>
</section>

<!-- Our Story -->
<section class="our-story" data-aos="fade-up">
    <div class="container">
        <div class="story-grid">
            <div class="story-content">
                <h2>Our Story</h2>
                <p>Founded in Kathmandu, Doctors At Door Step emerged from a simple yet powerful vision: to provide exceptional healthcare services in the comfort of people's homes. Our journey began when our founder recognized the growing need for personalized, professional healthcare services that would allow patients to maintain their independence while receiving top-quality care from qualified doctors.</p>
                <p>Over the years, we've grown from a small team of dedicated medical professionals to a comprehensive healthcare provider, serving thousands of families across Kathmandu. Our commitment to excellence and compassionate care remains at the heart of everything we do.</p>
            </div>
            <div class="story-image" data-aos="fade-left">
                <img src="images/our-story.jpg" alt="Doctors At Door Step's Journey" loading="lazy">
            </div>
        </div>
    </div>
</section>

<!-- Mission & Vision -->
<section class="mission-vision" data-aos="fade-up">
    <div class="container">
        <div class="mission-grid">
            <div class="mission-box" data-aos="fade-right">
                <h2>Our Mission</h2>
                <p>To enhance the quality of life for our patients through personalized, professional medical services delivered with compassion and excellence by qualified doctors in the comfort of their homes.</p>
                <div class="mission-icon">
                    <i class="fas fa-heart-pulse"></i>
                </div>
            </div>
            <div class="mission-box" data-aos="fade-left">
                <h2>Our Vision</h2>
                <p>To be the leading provider of doorstep medical services in Nepal, setting the standard for quality care, innovation, and patient satisfaction in the industry.</p>
                <div class="mission-icon">
                    <i class="fas fa-star"></i>
                </div>
            </div>
        </div>

        <!-- Core Values -->
        <div class="values-grid">
            <h2>Our Core Values</h2>
            <div class="values-list">
                <div class="value-item" data-aos="fade-up">
                    <i class="fas fa-heart"></i>
                    <h3>Compassion</h3>
                    <p>Treating each patient with kindness, empathy, and respect</p>
                </div>
                <div class="value-item" data-aos="fade-up" data-aos-delay="100">
                    <i class="fas fa-award"></i>
                    <h3>Excellence</h3>
                    <p>Striving for the highest standards in healthcare delivery</p>
                </div>
                <div class="value-item" data-aos="fade-up" data-aos-delay="200">
                    <i class="fas fa-users"></i>
                    <h3>Teamwork</h3>
                    <p>Collaborating to provide comprehensive care solutions</p>
                </div>
                <div class="value-item" data-aos="fade-up" data-aos-delay="300">
                    <i class="fas fa-shield-heart"></i>
                    <h3>Integrity</h3>
                    <p>Maintaining honesty and transparency in all interactions</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Meet the Team -->
<section class="team-section" data-aos="fade-up">
    <div class="container">
        <h2>Meet Our Team</h2>
        <p class="section-subtitle">Dedicated professionals committed to your care</p>
        
        <div class="team-grid">
            <?php if (empty($teamMembers)): ?>
                <p class="text-center">No team members found.</p>
            <?php else: ?>
                <?php foreach ($teamMembers as $member): ?>
                    <div class="team-member">
                        <div class="member-image">
                            <?php if ($member['photo_path']): ?>
                                <!-- Debug: <?php var_dump($member['photo_path']); ?> -->
                                <img src="/ddatd/<?php echo $member['photo_path']; ?>" 
                                     style="width: 100%; height: auto; object-fit: cover; border-radius: 8px;"
                                     alt="<?php echo htmlspecialchars($member['name']); ?>" 
                                     loading="lazy">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/300x400.jpg?text=<?php echo urlencode($member['name']); ?>" 
                                     alt="<?php echo htmlspecialchars($member['name']); ?>" 
                                     loading="lazy">
                            <?php endif; ?>
                        </div>
                        <div class="member-info">
                            <h3><?php echo htmlspecialchars($member['name']); ?></h3>
                            <p class="member-role"><?php echo htmlspecialchars($member['position']); ?></p>
                            <?php if ($member['qualifications']): ?>
                                <p class="member-qualifications"><?php echo htmlspecialchars($member['qualifications']); ?></p>
                            <?php endif; ?>
                            <p class="member-bio"><?php echo htmlspecialchars($member['bio']); ?></p>
                            <?php if ($member['specialties']): ?>
                                <p class="member-specialties">Specialties: <?php echo htmlspecialchars($member['specialties']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Our Approach -->
<section class="approach-section" data-aos="fade-up">
    <div class="container">
        <h2>Our Approach</h2>
        <p class="section-subtitle">A comprehensive, patient-centered care process</p>
        
        <div class="approach-steps">
            <!-- Step 1 -->
            <div class="approach-step" data-aos="fade-right">
                <div class="step-number">1</div>
                <div class="step-content">
                    <h3>Initial Consultation</h3>
                    <p>We begin with a thorough assessment of your needs and create a personalized care plan.</p>
                </div>
            </div>
            <!-- Step 2 -->
            <div class="approach-step" data-aos="fade-right" data-aos-delay="100">
                <div class="step-number">2</div>
                <div class="step-content">
                    <h3>Care Team Assignment</h3>
                    <p>We match you with healthcare professionals who best suit your specific needs.</p>
                </div>
            </div>
            <!-- Step 3 -->
            <div class="approach-step" data-aos="fade-right" data-aos-delay="200">
                <div class="step-number">3</div>
                <div class="step-content">
                    <h3>Care Implementation</h3>
                    <p>Our team delivers comprehensive care following your personalized plan.</p>
                </div>
            </div>
            <!-- Step 4 -->
            <div class="approach-step" data-aos="fade-right" data-aos-delay="300">
                <div class="step-number">4</div>
                <div class="step-content">
                    <h3>Continuous Monitoring</h3>
                    <p>We regularly assess and adjust your care plan to ensure optimal results.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact CTA -->
<section class="contact-cta" data-aos="fade-up">
    <div class="container">
        <div class="cta-content">
            <h2>Ready to Learn More?</h2>
            <p>Contact us today to schedule a consultation and discover how we can help.</p>
            <div class="cta-buttons">
                <a href="contact.php" class="btn btn-primary">Contact Us</a>
                <a href="booking.php" class="btn btn-outline">Book Consultation</a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
