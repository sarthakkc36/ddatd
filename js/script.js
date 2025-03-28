// DOM Elements
const header = document.getElementById('header');
const navToggle = document.getElementById('navToggle');
const navMenu = document.querySelector('.nav-menu');
const newsletterForm = document.getElementById('newsletterForm');

// Mobile Menu Toggle
navToggle.addEventListener('click', () => {
    navToggle.classList.toggle('active');
    navMenu.classList.toggle('active');
});

// Close mobile menu when clicking outside
document.addEventListener('click', (e) => {
    if (!navToggle.contains(e.target) && !navMenu.contains(e.target)) {
        navToggle.classList.remove('active');
        navMenu.classList.remove('active');
    }
});

// Sticky Header
let lastScroll = 0;
window.addEventListener('scroll', () => {
    const currentScroll = window.pageYOffset;
    
    if (currentScroll <= 0) {
        header.classList.remove('scroll-up');
        return;
    }
    
    if (currentScroll > lastScroll && !header.classList.contains('scroll-down')) {
        // Scrolling down
        header.classList.remove('scroll-up');
        header.classList.add('scroll-down');
    } else if (currentScroll < lastScroll && header.classList.contains('scroll-down')) {
        // Scrolling up
        header.classList.remove('scroll-down');
        header.classList.add('scroll-up');
    }
    lastScroll = currentScroll;
});

// Initialize AOS
document.addEventListener('DOMContentLoaded', () => {
    // Fix for service cards - remove AOS attributes
    const serviceCards = document.querySelectorAll('.service-card');
    if (serviceCards.length > 0) {
        serviceCards.forEach(card => {
            // Remove AOS attributes from service cards to prevent conflicts with GSAP
            card.removeAttribute('data-aos');
            card.removeAttribute('data-aos-delay');
        });
    }
    
    // Initialize AOS for other elements
    AOS.init({
        duration: 800,
        once: true,
        offset: 100
    });
});

// GSAP Animations
document.addEventListener('DOMContentLoaded', () => {
    // Register ScrollTrigger
    gsap.registerPlugin(ScrollTrigger);
    
    // Animate stats counter
    const statsNumbers = document.querySelectorAll('.stats-number');
    if (statsNumbers.length > 0) {
        statsNumbers.forEach(stat => {
            const value = parseFloat(stat.getAttribute('data-value'));
            gsap.to(stat, {
                textContent: value,
                duration: 2,
                ease: "power2.out",
                snap: { textContent: 1 },
                scrollTrigger: {
                    trigger: stat,
                    start: "top 80%",
                },
                onUpdate: function() {
                    stat.textContent = Math.round(stat.textContent);
                }
            });
        });
    }

// Animate service cards
const serviceCards = document.querySelectorAll('.service-card');
if (serviceCards.length > 0) {
    // Make sure service cards are visible initially
    serviceCards.forEach(card => {
        card.style.opacity = 1;
    });
    
    // Then apply GSAP animation
    gsap.from(serviceCards, {
        y: 60,
        opacity: 0,
        duration: 1,
        stagger: 0.2,
        ease: "power3.out",
        scrollTrigger: {
            trigger: ".services-grid",
            start: "top 80%",
        }
    });
}

    // Handle team members
    const teamMembers = document.querySelectorAll('.team-member');
    if (teamMembers.length > 0) {
        // Remove AOS attributes and make them visible
        teamMembers.forEach(member => {
            member.removeAttribute('data-aos');
            member.removeAttribute('data-aos-delay');
            member.style.opacity = 1;
        });

        // Apply GSAP animation
        gsap.from(teamMembers, {
            scale: 0.8,
            opacity: 0,
            duration: 1,
            stagger: 0.2,
            ease: "back.out(1.7)",
            scrollTrigger: {
                trigger: ".team-grid",
                start: "top 80%",
            }
        });
    }
});

// Newsletter Form Submission
if (newsletterForm) {
    newsletterForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const emailInput = newsletterForm.querySelector('input[type="email"]');
        const email = emailInput.value;

        try {
            // Here you would typically send this to your backend
            console.log('Newsletter subscription:', email);
            
            // Show success message
            const successMessage = document.createElement('div');
            successMessage.className = 'newsletter-success';
            successMessage.textContent = 'Thank you for subscribing!';
            
            // Replace form with success message
            newsletterForm.innerHTML = '';
            newsletterForm.appendChild(successMessage);
            
        } catch (error) {
            console.error('Newsletter submission error:', error);
            alert('There was an error subscribing to the newsletter. Please try again.');
        }
    });
}

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
            // Close mobile menu if open
            navToggle.classList.remove('active');
            navMenu.classList.remove('active');
        }
    });
});

// FAQ Accordion
const faqItems = document.querySelectorAll('.faq-item');
faqItems.forEach(item => {
    const question = item.querySelector('.faq-question');
    question.addEventListener('click', () => {
        // Close all other FAQ items
        faqItems.forEach(otherItem => {
            if (otherItem !== item && otherItem.classList.contains('active')) {
                otherItem.classList.remove('active');
            }
        });
        // Toggle current FAQ item
        item.classList.toggle('active');
    });
});

// Add active class to current nav link
const currentPage = window.location.pathname;
const navLinks = document.querySelectorAll('.nav-link');
navLinks.forEach(link => {
    if (link.getAttribute('href') === currentPage.split('/').pop()) {
        link.classList.add('active');
    }
});

// Lazy loading for images
if ('loading' in HTMLImageElement.prototype) {
    const images = document.querySelectorAll('img[loading="lazy"]');
    images.forEach(img => {
        img.src = img.dataset.src;
    });
} else {
    // Fallback for browsers that don't support lazy loading
    const script = document.createElement('script');
    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js';
    document.body.appendChild(script);
}
/* Scroll to Top Button */
document.addEventListener('DOMContentLoaded', () => {
    // Create scroll to top button
    const scrollToTopBtn = document.createElement('button');
    scrollToTopBtn.id = 'scrollToTopBtn';
    scrollToTopBtn.classList.add('scroll-to-top');
    scrollToTopBtn.setAttribute('aria-label', 'Scroll to top');
    scrollToTopBtn.innerHTML = '<i class="fas fa-chevron-up"></i>';
    document.body.appendChild(scrollToTopBtn);

    // Function to check scroll position and toggle button visibility
    const toggleScrollButton = () => {
        if (window.pageYOffset > 300) {
            scrollToTopBtn.classList.add('show');
        } else {
            scrollToTopBtn.classList.remove('show');
        }
    };

    // Function to scroll to top smoothly
    const scrollToTop = () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    };

    // Add scroll event listener
    window.addEventListener('scroll', toggleScrollButton);

    // Add click event listener
    scrollToTopBtn.addEventListener('click', scrollToTop);

    // Clean up event listeners (optional, but good practice)
    return () => {
        window.removeEventListener('scroll', toggleScrollButton);
        scrollToTopBtn.removeEventListener('click', scrollToTop);
    };
});