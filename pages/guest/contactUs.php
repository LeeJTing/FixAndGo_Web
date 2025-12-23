<?php
require_once __DIR__ . '/../../_base.php';
$_title = "Fix & Go | Contact Us";
include_once __DIR__ . '/../../_head.php';
?>

<style>
/* Contact Us Page Styles */
.contact-page {
    max-width: var(--container-width);
    margin: 0 auto;
    padding: 40px 20px;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    color: var(--text-color);
}

.contact-header {
    text-align: center;
    margin-bottom: 60px;
}

.contact-header h1 {
    font-size: 3rem;
    font-weight: 700;
    color: var(--text-color);
    margin-bottom: 20px;
    position: relative;
    display: inline-block;
}

.contact-header h1::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 100px;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-color), var(--primary-hover));
    border-radius: 2px;
}

.contact-header p {
    font-size: 1.2rem;
    color: var(--text-muted);
    max-width: 700px;
    margin: 0 auto;
    line-height: 1.6;
}

.contact-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 50px;
    margin-bottom: 60px;
}

/* Contact Information Section */
.contact-info {
    background: linear-gradient(135deg, var(--bg-card), #ffffff);
    border-radius: 16px;
    padding: 40px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border: 1px solid var(--border-color);
}

.contact-info h2 {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--text-color);
    margin-bottom: 30px;
    position: relative;
    padding-bottom: 15px;
}

.contact-info h2::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 60px;
    height: 3px;
    background: var(--primary-color);
    border-radius: 2px;
}

.info-grid {
    display: grid;
    gap: 25px;
}

.info-item {
    display: flex;
    align-items: flex-start;
    gap: 20px;
    padding: 20px;
    background: white;
    border-radius: 12px;
    border-left: 4px solid var(--primary-color);
    transition: all 0.3s ease;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

.info-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(245, 158, 11, 0.1);
    border-left-color: var(--primary-hover);
}

.info-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.info-icon i {
    font-size: 1.8rem;
    color: var(--text-light);
}

.info-content h3 {
    font-size: 1.2rem;
    font-weight: 600;
    color: var(--text-color);
    margin-bottom: 8px;
}

.info-content p {
    color: var(--text-muted);
    line-height: 1.6;
    margin: 0;
}

.info-content a {
    color: var(--primary-color);
    text-decoration: none;
    transition: color 0.3s ease;
}

.info-content a:hover {
    color: var(--primary-hover);
    text-decoration: underline;
}

.social-links {
    display: flex;
    gap: 15px;
    margin-top: 30px;
    padding-top: 30px;
    border-top: 1px solid var(--border-color);
}

.social-link {
    width: 45px;
    height: 45px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-color);
    text-decoration: none;
    transition: all 0.3s ease;
    border: 2px solid var(--border-color);
    font-size: 1.2rem;
}

.social-link:hover {
    background: var(--primary-color);
    color: white;
    transform: translateY(-3px);
    border-color: var(--primary-color);
}

/* Contact Form Section */
.contact-form-section {
    background: linear-gradient(135deg, var(--bg-card), #ffffff);
    border-radius: 16px;
    padding: 40px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border: 1px solid var(--border-color);
}

.contact-form-section h2 {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--text-color);
    margin-bottom: 30px;
    position: relative;
    padding-bottom: 15px;
}

.contact-form-section h2::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 60px;
    height: 3px;
    background: var(--primary-color);
    border-radius: 2px;
}

.contact-form {
    display: grid;
    gap: 20px;
}

.form-group {
    position: relative;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--text-color);
}

.form-group label span {
    color: var(--error-color);
    margin-left: 4px;
}

.form-control {
    width: 100%;
    padding: 14px 20px;
    font-size: 1rem;
    border: 2px solid var(--border-color);
    border-radius: 10px;
    background: white;
    color: var(--text-color);
    transition: all 0.3s ease;
    font-family: inherit;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
}

textarea.form-control {
    min-height: 150px;
    resize: vertical;
}

.form-control.error {
    border-color: var(--error-color);
}

.error-message {
    color: var(--error-color);
    font-size: 0.875rem;
    margin-top: 5px;
    display: none;
}

.form-submit {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
    color: var(--text-light);
    border: none;
    padding: 16px 32px;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-top: 10px;
    position: relative;
    overflow: hidden;
}

.form-submit:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(245, 158, 11, 0.2);
}

.form-submit:active {
    transform: translateY(-1px);
}

.form-submit:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.form-submit.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 20px;
    height: 20px;
    border: 3px solid rgba(255, 255, 255, 0.3);
    border-top-color: white;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: translate(-50%, -50%) rotate(360deg); }
}

.success-message {
    background: linear-gradient(135deg, var(--success-color), #27ae60);
    color: white;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    display: none;
    animation: slideIn 0.5s ease;
}

.error-alert {
    background: linear-gradient(135deg, var(--error-color), #c0392b);
    color: white;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    display: none;
    animation: slideIn 0.5s ease;
    margin-bottom: 20px;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Map Section */
.map-section {
    margin-top: 40px;
}

.map-section h2 {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--text-color);
    margin-bottom: 20px;
    text-align: center;
}

.map-container {
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border: 1px solid var(--border-color);
    height: 450px;
    background: var(--bg-card);
    position: relative;
}

.map-container iframe {
    width: 100%;
    height: 100%;
    border: 0;
}

/* Business Hours */
.business-hours {
    background: linear-gradient(135deg, var(--bg-card), #ffffff);
    border-radius: 16px;
    padding: 30px;
    margin-top: 40px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border: 1px solid var(--border-color);
}

.business-hours h3 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-color);
    margin-bottom: 20px;
}

.hours-table {
    width: 100%;
    border-collapse: collapse;
}

.hours-table tr {
    border-bottom: 1px solid var(--border-color);
}

.hours-table tr:last-child {
    border-bottom: none;
}

.hours-table td {
    padding: 12px 0;
    color: var(--text-color);
}

.hours-table td:first-child {
    font-weight: 600;
    width: 40%;
}

.hours-table td.open {
    color: var(--success-color);
    font-weight: 600;
}

.hours-table td.closed {
    color: var(--error-color);
    font-weight: 600;
}

/* FAQ Section */
.faq-section {
    margin-top: 60px;
}

.faq-section h2 {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-color);
    text-align: center;
    margin-bottom: 40px;
}

.faq-container {
    max-width: 800px;
    margin: 0 auto;
}

.faq-item {
    background: white;
    border-radius: 12px;
    margin-bottom: 15px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border: 1px solid var(--border-color);
}

.faq-question {
    padding: 20px 30px;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-weight: 600;
    color: var(--text-color);
    transition: all 0.3s ease;
    background: linear-gradient(135deg, var(--bg-card), white);
}

.faq-question:hover {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
    color: white;
}

.faq-question i {
    transition: transform 0.3s ease;
}

.faq-question.active i {
    transform: rotate(180deg);
}

.faq-answer {
    padding: 0 30px;
    max-height: 0;
    overflow: hidden;
    transition: all 0.3s ease;
    color: var(--text-muted);
}

.faq-answer.active {
    padding: 20px 30px;
    max-height: 500px;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .contact-container {
        gap: 30px;
    }
    
    .contact-info,
    .contact-form-section {
        padding: 30px;
    }
}

@media (max-width: 768px) {
    .contact-container {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    
    .contact-header h1 {
        font-size: 2.5rem;
    }
    
    .contact-header p {
        font-size: 1.1rem;
    }
    
    .info-item {
        padding: 15px;
    }
    
    .info-icon {
        width: 50px;
        height: 50px;
    }
    
    .info-icon i {
        font-size: 1.5rem;
    }
    
    .map-container {
        height: 350px;
    }
}

@media (max-width: 480px) {
    .contact-page {
        padding: 20px 15px;
    }
    
    .contact-header h1 {
        font-size: 2rem;
    }
    
    .info-item {
        flex-direction: column;
        text-align: center;
        gap: 15px;
    }
    
    .info-icon {
        margin: 0 auto;
    }
    
    .social-links {
        justify-content: center;
    }
    
    .faq-question {
        padding: 15px 20px;
    }
    
    .faq-answer {
        padding: 0 20px;
    }
    
    .faq-answer.active {
        padding: 15px 20px;
    }
}
</style>

<div class="contact-page">
    <!-- Header Section -->
    <div class="contact-header">
        <h1>Get in Touch</h1>
        <p>Have questions, comments, or concerns? We'd love to hear from you. Fill out the form below, and we'll get back to you as soon as possible.</p>
    </div>

    <!-- Main Contact Container -->
    <div class="contact-container" id="request-form">
        <!-- Contact Information -->
        <div class="contact-info">
            <h2>Contact Information</h2>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-icon">
                        <i>📍</i>
                    </div>
                    <div class="info-content">
                        <h3>Our Location</h3>
                        <p>Arena, TAR UMT, Jalan Genting Kelang,<br>Setapak, 53100 Kuala Lumpur</p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i>📞</i>
                    </div>
                    <div class="info-content">
                        <h3>Phone Number</h3>
                        <p><a href="tel:+60312345678">+60 3-1234 5678</a><br>Monday - Friday, 9am - 6pm</p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i>✉️</i>
                    </div>
                    <div class="info-content">
                        <h3>Email Address</h3>
                        <p><a href="mailto:leekeezhan@gmail.com">leekeezhan@gmail.com</a><br><a href="mailto:support@yourcompany.com">support@yourcompany.com</a></p>
                    </div>
                </div>
            </div>
            
            <!-- Social Media Links -->
            <div class="social-links">
                <a href="https://www.facebook.com/" target="_blank" class="social-link" title="Facebook">f</a>
                <a href="https://x.com/?lang=en-my" target="_blank" class="social-link" title="Twitter">𝕏</a>
                <a href="https://www.instagram.com/" target="_blank" class="social-link" title="Instagram">📷</a>
                <a href="https://www.linkedin.com/" target="_blank" class="social-link" title="LinkedIn">in</a>
                <a href="https://web.whatsapp.com/" target="_blank" class="social-link" title="WhatsApp">✆</a>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="contact-form-section">
            <h2>Send Us a Message</h2>
            <div class="error-alert" id="errorAlert"></div>
            <form class="contact-form" id="contactForm">
                <div class="form-group">
                    <label for="name">Full Name <span>*</span></label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Enter your full name" required>
                    <div class="error-message" id="nameError"></div>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address <span>*</span></label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email address" required>
                    <div class="error-message" id="emailError"></div>
                </div>
                
                <div class="form-group">
                    <label for="subject">Subject <span>*</span></label>
                    <input type="text" id="subject" name="subject" class="form-control" placeholder="What is this regarding?" required>
                    <div class="error-message" id="subjectError"></div>
                </div>
                
                <div class="form-group">
                    <label for="message">Message <span>*</span></label>
                    <textarea id="message" name="message" class="form-control" placeholder="Please describe your inquiry in detail..." required></textarea>
                    <div class="error-message" id="messageError"></div>
                </div>
                
                <button type="submit" class="form-submit">
                    <span>Send Message</span>
                </button>
                
                <div class="success-message" id="successMessage">
                    <h3>🎉 Thank You!</h3>
                    <p>Your message has been sent successfully. We'll get back to you within 24 hours.</p>
                </div>
            </form>
        </div>
    </div>

    <!-- Map Section with Google Maps -->
    <div class="map-section">
        <h2>Find Our Location</h2>
        <div class="map-container">
            <!-- Google Maps Embed - TAR UMT Arena Location -->
            <iframe
                src="https://www.google.com/maps?q=3.215963,101.728667&output=embed"
                width="100%"
                height="100%"
                style="border:0;"
                allowfullscreen
                loading="lazy">
            </iframe>
        </div>
    </div>

    <!-- Business Hours -->
    <div class="business-hours">
        <h3>Business Hours</h3>
        <table class="hours-table">
            <tr>
                <td>Monday - Friday</td>
                <td class="open">9:00 AM - 6:00 PM</td>
            </tr>
            <tr>
                <td>Saturday</td>
                <td class="open">10:00 AM - 4:00 PM</td>
            </tr>
            <tr>
                <td>Sunday</td>
                <td class="closed">Closed</td>
            </tr>
            <tr>
                <td>Public Holidays</td>
                <td class="closed">Closed</td>
            </tr>
        </table>
    </div>

    <!-- FAQ Section -->
    <div class="faq-section">
        <h2>Frequently Asked Questions</h2>
        <div class="faq-container">
            <div class="faq-item">
                <div class="faq-question">
                    <span>What is your typical response time?</span>
                    <i>▼</i>
                </div>
                <div class="faq-answer">
                    <p>We strive to respond to all inquiries within 24 hours during business days. For urgent matters, please call our customer service hotline.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    <span>Do you offer 24/7 customer support?</span>
                    <i>▼</i>
                </div>
                <div class="faq-answer">
                    <p>While we don't offer 24/7 live support, our ticketing system operates around the clock. You can submit tickets anytime, and we'll respond during business hours.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    <span>Can I schedule a meeting at your office?</span>
                    <i>▼</i>
                </div>
                <div class="faq-answer">
                    <p>Yes, we welcome scheduled meetings. Please contact us at least 24 hours in advance to arrange a suitable time with our team.</p>
                </div>
            </div>
            
            <div class="faq-item">
                <div class="faq-question">
                    <span>What payment methods do you accept?</span>
                    <i>▼</i>
                </div>
                <div class="faq-answer">
                    <p>We accept major credit cards (Visa, MasterCard, American Express), bank transfers, and online payment platforms like PayPal and Stripe.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // FAQ Accordion
    const faqQuestions = document.querySelectorAll('.faq-question');
    
    faqQuestions.forEach(question => {
        question.addEventListener('click', () => {
            const answer = question.nextElementSibling;
            const isActive = answer.classList.contains('active');
            
            // Close all other FAQ answers
            document.querySelectorAll('.faq-answer').forEach(ans => {
                ans.classList.remove('active');
            });
            document.querySelectorAll('.faq-question').forEach(q => {
                q.classList.remove('active');
            });
            
            // Open current FAQ answer if it wasn't active
            if (!isActive) {
                answer.classList.add('active');
                question.classList.add('active');
            }
        });
    });
    
    // Form Validation and Submission with AJAX
    const contactForm = document.getElementById('contactForm');
    const successMessage = document.getElementById('successMessage');
    const errorAlert = document.getElementById('errorAlert');
    
    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Reset errors
        document.querySelectorAll('.error-message').forEach(el => {
            el.style.display = 'none';
        });
        document.querySelectorAll('.form-control').forEach(el => {
            el.classList.remove('error');
        });
        errorAlert.style.display = 'none';
        successMessage.style.display = 'none';
        
        // Get form values
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const subject = document.getElementById('subject').value.trim();
        const message = document.getElementById('message').value.trim();
        
        let isValid = true;
        
        // Validate name
        if (!name) {
            showError('nameError', 'Please enter your name');
            isValid = false;
        } else if (name.length < 2) {
            showError('nameError', 'Name must be at least 2 characters');
            isValid = false;
        }
        
        // Validate email
        if (!email) {
            showError('emailError', 'Please enter your email');
            isValid = false;
        } else if (!isValidEmail(email)) {
            showError('emailError', 'Please enter a valid email address');
            isValid = false;
        }
        
        // Validate subject
        if (!subject) {
            showError('subjectError', 'Please enter a subject');
            isValid = false;
        } else if (subject.length < 5) {
            showError('subjectError', 'Subject must be at least 5 characters');
            isValid = false;
        }
        
        // Validate message
        if (!message) {
            showError('messageError', 'Please enter your message');
            isValid = false;
        } else if (message.length < 20) {
            showError('messageError', 'Message must be at least 20 characters');
            isValid = false;
        }
        
        if (isValid) {
            // Show loading state
            const submitBtn = contactForm.querySelector('.form-submit');
            submitBtn.disabled = true;
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '';
            
            // Prepare form data
            const formData = new FormData(contactForm);
            
            // Send AJAX request
            fetch('contact_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.classList.remove('loading');
                submitBtn.innerHTML = '<span>Send Message</span>';
                
                if (data.success) {
                    // Show success message
                    successMessage.style.display = 'block';
                    contactForm.reset();
                    
                    // Scroll to success message
                    successMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    
                    // Hide success message after 8 seconds
                    setTimeout(() => {
                        successMessage.style.display = 'none';
                    }, 8000);
                } else {
                    // Show error message
                    if (data.errors && data.errors.length > 0) {
                        errorAlert.innerHTML = '<strong>Error:</strong> ' + data.errors.join('<br>');
                        errorAlert.style.display = 'block';
                        errorAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        
                        // Hide error after 8 seconds
                        setTimeout(() => {
                            errorAlert.style.display = 'none';
                        }, 8000);
                    }
                }
            })
            .catch(error => {
                submitBtn.disabled = false;
                submitBtn.classList.remove('loading');
                submitBtn.innerHTML = '<span>Send Message</span>';
                
                errorAlert.innerHTML = '<strong>Error:</strong> There was a problem sending your message. Please try again or contact us directly at leekeezhan@gmail.com';
                errorAlert.style.display = 'block';
                errorAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                console.error('Error:', error);
            });
        }
    });
    
    function showError(elementId, message) {
        const errorElement = document.getElementById(elementId);
        const inputElement = document.getElementById(elementId.replace('Error', ''));
        
        errorElement.textContent = message;
        errorElement.style.display = 'block';
        inputElement.classList.add('error');
    }
    
    function isValidEmail(email) {
        const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }
});
</script>

<?php include_once __DIR__ . '/../../_foot.php'; ?>