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
if (empty($data['table']) || empty($data['id']) || empty($data['data'])) {
    echo json_encode(["status" => "error", "message" => "Missing required data"]);
    exit;
}

$table = $data['table'];
$id = $data['id'];
$updateData = $data['data'];

// Process the update data
$updateArray = [];
foreach ($updateData as $key => $value) {
    $updateArray[] = "$key = '$value'";
}
$updateString = implode(', ', $updateArray);

// Build and execute the SQL query
$sql = "UPDATE $table SET $updateString WHERE id = $id";
if ($conn->query($sql)) {
    echo json_encode(["status" => "success", "message" => "Record updated successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Error updating record: " . $conn->error]);
}
?>