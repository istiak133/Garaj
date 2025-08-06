<?php
session_start();
require_once 'config/database.php';

$page_title = "GARAJ - Login";
$error_message = '';
$success_message = '';

// Check for logout success message
if (isset($_GET['logout']) && $_GET['logout'] == 'success') {
    $success_message = "You have been logged out successfully.";
}

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    // Basic validation
    if (empty($email) || empty($password)) {
        $error_message = "Please fill in all fields.";
    } else {
        // Check user credentials
        $query = "SELECT user_id, full_name, email, password FROM users WHERE email = ? AND status = 'Active'";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            // Verify password (using MD5 as per your database setup)
            if (md5($password) === $user['password']) {
                // Set session variables
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_email'] = $user['email'];
                
                // Update last login
                $update_query = "UPDATE users SET last_login = NOW() WHERE user_id = ?";
                $update_stmt = $conn->prepare($update_query);
                $update_stmt->bind_param("i", $user['user_id']);
                $update_stmt->execute();
                
                // Redirect to main page
                header("Location: index.php");
                exit();
            } else {
                $error_message = "Invalid email or password.";
            }
        } else {
            $error_message = "Invalid email or password.";
        }
    }
}

include 'includes/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1 class="auth-title">Welcome Back</h1>
            <p class="auth-subtitle">Sign in to your account</p>
        </div>
        
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" class="auth-form">
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
                <label for="password" class="form-label">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-input" 
                    required
                    placeholder="Enter your password"
                >
            </div>
            
            <button type="submit" class="btn btn-primary btn-full">
                Sign In
            </button>
        </form>
        
        <div class="auth-footer">
            <p>Don't have an account? <a href="signup.php" class="auth-link">Sign up here</a></p>
        </div>
        
        <div class="demo-credentials">
            <h4>Demo Credentials:</h4>
            <p><strong>Email:</strong> ahmed@email.com</p>
            <p><strong>Password:</strong> password123</p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>