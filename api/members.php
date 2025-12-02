<?php
require_once 'db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["message" => "Unauthorized access"]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // List all members
    $stmt = $pdo->query("SELECT id, name, phone, business_name, role, created_at FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($users);

} elseif ($method === 'POST') {
    // Add new member
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->name) && !empty($data->phone) && !empty($data->password)) {
        try {
            $hashed_password = password_hash($data->password, PASSWORD_DEFAULT);
            $role = isset($data->role) && in_array($data->role, ['admin', 'member']) ? $data->role : 'member';

            $stmt = $pdo->prepare("INSERT INTO users (name, phone, password, business_name, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$data->name, $data->phone, $hashed_password, $data->business_name ?? '', $role]);

            echo json_encode(["message" => "Member added successfully"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error adding member: " . $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Incomplete data"]);
    }

} elseif ($method === 'DELETE') {
    // Delete member
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($id > 0) {
        if ($id === $_SESSION['user_id']) {
            http_response_code(400);
            echo json_encode(["message" => "You cannot delete yourself"]);
            exit;
        }

        try {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(["message" => "Member deleted successfully"]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Error deleting member: " . $e->getMessage()]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Invalid ID"]);
    }
}
?>
