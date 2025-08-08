<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'garaj');

// Create database connection
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Get user info
function getCurrentUser() {
    if (isLoggedIn()) {
        $conn = getDBConnection();
        $user_id = $_SESSION['user_id'];
        
        $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
    }
    return null;
}

// Update database to include mechanic specializations
function updateMechanicSpecializations() {
    $conn = getDBConnection();
    
    $updates = [
        [1, 'Engine Specialist', 'Engine Repair'],
        [2, 'Transmission Expert', 'Brake Repair'],
        [3, 'Brake Specialist', 'Brake Repair'],
        [4, 'Electrical Systems', 'Body Color Change'],
        [5, 'General Mechanic', 'General Service']
    ];
    
    foreach ($updates as $update) {
        $stmt = $conn->prepare("UPDATE mechanics SET specialization = ?, service_type = ? WHERE mechanic_id = ?");
        if (!$stmt) {
            // If service_type column doesn't exist, create it
            $conn->query("ALTER TABLE mechanics ADD COLUMN service_type VARCHAR(100) DEFAULT 'General Service'");
            $stmt = $conn->prepare("UPDATE mechanics SET specialization = ?, service_type = ? WHERE mechanic_id = ?");
        }
        $stmt->bind_param("ssi", $update[1], $update[2], $update[0]);
        $stmt->execute();
    }
    
    $conn->close();
}

// Call this once to update the database
// updateMechanicSpecializations();
?>