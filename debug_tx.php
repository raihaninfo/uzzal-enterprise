<?php
session_start();
require 'api/db.php';

// Simulate a logged-in user (Admin)
$_SESSION['user_id'] = 1; 
$_SESSION['user_role'] = 'admin';

$stmt = $pdo->query("SELECT t.id, t.user_id, t.source, t.amount FROM transactions t LIMIT 5");
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "--- Transactions ---\n";
foreach ($transactions as $tx) {
    echo "ID: " . $tx['id'] . ", UserID: " . var_export($tx['user_id'], true) . ", Source: " . $tx['source'] . "\n";
}

// Check a specific user
$stmt = $pdo->prepare("SELECT id, name, role FROM users WHERE id = 1");
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
echo "\n--- User ---\n";
print_r($user);
?>
