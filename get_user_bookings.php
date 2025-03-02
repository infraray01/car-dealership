<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers");

include 'db_connect.php';

// Log the request
file_put_contents('debug.log', "Request received: " . file_get_contents('php://input') . "\n", FILE_APPEND);

$session_id = $_GET['session_id'];

if (!$session_id) {
    echo json_encode(["status" => "error", "message" => "Session ID is missing"]);
    exit;
}

// Fetch user ID from session
$sql = "SELECT user_id FROM sessions WHERE session_id = '$session_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $user_id = $user['user_id'];

    // Fetch all bookings for the user with car details
    $sql = "SELECT purchased.*, cars.name AS car_name, cars.image AS car_image, cars.price AS car_price
            FROM purchased
            JOIN cars ON purchased.car_id = cars.id
            WHERE purchased.user_id = '$user_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $bookings = [];
        while ($row = $result->fetch_assoc()) {
            $bookings[] = $row;
        }
        echo json_encode($bookings);
    } else {
        echo json_encode(["status" => "error", "message" => "No bookings found"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid session"]);
}

$conn->close();
?>