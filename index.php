<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // User not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

// User is logged in, show main website
require_once 'config/database.php';

$page_title = "Car Workshop - Home";

// Get user information
$user_id = $_SESSION['user_id'];
$query = "SELECT full_name FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

include 'includes/header.php';
?>

<div class="hero-section">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">
                Welcome, <span class="gradient-text"><?php echo htmlspecialchars($user['full_name']); ?></span>!
            </h1>
            <p class="hero-subtitle">
                Professional car service at your fingertips. Book appointments with your preferred mechanic.
            </p>
            <div class="hero-buttons">
                <a href="booking.php" class="btn btn-primary">Book Appointment</a>
                <a href="dashboard.php" class="btn btn-secondary">My Dashboard</a>
            </div>
        </div>
    </div>
</div>

<div class="features-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Our Services</h2>
            <p class="section-subtitle">Expert mechanics ready to serve you</p>
        </div>
        
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">🔧</div>
                <h3>Engine Specialist</h3>
                <p>Complete engine diagnostics and repair services</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">⚙️</div>
                <h3>Transmission Expert</h3>
                <p>Professional transmission maintenance and repair</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">🛞</div>
                <h3>Brake Specialist</h3>
                <p>Complete brake system inspection and service</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">⚡</div>
                <h3>Electrical Systems</h3>
                <p>Advanced electrical diagnostics and repairs</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">🔨</div>
                <h3>General Mechanic</h3>
                <p>Complete automotive maintenance services</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">📅</div>
                <h3>Easy Booking</h3>
                <p>Book appointments with your preferred mechanic online</p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>