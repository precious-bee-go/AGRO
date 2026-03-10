<?php
session_start();
include 'db_connect.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../html/Login.html");
    exit();
}

// Handle product approval/rejection
if (isset($_GET['approve'])) {
    $product_id = (int)$_GET['approve'];
    $conn->query("UPDATE products SET status = 'approved' WHERE id = '$product_id'");
    
    // Get farmer id
    $product_result = $conn->query("SELECT farmer_id FROM products WHERE id = '$product_id'");
    $product = $product_result->fetch_assoc();
    $farmer_id = $product['farmer_id'];
    
    // Send notification
    $subject = "Product Approved!";
    $message = "Your product has been approved and is now available for sale.";
    $conn->query("INSERT INTO messages (sender_id, receiver_id, subject, message) VALUES ('{$_SESSION['user_id']}', '$farmer_id', '$subject', '$message')");
    
    header("Location: admin.php");
    exit();
}

if (isset($_GET['reject'])) {
    $product_id = (int)$_GET['reject'];
    $conn->query("UPDATE products SET status = 'rejected' WHERE id = '$product_id'");
    
    // Get farmer id
    $product_result = $conn->query("SELECT farmer_id FROM products WHERE id = '$product_id'");
    $product = $product_result->fetch_assoc();
    $farmer_id = $product['farmer_id'];
    
    // Send notification
    $subject = "Product Rejected";
    $message = "Your product has been rejected. Please check the requirements and try again.";
    $conn->query("INSERT INTO messages (sender_id, receiver_id, subject, message) VALUES ('{$_SESSION['user_id']}', '$farmer_id', '$subject', '$message')");
    
    header("Location: admin.php");
    exit();
}
if (isset($_GET['ready'])) {
    $order_id = (int)$_GET['ready'];
    $conn->query("UPDATE orders SET status = 'ready' WHERE id = '$order_id'");
    
    // Get buyer id
    $order_result = $conn->query("SELECT buyer_id FROM orders WHERE id = '$order_id'");
    $order = $order_result->fetch_assoc();
    $buyer_id = $order['buyer_id'];
    
    // Send notification
    $subject = "Your order is ready!";
    $message = "Your order #$order_id is now ready for pickup/delivery.";
    $conn->query("INSERT INTO messages (sender_id, receiver_id, subject, message) VALUES ('{$_SESSION['user_id']}', '$buyer_id', '$subject', '$message')");
    
    header("Location: admin.php");
    exit();
}

if (isset($_GET['delivered'])) {
    $order_id = (int)$_GET['delivered'];
    $conn->query("UPDATE orders SET status = 'delivered' WHERE id = '$order_id'");
    
    // Get buyer id
    $order_result = $conn->query("SELECT buyer_id FROM orders WHERE id = '$order_id'");
    $order = $order_result->fetch_assoc();
    $buyer_id = $order['buyer_id'];
    
    // Send notification
    $subject = "Your order has been delivered!";
    $message = "Your order #$order_id has been delivered. Please confirm receipt and leave feedback.";
    $conn->query("INSERT INTO messages (sender_id, receiver_id, subject, message) VALUES ('{$_SESSION['user_id']}', '$buyer_id', '$subject', '$message')");
    
    header("Location: admin.php");
    exit();
}

// Fetch pending products
$pending_products = $conn->query("SELECT p.*, u.name as farmer_name FROM products p JOIN users u ON p.farmer_id = u.id WHERE p.status = 'pending'");

// Fetch all users
$users = $conn->query("SELECT * FROM users ORDER BY user_type, name");

// Fetch orders
$orders = $conn->query("SELECT o.*, u.name as buyer_name FROM orders o JOIN users u ON o.buyer_id = u.id ORDER BY o.created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Preshy Marketplace</title>
    <link rel="stylesheet" href="/agro/css/admin.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">Preshy<span>Marketplace</span> - Admin</div>
        <ul class="nav-links">
            <li><a href="admin.php">Dashboard</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="admin-container">
        <h1>Admin Dashboard</h1>

        <div class="admin-section">
            <h2>Pending Product Approvals</h2>
            <?php if ($pending_products->num_rows > 0): ?>
                <div class="products-list">
                    <?php while($product = $pending_products->fetch_assoc()): ?>
                        <div class="product-item">
                            <h3><?php echo $product['name']; ?></h3>
                            <p>Farmer: <?php echo $product['farmer_name']; ?></p>
                            <p>Description: <?php echo $product['description']; ?></p>
                            <p>Quantity: <?php echo $product['quantity']; ?>, Price: <?php echo $product['price']; ?> frs</p>
                            <p>Ready in: <?php echo $product['readiness_time']; ?></p>
                            <div class="actions">
                                <a href="admin.php?approve=<?php echo $product['id']; ?>" class="btn-approve">Approve</a>
                                <a href="admin.php?reject=<?php echo $product['id']; ?>" class="btn-reject">Reject</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p>No pending products.</p>
            <?php endif; ?>
        </div>

        <div class="admin-section">
            <h2>All Users</h2>
            <div class="users-list">
                <?php while($user = $users->fetch_assoc()): ?>
                    <div class="user-item">
                        <p><strong><?php echo $user['name']; ?></strong> (<?php echo $user['user_type']; ?>)</p>
                        <p>Email: <?php echo $user['email']; ?></p>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="admin-section">
            <h2>Recent Orders</h2>
            <div class="orders-list">
                <?php while($order = $orders->fetch_assoc()): ?>
                    <div class="order-item">
                        <p>Order #<?php echo $order['id']; ?> by <?php echo $order['buyer_name']; ?></p>
                        <p>Total: <?php echo $order['total_amount']; ?> frs</p>
                        <p>Status: <?php echo $order['status']; ?></p>
                        <p>Date: <?php echo $order['created_at']; ?></p>
                        <?php if ($order['status'] == 'pending'): ?>
                            <a href="admin.php?ready=<?php echo $order['id']; ?>" class="btn-ready">Mark as Ready</a>
                        <?php elseif ($order['status'] == 'ready'): ?>
                            <a href="admin.php?delivered=<?php echo $order['id']; ?>" class="btn-delivered">Mark as Delivered</a>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
            <h2>Recent Notifications & Activities</h2>
            <?php
            $recent_messages = $conn->query("SELECT m.*, 
                                                   sender.name as sender_name, 
                                                   receiver.name as receiver_name 
                                            FROM messages m 
                                            JOIN users sender ON m.sender_id = sender.id 
                                            JOIN users receiver ON m.receiver_id = receiver.id 
                                            ORDER BY m.created_at DESC LIMIT 10");
            ?>
            <?php if ($recent_messages->num_rows > 0): ?>
                <div class="activities-list">
                    <?php while($msg = $recent_messages->fetch_assoc()): ?>
                        <div class="activity-item">
                            <p><strong><?php echo $msg['subject']; ?></strong></p>
                            <p>From: <?php echo $msg['sender_name']; ?> → To: <?php echo $msg['receiver_name']; ?></p>
                            <p><?php echo substr($msg['message'], 0, 100) . (strlen($msg['message']) > 100 ? '...' : ''); ?></p>
                            <small><?php echo $msg['created_at']; ?></small>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p>No recent activities.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="/agro/js/admin.js"></script>
</body>
</html>

<?php $conn->close(); ?>