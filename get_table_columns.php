<?php
error_reporting(E_ALL); // Report all errors
ini_set('display_errors', 1); // Display errors
?>
<?php
include 'db_connect.php';

$table = $_POST['table'];

$sql = "SHOW COLUMNS FROM $table";
$result = $conn->query($sql);
$columns = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row['Field'];
    }
}

echo json_encode(["columns" => $columns]);
?>