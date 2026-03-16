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

<?php
// Assuming $conn and $user_id are already defined above this block

// --- 1. HANDLE PRODUCT POSTING ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['post_product'])) {
    // Sanitize inputs
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $quantity = (int)$_POST['quantity'];
    $price = (float)$_POST['price'];
    $readiness_time = mysqli_real_escape_string($conn, $_POST['readiness_time']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);

    /* NEW IMAGE LOGIC: 
       This creates a filename like "fresh_tomato.jpg" from the product name.
    */
    $image_name = strtolower(str_replace(' ', '_', $product_name)) . ".jpg";

    // Check if the file exists in your "images" folder. If not, use default.jpg
    if (!file_exists("images/" . $image_name)) {
        $image_name = "default.jpg";
    }

    // Insert into database (Including the 'image' column)
    $sql = "INSERT INTO products (farmer_id, name, description, quantity, price, readiness_time, category, image) 
            VALUES ('$user_id', '$product_name', '$description', '$quantity', '$price', '$readiness_time', '$category', '$image_name')";

    if ($conn->query($sql) === TRUE) {
        $message = "Product posted successfully!";
    } else {
        $message = "Error: " . $sql . "<br>" . $conn->error;
    }
}

<?php
// --- PART 1: BACKEND LOGIC (The code you sent earlier) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['post_product'])) {
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $quantity = (int)$_POST['quantity'];
    
    // NOTE: Changed to match your DB column 'price_per_unit'
    $price = (float)$_POST['price']; 
    
    $readiness_time = mysqli_real_escape_string($conn, $_POST['readiness_time']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);

    // 1. Generate the name (e.g. "White Yam" -> "white_yam.jpg")
    $image_name = strtolower(str_replace(' ', '_', $product_name)) . ".jpg";

    // 2. BETTER FILE CHECK: 
    // We check if the file exists. If it doesn't, we save 'default.jpg'
    // Ensure the folder path is correct (try adding ./ if it fails)
    if (!file_exists("images/" . $image_name)) {
        $image_name = "default.jpg";
    }

    // 3. Updated SQL query with your ACTUAL database column names
    $sql = "INSERT INTO products (farmer_id, name, description, quantity, price_per_unit, readiness_time, category_id, image) 
            VALUES ('$user_id', '$product_name', '$description', '$quantity', '$price', '$readiness_time', '6', '$image_name')";

    if ($conn->query($sql) === TRUE) {
        $message = "Product posted successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Fetch products for the list
$sql = "SELECT * FROM products WHERE farmer_id = '$user_id' ORDER BY id DESC";
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
        
        <?php if (isset($message)) echo "<p class='message' style='color: green;'>$message</p>"; ?>

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
                <div class="products-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
                    <?php while($product = $products_result->fetch_assoc()): ?>
                        <div class="product-card" style="border: 1px solid #ddd; padding: 15px; border-radius: 10px;">
                            
                            <img src="images/<?php echo $product['image']; ?>" 
                                 alt="Product Image" 
                                 style="width: 100%; height: 150px; object-fit: cover; border-radius: 5px; margin-bottom: 10px;"
                                 onerror="this.src='images/default.jpg';">

                            <h3><?php echo $product['name']; ?></h3>
                            <p><?php echo $product['description']; ?></p>
                            <p><strong>Quantity:</strong> <?php echo $product['quantity']; ?></p>
                            <p><strong>Price:</strong> <?php echo $product['price']; ?> frs</p>
                            <p><strong>Ready in:</strong> <?php echo $product['readiness_time']; ?></p>
                            <p><strong>Status:</strong> <?php echo $product['status']; ?></p>
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