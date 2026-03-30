<?php
require_once "../config/config.php";
require_once "../config/database.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
    header("Location: ../login.php");
    exit();
}

// Fetch all available products + farmer name
$stmt = $conn->query("
    SELECT products.*, users.full_name AS farmer_name
    FROM products
    JOIN users ON products.farmer_id = users.id
    WHERE products.status = 'available'
    ORDER BY products.created_at DESC
");

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Products - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .product-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            transition: transform 0.2s;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <?php include "../includes/navbar.php"; ?>

    <div class="container mt-4">
        <h2>All Products</h2>

        <div class="row">
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 col-sm-6">
                    <div class="product-card">
                        <?php if ($product['image']): ?>
                            <img src="../uploads/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
                        <?php else: ?>
                            <div class="text-center text-muted" style="height: 200px; display: flex; align-items: center; justify-content: center; border: 1px dashed #ddd; border-radius: 5px;">
                                <i class="fas fa-image fa-3x"></i>
                            </div>
                        <?php endif; ?>

                        <h5 class="mt-2"><?php echo htmlspecialchars($product['name']); ?></h5>
                        <p class="text-muted">By: <?php echo htmlspecialchars($product['farmer_name']); ?></p>
                        <p class="price-tag text-success"><?php echo number_format($product['price'], 2); ?> FCFA</p>
                        <p class="text-muted">Available: <?php echo $product['quantity']; ?> <?php echo htmlspecialchars($product['unit']); ?></p>

                        <a href="../product_detail.php?id=<?php echo $product['id']; ?>" class="btn btn-success btn-sm">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if(empty($products)): ?>
            <div class="text-center mt-5">
                <i class="fas fa-box-open fa-4x text-muted"></i>
                <h4 class="mt-3 text-muted">No products available</h4>
                <p class="text-muted">Check back later for new products from our farmers.</p>
            </div>
        <?php endif; ?>
    </div>

    <?php include "../includes/footer.php"; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>