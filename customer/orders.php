<?php
require_once "../config/config.php";
require_once "../config/database.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch orders of logged-in user
$stmt = $conn->prepare("
    SELECT * FROM orders
    WHERE user_id = ?
    ORDER BY order_date DESC
");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include "../includes/navbar.php"; ?>

    <div class="container mt-4">
        <h2>My Orders</h2>

        <?php if (empty($orders)): ?>
            <div class="alert alert-info">
                <p>You haven't placed any orders yet.</p>
                <a href="../product.php" class="btn btn-success">Start Shopping</a>
            </div>
        <?php else: ?>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Total Amount</th>
                                <th>Payment Method</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo number_format($order['total_amount'], 2); ?> FCFA</td>
                                <td><?php echo strtoupper($order['payment_method']); ?></td>
                                <td>
                                    <span class="badge bg-<?php
                                        echo $order['status'] == 'delivered' ? 'success' :
                                            ($order['status'] == 'cancelled' ? 'danger' :
                                            ($order['status'] == 'pending' ? 'warning' : 'info'));
                                    ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d M Y H:i', strtotime($order['order_date'])); ?></td>
                                <td>
                                    <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">View Details</a>
                                    <?php if($order['status'] == 'pending'): ?>
                                        <form action="../handlers/order_handler.php" method="POST" class="d-inline ms-1" onsubmit="return confirm('Are you sure you want to cancel this order?')">
                                            <input type="hidden" name="action" value="cancel_order">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php endif; ?>
    </div>

    <?php include "../includes/footer.php"; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>