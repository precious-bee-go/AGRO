<?php
require_once "../includes/auth.php";
require_once "../config/database.php";

checkLogin();
checkRole('farmer');

include "../includes/header.php";

$stmt = $conn->prepare("SELECT * FROM products WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>My Products</h2>

<a href="add_product.php">+ Add New Product</a>

<table border="1" cellpadding="10">
    <tr>
        <th>Name</th>
        <th>Price</th>
        <th>Qty</th>
        <th>Image</th>
    </tr>

    <?php foreach ($products as $product): ?>
        <tr>
            <td><?php echo $product['name']; ?></td>
            <td><?php echo $product['price']; ?></td>
            <td><?php echo $product['quantity']; ?></td>
            <td>
                <?php if ($product['image']): ?>
                    <img src="../uploads/<?php echo $product['image']; ?>" width="50">
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<?php include "../includes/footer.php"; ?>