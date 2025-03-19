<?php
require_once 'includes/config.php';
require_once 'includes/Database.php';
include 'includes/header.php';
?>

<main class="container my-5">
    <h1 class="mb-4 text-center">Frequently Asked Questions</h1>
    
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="accordion" id="faqAccordion">
                <!-- FAQ Item 1 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            How does Doctors At Door Step work?
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>Doctors At Door Step is a home healthcare service that brings medical professionals directly to your doorstep. Simply book an appointment through our website or by calling our customer service. Select the service you need, provide your address and preferred time, and our qualified healthcare professional will visit you at home.</p>
                        </div>
                    </div>
                </div>
                
                <!-- FAQ Item 2 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            What services do you offer?
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>We offer a wide range of healthcare services including:</p>
                            <ul>
                                <li>General physician consultations</li>
                                <li>Specialist consultations</li>
                                <li>Nursing care</li>
                                <li>Physiotherapy</li>
                                <li>Lab sample collection</li>
                                <li>Medication delivery</li>
                                <li>Elderly care</li>
                                <li>Post-operative care</li>
                            </ul>
                            <p>Visit our <a href="services.php">Services page</a> for more details.</p>
                        </div>
                    </div>
                </div>
                
                <!-- FAQ Item 3 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            How much do your services cost?
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>Our service costs vary depending on the type of care needed, duration, and location. You can find detailed pricing information on our <a href="services.php">Services page</a>. We strive to keep our prices competitive and transparent, with no hidden fees.</p>
                        </div>
                    </div>
                </div>
                
                <!-- FAQ Item 4 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFour">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                            Are your healthcare professionals qualified?
                        </button>
                    </h2>
                    <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>Yes, all our healthcare professionals are fully qualified, licensed, and experienced. We have a rigorous selection process and verify all credentials before hiring. Our team includes doctors, nurses, physiotherapists, and other specialists who meet our high standards of care.</p>
                        </div>
                    </div>
                </div>
                
                <!-- FAQ Item 5 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFive">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                            How quickly can I get an appointment?
                        </button>
                    </h2>
                    <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>For routine appointments, we typically require 24-48 hours notice. However, we also offer urgent care services where we can arrange for a healthcare professional to visit you within a few hours, depending on availability and your location. For emergencies requiring immediate attention, please call emergency services.</p>
                        </div>
                    </div>
                </div>
                
                <!-- FAQ Item 6 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingSix">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                            Do you accept insurance?
                        </button>
                    </h2>
                    <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>Yes, we work with several insurance providers. Please contact our customer service team with your insurance details to verify coverage before booking an appointment. We can also provide detailed receipts for reimbursement purposes if you're using out-of-network benefits.</p>
                        </div>
                    </div>
                </div>
                
                <!-- FAQ Item 7 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingSeven">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                            What areas do you serve?
                        </button>
                    </h2>
                    <div id="collapseSeven" class="accordion-collapse collapse" aria-labelledby="headingSeven" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>We currently serve major metropolitan areas and surrounding suburbs. During the booking process, you can enter your location to check if we provide service in your area. We're continuously expanding our coverage, so check back if your area isn't currently served.</p>
                        </div>
                    </div>
                </div>
                
                <!-- FAQ Item 8 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingEight">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEight" aria-expanded="false" aria-controls="collapseEight">
                            How do I cancel or reschedule an appointment?
                        </button>
                    </h2>
                    <div id="collapseEight" class="accordion-collapse collapse" aria-labelledby="headingEight" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>You can cancel or reschedule your appointment by logging into your account on our website or by calling our customer service. We request at least 24 hours notice for cancellations or changes to avoid cancellation fees. For emergencies, please contact us as soon as possible.</p>
                        </div>
                    </div>
                </div>
                
                <!-- FAQ Item 9 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingNine">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNine" aria-expanded="false" aria-controls="collapseNine">
                            What should I prepare before the healthcare professional arrives?
                        </button>
                    </h2>
                    <div id="collapseNine" class="accordion-collapse collapse" aria-labelledby="headingNine" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>To make the most of your appointment, please:</p>
                            <ul>
                                <li>Have your ID and insurance information ready</li>
                                <li>Prepare a list of your current medications</li>
                                <li>Write down any symptoms or concerns you want to discuss</li>
                                <li>Have any relevant medical records available</li>
                                <li>Ensure there's a clean, well-lit space for the examination</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- FAQ Item 10 -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTen">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTen" aria-expanded="false" aria-controls="collapseTen">
                            How do I provide feedback about the service?
                        </button>
                    </h2>
                    <div id="collapseTen" class="accordion-collapse collapse" aria-labelledby="headingTen" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            <p>We value your feedback! After each appointment, you'll receive an email with a link to a satisfaction survey. You can also provide feedback through our <a href="contact.php">Contact page</a> or by calling our customer service. Your input helps us improve our services.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-5 text-center">
                <p>Don't see your question answered here? Contact us!</p>
                <a href="contact.php" class="btn btn-primary">Get in Touch</a>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
