<?php
require_once 'includes/config.php';
require_once 'includes/Database.php';
include 'includes/header.php';
?>

<main class="container my-5">
    <div class="row">
        <div class="col-lg-8">
            <h1 class="mb-4">Our Blog</h1>
            
            <div class="alert alert-info">
                <p>We're currently working on adding blog content. Please check back soon for health tips, medical advice, and updates from our team!</p>
            </div>
            
            <!-- Sample blog posts - these would typically come from a database -->
            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="card-title">The Importance of Regular Health Check-ups</h2>
                    <p class="card-text text-muted mb-2">Posted on March 15, 2025 by Dr. Smith</p>
                    <p class="card-text">Regular health check-ups are essential for maintaining good health and detecting potential issues early. In this article, we discuss why you shouldn't skip your annual physical examination...</p>
                    <a href="#" class="btn btn-primary">Read More</a>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="card-title">Understanding Telemedicine: Benefits and Limitations</h2>
                    <p class="card-text text-muted mb-2">Posted on March 10, 2025 by Dr. Johnson</p>
                    <p class="card-text">Telemedicine has revolutionized healthcare delivery, especially during the pandemic. Learn about the advantages, potential drawbacks, and when it's appropriate to use virtual consultations...</p>
                    <a href="#" class="btn btn-primary">Read More</a>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="card-title">Nutrition Tips for a Stronger Immune System</h2>
                    <p class="card-text text-muted mb-2">Posted on March 5, 2025 by Dr. Patel</p>
                    <p class="card-text">Your diet plays a crucial role in maintaining a healthy immune system. Discover which foods and nutrients can help strengthen your body's natural defenses against illness...</p>
                    <a href="#" class="btn btn-primary">Read More</a>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">Categories</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <ul class="list-unstyled mb-0">
                                <li><a href="#">Health Tips</a></li>
                                <li><a href="#">Medical Advice</a></li>
                                <li><a href="#">Nutrition</a></li>
                            </ul>
                        </div>
                        <div class="col-lg-6">
                            <ul class="list-unstyled mb-0">
                                <li><a href="#">Wellness</a></li>
                                <li><a href="#">COVID-19</a></li>
                                <li><a href="#">Mental Health</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">Subscribe to Our Newsletter</div>
                <div class="card-body">
                    <div class="input-group">
                        <input class="form-control" type="email" placeholder="Enter your email..." aria-label="Enter your email..." aria-describedby="button-newsletter">
                        <button class="btn btn-primary" id="button-newsletter" type="button">Subscribe</button>
                    </div>
                    <small class="text-muted">We'll never share your email with anyone else.</small>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
