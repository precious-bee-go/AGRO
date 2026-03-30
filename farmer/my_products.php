<?php
require_once "../config/config.php";
require_once "../config/database.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'farmer') {
    header("Location: ../login.php");
    exit();
}

$farmer_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM products WHERE farmer_id = ? AND status != 'deleted' ORDER BY created_at DESC");
$stmt->execute([$farmer_id]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Products - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include "../includes/navbar.php"; ?>

    <div class="container mt-4">
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>My Products</h2>
            <a href="add_product.php" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>Add New Product
            </a>
        </div>

        <?php if (empty($products)): ?>
            <div class="text-center mt-5">
                <i class="fas fa-box-open fa-4x text-muted"></i>
                <h4 class="mt-3 text-muted">No products added yet</h4>
                <p class="text-muted">Start by adding your first product to the marketplace.</p>
                <a href="add_product.php" class="btn btn-success">Add Your First Product</a>
            </div>
        <?php else: ?>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                            <tr>
                                <td>
                                    <?php if ($product['image']): ?>
                                        <img src="../uploads/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <span class="text-muted">No image</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo htmlspecialchars($product['category'] ?? 'N/A'); ?></td>
                                <td><?php echo number_format($product['price'], 2); ?> FCFA</td>
                                <td><?php echo $product['quantity']; ?> <?php echo htmlspecialchars($product['unit']); ?></td>
                                <td>
                                    <?php if($product['status'] == 'deleted'): ?>
                                        <span class="badge bg-secondary">Deleted</span>
                                    <?php elseif($product['status'] == 'sold'): ?>
                                        <span class="badge bg-danger">Sold Out</span>
                                    <?php elseif($product['quantity'] <= 0): ?>
                                        <span class="badge bg-warning">Out of Stock</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Available</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($product['status'] != 'deleted'): ?>
                                        <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form action="../handlers/product_handler.php" method="POST" class="d-inline ms-1">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Remove this product from your listings?')">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted">No actions available</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php endif; ?>
    </div>

    <?php include "../includes/footer.php"; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>