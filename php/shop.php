<?php
session_start();
include 'db_connect.php';

// Check if user is logged in and is a buyer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'buyer') {
    header("Location: ../html/Login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle add to cart
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    $max_wait = mysqli_real_escape_string($conn, $_POST['max_wait']);

    // Check if product exists and has enough quantity
    $sql = "SELECT * FROM products WHERE id = '$product_id' AND status = 'available'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        if ($product['quantity'] >= $quantity) {
            // Add to cart
            $_SESSION['cart'][$product_id] = [
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => $quantity,
                'max_wait' => $max_wait,
                'farmer_id' => $product['farmer_id']
            ];
            $message = "Added to cart successfully!";
        } else {
            $message = "Not enough quantity available.";
        }
    } else {
        $message = "Product not available.";
    }
}

// Fetch available products
$sql = "SELECT p.*, u.name as farmer_name FROM products p JOIN users u ON p.farmer_id = u.id WHERE p.status = 'available' AND p.quantity > 0";
$products_result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - Preshy Marketplace</title>
    <link rel="stylesheet" href="../css/shop.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">Preshy<span>Marketplace</span></div>
        <ul class="nav-links">
            <li><a href="shop.php">Shop</a></li>
            <li><a href="cart.php">Cart (<?php echo count($_SESSION['cart']); ?>)</a></li>
            <li><a href="orders.php">My Orders</a></li>
            <li><a href="notifications.php">Notifications</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="shop-container">
        <h1>Welcome to the Shop, <?php echo $user_name; ?>!</h1>
        <p>Choose fresh products from local farmers.</p>

        <?php if (isset($message)) echo "<p class='message'>$message</p>"; ?>

        <div class="products-grid">
            <?php if ($products_result->num_rows > 0): ?>
                <?php while($product = $products_result->fetch_assoc()): ?>
                    <div class="product-card">
                        <h3><?php echo $product['name']; ?></h3>
                        <p><?php echo $product['description']; ?></p>
                        <p>Farmer: <?php echo $product['farmer_name']; ?></p>
                        <p>Price: <?php echo $product['price']; ?> frs per unit</p>
                        <p>Available: <?php echo $product['quantity']; ?> units</p>
                        <p>Ready in: <?php echo $product['readiness_time']; ?></p>

                        <form action="shop.php" method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <div class="form-group">
                                <label>Quantity:</label>
                                <input type="number" name="quantity" min="1" max="<?php echo $product['quantity']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Max Wait Time:</label>
                                <input type="text" name="max_wait" placeholder="e.g., 4 weeks" required>
                            </div>
                            <button type="submit" name="add_to_cart" class="btn-add">Add to Cart</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No products available at the moment.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="../js/shop.js"></script>
</body>
</html>

<?php $conn->close(); ?>