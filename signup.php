<?php
session_start();
require_once 'config/database.php';

$page_title = "Sign Up - Car Workshop";
$error_message = '';
$success_message = '';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    // Validation
    if (empty($full_name) || empty($email) || empty($phone) || empty($address) || empty($password)) {
        $error_message = "Please fill in all fields.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error_message = "Password must be at least 6 characters long.";
    } else {
        // Check if email already exists
        $check_query = "SELECT user_id FROM users WHERE email = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $error_message = "Email address is already registered.";
        } else {
            // Check if phone already exists
            $phone_check_query = "SELECT user_id FROM users WHERE phone = ?";
            $phone_check_stmt = $conn->prepare($phone_check_query);
            $phone_check_stmt->bind_param("s", $phone);
            $phone_check_stmt->execute();
            $phone_check_result = $phone_check_stmt->get_result();
            
            if ($phone_check_result->num_rows > 0) {
                $error_message = "Phone number is already registered.";
            } else {
                // Insert new user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $insert_query = "INSERT INTO users (full_name, email, phone, address, password) VALUES (?, ?, ?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_query);
                $insert_stmt->bind_param("sssss", $full_name, $email, $phone, $address, $hashed_password);
                
                if ($insert_stmt->execute()) {
                    $success_message = "Account created successfully! You can now login.";
                    // Clear form data
                    $_POST = array();
                } else {
                    $error_message = "Error creating account. Please try again.";
                }
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1 class="auth-title">Create Account</h1>
            <p class="auth-subtitle">Join our GARAJ</p>
        </div>
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" class="auth-form">
            <div class="form-group">
                <label for="full_name" class="form-label">Full Name</label>
                <input 
                    type="text" 
                    id="full_name" 
                    name="full_name" 
                    class="form-input" 
                    value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>"
                    required
                    placeholder="Enter your full name"
                >
            </div>
            
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-input" 
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                    required
                    placeholder="Enter your email"
                >
            </div>
            
            <div class="form-group">
                <label for="phone" class="form-label">Phone Number</label>
                <input 
                    type="tel" 
                    id="phone" 
                    name="phone" 
                    class="form-input" 
                    value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                    required
                    placeholder="+8801XXXXXXXXX"
                    pattern="01[3-9][0-9]{8}"
                    title="Please enter a valid Bangladesh phone number (01XXXXXXXXX)"
                >
            </div>
            
            <div class="form-group">
                <label for="address" class="form-label">Address</label>
                <textarea 
                    id="address" 
                    name="address" 
                    class="form-input" 
                    rows="3"
                    required
                    placeholder="Enter your full address"
                ><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-input" 
                    required
                    minlength="6"
                    placeholder="Enter password (min 6 characters)"
                >
            </div>
            
            <div class="form-group">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input 
                    type="password" 
                    id="confirm_password" 
                    name="confirm_password" 
                    class="form-input" 
                    required
                    placeholder="Confirm your password"
                >
            </div>
            
            <button type="submit" class="btn btn-primary btn-full">
                Create Account
            </button>
        </form>
        
        <div class="auth-footer">
            <p>Already have an account? <a href="login.php" class="auth-link">Sign in here</a></p>
        </div>
    </div>
</div>

<script>
// Password confirmation validation
document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    if (password !== confirmPassword) {
        this.setCustomValidity('Passwords do not match');
    } else {
        this.setCustomValidity('');
    }
});
</script>

<?php include 'includes/footer.php'; ?>