<?php
require_once 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    
    if (!empty($data->phone) && !empty($data->password)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE phone = ?");
        $stmt->execute([$data->phone]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($data->password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            
            // Remove password from response
            unset($user['password']);
            echo json_encode(["message" => "Login successful", "user" => $user]);
        } else {
            http_response_code(401);
            echo json_encode(["message" => "Invalid credentials"]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Incomplete data"]);
    }
} elseif ($method === 'GET' && isset($_GET['action']) && $_GET['action'] === 'logout') {
    // Unset all session values
    $_SESSION = array();

    // Get session parameters 
    $params = session_get_cookie_params();

    // Delete the actual cookie
    setcookie(session_name(), '', time() - 42000, 
        $params["path"], $params["domain"], 
        $params["secure"], $params["httponly"]
    );

    // Destroy session
    session_destroy();
    
    echo json_encode(["message" => "Logged out successfully"]);
} elseif ($method === 'GET' && isset($_GET['action']) && $_GET['action'] === 'check') {
    if (isset($_SESSION['user_id'])) {
        echo json_encode(["isLoggedIn" => true, "user_id" => $_SESSION['user_id']]);
    } else {
        echo json_encode(["isLoggedIn" => false]);
    }
}
?>
