<?php
// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';
$userEmail = $isLoggedIn ? $_SESSION['user_email'] : '';

// Get current page for active navigation
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<header>
    <div class="container">
        <div class="header-content">
            <!-- Logo -->
            <div class="logo">
                <a href="index.php">
                    <h1>🚗 AutoCare</h1>
                </a>
            </div>

            <!-- Navigation Menu -->
            <nav class="nav-menu">
                <ul>
                    <li><a href="index.php" class="<?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>">Home</a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle">Services ▼</a>
                        <ul class="dropdown-menu">
                            <li><a href="services.php?service=engine">🔧 Engine Repair</a></li>
                            <li><a href="services.php?service=brake">🛑 Brake System</a></li>
                            <li><a href="services.php?service=bodywork">🎨 Body & Paint</a></li>
                            <li><a href="services.php?service=electrical">⚡ Electrical Systems</a></li>
                        </ul>
                    </li>
                    <li><a href="about.php" class="<?php echo ($currentPage == 'about.php') ? 'active' : ''; ?>">About</a></li>
                    <li><a href="contact.php" class="<?php echo ($currentPage == 'contact.php') ? 'active' : ''; ?>">Contact</a></li>
                </ul>
            </nav>

            <!-- User Section -->
            <div class="user-section">
                <?php if ($isLoggedIn): ?>
                    <!-- Logged In User Menu -->
                    <div class="user-menu">
                        <div class="user-info">
                            <span class="user-avatar">👤</span>
                            <span class="user-name"><?php echo htmlspecialchars($userName); ?></span>
                            <span class="dropdown-arrow">▼</span>
                        </div>
                        <div class="user-dropdown">
                            <div class="user-dropdown-header">
                                <div class="user-details">
                                    <strong><?php echo htmlspecialchars($userName); ?></strong>
                                    <small><?php echo htmlspecialchars($userEmail); ?></small>
                                </div>
                            </div>
                            <div class="user-dropdown-menu">
                                <a href="dashboard.php" class="dropdown-item">
                                    <span class="item-icon">📊</span>
                                    Dashboard
                                </a>
                                <a href="profile.php" class="dropdown-item">
                                    <span class="item-icon">👤</span>
                                    My Profile
                                </a>
                                <a href="appointments.php" class="dropdown-item">
                                    <span class="item-icon">📅</span>
                                    My Appointments
                                </a>
                                <a href="service-history.php" class="dropdown-item">
                                    <span class="item-icon">🔧</span>
                                    Service History
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="logout.php" class="dropdown-item logout">
                                    <span class="item-icon">🚪</span>
                                    Logout
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Guest User Buttons -->
                    <div class="auth-buttons">
                        <a href="login.php" class="btn btn-secondary">Login</a>
                        <a href="signup.php" class="btn btn-primary">Sign Up</a>
                    </div>
                <?php endif; ?>

                <!-- Mobile Menu Toggle -->
                <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <nav class="mobile-nav" id="mobileNav">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li>
                    <a href="#" class="mobile-dropdown-toggle" onclick="toggleMobileDropdown(this)">
                        Services <span class="arrow">▼</span>
                    </a>
                    <ul class="mobile-dropdown">
                        <li><a href="services.php?service=engine">🔧 Engine Repair</a></li>
                        <li><a href="services.php?service=brake">🛑 Brake System</a></li>
                        <li><a href="services.php?service=bodywork">🎨 Body & Paint</a></li>
                        <li><a href="services.php?service=electrical">⚡ Electrical Systems</a></li>
                    </ul>
                </li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
                
                <?php if ($isLoggedIn): ?>
                    <li class="mobile-user-section">
                        <div class="mobile-user-info">
                            <span class="user-avatar">👤</span>
                            <span><?php echo htmlspecialchars($userName); ?></span>
                        </div>
                        <ul class="mobile-user-menu">
                            <li><a href="dashboard.php">📊 Dashboard</a></li>
                            <li><a href="profile.php">👤 My Profile</a></li>
                            <li><a href="appointments.php">📅 My Appointments</a></li>
                            <li><a href="service-history.php">🔧 Service History</a></li>
                            <li><a href="logout.php" class="logout">🚪 Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="mobile-auth-buttons">
                        <a href="login.php" class="btn btn-secondary">Login</a>
                        <a href="signup.php" class="btn btn-primary">Sign Up</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<script>
// Header JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // User dropdown toggle
    const userInfo = document.querySelector('.user-info');
    const userDropdown = document.querySelector('.user-dropdown');
    
    if (userInfo && userDropdown) {
        userInfo.addEventListener('click', function(e) {
            e.preventDefault();
            userDropdown.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!userInfo.contains(e.target) && !userDropdown.contains(e.target)) {
                userDropdown.classList.remove('active');
            }
        });
    }

    // Desktop dropdown hover effects
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    dropdownToggles.forEach(toggle => {
        const dropdown = toggle.closest('.dropdown');
        
        dropdown.addEventListener('mouseenter', function() {
            this.classList.add('active');
        });
        
        dropdown.addEventListener('mouseleave', function() {
            this.classList.remove('active');
        });
    });
});

// Mobile menu functions
function toggleMobileMenu() {
    const mobileNav = document.getElementById('mobileNav');
    const menuToggle = document.querySelector('.mobile-menu-toggle');
    
    mobileNav.classList.toggle('active');
    menuToggle.classList.toggle('active');
}

function toggleMobileDropdown(element) {
    const dropdown = element.nextElementSibling;
    const arrow = element.querySelector('.arrow');
    
    dropdown.classList.toggle('active');
    arrow.style.transform = dropdown.classList.contains('active') ? 'rotate(180deg)' : 'rotate(0deg)';
}

// Close mobile menu when clicking outside
document.addEventListener('click', function(e) {
    const mobileNav = document.getElementById('mobileNav');
    const menuToggle = document.querySelector('.mobile-menu-toggle');
    
    if (!mobileNav.contains(e.target) && !menuToggle.contains(e.target)) {
        mobileNav.classList.remove('active');
        menuToggle.classList.remove('active');
    }
});
</script>