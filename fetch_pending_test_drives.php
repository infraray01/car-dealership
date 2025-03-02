<?php
// Database connection
$host = 'localhost';
$dbname = 'royal_cars';
$username = 'root'; // Replace with your database username
$password = ''; // Replace with your database password

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Fetch pending test drives
$stmt = $conn->prepare("SELECT * FROM purchased WHERE test_drive_status = 'pending'");
$stmt->execute();
$testDrives = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Generate HTML for the table
foreach ($testDrives as $testDrive) {
    echo "<tr>
            <td>{$testDrive['id']}</td>
            <td>{$testDrive['user_id']}</td>
            <td>{$testDrive['car_id']}</td>
            <td>{$testDrive['pickup_location']}</td>
            <td>{$testDrive['pickup_date']}</td>
            <td>{$testDrive['pickup_time']}</td>
            <td>{$testDrive['test_drive_status']}</td>
            <td>
                <button class='btn btn-success' onclick='updateTestDriveStatus({$testDrive['id']}, \"approve\")'>Approve</button>
                <button class='btn btn-danger' onclick='updateTestDriveStatus({$testDrive['id']}, \"decline\")'>Decline</button>
                <button class='btn btn-warning' onclick='rescheduleTestDrive({$testDrive['id']})'>Reschedule</button>
            </td>
          </tr>";
}
?>