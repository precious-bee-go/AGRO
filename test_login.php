<?php
require_once 'config/database.php';
$email='admin@agro.com';
$password='admin123';
$stmt=$conn->prepare('SELECT * FROM users WHERE email=?');
$stmt->execute([$email]);
$user=$stmt->fetch(PDO::FETCH_ASSOC);
if(!$user) { echo 'no user'; exit; }
$ok = password_verify($password,$user['password']);
var_export(['user'=>$user['username'],'role'=>$user['role'],'verify'=>$ok]);
?>