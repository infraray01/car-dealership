<?php
header("Access-Control-Allow-Origin: *"); 
header("Content-Type: application/json; charset=UTF-8");

include 'db_connect.php';

$car_id = $_GET['car_id'];

// Fetch car details from the booking table
$sql = "SELECT cars.name, cars.image, cars.price,
               booking.overview, booking.key_features, booking.pricing,
               booking.technical_specs, booking.additional_features,
               booking.warranty_maintenance, booking.purchase_options,
               booking.insurance_options, booking.test_drive
        FROM cars
        INNER JOIN booking ON cars.id = booking.car_id
        WHERE cars.id = ?";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $car_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $car = $result->fetch_assoc();
        echo json_encode(["status" => "success", "data" => $car]);
    } else {
        echo json_encode(["status" => "error", "message" => "Car not found"]);
    }
    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Query preparation failed"]);
}

$conn->close();
?>