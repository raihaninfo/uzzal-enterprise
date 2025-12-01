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
        $stmt = $pdo->query("SELECT * FROM dues WHERE is_paid = 0 ORDER BY created_at DESC");
        $dues = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($dues);
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->name) && !empty($data->amount)) {
            $sql = "INSERT INTO dues (name, amount, note, due_date) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $date = !empty($data->date) ? $data->date : date('Y-m-d H:i:s');
            if ($stmt->execute([$data->name, $data->amount, $data->note ?? '', $date])) {
                echo json_encode(["message" => "Due added", "id" => $pdo->lastInsertId()]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Unable to add due"]);
            }
        }
        break;

    case 'DELETE':
        if (isset($_GET['id'])) {
            $stmt = $pdo->prepare("DELETE FROM dues WHERE id = ?");
            if ($stmt->execute([$_GET['id']])) {
                echo json_encode(["message" => "Due deleted"]);
            }
        }
        break;
        
    case 'PUT': // Mark as paid or update
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->id) && isset($data->is_paid)) {
            $stmt = $pdo->prepare("UPDATE dues SET is_paid = ? WHERE id = ?");
            if ($stmt->execute([$data->is_paid, $data->id])) {
                echo json_encode(["message" => "Due updated"]);
            }
        }
        break;
}
?>
