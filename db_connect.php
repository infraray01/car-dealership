<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

$servername = "localhost";  
$username = "root";         
$password = "root";        
$dbname = "car_dealership"; 

$conn = new mysqli($servername, $username, $password, $dbname);

// Handle connection error
if ($conn->connect_error) {
    error_log("Database Connection Error: " . $conn->connect_error);
    die(json_encode(["status" => "error", "message" => "Database connection failed."]));
}
?>
