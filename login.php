<?php
session_start();
require_once 'config/database.php';

$page_title = "Login - GARAJ";
$error_message = '';
$success_message = '';

// Check for signup success message
if (isset($_SESSION['signup_success'])) {
    $success_message = $_SESSION['signup_success'];
    unset($_SESSION['signup_success']); // Remove after displaying
}

// Check for logout success message
if (isset($_GET['logout']) && $_GET['logout'] == 'success') {
    $success_message = "You have been logged out successfully. Have a great day!";
}

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Enhanced validation functions
function validate_login_email($email) {
    return !empty(trim($email)) && filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validate_login_password($password) {
    return !empty(trim($password));
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $remember_me = isset($_POST['remember_me']) ? true : false;
    
    // Basic validation
    if (!validate_login_email($email)) {
        $error_message = "Please enter a valid email address.";
    } elseif (!validate_login_password($password)) {
        $error_message = "Please enter your password.";
    } else {
        // Check user credentials
        $query = "SELECT user_id, full_name, email, password, status, created_at, last_login FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        
        if ($stmt === false) {
            $error_message = "System error occurred. Please try again later.";
        } else {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                
                // Check if account is active
                if ($user['status'] !== 'Active') {
                    $error_message = "Your account has been deactivated. Please contact support at support@garaj.com";
                } else {
                    // Check password - handle both MD5 (old) and password_hash (new) methods
                    $password_valid = false;
                    
                    // First try password_hash method (secure)
                    if (password_verify($password, $user['password'])) {
                        $password_valid = true;
                    } 
                    // Fallback to MD5 for existing accounts (for backward compatibility)
                    elseif (md5($password) === $user['password']) {
                        $password_valid = true;
                        
                        // Upgrade password to secure hash for future logins
                        $new_hash = password_hash($password, PASSWORD_DEFAULT);
                        $upgrade_query = "UPDATE users SET password = ? WHERE user_id = ?";
                        $upgrade_stmt = $conn->prepare($upgrade_query);
                        if ($upgrade_stmt) {
                            $upgrade_stmt->bind_param("si", $new_hash, $user['user_id']);
                            $upgrade_stmt->execute();
                        }
                    }
                    
                    if ($password_valid) {
                        // Set session variables with consistent naming
                        $_SESSION['user_id'] = $user['user_id'];
                        $_SESSION['user_name'] = $user['full_name']; // For header display
                        $_SESSION['full_name'] = $user['full_name']; // For consistency
                        $_SESSION['email'] = $user['email'];
                        $_SESSION['login_time'] = time();
                        
                        // Handle "Remember Me" functionality
                        if ($remember_me) {
                            // Set secure cookie that expires in 30 days
                            $cookie_token = bin2hex(random_bytes(16));
                            setcookie('remember_token', $cookie_token, time() + (30 * 24 * 60 * 60), '/', '', true, true);
                            
                            // Store token in database (you might want to add a remember_tokens table)
                            // For now, we'll just set the cookie
                        }
                        
                        // Update last login
                        $update_query = "UPDATE users SET last_login = NOW() WHERE user_id = ?";
                        $update_stmt = $conn->prepare($update_query);
                        if ($update_stmt) {
                            $update_stmt->bind_param("i", $user['user_id']);
                            $update_stmt->execute();
                        }
                        
                        // Determine redirect based on user preference or default to dashboard
                        $redirect_url = isset($_SESSION['intended_url']) ? $_SESSION['intended_url'] : 'dashboard.php';
                        unset($_SESSION['intended_url']); // Clear intended URL
                        
                        header("Location: " . $redirect_url);
                        exit();
                    } else {
                        $error_message = "Invalid email or password. Please check your credentials and try again.";
                        
                        // Optional: Add failed login attempt tracking here
                        // You could implement account lockout after multiple failed attempts
                    }
                }
            } else {
                $error_message = "Invalid email or password. Please check your credentials and try again.";
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1 class="auth-title">Welcome Back</h1>
            <p class="auth-subtitle">Sign in to your GARAJ account</p>
        </div>
        
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" class="auth-form" id="loginForm" novalidate>
            <div class="form-group">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope"></i> Email Address
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-input" 
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                    required
                    placeholder="Enter your email address"
                    autocomplete="email"
                    autofocus
                >
                <div class="field-feedback" id="emailFeedback"></div>
            </div>
            
            <div class="form-group">
                <label for="password" class="form-label">
                    <i class="fas fa-lock"></i> Password
                </label>
                <div class="password-input-wrapper">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-input" 
                        required
                        placeholder="Enter your password"
                        autocomplete="current-password"
                    >
                    <button type="button" class="password-toggle" id="passwordToggle">
                        <i class="fas fa-eye" id="toggleIcon"></i>
                    </button>
                </div>
                <div class="field-feedback" id="passwordFeedback"></div>
            </div>
            
            <div class="form-group">
                <div class="form-options">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember_me" id="remember_me">
                        <span class="checkbox-custom"></span>
                        Remember me for 30 days
                    </label>
                    <a href="forgot-password.php" class="forgot-link">Forgot Password?</a>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary btn-full" id="submitBtn">
                <span class="btn-text">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </span>
                <div class="loading" style="display: none;">
                    <i class="fas fa-spinner fa-spin"></i> Signing In...
                </div>
            </button>
        </form>
        
        <div class="auth-footer">
            <p>Don't have an account? <a href="signup.php" class="auth-link">Sign up here</a></p>
            <div class="divider"></div>
            <p><a href="admin/login.php" class="auth-link admin-link">
                <i class="fas fa-user-shield"></i> Admin Login
            </a></p>
        </div>
        
        <div class="demo-credentials">
            <h4><i class="fas fa-info-circle"></i> Demo Account:</h4>
            <div class="demo-credential-item">
                <strong>Email:</strong> <span class="demo-value" onclick="fillDemo('email', 'ahmed@email.com')">ahmed@email.com</span>
            </div>
            <div class="demo-credential-item">
                <strong>Password:</strong> <span class="demo-value" onclick="fillDemo('password', 'password123')">password123</span>
            </div>
            <button type="button" class="demo-fill-btn" onclick="fillDemoCredentials()">
                <i class="fas fa-magic"></i> Use Demo Account
            </button>
        </div>
    </div>
</div>

<style>
/* Enhanced form styles */
.password-input-wrapper {
    position: relative;
}

.password-toggle {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #71717a;
    cursor: pointer;
    padding: 4px;
    transition: color 0.3s ease;
}

.password-toggle:hover {
    color: #60a5fa;
}

.form-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 1rem 0;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #d1d5db;
    font-size: 0.875rem;
    cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
    display: none;
}

.checkbox-custom {
    width: 18px;
    height: 18px;
    border: 2px solid #3f3f46;
    border-radius: 4px;
    background: rgba(39, 39, 42, 0.8);
    transition: all 0.3s ease;
    position: relative;
}

.checkbox-label input[type="checkbox"]:checked + .checkbox-custom {
    background: linear-gradient(135deg, #60a5fa 0%, #a78bfa 100%);
    border-color: #60a5fa;
}

.checkbox-label input[type="checkbox"]:checked + .checkbox-custom::after {
    content: "✓";
    position: absolute;
    color: white;
    font-size: 12px;
    font-weight: bold;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
}

.forgot-link {
    color: #60a5fa;
    text-decoration: none;
    font-size: 0.875rem;
    transition: color 0.3s ease;
}

.forgot-link:hover {
    color: #3b82f6;
    text-decoration: underline;
}

.field-feedback {
    font-size: 0.875rem;
    margin-top: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.field-feedback.error {
    color: #f87171;
}

.field-feedback.success {
    color: #4ade80;
}

.loading {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.divider {
    height: 1px;
    background: linear-gradient(90deg, transparent, #27272a, transparent);
    margin: 1rem 0;
}

.admin-link {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    font-size: 0.9rem;
    color: #71717a;
    padding: 0.5rem;
    border: 1px solid #27272a;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.admin-link:hover {
    color: #60a5fa;
    border-color: #60a5fa;
    background: rgba(96, 165, 250, 0.05);
}

.demo-credentials {
    margin-top: 2rem;
    padding: 1.5rem;
    background: rgba(34, 197, 94, 0.1);
    border: 1px solid rgba(34, 197, 94, 0.2);
    border-radius: 12px;
    text-align: center;
}

.demo-credentials h4 {
    color: #4ade80;
    margin-bottom: 1rem;
    font-size: 0.875rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.demo-credential-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 0.5rem 0;
    font-size: 0.875rem;
}

.demo-credential-item strong {
    color: #a7f3d0;
}

.demo-value {
    color: #d1fae5;
    cursor: pointer;
    padding: 2px 6px;
    border-radius: 4px;
    transition: all 0.3s ease;
}

.demo-value:hover {
    background: rgba(34, 197, 94, 0.2);
    color: #4ade80;
}

.demo-fill-btn {
    margin-top: 1rem;
    padding: 8px 16px;
    background: rgba(34, 197, 94, 0.2);
    border: 1px solid rgba(34, 197, 94, 0.3);
    border-radius: 6px;
    color: #4ade80;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 1rem auto 0;
}

.demo-fill-btn:hover {
    background: rgba(34, 197, 94, 0.3);
    border-color: rgba(34, 197, 94, 0.5);
}

/* Responsive adjustments */
@media (max-width: 480px) {
    .form-options {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .demo-credential-item {
        flex-direction: column;
        gap: 0.25rem;
        align-items: flex-start;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const passwordToggle = document.getElementById('passwordToggle');
    const toggleIcon = document.getElementById('toggleIcon');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = submitBtn.querySelector('.btn-text');
    const loading = submitBtn.querySelector('.loading');

    // Password visibility toggle
    passwordToggle.addEventListener('click', function() {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.className = 'fas fa-eye-slash';
        } else {
            passwordInput.type = 'password';
            toggleIcon.className = 'fas fa-eye';
        }
    });

    // Real-time email validation
    emailInput.addEventListener('blur', function() {
        const email = this.value.trim();
        const feedback = document.getElementById('emailFeedback');
        
        if (email) {
            if (validateEmail(email)) {
                feedback.innerHTML = '<i class="fas fa-check-circle"></i> Valid email format';
                feedback.className = 'field-feedback success';
                this.style.borderColor = '#10b981';
            } else {
                feedback.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Invalid email format';
                feedback.className = 'field-feedback error';
                this.style.borderColor = '#ef4444';
            }
        } else {
            feedback.innerHTML = '';
            feedback.className = 'field-feedback';
            this.style.borderColor = '#3f3f46';
        }
    });

    // Email validation function
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    // Form submission with loading state
    form.addEventListener('submit', function(e) {
        const email = emailInput.value.trim();
        const password = passwordInput.value.trim();
        
        if (!email || !password) {
            e.preventDefault();
            
            if (!email) {
                emailInput.style.borderColor = '#ef4444';
                document.getElementById('emailFeedback').innerHTML = '<i class="fas fa-exclamation-triangle"></i> Email is required';
                document.getElementById('emailFeedback').className = 'field-feedback error';
            }
            
            if (!password) {
                passwordInput.style.borderColor = '#ef4444';
                document.getElementById('passwordFeedback').innerHTML = '<i class="fas fa-exclamation-triangle"></i> Password is required';
                document.getElementById('passwordFeedback').className = 'field-feedback error';
            }
            
            return false;
        }

        // Show loading state
        submitBtn.disabled = true;
        btnText.style.display = 'none';
        loading.style.display = 'flex';
        
        // Re-enable after 5 seconds in case of slow response
        setTimeout(function() {
            submitBtn.disabled = false;
            btnText.style.display = 'flex';
            loading.style.display = 'none';
        }, 5000);
    });

    // Clear error states on input
    [emailInput, passwordInput].forEach(input => {
        input.addEventListener('focus', function() {
            this.style.borderColor = '#60a5fa';
            const feedback = this.parentNode.parentNode.querySelector('.field-feedback');
            if (feedback && feedback.classList.contains('error')) {
                feedback.innerHTML = '';
                feedback.className = 'field-feedback';
            }
        });

        input.addEventListener('blur', function() {
            if (!this.classList.contains('error')) {
                this.style.borderColor = '#3f3f46';
            }
        });
    });

    // Auto-hide success/error alerts
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(function() {
                if (alert.parentNode) {
                    alert.remove();
                }
            }, 300);
        }, 5000);
    });
});

// Demo credential functions
function fillDemo(fieldName, value) {
    document.getElementById(fieldName).value = value;
    document.getElementById(fieldName).focus();
    document.getElementById(fieldName).blur();
}

function fillDemoCredentials() {
    fillDemo('email', 'ahmed@email.com');
    setTimeout(() => fillDemo('password', 'password123'), 100);
    
    // Show notification
    const notification = document.createElement('div');
    notification.innerHTML = '<i class="fas fa-check"></i> Demo credentials filled!';
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        font-size: 14px;
        z-index: 1000;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        display: flex;
        align-items: center;
        gap: 8px;
        animation: slideIn 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease forwards';
        setTimeout(() => notification.remove(), 300);
    }, 2000);
}

// Add CSS animations for notifications
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);
</script>

<?php include 'includes/footer.php'; ?>