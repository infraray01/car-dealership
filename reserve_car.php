<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers");

include 'db_connect.php';

// Log the request
file_put_contents('debug.log', "Request received: " . file_get_contents('php://input') . "\n", FILE_APPEND);

// Get raw JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    echo json_encode(["status" => "error", "message" => "Invalid input data"]);
    exit;
}

// Log the received data
file_put_contents('debug.log', "Received data: " . print_r($data, true) . "\n", FILE_APPEND);

// Extract data
$session_id = $data['session_id'] ?? null;
$car_id = $data['car_id'] ?? null;
$pickup_location = $data['pickup_location'] ?? null;
$pickup_date = $data['pickup_date'] ?? null;
$pickup_time = $data['pickup_time'] ?? null;
$additional_message = $data['additional_message'] ?? null;
$payment_method = $data['payment_method'] ?? null;

// Validate required fields
if (!$session_id || !$car_id || !$pickup_location || !$pickup_date || !$pickup_time || !$payment_method) {
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit;
}

// Fetch user ID from session
$sql = "SELECT user_id FROM sessions WHERE session_id = '$session_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $user_id = $user['user_id'];

    // Insert into the purchased table
    $sql = "INSERT INTO purchased (user_id, car_id, pickup_location, pickup_date, pickup_time, additional_message, payment_method, test_drive_status) 
            VALUES ('$user_id', '$car_id', '$pickup_location', '$pickup_date', '$pickup_time', '$additional_message', '$payment_method', 'pending')";

    if ($conn->query($sql)) {
        echo json_encode(["status" => "success", "message" => "Reservation successful!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Reservation failed: " . $conn->error]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid session"]);
}

$conn->close();
?>