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

$id = $_POST['id'];
$action = $_POST['action'];
$newDate = $_POST['newDate'];
$newTime = $_POST['newTime'];

if ($action === 'approve') {
    $status = 'approved';
    $message = 'Test drive approved by employee.';
} elseif ($action === 'decline') {
    $status = 'declined';
    $message = 'Test drive declined by employee.';
} elseif ($action === 'reschedule') {
    $status = 'rescheduled';
    $message = "Test drive rescheduled to $newDate at $newTime.";
}

// Update the test drive status
$stmt = $conn->prepare("UPDATE purchased SET test_drive_status = :status, employee_message = :message WHERE id = :id");
$stmt->bindParam(':status', $status);
$stmt->bindParam(':message', $message);
$stmt->bindParam(':id', $id);
$stmt->execute();

echo "Test drive status updated successfully!";
?>