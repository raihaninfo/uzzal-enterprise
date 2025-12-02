<?php
require 'api/db.php';

try {
    $stmt = $pdo->query("DESCRIBE transactions");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Columns in transactions table: " . implode(", ", $columns) . "\n";
    
    if (in_array('user_id', $columns)) {
        echo "user_id column exists.\n";
    } else {
        echo "user_id column MISSING!\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
