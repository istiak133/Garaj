<?php
session_start();
require_once 'config/database.php';

$page_title = "Dashboard - Car Workshop";
$error_message = '';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$user_query = "SELECT full_name, email, phone, address FROM users WHERE user_id = ?";
$user_stmt = $conn->prepare($user_query);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();

// Fetch appointments
$appointments_query = "SELECT appointment_id, car_license_number, car_engine_number, appointment_date, m.name AS mechanic_name 
                      FROM appointments a 
                      JOIN mechanics m ON a.mechanic_id = m.mechanic_id 
                      WHERE a.user_id = ? AND a.status = 'Active' 
                      ORDER BY appointment_date ASC";
$appointments_stmt = $conn->prepare($appointments_query);
$appointments_stmt->bind_param("i", $user_id);
$appointments_stmt->execute();
$appointments_result = $appointments_stmt->get_result();

include 'includes/header.php';
?>

<div class="form-container">
    <h2>Welcome, <?php echo htmlspecialchars($user['full_name']); ?>!</h2>
    <div class="profile-details">
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
        <p><strong>Address:</strong> <?php echo htmlspecialchars($user['address']); ?></p>
    </div>

    <h3>Your Upcoming Appointments</h3>
    <?php if ($appointments_result->num_rows > 0): ?>
        <table class="appointment-table">
            <thead>
                <tr>
                    <th>Appointment ID</th>
                    <th>Car License</th>
                    <th>Engine Number</th>
                    <th>Date</th>
                    <th>Mechanic</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($appointment = $appointments_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($appointment['appointment_id']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['car_license_number']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['car_engine_number']); ?></td>
                        <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($appointment['appointment_date']))); ?></td>
                        <td><?php echo htmlspecialchars($appointment['mechanic_name']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-appointments">You have no upcoming appointments.</p>
    <?php endif; ?>

    <a href="booking.php" class="btn btn-primary btn-full">Book New Appointment</a>
</div>

<?php include 'includes/footer.php'; ?>