<?php
header("Access-Control-Allow-Origin: *"); 
header("Content-Type: application/json");

$servername = "localhost";
$username = "root"; 
$password = "root"; 
$dbname = "car_dealership"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

$sql = "SELECT * FROM cars";
$result = $conn->query($sql);

$cars = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cars[] = $row;
    }
}

echo json_encode($cars);
$conn->close();
?>
