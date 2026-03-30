<?php
require_once "../includes/auth.php";
require_once "../config/database.php";

checkLogin();
checkRole('farmer');

include "../includes/header.php";

// Fetch categories
$stmt = $conn->query("SELECT * FROM categories");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Add Product</h2>

<form action="../handlers/product_handler.php" method="POST" enctype="multipart/form-data">

    <input type="text" name="name" placeholder="Product Name" required>

    <textarea name="description" placeholder="Description"></textarea>

    <input type="number" step="0.01" name="price" placeholder="Price" required>

    <input type="number" name="quantity" placeholder="Quantity" required>

    <select name="category_id">
        <option value="">Select Category</option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?php echo $cat['id']; ?>">
                <?php echo $cat['name']; ?>
            </option>
        <?php endforeach; ?>
    </select>

    <input type="file" name="image">

    <button type="submit" name="add_product">Add Product</button>

</form>

<?php include "../includes/footer.php"; ?>