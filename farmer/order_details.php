<?php
require_once "../config/config.php";
require_once "../config/database.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'farmer') {
    header("Location: ../login.php");
    exit();
}

$farmer_id = $_SESSION['user_id'];

if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: orders.php");
    exit();
}

$order_id = $_GET['id'];

// Fetch order details and verify the farmer owns products in this order
$stmt = $conn->prepare("
    SELECT o.*, u.full_name AS buyer_name, u.email AS buyer_email, u.phone AS buyer_phone
    FROM orders o
    JOIN users u ON o.user_id = u.id
    JOIN order_items oi ON o.id = oi.order_id
    JOIN products p ON oi.product_id = p.id
    WHERE o.id = ? AND p.farmer_id = ?
    LIMIT 1
");
$stmt->execute([$order_id, $farmer_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$order) {
    header("Location: orders.php");
    exit();
}

// Fetch order items for this farmer's products only
$stmt = $conn->prepare("
    SELECT oi.*, p.name, p.image, p.unit
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ? AND p.farmer_id = ?
");
$stmt->execute([$order_id, $farmer_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total for this farmer's items
$farmer_total = 0;
foreach ($items as $item) {
    $farmer_total += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include "../includes/navbar.php"; ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">Order #<?php echo $order['id']; ?> Details</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Order Date:</strong> <?php echo date('d M Y H:i', strtotime($order['order_date'])); ?>
                            </div>
                            <div class="col-md-6">
                                <strong>Status:</strong>
                                <span class="badge bg-<?php
                                    echo $order['status'] == 'delivered' ? 'success' :
                                        ($order['status'] == 'cancelled' ? 'danger' :
                                        ($order['status'] == 'pending' ? 'warning' : 'info'));
                                ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Buyer:</strong> <?php echo htmlspecialchars($order['buyer_name']); ?>
                            </div>
                            <div class="col-md-6">
                                <strong>Payment Method:</strong> <?php echo strtoupper($order['payment_method']); ?>
                            </div>
                        </div>

                        <?php if($order['buyer_email']): ?>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Buyer Email:</strong> <?php echo htmlspecialchars($order['buyer_email']); ?>
                            </div>
                            <?php if($order['buyer_phone']): ?>
                            <div class="col-md-6">
                                <strong>Buyer Phone:</strong> <?php echo htmlspecialchars($order['buyer_phone']); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <h5 class="mt-4">Your Products in This Order</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Image</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                                        <td>
                                            <?php if($item['image']): ?>
                                                <img src="../uploads/products/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" style="width: 50px; height: 50px; object-fit: cover;">
                                            <?php else: ?>
                                                <span class="text-muted">No image</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo number_format($item['price'], 2); ?> FCFA</td>
                                        <td><?php echo $item['quantity']; ?> <?php echo htmlspecialchars($item['unit']); ?></td>
                                        <td><?php echo number_format($item['price'] * $item['quantity'], 2); ?> FCFA</td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="table-primary">
                                        <td colspan="4" class="text-end"><strong>Your Total from This Order:</strong></td>
                                        <td><strong><?php echo number_format($farmer_total, 2); ?> FCFA</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="mt-3">
                            <a href="orders.php" class="btn btn-secondary">Back to Orders</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include "../includes/footer.php"; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>