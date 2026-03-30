<?php
require_once "../config/config.php";
require_once "../config/database.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'farmer') {
    header("Location: ../login.php");
    exit();
}

$farmer_id = $_SESSION['user_id'];

// Get orders for this farmer's products
$stmt = $conn->prepare("
    SELECT DISTINCT o.*, u.full_name AS buyer_name, u.email AS buyer_email
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN products p ON oi.product_id = p.id
    JOIN users u ON o.user_id = u.id
    WHERE p.farmer_id = ?
    ORDER BY o.order_date DESC
");
$stmt->execute([$farmer_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include "../includes/navbar.php"; ?>

    <div class="container mt-4">
        <h2>Orders for My Products</h2>

        <?php if (empty($orders)): ?>
            <div class="alert alert-info">
                <p>No orders received yet for your products.</p>
                <a href="add_product.php" class="btn btn-success">Add Your First Product</a>
            </div>
        <?php else: ?>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Buyer</th>
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
                                <td><?php echo htmlspecialchars($order['buyer_name']); ?></td>
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