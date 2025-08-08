<?php
// Start output buffering to handle any premature output
ob_start();
session_start();
require_once 'config/database.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$page_title = "Sign Up - GARAJ";
$error_message = '';
$success_message = '';
$validation_errors = [];

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Enhanced validation functions
function validate_name($name) {
    return !empty(trim($name)) && strlen(trim($name)) >= 2 && preg_match("/^[a-zA-Z\s]+$/", trim($name));
}

function validate_email_format($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validate_phone_bd($phone) {
    // Remove spaces, dashes, and plus signs
    $phone = preg_replace('/[\s\-\+]/', '', $phone);
    // Check Bangladesh mobile format: 01XXXXXXXXX
    return preg_match('/^01[3-9]\d{8}$/', $phone);
}

function validate_password_strength($password) {
    return strlen($password) >= 6;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    // Comprehensive validation
    if (empty($full_name)) {
        $validation_errors['full_name'] = "Full name is required.";
    } elseif (!validate_name($full_name)) {
        $validation_errors['full_name'] = "Please enter a valid name (letters and spaces only, minimum 2 characters).";
    }
    
    if (empty($email)) {
        $validation_errors['email'] = "Email address is required.";
    } elseif (!validate_email_format($email)) {
        $validation_errors['email'] = "Please enter a valid email address.";
    }
    
    if (empty($phone)) {
        $validation_errors['phone'] = "Phone number is required.";
    } elseif (!validate_phone_bd($phone)) {
        $validation_errors['phone'] = "Please enter a valid Bangladesh mobile number (01XXXXXXXXX).";
    }
    
    if (empty($address)) {
        $validation_errors['address'] = "Address is required.";
    } elseif (strlen($address) < 10) {
        $validation_errors['address'] = "Please provide a detailed address (minimum 10 characters).";
    }
    
    if (empty($password)) {
        $validation_errors['password'] = "Password is required.";
    } elseif (!validate_password_strength($password)) {
        $validation_errors['password'] = "Password must be at least 6 characters long.";
    }
    
    if (empty($confirm_password)) {
        $validation_errors['confirm_password'] = "Please confirm your password.";
    } elseif ($password !== $confirm_password) {
        $validation_errors['confirm_password'] = "Passwords do not match.";
    }
    
    // If no validation errors, proceed with database checks
    if (empty($validation_errors)) {
        // Normalize phone number (remove any formatting)
        $phone = preg_replace('/[\s\-\+]/', '', $phone);
        
        // Check if email already exists
        $check_email_query = "SELECT user_id FROM users WHERE email = ?";
        $check_email_stmt = $conn->prepare($check_email_query);
        
        if ($check_email_stmt === false) {
            $error_message = "Database error occurred. Please try again.";
        } else {
            $check_email_stmt->bind_param("s", $email);
            $check_email_stmt->execute();
            $email_result = $check_email_stmt->get_result();
            
            if ($email_result->num_rows > 0) {
                $validation_errors['email'] = "This email address is already registered.";
            }
            
            // Check if phone already exists
            $check_phone_query = "SELECT user_id FROM users WHERE phone = ?";
            $check_phone_stmt = $conn->prepare($check_phone_query);
            
            if ($check_phone_stmt === false) {
                $error_message = "Database error occurred. Please try again.";
            } else {
                $check_phone_stmt->bind_param("s", $phone);
                $check_phone_stmt->execute();
                $phone_result = $check_phone_stmt->get_result();
                
                if ($phone_result->num_rows > 0) {
                    $validation_errors['phone'] = "This phone number is already registered.";
                }
                
                // If still no errors, create the account
                if (empty($validation_errors) && empty($error_message)) {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $insert_query = "INSERT INTO users (full_name, email, phone, address, password, status, created_at) VALUES (?, ?, ?, ?, ?, 'Active', NOW())";
                    $insert_stmt = $conn->prepare($insert_query);
                    
                    if ($insert_stmt === false) {
                        $error_message = "Database error occurred. Please try again.";
                    } else {
                        $insert_stmt->bind_param("sssss", $full_name, $email, $phone, $address, $hashed_password);
                        
                        if ($insert_stmt->execute()) {
                            // Success! Set session message and redirect
                            $_SESSION['signup_success'] = "Account created successfully! Welcome to GARAJ. Please log in with your credentials.";
                            
                            // Redirect to login page
                            header("Location: login.php");
                            exit();
                        } else {
                            $error_message = "Failed to create account. Please try again.";
                        }
                    }
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
            <p class="auth-subtitle">Join GARAJ for professional car service</p>
        </div>
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="" class="auth-form" id="signupForm" novalidate>
            <div class="form-group">
                <label for="full_name" class="form-label">
                    <i class="fas fa-user"></i> Full Name
                </label>
                <input 
                    type="text" 
                    id="full_name" 
                    name="full_name" 
                    class="form-input <?php echo isset($validation_errors['full_name']) ? 'error' : ''; ?>" 
                    value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>"
                    required
                    placeholder="Enter your full name"
                    autocomplete="name"
                >
                <?php if (isset($validation_errors['full_name'])): ?>
                    <div class="field-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?php echo $validation_errors['full_name']; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="email" class="form-label">
                    <i class="fas fa-envelope"></i> Email Address
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-input <?php echo isset($validation_errors['email']) ? 'error' : ''; ?>" 
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                    required
                    placeholder="Enter your email address"
                    autocomplete="email"
                >
                <?php if (isset($validation_errors['email'])): ?>
                    <div class="field-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?php echo $validation_errors['email']; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="phone" class="form-label">
                    <i class="fas fa-phone"></i> Phone Number
                </label>
                <input 
                    type="tel" 
                    id="phone" 
                    name="phone" 
                    class="form-input <?php echo isset($validation_errors['phone']) ? 'error' : ''; ?>" 
                    value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                    required
                    placeholder="01XXXXXXXXX"
                    pattern="01[3-9][0-9]{8}"
                    autocomplete="tel"
                >
                <small class="form-hint">Bangladesh mobile number format: 01XXXXXXXXX</small>
                <?php if (isset($validation_errors['phone'])): ?>
                    <div class="field-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?php echo $validation_errors['phone']; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="address" class="form-label">
                    <i class="fas fa-map-marker-alt"></i> Address
                </label>
                <textarea 
                    id="address" 
                    name="address" 
                    class="form-input <?php echo isset($validation_errors['address']) ? 'error' : ''; ?>" 
                    rows="3"
                    required
                    placeholder="Enter your complete address"
                    autocomplete="street-address"
                ><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                <?php if (isset($validation_errors['address'])): ?>
                    <div class="field-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?php echo $validation_errors['address']; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="password" class="form-label">
                    <i class="fas fa-lock"></i> Password
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-input <?php echo isset($validation_errors['password']) ? 'error' : ''; ?>" 
                    required
                    minlength="6"
                    placeholder="Create a strong password (min 6 characters)"
                    autocomplete="new-password"
                >
                <div class="password-strength" id="passwordStrength" style="display: none;">
                    <div class="strength-bar">
                        <div class="strength-fill" id="strengthFill"></div>
                    </div>
                    <span class="strength-text" id="strengthText"></span>
                </div>
                <?php if (isset($validation_errors['password'])): ?>
                    <div class="field-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?php echo $validation_errors['password']; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="confirm_password" class="form-label">
                    <i class="fas fa-lock"></i> Confirm Password
                </label>
                <input 
                    type="password" 
                    id="confirm_password" 
                    name="confirm_password" 
                    class="form-input <?php echo isset($validation_errors['confirm_password']) ? 'error' : ''; ?>" 
                    required
                    placeholder="Confirm your password"
                    autocomplete="new-password"
                >
                <div class="password-match" id="passwordMatch" style="display: none;"></div>
                <?php if (isset($validation_errors['confirm_password'])): ?>
                    <div class="field-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?php echo $validation_errors['confirm_password']; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <button type="submit" class="btn btn-primary btn-full" id="submitBtn">
                <span class="btn-text">
                    <i class="fas fa-user-plus"></i> Create Account
                </span>
                <div class="loading" style="display: none;">
                    <i class="fas fa-spinner fa-spin"></i> Creating Account...
                </div>
            </button>
        </form>
        
        <div class="auth-footer">
            <p>Already have an account? <a href="login.php" class="auth-link">Sign in here</a></p>
            <p><a href="admin/login.php" class="auth-link" style="font-size: 0.9em; color: #71717a;">Admin Login</a></p>
        </div>
    </div>
</div>

<style>
/* Enhanced form styles */
.form-input.error {
    border-color: #ef4444;
    background: rgba(239, 68, 68, 0.1);
}

.field-error {
    color: #f87171;
    font-size: 0.875rem;
    margin-top: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-hint {
    color: #71717a;
    font-size: 0.75rem;
    margin-top: 0.25rem;
    display: block;
}

.form-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
    color: #d1d5db;
    font-weight: 500;
}

.password-strength {
    margin-top: 0.5rem;
}

.strength-bar {
    height: 4px;
    background: #27272a;
    border-radius: 2px;
    overflow: hidden;
}

.strength-fill {
    height: 100%;
    width: 0%;
    transition: width 0.3s ease, background-color 0.3s ease;
    border-radius: 2px;
}

.strength-text {
    font-size: 0.75rem;
    margin-top: 0.25rem;
    display: block;
}

.password-match {
    font-size: 0.875rem;
    margin-top: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.loading {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('signupForm');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const phoneInput = document.getElementById('phone');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = submitBtn.querySelector('.btn-text');
    const loading = submitBtn.querySelector('.loading');

    // Password strength checker
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        const strengthIndicator = document.getElementById('passwordStrength');
        const strengthFill = document.getElementById('strengthFill');
        const strengthText = document.getElementById('strengthText');

        if (password.length > 0) {
            strengthIndicator.style.display = 'block';
            
            let strength = 0;
            let strengthLabel = '';
            let strengthColor = '';

            if (password.length >= 6) strength += 25;
            if (password.match(/[a-z]/)) strength += 25;
            if (password.match(/[A-Z]/)) strength += 25;
            if (password.match(/[0-9]/)) strength += 25;

            if (strength <= 25) {
                strengthLabel = 'Weak';
                strengthColor = '#ef4444';
            } else if (strength <= 50) {
                strengthLabel = 'Fair';
                strengthColor = '#f59e0b';
            } else if (strength <= 75) {
                strengthLabel = 'Good';
                strengthColor = '#10b981';
            } else {
                strengthLabel = 'Strong';
                strengthColor = '#059669';
            }

            strengthFill.style.width = strength + '%';
            strengthFill.style.backgroundColor = strengthColor;
            strengthText.textContent = strengthLabel;
            strengthText.style.color = strengthColor;
        } else {
            strengthIndicator.style.display = 'none';
        }

        checkPasswordMatch();
    });

    // Password match checker
    function checkPasswordMatch() {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        const matchIndicator = document.getElementById('passwordMatch');

        if (confirmPassword.length > 0) {
            matchIndicator.style.display = 'block';
            
            if (password === confirmPassword) {
                matchIndicator.innerHTML = '<i class="fas fa-check-circle" style="color: #10b981;"></i> <span style="color: #10b981;">Passwords match</span>';
            } else {
                matchIndicator.innerHTML = '<i class="fas fa-times-circle" style="color: #ef4444;"></i> <span style="color: #ef4444;">Passwords do not match</span>';
            }
        } else {
            matchIndicator.style.display = 'none';
        }
    }

    confirmPasswordInput.addEventListener('input', checkPasswordMatch);

    // Phone number formatting
    phoneInput.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        if (value.length > 11) {
            value = value.substring(0, 11);
        }
        this.value = value;
    });

    // Form submission with loading state
    form.addEventListener('submit', function(e) {
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

    // Real-time validation
    const inputs = form.querySelectorAll('input, textarea');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            this.classList.remove('error');
            const errorDiv = this.parentNode.querySelector('.field-error');
            if (errorDiv && !errorDiv.classList.contains('server-error')) {
                errorDiv.remove();
            }
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>
<?php ob_end_flush(); ?>