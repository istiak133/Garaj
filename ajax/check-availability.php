<?php
require_once '../config.php';

// Set content type to JSON
header('Content-Type: application/json');

// Handle only POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($input['mechanic_id']) || !isset($input['date'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required parameters']);
    exit;
}

$mechanic_id = (int)$input['mechanic_id'];
$date = $input['date'];

// Validate date format
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid date format']);
    exit;
}

// Check if date is not in the past
if (strtotime($date) < strtotime(date('Y-m-d'))) {
    echo json_encode([
        'available' => false,
        'available_slots' => 0,
        'message' => 'Cannot book appointments for past dates'
    ]);
    exit;
}

// Check if it's a Sunday (assuming workshop is closed)
if (date('w', strtotime($date)) == 0) {
    echo json_encode([
        'available' => false,
        'available_slots' => 0,
        'message' => 'Workshop is closed on Sundays'
    ]);
    exit;
}

// Check mechanic availability
$query = "SELECT COUNT(*) as booked_slots 
          FROM appointments 
          WHERE mechanic_id = ? AND appointment_date = ? AND status != 'cancelled'";

$stmt = $conn->prepare($query);
$stmt->bind_param("is", $mechanic_id, $date);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$booked_slots = (int)$row['booked_slots'];
$available_slots = 4 - $booked_slots;
$is_available = $available_slots > 0;

// Get mechanic name for response
$mechanic_query = "SELECT name FROM mechanics WHERE mechanic_id = ?";
$mechanic_stmt = $conn->prepare($mechanic_query);
$mechanic_stmt->bind_param("i", $mechanic_id);
$mechanic_stmt->execute();
$mechanic_result = $mechanic_stmt->get_result();
$mechanic = $mechanic_result->fetch_assoc();

$response = [
    'available' => $is_available,
    'available_slots' => $available_slots,
    'booked_slots' => $booked_slots,
    'max_slots' => 4,
    'date' => $date,
    'mechanic_name' => $mechanic['name'] ?? 'Unknown',
    'message' => $is_available 
        ? "Available - $available_slots slots remaining"
        : "Fully booked - no slots available"
];

echo json_encode($response);
?>