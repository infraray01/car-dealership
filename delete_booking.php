<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers");

include 'db_connect.php';

// Get the booking ID from the query string
$booking_id = $_GET['booking_id'];

if (!$booking_id) {
    echo json_encode(["status" => "error", "message" => "Booking ID is missing"]);
    exit;
}

// Delete the booking from the database
$sql = "DELETE FROM purchased WHERE id = '$booking_id'";

if ($conn->query($sql)) {
    echo json_encode(["status" => "success", "message" => "Booking deleted successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to delete booking: " . $conn->error]);
}

$conn->close();
?>