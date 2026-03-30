<?php
require_once "../config/database.php";
include "../includes/header.php";

// Fetch all products + farmer name
$stmt = $conn->query("
    SELECT products.*, users.name AS farmer_name 
    FROM products 
    JOIN users ON products.user_id = users.id
    ORDER BY products.created_at DESC
");

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>All Products</h2>

<div class="product-grid">

<?php foreach ($products as $product): ?>

    <div class="product-card">

        <?php if ($product['image']): ?>
            <img src="../uploads/<?php echo $product['image']; ?>" width="150">
        <?php endif; ?>

        <h3><?php echo $product['name']; ?></h3>

        <p>By: <?php echo $product['farmer_name']; ?></p>

        <p>Price: $<?php echo $product['price']; ?></p>

        <a href="../product.php?id=<?php echo $product['id']; ?>">
            View Details
        </a>

    </div>

<?php endforeach; ?>

</div>

<?php include "../includes/footer.php"; ?>