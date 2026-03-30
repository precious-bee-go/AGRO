<?php
require_once "../config/config.php";
require_once "../config/database.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user's orders
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include "../includes/navbar.php"; ?>
    
    <div class="container mt-4">
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        
        <h2>My Dashboard</h2>
        
        <div class="row">
            <div class="col-md-3">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Orders</h5>
                        <h2><?php echo count($orders); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Completed</h5>
                        <h2>
                            <?php 
                            $completed = array_filter($orders, function($o) { return $o['status'] == 'delivered'; });
                            echo count($completed);
                            ?>
                        </h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Pending</h5>
                        <h2>
                            <?php 
                            $pending = array_filter($orders, function($o) { return $o['status'] == 'pending'; });
                            echo count($pending);
                            ?>
                        </h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Spent</h5>
                        <h2>
                            <?php 
                            $total = array_sum(array_column($orders, 'total_amount'));
                            echo number_format($total, 2);
                            ?>FCFA
                        </h2>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- My Orders -->
        <div class="card mt-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">My Orders</h5>
            </div>
            <div class="card-body">
                <?php if(count($orders) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date</th>
                                    <th>Total Amount</th>
                                    <th>Payment Method</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($orders as $order): ?>
                                <tr>
                                    <td>#<?php echo $order['id']; ?></td>
                                    <td><?php echo date('d M Y', strtotime($order['order_date'])); ?></td>
                                    <td><?php echo number_format($order['total_amount'], 2); ?>FCFA</td>
                                    <td><?php echo strtoupper($order['payment_method']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo $order['status'] == 'delivered' ? 'success' : 
                                                ($order['status'] == 'cancelled' ? 'danger' : 
                                                ($order['status'] == 'pending' ? 'warning' : 'info')); 
                                        ?>">
                                            <?php echo $order['status']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">View Details</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">You haven't placed any orders yet.</p>
                    <a href="../product.php" class="btn btn-success">Start Shopping</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php include "../includes/footer.php"; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>