<?php
require_once 'config/database.php';
$u=$conn->query('SELECT id,username,role,is_active FROM users')->fetchAll(PDO::FETCH_ASSOC);
var_export($u);
?>