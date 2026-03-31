<?php
require_once "../config/config.php";
require_once "../config/database.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'farmer') {
    header("Location: ../login.php");
    exit();
}

if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: my_products.php");
    exit();
}

$product_id = intval($_GET['id']);
$farmer_id = $_SESSION['user_id'];

// Fetch product and verify ownership
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND farmer_id = ?");
$stmt->execute([$product_id, $farmer_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$product) {
    header("Location: my_products.php");
    exit();
}

// Fetch categories (restricted)
$categories = ['Vegetables', 'Fruits', 'Tubers', 'Spices'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include "../includes/navbar.php"; ?>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h4 class="mb-0">Edit Product</h4>
                    </div>
                    <div class="card-body">
                        <?php if(isset($_SESSION['success'])): ?>
                            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
                        <?php endif; ?>
                        <?php if(isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                        <?php endif; ?>

                        <form method="POST" action="../handlers/product_handler.php" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">

                            <div class="mb-3">
                                <label class="form-label">Product Name</label>
                                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select name="category" class="form-control" required>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category; ?>" <?php echo ($product['category'] == $category) ? 'selected' : ''; ?>><?php echo $category; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Price (FCFA)</label>
                                <input type="number" name="price" step="0.01" class="form-control" value="<?php echo (float)$product['price']; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Quantity</label>
                                <input type="number" name="quantity" class="form-control" value="<?php echo (int)$product['quantity']; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Unit</label>
                                <select name="unit" class="form-control" required>
                                    <option value="bunch" <?php echo $product['unit'] == 'bunch' ? 'selected' : ''; ?>>Bunch</option>
                                    <option value="basket" <?php echo $product['unit'] == 'basket' ? 'selected' : ''; ?>>Basket</option>
                                    <option value="hip" <?php echo $product['unit'] == 'hip' ? 'selected' : ''; ?>>Hip</option>
                                    <option value="bags" <?php echo $product['unit'] == 'bags' ? 'selected' : ''; ?>>Bags</option>
                                </select>
                            </div>

                            <!-- Description removed per request -->

                            <div class="mb-3">
                                <label class="form-label">Image</label>
                                <input type="file" name="image" class="form-control">
                                <?php if ($product['image']): ?>
                                    <img src="../uploads/products/<?php echo htmlspecialchars($product['image']); ?>" alt="" class="img-thumbnail mt-2" style="max-width: 150px;">
                                <?php endif; ?>
                            </div>

                            <button type="submit" name="update_product" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Update Product
                            </button>
                            <a href="my_products.php" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include "../includes/footer.php"; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>