<?php
require_once "../config/config.php";
require_once "../config/database.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'farmer') {
    header("Location: ../login.php");
    exit();
}

$farmer_id = $_SESSION['user_id'];

// Check payment status
$stmt = $conn->prepare("SELECT payment_status, payment_amount FROM users WHERE id = ?");
$stmt->execute([$farmer_id]);
$payment_info = $stmt->fetch(PDO::FETCH_ASSOC);

if($payment_info['payment_status'] != 'paid') {
    header("Location: payment.php");
    exit();
}

// Fetch categories (restricted)
$categories = ['Vegetables', 'Fruits', 'Tubers', 'Spices'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Product - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/product.css">
</head>
<body>
    <?php include "../includes/navbar.php"; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0"><i class="fas fa-plus me-2"></i>Add New Product</h4>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo BASE_URL; ?>/handlers/product_handler.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="add">

                            <div class="mb-3">
                                <label for="name" class="form-label">Product Name *</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>

                            <div class="mb-3">
                                <label for="category" class="form-label">Category *</label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo $cat; ?>"><?php echo $cat; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="price" class="form-label">Price (FCFA) *</label>
                                    <input type="number" step="0.01" class="form-control" id="price" name="price" min="0" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="unit" class="form-label">Unit *</label>
                                    <select class="form-select" id="unit" name="unit" required>
                                        <option value="">Select Unit</option>
                                        <option value="bunch">Bunch</option>
                                        <option value="basket">Basket</option>
                                        <option value="hip">Hip</option>
                                        <option value="bags">Bags</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="quantity" class="form-label">Quantity *</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">Product Image</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                <div class="form-text">Upload a clear image of your product (optional but recommended)</div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="ready_status" class="form-label">Product Status</label>
                                    <select class="form-select" id="ready_status" name="ready_status">
                                        <option value="cultivating">Currently Cultivating</option>
                                        <option value="ready">Ready for Harvest/Sale</option>
                                        <option value="harvested">Already Harvested</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="harvest_date" class="form-label">Harvest Date</label>
                                    <input type="date" class="form-control" id="harvest_date" name="harvest_date">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="estimated_ready_time" class="form-label">Estimated Ready Time</label>
                                <input type="text" class="form-control" id="estimated_ready_time" name="estimated_ready_time"
                                       placeholder="e.g., 2 weeks, 3 months">
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-save me-2"></i>Add Product
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include "../includes/footer.php"; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>