<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the database connection file
include('db_connection.php');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the posted data
    $user_id = $_POST['user_id']; // Assuming user_id is passed from the frontend
    $name = $_POST['name'] ?? null;       // Name
    $email = $_POST['email'] ?? null;     // Email
    $bio = $_POST['bio'] ?? null;         // Bio
    $phone = $_POST['phone'] ?? null;     // Phone
    $address = $_POST['address'] ?? null; // Address

    // Initialize file paths
    $profile_picture = null;
    $cover_photo = null;

    // Handle profile picture upload
    if (!empty($_FILES['profile_picture']['name'])) {
        $target_dir = "img/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true); // Create directory if it doesn't exist
        }
        $profile_target_file = $target_dir . basename($_FILES['profile_picture']['name']);
        $profile_imageFileType = strtolower(pathinfo($profile_target_file, PATHINFO_EXTENSION));

        // Validate file type
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($profile_imageFileType, $allowed_types)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid profile picture file type']);
            exit;
        }

        // Move uploaded file
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profile_target_file)) {
            $profile_picture = $profile_target_file;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload profile picture']);
            exit;
        }
    }

    // Handle cover photo upload
    if (!empty($_FILES['cover_photo']['name'])) {
        $target_dir = "img/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true); // Create directory if it doesn't exist
        }
        $cover_target_file = $target_dir . basename($_FILES['cover_photo']['name']);
        $cover_imageFileType = strtolower(pathinfo($cover_target_file, PATHINFO_EXTENSION));

        // Validate file type
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($cover_imageFileType, $allowed_types)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid cover photo file type']);
            exit;
        }

        // Move uploaded file
        if (move_uploaded_file($_FILES['cover_photo']['tmp_name'], $cover_target_file)) {
            $cover_photo = $cover_target_file;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload cover photo']);
            exit;
        }
    }

    // Build the SQL query dynamically based on provided fields
    $sql = "UPDATE users SET ";
    $params = [];
    $types = "";

    if ($name !== null) {
        $sql .= "name = ?, ";
        $params[] = $name;
        $types .= "s";
    }
    if ($email !== null) {
        $sql .= "email = ?, ";
        $params[] = $email;
        $types .= "s";
    }
    if ($bio !== null) {
        $sql .= "bio = ?, ";
        $params[] = $bio;
        $types .= "s";
    }
    if ($phone !== null) {
        $sql .= "phone = ?, ";
        $params[] = $phone;
        $types .= "s";
    }
    if ($address !== null) {
        $sql .= "address = ?, ";
        $params[] = $address;
        $types .= "s";
    }
    if ($profile_picture !== null) {
        $sql .= "profile_picture = ?, ";
        $params[] = $profile_picture;
        $types .= "s";
    }
    if ($cover_photo !== null) {
        $sql .= "cover_photo = ?, ";
        $params[] = $cover_photo;
        $types .= "s";
    }

    // Remove the trailing comma and space
    $sql = rtrim($sql, ", ");

    // Add the WHERE clause
    $sql .= " WHERE id = ?";
    $params[] = $user_id;
    $types .= "i";

    // Prepare and execute the SQL statement
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $conn->error]);
        exit;
    }

    // Bind parameters dynamically
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

// Close the database connection
$conn->close();
?>