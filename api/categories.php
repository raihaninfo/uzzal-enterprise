<?php
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized"]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $stmt = $pdo->query("SELECT name FROM categories ORDER BY name ASC");
        $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo json_encode($categories);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->name)) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO categories (name) VALUES (?)");
            if ($stmt->execute([$data->name])) {
                echo json_encode(["message" => "Category added"]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Unable to add category"]);
            }
        }
        break;
}
?>
