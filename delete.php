<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db_connect.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit;
}

// Get the raw POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validate required fields
if (empty($data['table']) || empty($data['id'])) {
    echo json_encode(["status" => "error", "message" => "Missing required data"]);
    exit;
}

$table = $data['table'];
$id = $data['id'];

// Build and execute the SQL query
$sql = "DELETE FROM $table WHERE id = $id";
if ($conn->query($sql)) {
    echo json_encode(["status" => "success", "message" => "Record deleted successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Error deleting record: " . $conn->error]);
}
?>