<?php
require_once "../includes/auth.php";
require_once "../config/database.php";

checkLogin();
checkRole('farmer');

include "../includes/header.php";

$stmt = $conn->prepare("
    SELECT orders.*, users.name AS buyer_name 
    FROM orders
    JOIN users ON orders.user_id = users.id
    ORDER BY orders.created_at DESC
");

$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Orders Received</h2>

<table border="1">
<tr>
    <th>Buyer</th>
    <th>Total</th>
    <th>Status</th>
</tr>

<?php foreach ($orders as $order): ?>
<tr>
    <td><?php echo $order['buyer_name']; ?></td>
    <td>$<?php echo $order['total_price']; ?></td>
    <td><?php echo $order['status']; ?></td>
</tr>
<?php endforeach; ?>

</table>

<?php include "../includes/footer.php"; ?>