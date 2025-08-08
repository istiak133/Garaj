<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoCare Workshop - Professional Car Repair Services</title>
    <meta name="description" content="Professional car repair services including engine repair, brake system, body paint, and electrical systems. Book appointments with our skilled mechanics.">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <!-- Hero Section -->
        <section class="hero">
            <div class="container">
                <h1>Professional Car Care Services</h1>
                <p>Expert mechanics, quality service, and reliable repairs. Your car deserves the best care from our experienced team of professionals.</p>
                <div class="hero-buttons">
                    <a href="#services" class="btn btn-primary">View Services</a>
                    <a href="tel:+8801234567890" class="btn btn-secondary">📞 Call Now</a>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section class="services" id="services">
            <div class="container">
                <h2 class="section-title">Our Professional Services</h2>
                <p class="section-subtitle">Choose from our specialized car repair services. Each service is handled by certified mechanics with years of experience.</p>
                
                <div class="services-grid">
                    <!-- Engine Repair Service -->
                    <div class="service-card" data-service="engine">
                        <div class="service-icon">🔧</div>
                        <h3>Engine Repair</h3>
                        <p>Complete engine diagnostics, repair, and maintenance. From minor tune-ups to major overhauls, our engine specialists have you covered.</p>
                        <ul class="service-features">
                            <li>Engine Diagnostics</li>
                            <li>Oil Change & Filter Replacement</li>
                            <li>Timing Belt Replacement</li>
                            <li>Engine Overhaul</li>
                        </ul>
                        <div class="service-price">Starting from ৳5,000</div>
                        <a href="services.php?service=engine" class="btn btn-primary">
                            View Available Mechanics
                        </a>
                    </div>

                    <!-- Brake System Service -->
                    <div class="service-card" data-service="brake">
                        <div class="service-icon">🛑</div>
                        <h3>Brake System</h3>
                        <p>Comprehensive brake services to ensure your safety. From brake pad replacement to complete brake system overhaul.</p>
                        <ul class="service-features">
                            <li>Brake Pad Replacement</li>
                            <li>Brake Fluid Change</li>
                            <li>Brake Disc Repair</li>
                            <li>ABS System Service</li>
                        </ul>
                        <div class="service-price">Starting from ৳3,500</div>
                        <a href="services.php?service=brake" class="btn btn-primary">
                            View Available Mechanics
                        </a>
                    </div>

                    <!-- Body & Paint Service -->
                    <div class="service-card" data-service="bodywork">
                        <div class="service-icon">🎨</div>
                        <h3>Body & Paint</h3>
                        <p>Professional body repair and painting services. From minor scratches to complete paint jobs and accident damage repair.</p>
                        <ul class="service-features">
                            <li>Scratch Removal</li>
                            <li>Dent Repair</li>
                            <li>Full Body Paint</li>
                            <li>Color Matching</li>
                        </ul>
                        <div class="service-price">Starting from ৳8,000</div>
                        <a href="services.php?service=bodywork" class="btn btn-primary">
                            View Available Mechanics
                        </a>
                    </div>

                    <!-- Electrical Systems Service -->
                    <div class="service-card" data-service="electrical">
                        <div class="service-icon">⚡</div>
                        <h3>Electrical Systems</h3>
                        <p>Expert electrical diagnostics and repair. Battery replacement, wiring issues, and electronic system troubleshooting.</p>
                        <ul class="service-features">
                            <li>Battery Replacement</li>
                            <li>Wiring Repair</li>
                            <li>Electronic Diagnostics</li>
                            <li>Alternator Service</li>
                        </ul>
                        <div class="service-price">Starting from ৳2,500</div>
                        <a href="services.php?service=electrical" class="btn btn-primary">
                            View Available Mechanics
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Why Choose Us Section -->
        <section class="why-choose-us">
            <div class="container">
                <h2 class="section-title">Why Choose AutoCare Workshop?</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">👨‍🔧</div>
                        <h4>Expert Mechanics</h4>
                        <p>Certified professionals with years of experience in automotive repair and maintenance.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">⚡</div>
                        <h4>Quick Service</h4>
                        <p>Efficient service delivery without compromising on quality. Most repairs completed same day.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">💯</div>
                        <h4>Quality Guarantee</h4>
                        <p>All our work comes with warranty. We stand behind our repairs and services.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">📱</div>
                        <h4>Easy Booking</h4>
                        <p>Book appointments online at your convenience. Choose your preferred mechanic and time slot.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Statistics Section -->
        <section class="statistics">
            <div class="container">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number">500+</div>
                        <div class="stat-label">Happy Customers</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">5</div>
                        <div class="stat-label">Expert Mechanics</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">1000+</div>
                        <div class="stat-label">Services Completed</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">24/7</div>
                        <div class="stat-label">Emergency Support</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works Section -->
        <section class="how-it-works">
            <div class="container">
                <h2 class="section-title">How It Works</h2>
                <div class="steps-grid">
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <h4>Choose Service</h4>
                        <p>Select the type of service your car needs from our available options.</p>
                    </div>
                    <div class="step-card">
                        <div class="step-number">2</div>
                        <h4>Pick Mechanic</h4>
                        <p>Choose from available mechanics based on their specialization and availability.</p>
                    </div>
                    <div class="step-card">
                        <div class="step-number">3</div>
                        <h4>Book Appointment</h4>
                        <p>Select your preferred date and time slot for the service appointment.</p>
                    </div>
                    <div class="step-card">
                        <div class="step-number">4</div>
                        <h4>Get Service</h4>
                        <p>Visit our workshop at the scheduled time and get professional service.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Emergency Service Banner -->
        <section class="emergency-banner">
            <div class="container">
                <div class="emergency-content">
                    <div class="emergency-icon">🚨</div>
                    <div class="emergency-text">
                        <h3>Emergency Car Service Available</h3>
                        <p>Need immediate assistance? Our emergency team is available 24/7 for urgent repairs.</p>
                    </div>
                    <div class="emergency-action">
                        <a href="tel:+8801234567890" class="btn btn-emergency">
                            Call Emergency: +880-1234-567890
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="js/script.js"></script>
    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
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

        // Service card hover effects
        document.querySelectorAll('.service-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Counter animation for statistics
        function animateCounters() {
            const counters = document.querySelectorAll('.stat-number');
            
            counters.forEach(counter => {
                const target = parseInt(counter.textContent);
                const increment = target / 100;
                let current = 0;
                
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        counter.textContent = target + (counter.textContent.includes('+') ? '+' : '');
                        clearInterval(timer);
                    } else {
                        counter.textContent = Math.floor(current) + (counter.textContent.includes('+') ? '+' : '');
                    }
                }, 20);
            });
        }

        // Trigger counter animation when statistics section is visible
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounters();
                    observer.unobserve(entry.target);
                }
            });
        });

        const statsSection = document.querySelector('.statistics');
        if (statsSection) {
            observer.observe(statsSection);
        }
    </script>
</body>
</html>