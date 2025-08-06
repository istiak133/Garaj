<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

$page_title = "Admin Login - Car Workshop";
$error_message = '';

// Direct database connection (update with your MAMP settings)
$host = 'localhost';
$username = 'root';
$password = '';  // Change if your MAMP password is different
$database = 'garaj';
$port = 3306;  // MAMP MySQL port

$conn = new mysqli($host, $username, $password, $database, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Redirect if already logged in as admin
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $admin_username = trim($_POST['username']);
    $admin_password = $_POST['password'];
    
    // Validate input
    if (empty($admin_username) || empty($admin_password)) {
        $error_message = "Please fill in all fields";
    } else {
        // Check admin credentials
        $query = "SELECT admin_id, username, full_name, password FROM admin_users WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $admin_username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $admin = $result->fetch_assoc();
            
            // Verify password (MD5)
            if (md5($admin_password) == $admin['password']) {
                // Login successful
                $_SESSION['admin_id'] = $admin['admin_id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_name'] = $admin['full_name'];
                
                // Update last login
                $update_query = "UPDATE admin_users SET last_login = NOW() WHERE admin_id = ?";
                $update_stmt = $conn->prepare($update_query);
                $update_stmt->bind_param("i", $admin['admin_id']);
                $update_stmt->execute();
                
                header("Location: dashboard.php");
                exit();
            } else {
                $error_message = "Invalid username or password";
            }
        } else {
            $error_message = "Invalid username or password";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Your dark theme CSS here - copying from the main style.css */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.6;
            color: #e4e4e7;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 100%);
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .form-container {
            max-width: 500px;
            margin: 2rem auto;
            background: rgba(17, 17, 19, 0.9);
            backdrop-filter: blur(10px);
            padding: 2.5rem;
            border-radius: 16px;
            border: 1px solid #27272a;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .form-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .gradient-text {
            background: linear-gradient(135deg, #60a5fa 0%, #a78bfa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #f4f4f5;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 14px 16px;
            background: rgba(39, 39, 42, 0.8);
            border: 1px solid #404040;
            border-radius: 8px;
            color: #f4f4f5;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #60a5fa;
            box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.1);
        }

        .btn {
            display: inline-block;
            padding: 14px 28px;
            background: linear-gradient(135deg, #60a5fa 0%, #a78bfa 100%);
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }

        .btn-full {
            width: 100%;
            text-align: center;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(96, 165, 250, 0.3);
        }

        .alert {
            padding: 16px 20px;
            margin-bottom: 1.5rem;
            border-radius: 8px;
            text-align: center;
            border: 1px solid;
            font-weight: 500;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            color: #fca5a5;
            border-color: rgba(239, 68, 68, 0.3);
        }

        .text-center {
            text-align: center;
        }

        .text-center a {
            color: #60a5fa;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .text-center a:hover {
            color: #3b82f6;
            text-decoration: underline;
        }

        .loading {
            border: 3px solid rgba(96, 165, 250, 0.2);
            border-top: 3px solid #60a5fa;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            display: inline-block;
            margin-left: 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div style="padding-top: 5rem;">
        <div class="container">
            <div class="form-container">
                <div class="form-header">
                    <h2 class="gradient-text">Admin Login</h2>
                    <p>Access the workshop management dashboard</p>
                </div>
                
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-error">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" id="adminLoginForm">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            required 
                            placeholder="Enter your admin username"
                            value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required 
                            placeholder="Enter your password"
                        >
                    </div>
                    
                    <button type="submit" class="btn btn-full">
                        <span class="btn-text">Login to Dashboard</span>
                        <div class="loading" style="display: none;"></div>
                    </button>
                </form>
                
                <div class="text-center" style="margin-top: 1.5rem;">
                    <p>
                        <a href="../index.php">← Back to Main Site</a>
                    </p>
                    <p style="margin-top: 1rem; font-size: 0.9rem; color: #71717a;">
                        Demo Credentials: <strong>admin</strong> / <strong>admin123</strong>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('adminLoginForm').addEventListener('submit', function() {
        const button = this.querySelector('.btn');
        const buttonText = button.querySelector('.btn-text');
        const loading = button.querySelector('.loading');
        
        button.disabled = true;
        buttonText.style.display = 'none';
        loading.style.display = 'inline-block';
    });
    </script>
</body>
</html>