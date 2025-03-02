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

// Log the received data for debugging
error_log("Received data: " . print_r($data, true));

// Validate required fields
if (empty($data['table']) || empty($data['data'])) {
    echo json_encode(["status" => "error", "message" => "Missing required data"]);
    exit;
}

$table = $data['table'];
$insertData = $data['data'];

// Build the SQL query
$columns = implode(', ', array_keys($insertData));
$values = "'" . implode("', '", array_values($insertData)) . "'";
$sql = "INSERT INTO $table ($columns) VALUES ($values)";

// Log the SQL query for debugging
error_log("SQL Query: " . $sql);

if ($conn->query($sql)) {
    echo json_encode(["status" => "success", "message" => "Record added successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Error adding record: " . $conn->error]);
}
?>