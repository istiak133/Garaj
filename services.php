<?php
require_once 'config.php';

// Get service type from URL parameter
$service = isset($_GET['service']) ? $_GET['service'] : '';

// Define service details
$services = [
    'engine' => [
        'title' => 'Engine Repair',
        'description' => 'Complete engine diagnostics, repair, and maintenance services',
        'icon' => '🔧',
        'mechanics' => [1, 2, 3] // Mechanic IDs who can handle this service
    ],
    'brake' => [
        'title' => 'Brake System',
        'description' => 'Brake pad replacement, brake fluid change, and brake system repair',
        'icon' => '🛑',
        'mechanics' => [2, 4, 5]
    ],
    'bodywork' => [
        'title' => 'Body & Paint',
        'description' => 'Body repair, painting, dent removal, and cosmetic restoration',
        'icon' => '🎨',
        'mechanics' => [3, 5]
    ],
    'electrical' => [
        'title' => 'Electrical Systems',
        'description' => 'Electrical diagnostics, wiring, battery, and electronic system repair',
        'icon' => '⚡',
        'mechanics' => [1, 4]
    ]
];

// Validate service
if (!isset($services[$service])) {
    header('Location: index.php');
    exit;
}

$current_service = $services[$service];

// Function to get mechanic availability
function getMechanicAvailability($conn, $mechanic_id, $date = null) {
    if (!$date) {
        $date = date('Y-m-d');
    }
    
    $query = "SELECT COUNT(*) as booked_slots 
              FROM appointments 
              WHERE mechanic_id = ? AND appointment_date = ? AND status != 'cancelled'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $mechanic_id, $date);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $booked_slots = $row['booked_slots'];
    $available_slots = 4 - $booked_slots;
    
    return [
        'available_slots' => $available_slots,
        'is_available' => $available_slots > 0
    ];
}

// Function to get next available date for a mechanic
function getNextAvailableDate($conn, $mechanic_id) {
    $date = date('Y-m-d');
    $max_days = 30; // Check for next 30 days
    
    for ($i = 0; $i < $max_days; $i++) {
        $check_date = date('Y-m-d', strtotime($date . " +$i days"));
        
        // Skip Sundays (assuming workshop is closed on Sundays)
        if (date('w', strtotime($check_date)) == 0) {
            continue;
        }
        
        $availability = getMechanicAvailability($conn, $mechanic_id, $check_date);
        if ($availability['is_available']) {
            return $check_date;
        }
    }
    
    return date('Y-m-d', strtotime($date . " +30 days")); // Default to 30 days later
}

// Get mechanics for this service
$mechanic_ids = implode(',', $current_service['mechanics']);
$query = "SELECT * FROM mechanics WHERE mechanic_id IN ($mechanic_ids) ORDER BY name";
$mechanics_result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $current_service['title']; ?> - AutoCare Workshop</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <!-- Service Header -->
        <section class="service-header">
            <div class="container">
                <div class="service-title">
                    <span class="service-icon"><?php echo $current_service['icon']; ?></span>
                    <h1><?php echo $current_service['title']; ?></h1>
                    <p><?php echo $current_service['description']; ?></p>
                </div>
            </div>
        </section>

        <!-- Available Mechanics -->
        <section class="mechanics-section">
            <div class="container">
                <h2 class="section-title">Available Mechanics</h2>
                
                <div class="mechanics-grid">
                    <?php while ($mechanic = $mechanics_result->fetch_assoc()): ?>
                        <?php 
                        $availability = getMechanicAvailability($conn, $mechanic['mechanic_id']);
                        $next_available = getNextAvailableDate($conn, $mechanic['mechanic_id']);
                        ?>
                        
                        <div class="mechanic-card" data-mechanic-id="<?php echo $mechanic['mechanic_id']; ?>">
                            <div class="mechanic-info">
                                <h4><?php echo htmlspecialchars($mechanic['name']); ?></h4>
                                <p class="specialization"><?php echo htmlspecialchars($mechanic['specialization']); ?></p>
                                
                                <div class="availability">
                                    <?php if ($availability['is_available']): ?>
                                        <span class="status-available">
                                            ✅ Available Today (<?php echo $availability['available_slots']; ?> slots left)
                                        </span>
                                    <?php else: ?>
                                        <span class="status-busy">❌ Fully Booked Today</span>
                                        <div class="next-available">
                                            Next available: <?php echo date('M d, Y', strtotime($next_available)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="rating">
                                    <?php
                                    // Display star rating (you can implement actual rating system later)
                                    $rating = rand(4, 5); // Temporary random rating
                                    for ($i = 0; $i < 5; $i++) {
                                        echo $i < $rating ? '⭐' : '☆';
                                    }
                                    echo " ($rating.0/5.0)";
                                    ?>
                                </div>
                                
                                <div class="mechanic-actions">
                                    <button class="btn btn-primary book-appointment" 
                                            data-mechanic-id="<?php echo $mechanic['mechanic_id']; ?>"
                                            data-service="<?php echo $service; ?>"
                                            onclick="bookAppointment(this)">
                                        Book Appointment
                                    </button>
                                    
                                    <button class="btn btn-secondary check-availability" 
                                            data-mechanic-id="<?php echo $mechanic['mechanic_id']; ?>"
                                            onclick="checkAvailability(this)">
                                        Check Other Dates
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

                <!-- Date Picker Modal -->
                <div id="datePickerModal" class="modal" style="display: none;">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3>Select Appointment Date</h3>
                            <span class="close" onclick="closeDatePicker()">&times;</span>
                        </div>
                        <div class="modal-body">
                            <div class="date-picker">
                                <label for="appointmentDate">Choose Date:</label>
                                <input type="date" id="appointmentDate" min="<?php echo date('Y-m-d'); ?>" 
                                       max="<?php echo date('Y-m-d', strtotime('+30 days')); ?>">
                                <div id="dateAvailability" class="availability-info"></div>
                            </div>
                            <button class="btn btn-primary" onclick="proceedToBooking()">
                                Proceed to Booking
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Emergency Service Notice -->
                <div class="emergency-notice">
                    <h3>🚨 Emergency Service Available</h3>
                    <p>Need urgent repair? Call us at <strong>+880-1234-567890</strong> for immediate assistance.</p>
                </div>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script src="js/script.js"></script>
    <script>
        let selectedMechanicId = null;
        let selectedService = '<?php echo $service; ?>';

        function bookAppointment(button) {
            // Check if user is logged in
            <?php if (!isset($_SESSION['user_id'])): ?>
                alert('Please login to book an appointment');
                window.location.href = 'login.php?redirect=services.php?service=<?php echo $service; ?>';
                return;
            <?php endif; ?>

            selectedMechanicId = button.getAttribute('data-mechanic-id');
            
            // Check today's availability first
            checkMechanicAvailability(selectedMechanicId, '<?php echo date('Y-m-d'); ?>', function(isAvailable) {
                if (isAvailable) {
                    // Directly proceed to booking for today
                    window.location.href = `booking.php?mechanic_id=${selectedMechanicId}&service=${selectedService}&date=<?php echo date('Y-m-d'); ?>`;
                } else {
                    // Show date picker
                    document.getElementById('datePickerModal').style.display = 'block';
                    document.getElementById('appointmentDate').value = '';
                }
            });
        }

        function checkAvailability(button) {
            selectedMechanicId = button.getAttribute('data-mechanic-id');
            document.getElementById('datePickerModal').style.display = 'block';
        }

        function closeDatePicker() {
            document.getElementById('datePickerModal').style.display = 'none';
            selectedMechanicId = null;
        }

        function checkMechanicAvailability(mechanicId, date, callback) {
            fetch('ajax/check_availability.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    mechanic_id: mechanicId,
                    date: date
                })
            })
            .then(response => response.json())
            .then(data => {
                callback(data.available);
                
                // Update availability display
                const availabilityDiv = document.getElementById('dateAvailability');
                if (data.available) {
                    availabilityDiv.innerHTML = `<span class="status-available">✅ ${data.available_slots} slots available</span>`;
                } else {
                    availabilityDiv.innerHTML = `<span class="status-busy">❌ Fully booked on this date</span>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                callback(false);
            });
        }

        function proceedToBooking() {
            const selectedDate = document.getElementById('appointmentDate').value;
            
            if (!selectedDate) {
                alert('Please select a date');
                return;
            }

            if (!selectedMechanicId) {
                alert('Please select a mechanic');
                return;
            }

            // Check if user is logged in
            <?php if (!isset($_SESSION['user_id'])): ?>
                alert('Please login to book an appointment');
                window.location.href = `login.php?redirect=booking.php?mechanic_id=${selectedMechanicId}&service=${selectedService}&date=${selectedDate}`;
                return;
            <?php endif; ?>

            // Check availability for selected date
            checkMechanicAvailability(selectedMechanicId, selectedDate, function(isAvailable) {
                if (isAvailable) {
                    window.location.href = `booking.php?mechanic_id=${selectedMechanicId}&service=${selectedService}&date=${selectedDate}`;
                } else {
                    alert('Sorry, this mechanic is not available on the selected date. Please choose another date.');
                }
            });
        }

        // Date picker change handler
        document.getElementById('appointmentDate').addEventListener('change', function() {
            const selectedDate = this.value;
            if (selectedDate && selectedMechanicId) {
                checkMechanicAvailability(selectedMechanicId, selectedDate, function() {
                    // Callback handled in checkMechanicAvailability function
                });
            }
        });

        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('datePickerModal');
            if (event.target === modal) {
                closeDatePicker();
            }
        });
    </script>
</body>
</html>