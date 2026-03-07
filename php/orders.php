<?php
session_start();
include 'db_connect.php';

// Check if user is logged in and is a buyer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'buyer') {
    header("Location: ../html/Login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle feedback submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_feedback'])) {
    $order_id = (int)$_POST['order_id'];
    $rating = (int)$_POST['rating'];
    $feedback = mysqli_real_escape_string($conn, $_POST['feedback']);
    
    // Update order status to completed
    $conn->query("UPDATE orders SET status = 'completed' WHERE id = '$order_id' AND buyer_id = '$user_id'");
    
    // Store feedback (you might want to add a feedback table, for now using messages)
    $subject = "Feedback for Order #$order_id";
    $message = "Rating: $rating/5\nFeedback: $feedback";
    $conn->query("INSERT INTO messages (sender_id, receiver_id, subject, message) VALUES ('$user_id', '1', '$subject', '$message')"); // Assuming admin id is 1
    
    $feedback_message = "Thank you for your feedback!";
}

// Fetch user's orders
$sql = "SELECT o.*, GROUP_CONCAT(p.name SEPARATOR ', ') as products 
        FROM orders o 
        JOIN order_items oi ON o.id = oi.order_id 
        JOIN products p ON oi.product_id = p.id 
        WHERE o.buyer_id = '$user_id' 
        GROUP BY o.id 
        ORDER BY o.created_at DESC";
$orders_result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Preshy Marketplace</title>
    <link rel="stylesheet" href="../css/orders.css">
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

    <div class="orders-container">
        <h1>My Orders</h1>

        <?php if (isset($feedback_message)) echo "<p class='message'>$feedback_message</p>"; ?>

        <?php if ($orders_result->num_rows > 0): ?>
            <div class="orders-list">
                <?php while($order = $orders_result->fetch_assoc()): ?>
                    <div class="order-card">
                        <h3>Order #<?php echo $order['id']; ?></h3>
                        <p>Products: <?php echo $order['products']; ?></p>
                        <p>Total: <?php echo $order['total_amount']; ?> frs</p>
                        <p>Status: <?php echo $order['status']; ?></p>
                        <p>Date: <?php echo $order['created_at']; ?></p>
                        
                        <?php if ($order['status'] == 'delivered'): ?>
                            <form action="orders.php" method="POST">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <div class="feedback-form">
                                    <h4>Leave Feedback</h4>
                                    <label>Rating: 
                                        <select name="rating" required>
                                            <option value="5">5 Stars</option>
                                            <option value="4">4 Stars</option>
                                            <option value="3">3 Stars</option>
                                            <option value="2">2 Stars</option>
                                            <option value="1">1 Star</option>
                                        </select>
                                    </label>
                                    <textarea name="feedback" placeholder="Your feedback..." required></textarea>
                                    <button type="submit" name="submit_feedback" class="btn-submit">Submit Feedback</button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>You haven't placed any orders yet.</p>
        <?php endif; ?>
    </div>

    <script src="../js/orders.js"></script>
</body>
</html>

<?php $conn->close(); ?>