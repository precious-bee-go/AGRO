<?php
session_start();
include 'db_connect.php';

// Check if user is logged in and is a buyer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'buyer') {
    header("Location: ../html/Login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle remove from cart
if (isset($_GET['remove'])) {
    $product_id = (int)$_GET['remove'];
    unset($_SESSION['cart'][$product_id]);
    header("Location: cart.php");
    exit();
}

// Handle checkout
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['checkout'])) {
    if (!empty($_SESSION['cart'])) {
        // Start transaction
        $conn->begin_transaction();

        try {
            // Create order
            $total_amount = 0;
            foreach ($_SESSION['cart'] as $product_id => $item) {
                $total_amount += $item['price'] * $item['quantity'];
            }

            $sql = "INSERT INTO orders (buyer_id, total_amount, status) VALUES ('$user_id', '$total_amount', 'pending')";
            $conn->query($sql);
            $order_id = $conn->insert_id;

            // Add order items
            foreach ($_SESSION['cart'] as $product_id => $item) {
                $quantity = $item['quantity'];
                $price = $item['price'];
                $max_wait = $item['max_wait'];

                $sql = "INSERT INTO order_items (order_id, product_id, quantity, price, max_wait) 
                        VALUES ('$order_id', '$product_id', '$quantity', '$price', '$max_wait')";
                $conn->query($sql);

                // Update product quantity
                $sql = "UPDATE products SET quantity = quantity - $quantity WHERE id = '$product_id'";
                $conn->query($sql);

                // If quantity becomes 0, set status to sold out
                $sql = "UPDATE products SET status = 'sold_out' WHERE id = '$product_id' AND quantity = 0";
                $conn->query($sql);
            }

            $conn->commit();
            $_SESSION['cart'] = [];
            $message = "Order placed successfully! You will be notified when products are ready.";
        } catch (Exception $e) {
            $conn->rollback();
            $message = "Error placing order: " . $e->getMessage();
        }
    } else {
        $message = "Your cart is empty.";
    }
}

$cart = $_SESSION['cart'] ?? [];
$total = 0;
foreach ($cart as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - Preshy Marketplace</title>
    <link rel="stylesheet" href="../css/cart.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">Preshy<span>Marketplace</span></div>
        <ul class="nav-links">
            <li><a href="shop.php">Shop</a></li>
            <li><a href="cart.php">Cart</a></li>
            <li><a href="orders.php">My Orders</a></li>
            <li><a href="notifications.php">Notifications</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="cart-container">
        <h1>Your Cart</h1>

        <?php if (isset($message)) echo "<p class='message'>$message</p>"; ?>

        <?php if (!empty($cart)): ?>
            <div class="cart-items">
                <?php foreach ($cart as $product_id => $item): ?>
                    <div class="cart-item">
                        <h3><?php echo $item['name']; ?></h3>
                        <p>Quantity: <?php echo $item['quantity']; ?></p>
                        <p>Price: <?php echo $item['price']; ?> frs each</p>
                        <p>Max Wait: <?php echo $item['max_wait']; ?></p>
                        <p>Subtotal: <?php echo $item['price'] * $item['quantity']; ?> frs</p>
                        <a href="cart.php?remove=<?php echo $product_id; ?>" class="btn-remove">Remove</a>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-total">
                <h3>Total: <?php echo $total; ?> frs</h3>
                <form action="cart.php" method="POST">
                    <button type="submit" name="checkout" class="btn-checkout">Checkout (Cash on Delivery)</button>
                </form>
            </div>
        <?php else: ?>
            <p>Your cart is empty. <a href="shop.php">Continue shopping</a></p>
        <?php endif; ?>
    </div>

    <script src="../js/cart.js"></script>
</body>
</html>

<?php $conn->close(); ?>