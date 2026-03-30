<?php
require_once "../config/config.php";
require_once "../config/database.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'farmer') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get farmer payment status
$stmt = $conn->prepare("SELECT payment_status, payment_amount, payment_date FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$payment_info = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle payment submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['make_payment'])) {
    // In a real application, this would integrate with a payment gateway
    // For now, we'll simulate the payment process

    $payment_method = $_POST['payment_method'];
    $amount = floatval($_POST['amount']);

    if($amount == 1000) {
        // Update payment status
        $stmt = $conn->prepare("UPDATE users SET payment_status = 'paid', payment_date = NOW() WHERE id = ?");
        if($stmt->execute([$user_id])) {
            $_SESSION['success'] = "Payment successful! You can now add products.";
            header("Location: dashboard.php");
            exit();
        } else {
            $_SESSION['error'] = "Payment failed. Please try again.";
        }
    } else {
        $_SESSION['error'] = "Invalid payment amount.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Payment - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .payment-card {
            max-width: 600px;
            margin: 2rem auto;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .payment-amount {
            font-size: 2.5rem;
            font-weight: bold;
            color: #28a745;
            text-align: center;
        }
        .payment-methods {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }
        .payment-method {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .payment-method:hover {
            border-color: #28a745;
            background-color: #f8f9fa;
        }
        .payment-method.selected {
            border-color: #28a745;
            background-color: #d4edda;
        }
        .status-card {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }
    </style>
</head>
<body>
    <?php include "../includes/navbar.php"; ?>

    <div class="container mt-4">
        <?php if(!empty($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if(!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if($payment_info['payment_status'] == 'paid'): ?>
            <!-- Payment Completed Card -->
            <div class="card status-card payment-card">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-4x mb-3"></i>
                    <h2 class="card-title">Payment Completed!</h2>
                    <p class="card-text">You have successfully paid the registration fee.</p>
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <strong>Amount Paid:</strong><br>
                            <span class="payment-amount"><?php echo number_format($payment_info['payment_amount'], 2); ?> FCFA</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Payment Date:</strong><br>
                            <span class="h5"><?php echo date('d M Y, H:i', strtotime($payment_info['payment_date'])); ?></span>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="dashboard.php" class="btn btn-light btn-lg me-2">
                            <i class="fas fa-tachometer-alt me-2"></i>Go to Dashboard
                        </a>
                        <a href="add_product.php" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-plus me-2"></i>Add Product
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Payment Required Card -->
            <div class="card payment-card">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Registration Fee Required</h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h5>One-time Registration Fee</h5>
                        <div class="payment-amount"><?php echo number_format($payment_info['payment_amount'], 2); ?> FCFA</div>
                        <p class="text-muted">Required to start selling your agricultural products</p>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Why pay this fee?</strong> This helps us maintain the platform, verify farmers, and provide better services to connect you with buyers.
                    </div>

                    <form method="POST" id="paymentForm">
                        <input type="hidden" name="amount" value="<?php echo $payment_info['payment_amount']; ?>">

                        <div class="mb-4">
                            <label class="form-label fw-bold">Select Payment Method</label>
                            <div class="payment-methods">
                                <div class="payment-method" data-method="mobile_money">
                                    <i class="fas fa-mobile-alt fa-2x mb-2 text-success"></i>
                                    <div class="fw-bold">Mobile Money</div>
                                    <small class="text-muted">MTN, Orange, etc.</small>
                                    <input type="radio" name="payment_method" value="mobile_money" class="d-none" required>
                                </div>
                                <div class="payment-method" data-method="bank_transfer">
                                    <i class="fas fa-university fa-2x mb-2 text-primary"></i>
                                    <div class="fw-bold">Bank Transfer</div>
                                    <small class="text-muted">Direct bank transfer</small>
                                    <input type="radio" name="payment_method" value="bank_transfer" class="d-none" required>
                                </div>
                                <div class="payment-method" data-method="card">
                                    <i class="fas fa-credit-card fa-2x mb-2 text-warning"></i>
                                    <div class="fw-bold">Credit/Debit Card</div>
                                    <small class="text-muted">Visa, Mastercard</small>
                                    <input type="radio" name="payment_method" value="card" class="d-none" required>
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" name="make_payment" class="btn btn-success btn-lg px-5" id="payButton" disabled>
                                <i class="fas fa-credit-card me-2"></i>Pay <?php echo number_format($payment_info['payment_amount'], 2); ?> FCFA
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php include "../includes/footer.php"; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Payment method selection
        document.querySelectorAll('.payment-method').forEach(method => {
            method.addEventListener('click', function() {
                // Remove selected class from all methods
                document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('selected'));
                // Add selected class to clicked method
                this.classList.add('selected');
                // Check the radio button
                this.querySelector('input[type="radio"]').checked = true;
                // Enable pay button
                document.getElementById('payButton').disabled = false;
            });
        });

        // Form validation
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
            if (!selectedMethod) {
                e.preventDefault();
                alert('Please select a payment method.');
            }
        });
    </script>
</body>
</html>