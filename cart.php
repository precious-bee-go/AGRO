<?php
require_once "config/config.php";
require_once "config/database.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
    $_SESSION['error'] = "Please login as customer to view cart";
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get cart items
$stmt = $conn->prepare("SELECT c.id as cart_id, c.quantity, p.*, u.full_name as farmer_name 
                        FROM cart c 
                        JOIN products p ON c.product_id = p.id 
                        JOIN users u ON p.farmer_id = u.id
                        WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Shopping Cart - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .cart-item-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }
        .quantity-input {
            width: 70px;
            text-align: center;
        }
    </style>
</head>
<body>
    <?php include "includes/navbar.php"; ?>
    
    <div class="container mt-4">
        <h2 class="mb-4">Shopping Cart</h2>
        
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if(count($cart_items) > 0): ?>
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Cart Items (<?php echo count($cart_items); ?>)</h5>
                        </div>
                        <div class="card-body">
                            <?php foreach($cart_items as $index => $item): ?>
                                <div class="row <?php echo $index < count($cart_items)-1 ? 'border-bottom pb-3 mb-3' : ''; ?>">
                                    <div class="col-md-2">
                                        <img src="<?php echo $item['image'] ? 'uploads/products/'.$item['image'] : 'https://via.placeholder.com/80x80?text=Product'; ?>" 
                                             class="cart-item-image" alt="<?php echo $item['name']; ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <h5><?php echo $item['name']; ?></h5>
                                        <p class="text-muted mb-1">Farmer: <?php echo $item['farmer_name']; ?></p>
                                        <p class="text-muted">Category: <?php echo $item['category']; ?></p>
                                    </div>
                                    <div class="col-md-2">
                                        <p class="fw-bold mb-1">Price</p>
                                        <p><?php echo number_format($item['price'], 2); ?>/<?php echo $item['unit']; ?>FCFA</p>
                                    </div>
                                    <div class="col-md-2">
                                        <p class="fw-bold mb-1">Quantity</p>
                                        <form action="handlers/cart_handler.php" method="POST" class="d-flex align-items-center">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                                                   min="1" max="<?php echo $item['quantity']; ?>" 
                                                   class="form-control form-control-sm quantity-input me-2"
                                                   onchange="this.form.submit()">
                                        </form>
                                    </div>
                                    <div class="col-md-1">
                                        <p class="fw-bold mb-1">Total</p>
                                        <p class="text-success fw-bold"><?php echo number_format($item['price'] * $item['quantity'], 2); ?>FCFA</p>
                                    </div>
                                    <div class="col-md-1">
                                        <form action="handlers/cart_handler.php" method="POST">
                                            <input type="hidden" name="action" value="remove">
                                            <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Remove this item from cart?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                            <div class="mt-3">
                                <form action="handlers/cart_handler.php" method="POST" class="d-inline">
                                    <input type="hidden" name="action" value="clear">
                                    <button type="submit" class="btn btn-outline-danger" 
                                            onclick="return confirm('Clear your entire cart?')">
                                        <i class="fas fa-trash-alt"></i> Clear Cart
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <td>Subtotal:</td>
                                    <td class="text-end"><?php echo number_format($total, 2); ?>FCFA</td>
                                </tr>
                                <tr>
                                    <td>Shipping:</td>
                                    <td class="text-end text-success">Free</td>
                                </tr>
                                <tr>
                                    <td>Tax (GST):</td>
                                    <td class="text-end"><?php echo number_format($total * 0.05, 2); ?>FCFA</td>
                                </tr>
                                <tr class="fw-bold fs-5">
                                    <td>Total:</td>
                                    <td class="text-end text-success"><?php echo number_format($total * 1.05, 2); ?>FCFA</td>
                                </tr>
                            </table>
                            
                            <div class="d-grid gap-2">
                                <a href="checkout.php" class="btn btn-success btn-lg">
                                    <i class="fas fa-credit-card"></i> Proceed to Checkout
                                </a>
                                <a href="product.php" class="btn btn-outline-success">
                                    <i class="fas fa-shopping-bag"></i> Continue Shopping
                                </a>
                            </div>
                            
                            <div class="mt-3">
                                <p class="text-muted small mb-0">
                                    <i class="fas fa-shield-alt"></i> Secure checkout. We never share your information.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Accepted Payment Methods -->
                    <div class="card mt-3">
                        <div class="card-body text-center">
                            <p class="mb-2">We Accept</p>
                            <i class="fab fa-cc-visa fa-2x mx-1 text-primary"></i>
                            <i class="fab fa-cc-mastercard fa-2x mx-1 text-danger"></i>
                            <i class="fab fa-cc-amex fa-2x mx-1 text-info"></i>
                            <i class="fab fa-cc-paypal fa-2x mx-1 text-dark"></i>
                            <p class="mt-2 mb-0 small">Cash on Delivery also available</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-shopping-cart fa-5x text-muted"></i>
                </div>
                <h3 class="text-muted">Your cart is empty</h3>
                <p class="lead">Looks like you haven't added any items to your cart yet.</p>
                <a href="product.php" class="btn btn-success btn-lg">
                    <i class="fas fa-store"></i> Start Shopping
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include "includes/footer.php"; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Auto-submit when quantity changes
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            this.form.submit();
        });
    });
    </script>
</body>
</html>