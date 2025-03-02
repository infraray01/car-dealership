<?php
error_reporting(E_ALL); // Report all errors
ini_set('display_errors', 1); // Display errors
?>
<?php
header("Content-Type: application/json");
include 'db_connect.php';

function fetchTableData($conn, $tableName) {
    $sql = "SELECT * FROM $tableName";
    $result = $conn->query($sql);
    $data = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    return $data;
}

$tables = [
    'booking' => fetchTableData($conn, 'booking'),
    'cardetails' => fetchTableData($conn, 'cardetails'),
    'cars' => fetchTableData($conn, 'cars'),
    'purchased' => fetchTableData($conn, 'purchased'),
    'services' => fetchTableData($conn, 'services'),
    'sessions' => fetchTableData($conn, 'sessions'),
    'users' => fetchTableData($conn, 'users')
];

echo json_encode($tables);
?>