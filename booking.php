<?php
session_start();
require_once 'config/database.php';

$page_title = "Book Appointment - Car Workshop";
$error_message = '';
$success_message = '';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch mechanics for the dropdown
$mechanics_query = "SELECT mechanic_id, name FROM mechanics";
$mechanics_stmt = $conn->prepare($mechanics_query);
$mechanics_stmt->execute();
$mechanics_result = $mechanics_stmt->get_result();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $car_license_number = sanitize_input($_POST['car_license_number']);
    $car_engine_number = sanitize_input($_POST['car_engine_number']);
    $appointment_date = $_POST['appointment_date'];
    $mechanic_id = $_POST['mechanic_id'];

    // Basic validation
    if (empty($car_license_number) || empty($car_engine_number) || empty($appointment_date) || empty($mechanic_id)) {
        $error_message = "All fields are required.";
    } else {
        // Check availability (max 4 appointments per mechanic per day)
        $check_query = "SELECT COUNT(*) as count FROM appointments WHERE mechanic_id = ? AND DATE(appointment_date) = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("is", $mechanic_id, $appointment_date);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $appointment_count = $check_result->fetch_assoc()['count'];

        if ($appointment_count >= 4) {
            $error_message = "This mechanic is fully booked for the selected date. Please choose another date or mechanic.";
        } else {
            // Check for duplicate booking for the user on the same date
            $duplicate_query = "SELECT COUNT(*) as count FROM appointments WHERE user_id = ? AND DATE(appointment_date) = ?";
            $duplicate_stmt = $conn->prepare($duplicate_query);
            $duplicate_stmt->bind_param("is", $user_id, $appointment_date);
            $duplicate_stmt->execute();
            $duplicate_result = $duplicate_stmt->get_result();
            $duplicate_count = $duplicate_result->fetch_assoc()['count'];

            if ($duplicate_count > 0) {
                $error_message = "You already have an appointment on this date. Please choose a different date.";
            } else {
                // Insert appointment
                $insert_query = "INSERT INTO appointments (user_id, mechanic_id, car_license_number, car_engine_number, appointment_date, status) VALUES (?, ?, ?, ?, ?, 'Active')";
                $insert_stmt = $conn->prepare($insert_query);
                $insert_stmt->bind_param("iisss", $user_id, $mechanic_id, $car_license_number, $car_engine_number, $appointment_date);

                if ($insert_stmt->execute()) {
                    $success_message = "Appointment booked successfully! You can view it in your dashboard.";
                } else {
                    $error_message = "Error booking appointment. Please try again.";
                }
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="form-container">
    <h2>Book a New Appointment</h2>

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>

    <form method="POST" action="" class="auth-form">
        <div class="form-group">
            <label for="car_license_number" class="form-label">Car License Number</label>
            <input type="text" id="car_license_number" name="car_license_number" class="form-input" required placeholder="e.g., DHA-1234">
        </div>
        <div class="form-group">
            <label for="car_engine_number" class="form-label">Car Engine Number</label>
            <input type="text" id="car_engine_number" name="car_engine_number" class="form-input" required placeholder="e.g., ENG123456">
        </div>
        <div class="form-group">
            <label for="appointment_date" class="form-label">Appointment Date</label>
            <input type="date" id="appointment_date" name="appointment_date" class="form-input" required min="<?php echo date('Y-m-d'); ?>">
        </div>
        <div class="form-group">
            <label for="mechanic_id" class="form-label">Preferred Mechanic</label>
            <select id="mechanic_id" name="mechanic_id" class="form-input" required>
                <option value="">Select a Mechanic</option>
                <?php
                $mechanics_stmt->data_seek(0); // Reset pointer
                while ($mechanic = $mechanics_result->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($mechanic['mechanic_id']); ?>">
                        <?php echo htmlspecialchars($mechanic['name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary btn-full">Book Appointment</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>