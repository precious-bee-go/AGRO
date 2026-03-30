<?php
require_once "config/config.php";
require_once "config/database.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Fresh Agricultural Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .hero-section {
            background: linear-gradient(rgba(16, 16, 16, 0.5), rgba(0,0,0,0.5)), url('<?php echo BASE_URL; ?>../assets/images/welcome_page.png');
            background-size: cover;
            height: 500px;
            color: white;
            display: flex;
            align-items: center;
            text-align: center; 
        }
        .product-card {
            transition: transform 0.3s;
            margin-bottom: 20px;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(200, 168, 168, 0.1);
        }
        /* For the entire page background */


/* Optional: Add a dark overlay to make text readable */

    </style>
</head>
<body>
    <?php include "includes/navbar.php"; ?>
    

    <!-- Hero Section -->
    <div class="hero-section">
       
        <div class="container">
            <h1 class="display-4">Fresh From Farm to Your Table</h1>
            <p class="lead">Discover the finest agricultural products directly from local farmers</p>
            <a href="product.php" class="btn btn-success btn-lg">Shop Now</a>
        </div>
    </div>

    <!-- Categories -->
    <div class="container my-5">
        <h2 class="text-center mb-4">Shop by Category</h2>
        <div class="row">
            <?php
            $categories = ['Vegetables', 'Fruits', 'Grains', 'Dairy', 'Organic', 'Seeds'];
            foreach($categories as $cat) {
                echo '<div class="col-md-4 mb-3">';
                echo '<div class="card text-center">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . $cat . '</h5>';
                echo '<a href="product.php?category=' . $cat . '" class="btn btn-outline-success">Browse</a>';
                echo '</div></div></div>';
            }
            ?>
        </div>
    </div>

    <!-- Featured Products -->
    <div class="container my-5">
        <h2 class="text-center mb-4">Featured Products</h2>
        <div class="row">
            <?php
            $stmt = $conn->prepare("SELECT p.*, u.full_name as farmer_name FROM products p 
                                    JOIN users u ON p.farmer_id = u.id 
                                    WHERE p.status != 'deleted' AND p.status != 'sold' AND p.quantity > 0
                                    ORDER BY p.created_at DESC LIMIT 8");
            $stmt->execute();
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach($products as $product) {
                echo '<div class="col-md-3">';
                echo '<div class="card product-card">';
                

                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . $product['name'] . '</h5>';
                echo '<p class="card-text">' . number_format($product['price'], 2) . ' per ' . $product['unit'] . '</p>';
                echo '<p class="card-text"><small class="text-muted">Farmer: ' . $product['farmer_name'] . '</small></p>';
                
                if($product['status'] == 'sold' || $product['quantity'] <= 0) {
                    echo '<div class="alert alert-danger mb-2" style="padding: 5px 10px; font-size: 0.9rem;"><strong>Sold Out</strong></div>';
                    echo '<a href="product_detail.php?id=' . $product['id'] . '" class="btn btn-secondary btn-sm disabled">View Details</a>';
                } else {
                    echo '<a href="product_detail.php?id=' . $product['id'] . '" class="btn btn-success btn-sm">View Details</a>';
                }
                echo '</div></div></div>';
            }
            ?>
        </div>
    </div>

    <!-- Footer -->
    <?php include "includes/footer.php"; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>