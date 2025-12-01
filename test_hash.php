<?php
$password = '123456';
$hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

if (password_verify($password, $hash)) {
    echo "Match";
} else {
    echo "No Match";
    echo "\nNew Hash: " . password_hash($password, PASSWORD_DEFAULT);
}
?>
