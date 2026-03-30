<?php
require_once 'config/database.php';
$u = $conn->query('SELECT username,email,password,role FROM users')->fetchAll(PDO::FETCH_ASSOC);
foreach($u as $row){
    echo $row['username'].' '.$row['email'].' '.substr($row['password'],0,10).'... role:'.$row['role']."\n";
}
?>