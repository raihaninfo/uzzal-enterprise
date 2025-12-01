<?php
require_once 'api/db.php';

$phone = '01727215472';
$password = '123456';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {
    // Check if user exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE phone = ?");
    $stmt->execute([$phone]);
    $user = $stmt->fetch();

    if ($user) {
        // Update password
        $update = $pdo->prepare("UPDATE users SET password = ? WHERE phone = ?");
        $update->execute([$hashed_password, $phone]);
        echo "Password updated successfully for user $phone. New password is: $password";
    } else {
        // Create user
        $insert = $pdo->prepare("INSERT INTO users (name, phone, password, business_name) VALUES (?, ?, ?, ?)");
        $insert->execute(['Uzzal Enterprise', $phone, $hashed_password, 'Uzzal Enterprise']);
        echo "User created successfully. Phone: $phone, Password: $password";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
