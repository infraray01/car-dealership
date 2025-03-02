<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers");

include 'db_connect.php';

// Log incoming data
$raw_data = file_get_contents("php://input");
error_log("Received data: " . $raw_data);
$data = json_decode($raw_data);

// Check if data is received properly
if (!$data) {
    echo json_encode(["status" => "error", "message" => "Invalid JSON input"]);
    exit;
}

if (!empty($data->full_name) && !empty($data->email) && !empty($data->password)) {
    $full_name = $conn->real_escape_string($data->full_name);
    $email = $conn->real_escape_string($data->email);
    $password = password_hash($data->password, PASSWORD_BCRYPT);

    // Check if email already exists
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $response = ["status" => "error", "message" => "Email already exists"];
    } else {
        // Insert new user
        $sql = "INSERT INTO users (full_name, email, password) VALUES ('$full_name', '$email', '$password')";

        if ($conn->query($sql) === TRUE) {
            $response = ["status" => "success", "message" => "User registered successfully"];
        } else {
            $response = ["status" => "error", "message" => "Database error: " . $conn->error];
        }
    }
} else {
    $response = ["status" => "error", "message" => "All fields are required"];
}

// Ensure no extra output before JSON response
ob_clean();
echo json_encode($response);
exit;
?>
