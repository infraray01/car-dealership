<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers");

include 'db_connect.php';

// Log requests for debugging
file_put_contents('debug.log', "Request received: " . file_get_contents('php://input') . "\n", FILE_APPEND);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch user profile data
    $session_id = $_GET['session_id'] ?? null;

    if (!$session_id) {
        echo json_encode(["status" => "error", "message" => "Session ID is missing"]);
        exit;
    }

    $stmt = $conn->prepare("SELECT users.* FROM users JOIN sessions ON users.id = sessions.user_id WHERE sessions.session_id = ?");
    $stmt->bind_param("s", $session_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo json_encode(["status" => "success", "data" => $user]);
    } else {
        echo json_encode(["status" => "error", "message" => "User not found"]);
    }

    $stmt->close();
}

// Update profile details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $session_id = $_POST['session_id'] ?? null;
    $full_name = $_POST['full_name'] ?? "";
    $bio = $_POST['bio'] ?? "";
    $phone = $_POST['phone'] ?? "";
    $address = $_POST['address'] ?? "";
    $profile_pic = "";

    if (!$session_id) {
        echo json_encode(["status" => "error", "message" => "Session ID is required"]);
        exit;
    }

    // Check if the user exists
    $stmt = $conn->prepare("SELECT user_id FROM sessions WHERE session_id = ?");
    $stmt->bind_param("s", $session_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        echo json_encode(["status" => "error", "message" => "Invalid session"]);
        exit;
    }

    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    // Handle profile picture upload
    if (!empty($_FILES["profile_pic"]["name"])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true); // Create uploads directory if not exists
        }
        $file_name = basename($_FILES["profile_pic"]["name"]);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $check = getimagesize($_FILES["profile_pic"]["tmp_name"]);

        if ($check === false) {
            echo json_encode(["status" => "error", "message" => "File is not a valid image"]);
            exit;
        }

        if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
            $profile_pic = $target_file;
        } else {
            echo json_encode(["status" => "error", "message" => "File upload failed"]);
            exit;
        }
    }

    // Update user details using prepared statements
    if (!empty($profile_pic)) {
        $update_sql = "UPDATE users SET full_name = ?, bio = ?, phone = ?, address = ?, profile_pic = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("sssssi", $full_name, $bio, $phone, $address, $profile_pic, $user_id);
    } else {
        $update_sql = "UPDATE users SET full_name = ?, bio = ?, phone = ?, address = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssssi", $full_name, $bio, $phone, $address, $user_id);
    }

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Profile updated successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database update failed: " . $stmt->error]);
    }

    $stmt->close();
}

$conn->close();
?>
