<?php
require_once 'config/database.php';
$tables = ['products','orders','order_items','cart'];
foreach($tables as $table){
    echo "\n" . strtoupper($table) . "\n";
    $stmt = $conn->query("DESCRIBE $table");
    foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $col){
        echo $col['Field'].' '.$col['Type']."\n";
    }
}
?>