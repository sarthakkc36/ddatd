// Validate date to ensure it's not in the past
function validateDate(dateInput) {
    const selectedDate = new Date(dateInput.value);
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    if (selectedDate < today) {
        alert('Please select a future date');
        dateInput.value = '';
        return false;
    }
    return true;
}

// Initialize date restrictions
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('preferredDate');
    const today = new Date().toISOString().split('T')[0];
    dateInput.min = today;
    
    dateInput.addEventListener('change', function() {
        validateDate(this);
    });
});

// Handle form submission
document.getElementById('bookingForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Form validation
    const requiredFields = [
        'firstName', 'lastName', 'email', 'phone',
        'service', 'careType', 'preferredDate', 'preferredTime',
        'duration', 'emergencyName', 'emergencyPhone', 'relationship'
    ];

    let isValid = true;
    const formData = new FormData(this);

    // Check required fields
    requiredFields.forEach(field => {
        const input = document.getElementById(field);
        if (!formData.get(field)) {
            isValid = false;
            input.classList.add('error');
        } else {
            input.classList.remove('error');
        }
    });

    if (!isValid) {
        alert('Please fill in all required fields');
        return;
    }

    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(formData.get('email'))) {
        alert('Please enter a valid email address');
        document.getElementById('email').classList.add('error');
        return;
    }

    // Phone validation
    const phoneRegex = /^\+?[\d\s-]{10,}$/;
    if (!phoneRegex.test(formData.get('phone'))) {
        alert('Please enter a valid phone number');
        document.getElementById('phone').classList.add('error');
        return;
    }

    // Date validation
    const selectedDate = new Date(formData.get('preferredDate'));
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    if (selectedDate < today) {
        alert('Please select a future date');
        document.getElementById('preferredDate').classList.add('error');
        return;
    }

    // Submit form via AJAX
    const submitButton = this.querySelector('button[type="submit"]');
    submitButton.disabled = true;
    submitButton.innerHTML = 'Processing...';

    fetch('process_booking.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        submitButton.disabled = false;
        submitButton.innerHTML = 'Schedule Appointment';

        if (data.success) {
            // Update reference number and show success modal
            document.getElementById('bookingReference').textContent = data.reference;
            document.getElementById('bookingSuccessModal').style.display = 'block';
            
            // Reset form
            this.reset();
            
            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } else {
            alert(data.message || 'An error occurred. Please try again.');
        }
    })
    .catch(error => {
        submitButton.disabled = false;
        submitButton.innerHTML = 'Schedule Appointment';
        alert('An error occurred. Please try again later.');
        console.error('Booking error:', error);
    });
});

// Service selection handler
document.getElementById('service').addEventListener('change', function() {
    const service = this.value;
    const durationSelect = document.getElementById('duration');
    
    // Reset duration options
    durationSelect.innerHTML = '<option value="">Select duration...</option>';
    
    // Add duration options based on service
    if (service === 'home-nursing' || service === 'medical-support') {
        const durations = [
            ['2-hours', '2 Hours'],
            ['4-hours', '4 Hours'],
            ['8-hours', '8 Hours'],
            ['12-hours', '12 Hours'],
            ['24-hours', '24 Hours']
        ];
        durations.forEach(([value, text]) => {
            const option = new Option(text, value);
            durationSelect.add(option);
        });
    } else {
        const durations = [
            ['1-hour', '1 Hour'],
            ['2-hours', '2 Hours'],
            ['4-hours', '4 Hours'],
            ['8-hours', '8 Hours'],
            ['full-day', 'Full Day']
        ];
        durations.forEach(([value, text]) => {
            const option = new Option(text, value);
            durationSelect.add(option);
        });
    }
});

// Care type change handler
document.getElementById('careType').addEventListener('change', function() {
    const careType = this.value;
    const timeSelect = document.getElementById('preferredTime');
    
    if (careType === 'recurring') {
        document.getElementById('duration').value = '';
        document.getElementById('duration').disabled = true;
    } else {
        document.getElementById('duration').disabled = false;
    }
});

// Error highlighting for inputs
document.querySelectorAll('input, select, textarea').forEach(input => {
    input.addEventListener('focus', function() {
        this.classList.remove('error');
    });
});

// Modal close handlers
document.querySelector('.close').addEventListener('click', function() {
    document.getElementById('bookingSuccessModal').style.display = 'none';
});

window.addEventListener('click', function(e) {
    const modal = document.getElementById('bookingSuccessModal');
    if (e.target === modal) {
        modal.style.display = 'none';
    }
});

// Character counter for textareas
document.querySelectorAll('textarea').forEach(textarea => {
    textarea.addEventListener('input', function() {
        const maxLength = this.getAttribute('maxlength') || 500;
        const remaining = maxLength - this.value.length;
        const counter = this.nextElementSibling;
        
        if (!counter || !counter.classList.contains('char-counter')) {
            const counterElem = document.createElement('div');
            counterElem.classList.add('char-counter');
            counterElem.textContent = `${remaining} characters remaining`;
            this.parentNode.insertBefore(counterElem, this.nextSibling);
        } else {
            counter.textContent = `${remaining} characters remaining`;
        }
    });
});
