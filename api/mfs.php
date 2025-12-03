<?php
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized"]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// Helper to update account balance
function updateAccountBalance($pdo, $accountId) {
    $stmt = $pdo->prepare("SELECT 
        SUM(CASE WHEN type = 'in' THEN amount ELSE 0 END) - 
        SUM(CASE WHEN type = 'out' THEN amount ELSE 0 END) as balance 
        FROM mfs_transactions WHERE mfs_account_id = ?");
    $stmt->execute([$accountId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $balance = $result['balance'] ?? 0;

    $update = $pdo->prepare("UPDATE mfs_accounts SET balance = ? WHERE id = ?");
    $update->execute([$balance, $accountId]);
}

switch ($method) {
    case 'GET':
        if ($action === 'transactions' && isset($_GET['account_id'])) {
            // Get transactions for a specific account
            $stmt = $pdo->prepare("SELECT t.*, u.name as user_name FROM mfs_transactions t LEFT JOIN users u ON t.user_id = u.id WHERE t.mfs_account_id = ? ORDER BY t.created_at DESC");
            $stmt->execute([$_GET['account_id']]);
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        } else {
            // Get all MFS accounts with user details and last update time
            $sql = "SELECT m.*, u.name as user_name, 
                    (SELECT MAX(created_at) FROM mfs_transactions WHERE mfs_account_id = m.id) as last_updated
                    FROM mfs_accounts m 
                    LEFT JOIN users u ON m.user_id = u.id 
                    ORDER BY m.balance DESC";
            $stmt = $pdo->query($sql);
            $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($accounts);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        
        if ($action === 'transaction') {
            // Add Transaction (ONLY OWNER)
            if (!empty($data->account_id) && !empty($data->amount) && !empty($data->type)) {
                // Check ownership and balance
                $check = $pdo->prepare("SELECT user_id, balance FROM mfs_accounts WHERE id = ?");
                $check->execute([$data->account_id]);
                $account = $check->fetch();

                if ($account && $account['user_id'] == $_SESSION['user_id']) {
                    // Check for insufficient balance
                    if ($data->type === 'out' && $account['balance'] < $data->amount) {
                        http_response_code(400);
                        echo json_encode(["message" => "পর্যাপ্ত ব্যালেন্স নেই"]);
                        exit;
                    }

                    $sql = "INSERT INTO mfs_transactions (mfs_account_id, user_id, type, amount, note) VALUES (?, ?, ?, ?, ?)";
                    $stmt = $pdo->prepare($sql);
                    if ($stmt->execute([$data->account_id, $_SESSION['user_id'], $data->type, $data->amount, $data->note ?? ''])) {
                        updateAccountBalance($pdo, $data->account_id);
                        echo json_encode(["message" => "Transaction added"]);
                    } else {
                        http_response_code(503);
                        echo json_encode(["message" => "Error adding transaction"]);
                    }
                } else {
                    http_response_code(403);
                    echo json_encode(["message" => "Only owner can add transactions"]);
                }
            }
        } elseif ($action === 'set_balance') {
            // Set Exact Balance (ONLY OWNER)
            if (!empty($data->account_id) && isset($data->balance)) {
                // Check ownership
                $check = $pdo->prepare("SELECT user_id, balance FROM mfs_accounts WHERE id = ?");
                $check->execute([$data->account_id]);
                $account = $check->fetch();

                if ($account && $account['user_id'] == $_SESSION['user_id']) {
                    $current = $account['balance'];
                    $diff = $data->balance - $current;
                    
                    if ($diff != 0) {
                        $type = $diff > 0 ? 'in' : 'out';
                        $amount = abs($diff);
                        $note = "Balance Correction";
                        
                        $sql = "INSERT INTO mfs_transactions (mfs_account_id, user_id, type, amount, note) VALUES (?, ?, ?, ?, ?)";
                        $stmt = $pdo->prepare($sql);
                        if ($stmt->execute([$data->account_id, $_SESSION['user_id'], $type, $amount, $note])) {
                            updateAccountBalance($pdo, $data->account_id);
                            echo json_encode(["message" => "Balance updated"]);
                        } else {
                            http_response_code(503);
                            echo json_encode(["message" => "Error updating balance"]);
                        }
                    } else {
                        echo json_encode(["message" => "Balance unchanged"]);
                    }
                } else {
                    http_response_code(403);
                    echo json_encode(["message" => "Only owner can update balance"]);
                }
            }
        } else {
            // Add Account
            if (!empty($data->provider)) {
                // Check if account already exists for this user and provider
                $check = $pdo->prepare("SELECT id FROM mfs_accounts WHERE user_id = ? AND provider = ?");
                $check->execute([$_SESSION['user_id'], $data->provider]);
                if ($check->fetch()) {
                    http_response_code(409); // Conflict
                    echo json_encode(["message" => "আপনার ইতিমধ্যে একটি " . $data->provider . " অ্যাকাউন্ট আছে"]);
                    exit;
                }

                $number = !empty($data->number) ? $data->number : '';
                
                $sql = "INSERT INTO mfs_accounts (user_id, provider, number, balance) VALUES (?, ?, ?, 0)";
                $stmt = $pdo->prepare($sql);
                
                if ($stmt->execute([$_SESSION['user_id'], $data->provider, $number])) {
                    $newId = $pdo->lastInsertId();
                    // If initial balance provided
                    if (!empty($data->balance) && $data->balance > 0) {
                        $tSql = "INSERT INTO mfs_transactions (mfs_account_id, user_id, type, amount, note) VALUES (?, ?, 'in', ?, 'Initial Balance')";
                        $pdo->prepare($tSql)->execute([$newId, $_SESSION['user_id'], $data->balance]);
                        updateAccountBalance($pdo, $newId);
                    }
                    echo json_encode(["message" => "Account added", "id" => $newId]);
                } else {
                    http_response_code(503);
                    echo json_encode(["message" => "Unable to add account"]);
                }
            }
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        if (!empty($data->id)) {
            // Update Account Details (Provider/Number) - ONLY OWNER
            $stmt = $pdo->prepare("SELECT user_id FROM mfs_accounts WHERE id = ?");
            $stmt->execute([$data->id]);
            $account = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($account && $account['user_id'] == $_SESSION['user_id']) {
                $sql = "UPDATE mfs_accounts SET provider = ?, number = ? WHERE id = ?";
                $params = [$data->provider, $data->number ?? '', $data->id];

                $stmt = $pdo->prepare($sql);
                if ($stmt->execute($params)) {
                    echo json_encode(["message" => "Account updated"]);
                } else {
                    http_response_code(503);
                    echo json_encode(["message" => "Unable to update account"]);
                }
            } else {
                http_response_code(403);
                echo json_encode(["message" => "Unauthorized"]);
            }
        }
        break;

    case 'DELETE':
        if (isset($_GET['id'])) {
            if ($action === 'transaction') {
                // Delete Transaction (ONLY OWNER OF TRANSACTION)
                $stmt = $pdo->prepare("SELECT mfs_account_id, user_id FROM mfs_transactions WHERE id = ?");
                $stmt->execute([$_GET['id']]);
                $tx = $stmt->fetch();

                if ($tx && $tx['user_id'] == $_SESSION['user_id']) {
                    $del = $pdo->prepare("DELETE FROM mfs_transactions WHERE id = ?");
                    if ($del->execute([$_GET['id']])) {
                        updateAccountBalance($pdo, $tx['mfs_account_id']);
                        echo json_encode(["message" => "Transaction deleted"]);
                    }
                } else {
                    http_response_code(403);
                    echo json_encode(["message" => "Unauthorized"]);
                }
            } else {
                // Delete Account (Only Owner)
                $stmt = $pdo->prepare("DELETE FROM mfs_accounts WHERE id = ? AND user_id = ?");
                if ($stmt->execute([$_GET['id'], $_SESSION['user_id']])) {
                    if ($stmt->rowCount() > 0) {
                        echo json_encode(["message" => "Account deleted"]);
                    } else {
                        http_response_code(403);
                        echo json_encode(["message" => "Unauthorized"]);
                    }
                }
            }
        }
        break;
}
?>
