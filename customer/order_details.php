<?php
require_once "../includes/auth.php";
require_once "../config/database.php";

checkLogin();
checkRole('buyer');

include "../includes/header.php";

$order_id = $_GET['id'];

// Fetch order items
$stmt = $conn->prepare("
    SELECT order_items.*, products.name 
    FROM order_items
    JOIN products ON order_items.product_id = products.id
    WHERE order_items.order_id = ?
");

$stmt->execute([$order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Order Details</h2>

<table border="1" cellpadding="10">
<tr>
    <th>Product</th>
    <th>Price</th>
    <th>Quantity</th>
</tr>

<?php foreach ($items as $item): ?>
<tr>
    <td><?php echo $item['name']; ?></td>
    <td>$<?php echo $item['price']; ?></td>
    <td><?php echo $item['quantity']; ?></td>
</tr>
<?php endforeach; ?>

</table>

<?php include "../includes/footer.php"; ?>