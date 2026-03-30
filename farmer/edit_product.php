<?php
require_once "../includes/auth.php";
require_once "../config/database.php";

checkLogin();
checkRole('farmer');

$id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

include "../includes/header.php";
?>

<h2>Edit Product</h2>

<form method="POST" action="../handlers/product_handler.php">
    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">

    <input type="text" name="name" value="<?php echo $product['name']; ?>">

    <input type="number" name="price" value="<?php echo $product['price']; ?>">

    <input type="number" name="quantity" value="<?php echo $product['quantity']; ?>">

    <button type="submit" name="update_product">Update</button>
</form>

<?php include "../includes/footer.php"; ?>