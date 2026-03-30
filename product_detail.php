<?php
require_once "config/config.php";
require_once "config/database.php";

$product_id = $_GET['id'] ?? 0;
$product_id = intval($product_id);

if($product_id <= 0) {
    header("Location: product.php");
    exit();
}

// Get product details
$stmt = $conn->prepare("SELECT p.*, u.full_name as farmer_name, u.email as farmer_email, u.phone as farmer_phone
                        FROM products p
                        JOIN users u ON p.farmer_id = u.id
                        WHERE p.id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$product) {
    header("Location: product.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .product-image {
            max-height: 500px;
            object-fit: cover;
            border-radius: 10px;
        }
        .farmer-info {
            background: #cbd1d7;
            border-radius: 10px;
            padding: 20px;
        }
        .price-tag {
            font-size: 2rem;
            font-weight: bold;
            color: #28a745;
        }
        .quantity-input {
            width: 80px;
            text-align: center;
        }
        .stock-badge {
            font-size: 1.1rem;
            padding: 8px 16px;
        }
    </style>
</head>
<body>
    <?php include "includes/navbar.php"; ?>

    <div class="container mt-4">
        <div class="row">
            <!-- Product Image -->
            <div class="col-md-6">
                <img src="<?php echo $product['image'] ? 'uploads/products/'.$product['image'] : 'https://via.placeholder.com/600x400?text=Product+Image'; ?>"
                     class="img-fluid product-image w-100" alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>

            <!-- Product Details -->
            <div class="col-md-6">
                <h1 class="mb-3"><?php echo htmlspecialchars($product['name']); ?></h1>

                <div class="mb-3">
                    <span class="price-tag">FCFA <?php echo number_format($product['price'], 2); ?></span>
                    <small class="text-muted">per <?php echo htmlspecialchars($product['unit']); ?></small>
                </div>

                <div class="mb-3">
                    <?php if($product['status'] == 'sold' || $product['quantity'] <= 0): ?>
                        <span class="badge bg-danger stock-badge">Sold Out</span>
                    <?php else: ?>
                        <span class="badge bg-success stock-badge">In Stock: <?php echo $product['quantity']; ?> <?php echo htmlspecialchars($product['unit']); ?></span>
                    <?php endif; ?>
                </div>

                <div class="mb-4">
                    <h5>Description</h5>
                    <p class="lead"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                </div>

                <div class="mb-4">
                    <div class="row">
                        <div class="col-sm-6">
                            <strong>Category:</strong><br>
                            <span class="badge bg-primary"><?php echo htmlspecialchars($product['category']); ?></span>
                        </div>
                        <div class="col-sm-6">
                            <strong>Product ID:</strong><br>
                            #<?php echo $product['id']; ?>
                        </div>
                    </div>
                </div>

                <!-- Add to Cart Section -->
                <?php if($product['status'] != 'sold' && $product['quantity'] > 0): ?>
                    <?php if(isset($_SESSION['user_id']) && $_SESSION['role'] == 'customer'): ?>
                        <div class="card border-success mb-4">
                            <div class="card-body">
                                <h5 class="card-title text-success">Add to Cart</h5>
                                <form action="handlers/cart_handler.php" method="POST" class="d-flex align-items-center gap-3">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">

                                    <div class="input-group" style="max-width: 150px;">
                                        <span class="input-group-text">Qty</span>
                                        <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['quantity']; ?>"
                                               class="form-control quantity-input" required>
                                        <span class="input-group-text"><?php echo htmlspecialchars($product['unit']); ?></span>
                                    </div>

                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fas fa-cart-plus me-2"></i>Add to Cart
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <a href="login.php" class="alert-link">Login as a customer</a> to purchase this product.
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        This product is currently sold out.
                    </div>
                <?php endif; ?>

                <!-- Back to Products -->
                <div class="mb-3">
                    <a href="product.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Products
                    </a>
                </div>
            </div>
        </div>

        <!-- Farmer Information -->
        <div class="row mt-5">
            <div class="col-md-8 mx-auto">
                <div class="farmer-info">
                    <h4 class="mb-3"><i class="fas fa-user-circle text-success me-2"></i>Farmer Information</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($product['farmer_name']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($product['farmer_email']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($product['farmer_phone'] ?? 'Not provided'); ?></p>
                            <p><strong>Member since:</strong> <?php echo date('F Y', strtotime($product['created_at'])); ?></p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-1"></i>
                            All farmers on our platform are verified and committed to providing quality agricultural products.
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products Section (Optional) -->
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="mb-4">More Products from <?php echo htmlspecialchars($product['farmer_name']); ?></h3>
                <div class="row">
                    <?php
                    $stmt = $conn->prepare("SELECT * FROM products WHERE farmer_id = ? AND id != ? AND status != 'sold' AND quantity > 0 LIMIT 4");
                    $stmt->execute([$product['farmer_id'], $product['id']]);
                    $related_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if(count($related_products) > 0) {
                        foreach($related_products as $related) {
                            ?>
                            <div class="col-md-3 mb-3">
                                <div class="card h-100">
                                    <img src="<?php echo $related['image'] ? 'uploads/products/'.$related['image'] : 'https://via.placeholder.com/300x200?text=Product'; ?>"
                                         class="card-img-top" alt="<?php echo htmlspecialchars($related['name']); ?>" style="height: 150px; object-fit: cover;">
                                    <div class="card-body">
                                        <h6 class="card-title"><?php echo htmlspecialchars($related['name']); ?></h6>
                                        <p class="card-text text-success fw-bold">FCFA<?php echo number_format($related['price'], 2); ?>/<?php echo $related['unit']; ?></p>
                                        <a href="product_detail.php?id=<?php echo $related['id']; ?>" class="btn btn-outline-success btn-sm">View Details</a>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<div class="col-12"><p class="text-muted">No other products from this farmer.</p></div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <?php include "includes/footer.php"; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>