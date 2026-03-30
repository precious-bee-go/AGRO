<?php
require_once "../includes/auth.php";
require_once "../config/database.php";

checkLogin();
checkRole('buyer');

include "../includes/header.php";

// Fetch orders of logged-in user
$stmt = $conn->prepare("
    SELECT * FROM orders 
    WHERE user_id = ?
    ORDER BY created_at DESC
");

$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>My Orders</h2>

<?php if (empty($orders)): ?>
    <p>No orders yet.</p>
<?php else: ?>

<table border="1" cellpadding="10">
<tr>
    <th>Order ID</th>
    <th>Total</th>
    <th>Status</th>
    <th>Date</th>
    <th>Details</th>
</tr>

<?php foreach ($orders as $order): ?>
<tr>
    <td>#<?php echo $order['id']; ?></td>
    <td>$<?php echo $order['total_price']; ?></td>
    <td><?php echo $order['status']; ?></td>
    <td><?php echo $order['created_at']; ?></td>
    <td>
        <a href="order_details.php?id=<?php echo $order['id']; ?>">
            View
        </a>
    </td>
</tr>
<?php endforeach; ?>

</table>

<?php endif; ?>

<?php include "../includes/footer.php"; ?>