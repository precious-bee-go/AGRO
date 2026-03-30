<?php
require_once "config/config.php";
require_once "config/database.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user details
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get cart items
$stmt = $conn->prepare("SELECT c.*, p.name, p.price, p.unit 
                        FROM cart c 
                        JOIN products p ON c.product_id = p.id 
                        WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if(empty($cart_items)) {
    header("Location: cart.php");
    exit();
}

$total = 0;
foreach($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include "includes/navbar.php"; ?>
    
    <div class="container mt-4">
        <h2>Checkout</h2>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Shipping Information</h5>
                    </div>
                    <div class="card-body">
                        <form action="handlers/checkout_handler.php" method="POST" id="checkoutForm">
                            <div class="mb-3">
                                <label>Full Name</label>
                                <input type="text" class="form-control" value="<?php echo $user['full_name']; ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label>Email</label>
                                <input type="email" class="form-control" value="<?php echo $user['email']; ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label>Phone</label>
                                <input type="text" class="form-control" value="<?php echo $user['phone']; ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label>Shipping Address *</label>
                                <textarea name="shipping_address" class="form-control" rows="3" required><?php echo $user['address']; ?></textarea>
                            </div>
                            
                            <h5 class="mt-4">Payment Method</h5>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" value="cod" id="cod" checked>
                                    <label class="form-check-label" for="cod">
                                        Cash on Delivery
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" value="card" id="card">
                                    <label class="form-check-label" for="card">
                                        Credit/Debit Card
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" value="upi" id="upi">
                                    <label class="form-check-label" for="upi">
                                        UPI
                                    </label>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <?php foreach($cart_items as $item): ?>
                                <div class="d-flex justify-content-between mb-2">
                                    <span><?php echo $item['name']; ?> x <?php echo $item['quantity']; ?></span>
                                    <span><?php echo number_format($item['price'] * $item['quantity'], 2); ?>FCFA</span>
                                </div>
                            <?php endforeach; ?>
                            <hr>
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Total:</span>
                                <span><?php echo number_format($total, 2); ?>FCFA</span>
                            </div>
                        </div>
                        
                        <button type="submit" form="checkoutForm" class="btn btn-success w-100">Place Order</button>
                        <a href="cart.php" class="btn btn-outline-success w-100 mt-2">Back to Cart</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include "includes/footer.php"; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>