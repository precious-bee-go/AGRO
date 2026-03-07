<?php
session_start();
include 'db_connect.php';

// Check if user is logged in and is a farmer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'farmer') {
    header("Location: Login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

// Handle product posting
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['post_product'])) {
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $quantity = (int)$_POST['quantity'];
    $price = (float)$_POST['price'];
    $readiness_time = mysqli_real_escape_string($conn, $_POST['readiness_time']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);

    $sql = "INSERT INTO products (farmer_id, name, description, quantity, price, readiness_time, category, status) 
            VALUES ('$user_id', '$product_name', '$description', '$quantity', '$price', '$readiness_time', '$category', 'pending')";

    if ($conn->query($sql) === TRUE) {
        $message = "Product posted successfully!";
    } else {
        $message = "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Fetch farmer's products
$sql = "SELECT * FROM products WHERE farmer_id = '$user_id' ORDER BY created_at DESC";
$products_result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Dashboard - Preshy Marketplace</title>
    <link rel="stylesheet" href="/agro/css/farmer_dashboard.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">Preshy<span>Marketplace</span></div>
        <ul class="nav-links">
            <li><a href="farmer_dashboard.php">Dashboard</a></li>
            <li><a href="notifications.php">Notifications</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="dashboard-container">
        <h1>Welcome, <?php echo $user_name; ?>!</h1>
        <p>Manage your farm products here.</p>

        <?php if (isset($message)) echo "<p class='message'>$message</p>"; ?>

        <div class="post-product-section">
            <h2>Post New Product</h2>
            <form action="farmer_dashboard.php" method="POST">
                <input type="hidden" name="post_product" value="1">
                <div class="form-group">
                    <label for="product_name">Product Name:</label>
                    <input type="text" id="product_name" name="product_name" required>
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" required></textarea>
                </div>
                <div class="form-group">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" min="1" required>
                </div>
                <div class="form-group">
                    <label for="price">Price per unit (frs):</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label for="readiness_time">Readiness Time:</label>
                    <input type="text" id="readiness_time" name="readiness_time" placeholder="e.g., 2-3 weeks" required>
                </div>
                <div class="form-group">
                    <label for="category">Category:</label>
                    <select id="category" name="category" required>
                        <option value="vegetables">Vegetables</option>
                        <option value="fruits">Fruits</option>
                        <option value="grains">Grains</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <button type="submit" class="btn-post">Post Product</button>
            </form>
        </div>

        <div class="products-section">
            <h2>Your Products</h2>
            <?php if ($products_result->num_rows > 0): ?>
                <div class="products-grid">
                    <?php while($product = $products_result->fetch_assoc()): ?>
                        <div class="product-card">
                            <h3><?php echo $product['name']; ?></h3>
                            <p><?php echo $product['description']; ?></p>
                            <p>Quantity: <?php echo $product['quantity']; ?></p>
                            <p>Price: <?php echo $product['price']; ?> frs</p>
                            <p>Ready in: <?php echo $product['readiness_time']; ?></p>
                            <p>Status: <?php echo $product['status']; ?></p>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p>No products posted yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="/agro/js/farmer_dashboard.js"></script>
</body>
</html>

<?php $conn->close(); ?>