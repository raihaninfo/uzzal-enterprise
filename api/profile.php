<?php
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $pdo->prepare("SELECT name, phone, business_name FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo json_encode($user);
    } else {
        http_response_code(404);
        echo json_encode(["message" => "User not found"]);
    }
} elseif ($method === 'PUT') {
    $data = json_decode(file_get_contents("php://input"));
    
    // Change Password
    if (isset($data->old_password) && isset($data->new_password)) {
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $current = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($current && password_verify($data->old_password, $current['password'])) {
            $new_hash = password_hash($data->new_password, PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            if ($update->execute([$new_hash, $user_id])) {
                echo json_encode(["message" => "Password updated successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Failed to update password"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Incorrect old password"]);
        }
    } 
    // Update Profile Info
    elseif (isset($data->name) || isset($data->business_name) || isset($data->phone)) {
        $fields = [];
        $params = [];
        
        if (!empty($data->name)) { $fields[] = "name = ?"; $params[] = $data->name; }
        if (!empty($data->business_name)) { $fields[] = "business_name = ?"; $params[] = $data->business_name; }
        if (!empty($data->phone)) { $fields[] = "phone = ?"; $params[] = $data->phone; }
        
        if (count($fields) > 0) {
            $params[] = $user_id;
            $sql = "UPDATE users SET " . implode(", ", $fields) . " WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute($params)) {
                // Update session if name changed
                if (!empty($data->name)) { $_SESSION['user_name'] = $data->name; }
                echo json_encode(["message" => "Profile updated successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["message" => "Failed to update profile"]);
            }
        } else {
            echo json_encode(["message" => "No changes made"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Invalid data"]);
    }
}
?>
