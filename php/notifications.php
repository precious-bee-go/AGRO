<?php
session_start();
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../html/Login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

// Mark message as read
if (isset($_GET['read'])) {
    $message_id = (int)$_GET['read'];
    $conn->query("UPDATE messages SET is_read = TRUE WHERE id = '$message_id' AND receiver_id = '$user_id'");
    header("Location: notifications.php");
    exit();
}

// Fetch messages
if ($_SESSION['user_type'] == 'admin') {
    // Admin sees all messages
    $messages = $conn->query("SELECT m.*, 
                                     sender.name as sender_name, 
                                     receiver.name as receiver_name 
                              FROM messages m 
                              JOIN users sender ON m.sender_id = sender.id 
                              JOIN users receiver ON m.receiver_id = receiver.id 
                              ORDER BY m.created_at DESC");
} else {
    // Regular users see only their messages
    $messages = $conn->query("SELECT * FROM messages WHERE receiver_id = '$user_id' ORDER BY created_at DESC");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - Preshy Marketplace</title>
    <link rel="stylesheet" href="/agro/css/notifications.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">Preshy<span>Marketplace</span></div>
        <ul class="nav-links">
            <?php if ($user_type == 'buyer'): ?>
                <li><a href="shop.php">Shop</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="orders.php">My Orders</a></li>
            <?php elseif ($user_type == 'farmer'): ?>
                <li><a href="farmer_dashboard.php">Dashboard</a></li>
            <?php elseif ($user_type == 'admin'): ?>
                <li><a href="admin.php">Dashboard</a></li>
            <?php endif; ?>
            <li><a href="notifications.php">Notifications</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <div class="notifications-container">
        <h1><?php echo $_SESSION['user_type'] == 'admin' ? 'All System Notifications' : 'Your Notifications'; ?></h1>

        <?php if ($messages->num_rows > 0): ?>
            <div class="messages-list">
                <?php while($msg = $messages->fetch_assoc()): ?>
                    <div class="message-item <?php echo $msg['is_read'] ? 'read' : 'unread'; ?>">
                        <h3><?php echo $msg['subject']; ?></h3>
                        <?php if ($_SESSION['user_type'] == 'admin'): ?>
                            <p><strong>From:</strong> <?php echo $msg['sender_name']; ?> → <strong>To:</strong> <?php echo $msg['receiver_name']; ?></p>
                        <?php endif; ?>
                        <p><?php echo nl2br($msg['message']); ?></p>
                        <small><?php echo $msg['created_at']; ?></small>
                        <?php if (!$msg['is_read'] && $_SESSION['user_type'] != 'admin'): ?>
                            <a href="notifications.php?read=<?php echo $msg['id']; ?>" class="btn-mark-read">Mark as Read</a>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>No notifications yet.</p>
        <?php endif; ?>
    </div>

    <script src="/agro/js/notifications.js"></script>
</body>
</html>

<?php $conn->close(); ?>