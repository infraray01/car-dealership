<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers");

include 'db_connect.php';

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->email) && !empty($data->password)) {
    $email = $data->email;
    $password = $data->password;

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Create a session
            $session_id = bin2hex(random_bytes(32));
            $user_id = $user['id'];
            $sql = "INSERT INTO sessions (session_id, user_id) VALUES ('$session_id', '$user_id')";
            
            if ($conn->query($sql)) {
                ob_clean(); // ✅ Clear any previous output
                echo json_encode(["status" => "success", "message" => "Login successful", "session_id" => $session_id]);
            } else {
                ob_clean();
                echo json_encode(["status" => "error", "message" => "Session creation failed"]);
            }
        } else {
            ob_clean();
            echo json_encode(["status" => "error", "message" => "Invalid password"]);
        }
    } else {
        ob_clean();
        echo json_encode(["status" => "error", "message" => "User not found"]);
    }
} else {
    ob_clean();
    echo json_encode(["status" => "error", "message" => "Email and password are required"]);
}

$conn->close();
?>
