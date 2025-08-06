<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Car Workshop - Appointment System'; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <header class="header">
        <div class="container">
            <h1><i class="fas fa-wrench"></i>GARAJ</h1>
            <nav class="nav">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="index.php"><i class="fas fa-home"></i> Home</a>
                    <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                    <a href="signup.php"><i class="fas fa-user-plus"></i> Sign Up</a>
                <?php else: ?>
                    <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    <a href="booking.php"><i class="fas fa-calendar-plus"></i> Book Appointment</a>
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    <span style="margin-left: 20px; color: #60a5fa; font-weight: 500;">
                        <i class="fas fa-user-circle"></i> Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!
                    </span>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    
    <main class="main-content">
        <div class="container">
            