<footer>
    <div class="container">
        <div class="footer-content">
            <!-- Company Info -->
            <div class="footer-section">
                <h3>🚗 AutoCare Workshop</h3>
                <p>Your trusted partner for professional car repair and maintenance services. Quality work, reliable service, competitive prices.</p>
                <div class="social-links">
                    <a href="#" class="social-link" title="Facebook">📘</a>
                    <a href="#" class="social-link" title="Instagram">📷</a>
                    <a href="#" class="social-link" title="YouTube">📺</a>
                    <a href="#" class="social-link" title="WhatsApp">💬</a>
                </div>
            </div>

            <!-- Services -->
            <div class="footer-section">
                <h3>Our Services</h3>
                <ul class="footer-links">
                    <li><a href="services.php?service=engine">🔧 Engine Repair</a></li>
                    <li><a href="services.php?service=brake">🛑 Brake System</a></li>
                    <li><a href="services.php?service=bodywork">🎨 Body & Paint</a></li>
                    <li><a href="services.php?service=electrical">⚡ Electrical Systems</a></li>
                    <li><a href="tel:+8801234567890">🚨 Emergency Service</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="footer-section">
                <h3>Contact Information</h3>
                <div class="contact-info">
                    <div class="contact-item">
                        <span class="contact-icon">📍</span>
                        <div class="contact-details">
                            <strong>Address:</strong><br>
                            123 Workshop Street<br>
                            Dhaka-1000, Bangladesh
                        </div>
                    </div>
                    <div class="contact-item">
                        <span class="contact-icon">📞</span>
                        <div class="contact-details">
                            <strong>Phone:</strong><br>
                            <a href="tel:+8801234567890">+880-1234-567890</a>
                        </div>
                    </div>
                    <div class="contact-item">
                        <span class="contact-icon">✉️</span>
                        <div class="contact-details">
                            <strong>Email:</strong><br>
                            <a href="mailto:info@autocare.com">info@autocare.com</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Business Hours -->
            <div class="footer-section">
                <h3>Business Hours</h3>
                <div class="business-hours">
                    <div class="hours-item">
                        <span class="day">Monday - Friday</span>
                        <span class="time">8:00 AM - 6:00 PM</span>
                    </div>
                    <div class="hours-item">
                        <span class="day">Saturday</span>
                        <span class="time">9:00 AM - 5:00 PM</span>
                    </div>
                    <div class="hours-item">
                        <span class="day">Sunday</span>
                        <span class="time closed">Closed</span>
                    </div>
                    <div class="hours-item emergency">
                        <span class="day">🚨 Emergency</span>
                        <span class="time">24/7 Available</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Newsletter Signup -->
        <div class="newsletter-section">
            <div class="newsletter-content">
                <h3>Stay Updated</h3>
                <p>Subscribe to get updates about our services and special offers.</p>
                <form class="newsletter-form" onsubmit="subscribeNewsletter(event)">
                    <input type="email" placeholder="Enter your email address" required>
                    <button type="submit" class="btn btn-primary">Subscribe</button>
                </form>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="footer-bottom-content">
                <div class="copyright">
                    <p>&copy; <?php echo date('Y'); ?> AutoCare Workshop. All rights reserved.</p>
                </div>
                <div class="footer-links">
                    <a href="privacy-policy.php">Privacy Policy</a>
                    <a href="terms-of-service.php">Terms of Service</a>
                    <a href="sitemap.php">Sitemap</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<script>
// Newsletter subscription
function subscribeNewsletter(event) {
    event.preventDefault();
    const form = event.target;
    const email = form.querySelector('input[type="email"]').value;
    
    // Simple validation
    if (!email) {
        alert('Please enter your email address');
        return;
    }
    
    // Here you would typically send the email to your backend
    // For now, we'll just show a success message
    alert('Thank you for subscribing! We\'ll keep you updated with our latest news and offers.');
    form.reset();
    
    // You can implement actual newsletter subscription logic here
    // fetch('subscribe.php', {
    //     method: 'POST',
    //     headers: {'Content-Type': 'application/json'},
    //     body: JSON.stringify({email: email})
    // }).then(response => response.json()).then(data => {
    //     if (data.success) {
    //         alert('Successfully subscribed!');
    //         form.reset();
    //     } else {
    //         alert('Error: ' + data.message);
    //     }
    // });
}

// Smooth scrolling for footer links
document.querySelectorAll('.footer-links a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Social links hover effects
document.querySelectorAll('.social-link').forEach(link => {
    link.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-3px) scale(1.1)';
    });
    
    link.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0) scale(1)';
    });
});
</script>