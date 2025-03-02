<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

$servername = "localhost";  
$username = "root";         
$password = "root";        
$dbname = "car_dealership"; 

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
} 

// Get car_id from URL
$car_id = isset($_GET['id']) ? $_GET['id'] : null;

if ($car_id) {
    // Prepare the query
    $query = "
        SELECT cars.name, cars.image, cars.year, cars.transmission, cars.mileage, cars.price,
               cardetails.description, cardetails.features, cardetails.condition,
               cardetails.image1, cardetails.image2, cardetails.image3, cardetails.image4
        FROM cars
        INNER JOIN cardetails ON cars.id = cardetails.car_id
        WHERE cars.id = ?";
        
    // Prepare the statement
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $car_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $car = $result->fetch_assoc();
            echo json_encode($car);
        } else {
            echo json_encode(["status" => "error", "message" => "Car not found"]);
        }
        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Query preparation failed"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid car ID"]);
}

$conn->close();
?>
