<?php
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized"]);
    exit;
}

$user_id = $_SESSION['user_id'];

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Get all transactions (Shared Visibility)
        $stmt = $pdo->query("SELECT t.*, c.name as category_name, u.name as user_name 
                             FROM transactions t 
                             LEFT JOIN categories c ON t.category_id = c.id 
                             LEFT JOIN users u ON t.user_id = u.id 
                             ORDER BY t.transaction_date DESC");
        $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($transactions);
        break;

    case 'POST':
        // Add new transaction
        $data = json_decode(file_get_contents("php://input"));
        
        if (!empty($data->source) && !empty($data->amount) && !empty($data->type)) {
            // Get category ID
            $cat_id = null;
            if (!empty($data->category)) {
                $stmt = $pdo->prepare("SELECT id FROM categories WHERE name = ?");
                $stmt->execute([$data->category]);
                $cat = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($cat) {
                    $cat_id = $cat['id'];
                } else {
                    // Create category if not exists
                    $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
                    $stmt->execute([$data->category]);
                    $cat_id = $pdo->lastInsertId();
                }
            }

            $sql = "INSERT INTO transactions (user_id, source, amount, type, category_id, transaction_date) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $date = !empty($data->date) ? $data->date : date('Y-m-d H:i:s');
            
            if ($stmt->execute([$user_id, $data->source, $data->amount, $data->type, $cat_id, $date])) {
                echo json_encode(["message" => "Transaction created", "id" => $pdo->lastInsertId()]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Unable to create transaction"]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Incomplete data"]);
        }
        break;

    case 'PUT':
        // Update transaction
        $data = json_decode(file_get_contents("php://input"));
        
        if (!empty($data->id)) {
            $fields = [];
            $params = [];

            if (!empty($data->source)) { $fields[] = "source = ?"; $params[] = $data->source; }
            if (!empty($data->amount)) { $fields[] = "amount = ?"; $params[] = $data->amount; }
            if (!empty($data->type)) { $fields[] = "type = ?"; $params[] = $data->type; }
            if (!empty($data->date)) { $fields[] = "transaction_date = ?"; $params[] = $data->date; }
            
            if (!empty($data->category)) {
                // Resolve category ID similar to POST
                $stmt = $pdo->prepare("SELECT id FROM categories WHERE name = ?");
                $stmt->execute([$data->category]);
                $cat = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($cat) {
                    $cat_id = $cat['id'];
                } else {
                    $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
                    $stmt->execute([$data->category]);
                    $cat_id = $pdo->lastInsertId();
                }
                $fields[] = "category_id = ?";
                $params[] = $cat_id;
            }

            if (count($fields) > 0) {
                // Check if user is admin or owner
                $user_role = $_SESSION['user_role'] ?? 'member';
                $params[] = $data->id;
                
                if ($user_role === 'admin') {
                    $sql = "UPDATE transactions SET " . implode(", ", $fields) . " WHERE id = ?";
                } else {
                    $params[] = $user_id;
                    $sql = "UPDATE transactions SET " . implode(", ", $fields) . " WHERE id = ? AND user_id = ?";
                }
                
                $stmt = $pdo->prepare($sql);
                if ($stmt->execute($params)) {
                    echo json_encode(["message" => "Transaction updated"]);
                } else {
                    http_response_code(503);
                    echo json_encode(["message" => "Unable to update transaction"]);
                }
            }
        }
        break;

    case 'DELETE':
        // Delete transaction
        if (isset($_GET['id'])) {
            $user_role = $_SESSION['user_role'] ?? 'member';
            
            if ($user_role === 'admin') {
                $stmt = $pdo->prepare("DELETE FROM transactions WHERE id = ?");
                $params = [$_GET['id']];
            } else {
                $stmt = $pdo->prepare("DELETE FROM transactions WHERE id = ? AND user_id = ?");
                $params = [$_GET['id'], $user_id];
            }
            
            if ($stmt->execute($params)) {
                echo json_encode(["message" => "Transaction deleted"]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Unable to delete transaction"]);
            }
        }
        break;
}
?>
