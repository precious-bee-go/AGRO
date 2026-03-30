<?php
require_once 'config/database.php';
$stmt = $conn->query('DESCRIBE users');
foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $column) {
    echo $column['Field'] . ' ' . $column['Type'] . "\n";
}
?>