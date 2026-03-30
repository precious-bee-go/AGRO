<?php
require_once "../config/config.php";
require_once "../config/database.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'farmer') {
    header("Location: ../login.php");
    exit();
}

$farmer_id = $_SESSION['user_id'];

// Get farmer payment status
$stmt = $conn->prepare("SELECT payment_status, payment_amount FROM users WHERE id = ?");
$stmt->execute([$farmer_id]);
$payment_info = $stmt->fetch(PDO::FETCH_ASSOC);

// Get farmer's products (exclude deleted)
$stmt = $conn->prepare("SELECT * FROM products WHERE farmer_id = ? AND status != 'deleted' ORDER BY created_at DESC");
$stmt->execute([$farmer_id]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get recent orders for farmer's products
$stmt = $conn->prepare("SELECT oi.*, o.order_date, o.status, o.user_id, p.name as product_name, u.username 
                        FROM order_items oi 
                        JOIN orders o ON oi.order_id = o.id 
                        JOIN products p ON oi.product_id = p.id 
                        JOIN users u ON o.user_id = u.id 
                        WHERE p.farmer_id = ? 
                        ORDER BY o.order_date DESC LIMIT 10");
$stmt->execute([$farmer_id]);
$recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate revenue earned by this farmer for completed orders (non-cancelled)
$stmt = $conn->prepare("SELECT SUM(oi.price * oi.quantity) AS farmer_revenue
                        FROM order_items oi
                        JOIN orders o ON oi.order_id = o.id
                        JOIN products p ON oi.product_id = p.id
                        WHERE p.farmer_id = ? AND o.status != 'cancelled'");
$stmt->execute([$farmer_id]);
$farmer_revenue = $stmt->fetchColumn() ?: 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Dashboard - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .ready-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .ready-cultivating {
            background: #f1e8cd;
            color: #977205;
            border: 1px solid #e6dec7;
        }
        .ready-ready {
            background: #d0ead6;
            color: #155724;
            border: 1px solid #bae7c5;
        }
        .ready-harvested {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .days-counter {
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 3px;
        }
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        .harvest-info {
            background: #e8f5e9;
            border-left: 4px solid #28a745;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <?php include "../includes/navbar.php"; ?>
    
    <div class="container mt-4">
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h3>Welcome back, <?php echo htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username']); ?>!</h3>
                        <p class="mb-0">Manage your farm products and track harvest schedules</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Status Alert -->
        <?php if($payment_info['payment_status'] != 'paid'): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Payment Required:</strong> You need to pay the registration fee of <?php echo number_format($payment_info['payment_amount'], 2); ?> FCFA to start adding products.
                    <a href="payment.php" class="alert-link">Make Payment Now</a>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Payment Completed:</strong> Your registration fee has been paid. You can now add and manage products.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Quick Actions</h5>
                        <div class="d-flex gap-2 flex-wrap">
                            <?php if($payment_info['payment_status'] == 'paid'): ?>
                                <a href="add_product.php" class="btn btn-success">
                                    <i class="fas fa-plus me-2"></i>Add New Product
                                </a>
                                <a href="my_products.php" class="btn btn-outline-primary">
                                    <i class="fas fa-box me-2"></i>View My Products
                                </a>
                            <?php else: ?>
                                <a href="payment.php" class="btn btn-warning">
                                    <i class="fas fa-credit-card me-2"></i>Make Payment
                                </a>
                                <button class="btn btn-secondary" disabled title="Complete payment first">
                                    <i class="fas fa-plus me-2"></i>Add New Product
                                </button>
                            <?php endif; ?>
                            <a href="orders.php" class="btn btn-outline-info">
                                <i class="fas fa-shopping-cart me-2"></i>View Orders
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <h6 class="text-muted">Total Products</h6>
                    <h2><?php echo count($products); ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <h6 class="text-muted">Ready to Harvest</h6>
                    <h2>
                        <?php 
                        $ready_count = 0;
                        foreach($products as $p) {
                            if(isset($p['ready_status']) && $p['ready_status'] == 'ready') $ready_count++;
                        }
                        echo $ready_count;
                        ?>
                    </h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <h6 class="text-muted">In Cultivation</h6>
                    <h2>
                        <?php 
                        $cultivating_count = 0;
                        foreach($products as $p) {
                            if(!isset($p['ready_status']) || $p['ready_status'] == 'cultivating') $cultivating_count++;
                        }
                        echo $cultivating_count;
                        ?>
                    </h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <h6 class="text-muted">Revenue Earned</h6>
                    <h2><?php echo number_format($farmer_revenue, 2); ?> FCFA</h2>
                </div>
            </div>
        </div>
        
        <!-- Products Section -->
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-seedling me-2"></i>My Farm Products</h5>
                        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addProductModal">
                            <i class="fas fa-plus"></i> Add New Product
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Product</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Harvest Status</th>
                                        <th>Ready Time</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($products as $product): 
                                        // Calculate days until harvest if harvest date is set
                                        $days_until_harvest = '';
                                        $harvest_status_class = 'ready-cultivating';
                                        $harvest_status_text = 'Cultivating';
                                        
                                        if(isset($product['ready_status'])) {
                                            if($product['ready_status'] == 'ready') {
                                                $harvest_status_class = 'ready-ready';
                                                $harvest_status_text = '✓ Ready to Harvest';
                                            } elseif($product['ready_status'] == 'harvested') {
                                                $harvest_status_class = 'ready-harvested';
                                                $harvest_status_text = 'Harvested';
                                            }
                                        }
                                        
                                        if(isset($product['harvest_date']) && $product['harvest_date']) {
                                            $harvest_date = new DateTime($product['harvest_date']);
                                            $today = new DateTime();
                                            $interval = $today->diff($harvest_date);
                                            
                                            if($harvest_date > $today) {
                                                $days_until_harvest = $interval->days . ' days remaining';
                                            } elseif($harvest_date == $today) {
                                                $days_until_harvest = 'Ready today!';
                                                $harvest_status_class = 'ready-ready';
                                                $harvest_status_text = '✓ Ready Today';
                                            } else {
                                                $days_until_harvest = 'Overdue by ' . $interval->days . ' days';
                                            }
                                        }
                                    ?>
                                    <tr>
                                        <td>
                                            <img src="<?php echo $product['image'] ? '../uploads/products/'.$product['image'] : 'https://via.placeholder.com/50x50?text=No+Image'; ?>" 
                                                 width="50" height="50" class="rounded" style="object-fit: cover;">
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?php echo $product['category']; ?></span>
                                        </td>
                                        <td>
                                            <strong><?php echo number_format($product['price'], 0); ?> FCFA</strong>
                                            <br><small class="text-muted">/<?php echo $product['unit']; ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $product['quantity'] > 0 ? 'success' : 'danger'; ?>">
                                                <?php echo $product['quantity']; ?> <?php echo $product['unit']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="ready-badge <?php echo $harvest_status_class; ?>">
                                                <i class="fas fa-<?php 
                                                    echo $product['ready_status'] == 'ready' ? 'check-circle' : 
                                                        ($product['ready_status'] == 'harvested' ? 'times-circle' : 'clock'); 
                                                ?>"></i>
                                                <?php echo $harvest_status_text; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if(isset($product['estimated_ready_time']) && $product['estimated_ready_time']): ?>
                                                <small><?php echo htmlspecialchars($product['estimated_ready_time']); ?></small>
                                            <?php endif; ?>
                                            <?php if($days_until_harvest): ?>
                                                <div class="days-counter">
                                                    <i class="far fa-calendar-alt"></i> <?php echo $days_until_harvest; ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" onclick="editProduct(<?php echo htmlspecialchars(json_encode($product)); ?>)">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="btn btn-sm btn-outline-success" onclick="updateHarvestStatus(<?php echo $product['id']; ?>, 'ready')">
                                                <i class="fas fa-check"></i> Ready
                                            </button>
                                            <form action="../handlers/product_handler.php" method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this product?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Orders -->
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Recent Orders</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($recent_orders as $order): ?>
                                    <tr>
                                        <td><strong>#<?php echo str_pad($order['order_id'], 5, '0', STR_PAD_LEFT); ?></strong></td>
                                        <td>
                                            <i class="fas fa-user-circle text-success"></i>
                                            <?php echo htmlspecialchars($order['username']); ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                                        <td><?php echo $order['quantity']; ?></td>
                                        <td><strong><?php echo number_format($order['price'] * $order['quantity'], 0); ?> FCFA</strong></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $order['status'] == 'delivered' ? 'success' : 
                                                    ($order['status'] == 'cancelled' ? 'danger' : 
                                                    ($order['status'] == 'pending' ? 'warning' : 'info')); 
                                            ?>">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <i class="far fa-calendar-alt text-muted me-1"></i>
                                            <?php echo date('d M Y', strtotime($order['order_date'])); ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-plus-circle"></i> Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="../handlers/product_handler.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Product Name *</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Category *</label>
                                <select name="category" class="form-control" required>
                                    <option value="">Select Category</option>
                                    <option value="Vegetables">Vegetables</option>
                                    <option value="Fruits">Fruits</option>
                                    <option value="Grains">Grains</option>
                                    <option value="Dairy">Dairy</option>
                                    <option value="Organic">Organic</option>
                                    <option value="Seeds">Seeds</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label>Price (FCFA) *</label>
                                <input type="number" step="0.01" name="price" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Unit *</label>
                                <input type="text" name="unit" class="form-control" placeholder="kg, basket, bundle" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Quantity *</label>
                                <input type="number" name="quantity" class="form-control" required>
                            </div>
                        </div>
                        
                        <!-- NEW: Harvest Information Section -->
                        <div class="card mb-3 border-success">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="fas fa-calendar-alt"></i> Harvest Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label>Harvest/Ready Date</label>
                                        <input type="date" name="harvest_date" class="form-control" 
                                               min="<?php echo date('Y-m-d'); ?>">
                                        <small class="text-muted">When will this product be ready?</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label>Initial Status</label>
                                        <select name="ready_status" class="form-control">
                                            <option value="cultivating">Still Cultivating</option>
                                            <option value="ready">Ready Now</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label>Estimated Ready Time (Optional)</label>
                                    <input type="text" name="estimated_ready_time" class="form-control" 
                                           placeholder="e.g., 'Next week', 'In 2 weeks', 'End of month'">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label>Product Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Add Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="../handlers/product_handler.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Product Name *</label>
                                <input type="text" name="name" id="edit_name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Category *</label>
                                <select name="category" id="edit_category" class="form-control" required>
                                    <option value="Vegetables">Vegetables</option>
                                    <option value="Fruits">Fruits</option>
                                    <option value="Grains">Grains</option>
                                    <option value="Dairy">Dairy</option>
                                    <option value="Organic">Organic</option>
                                    <option value="Seeds">Seeds</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label>Description</label>
                            <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label>Price (FCFA) *</label>
                                <input type="number" step="0.01" name="price" id="edit_price" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Unit *</label>
                                <input type="text" name="unit" id="edit_unit" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Quantity *</label>
                                <input type="number" name="quantity" id="edit_quantity" class="form-control" required>
                            </div>
                        </div>
                        
                        <!-- NEW: Harvest Information Section in Edit Modal -->
                        <div class="card mb-3 border-primary">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fas fa-calendar-alt"></i> Harvest Status</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label>Harvest/Ready Date</label>
                                        <input type="date" name="harvest_date" id="edit_harvest_date" class="form-control">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label>Status</label>
                                        <select name="ready_status" id="edit_ready_status" class="form-control">
                                            <option value="cultivating">Still Cultivating</option>
                                            <option value="ready">Ready to Harvest</option>
                                            <option value="harvested">Already Harvested</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label>Estimated Ready Time</label>
                                    <input type="text" name="estimated_ready_time" id="edit_estimated_time" class="form-control" 
                                           placeholder="e.g., 'Next week', 'In 2 weeks'">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label>New Image (optional)</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Quick Status Update Modal -->
    <div class="modal fade" id="statusUpdateModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Update Harvest Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="../handlers/product_handler.php" method="POST">
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="id" id="status_product_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Select Status</label>
                            <select name="ready_status" class="form-control" required>
                                <option value="cultivating">Still Cultivating</option>
                                <option value="ready">Ready to Harvest</option>
                                <option value="harvested">Harvested</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Estimated Ready Time (optional)</label>
                            <input type="text" name="estimated_ready_time" class="form-control" 
                                   placeholder="e.g., 'Ready now', 'In 3 days'">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function editProduct(product) {
        document.getElementById('edit_id').value = product.id;
        document.getElementById('edit_name').value = product.name;
        document.getElementById('edit_category').value = product.category;
        document.getElementById('edit_description').value = product.description || '';
        document.getElementById('edit_price').value = product.price;
        document.getElementById('edit_unit').value = product.unit;
        document.getElementById('edit_quantity').value = product.quantity;
        
        // Set harvest information if exists
        if(product.harvest_date) {
            document.getElementById('edit_harvest_date').value = product.harvest_date;
        }
        if(product.ready_status) {
            document.getElementById('edit_ready_status').value = product.ready_status;
        }
        if(product.estimated_ready_time) {
            document.getElementById('edit_estimated_time').value = product.estimated_ready_time;
        }
        
        new bootstrap.Modal(document.getElementById('editProductModal')).show();
    }
    
    function updateHarvestStatus(productId, status) {
        document.getElementById('status_product_id').value = productId;
        let statusSelect = document.querySelector('#statusUpdateModal select[name="ready_status"]');
        if(statusSelect) {
            statusSelect.value = status;
        }
        new bootstrap.Modal(document.getElementById('statusUpdateModal')).show();
    }
    
    // Calculate days until harvest
    function calculateDaysUntilHarvest(harvestDate) {
        if(!harvestDate) return '';
        
        let today = new Date();
        let harvest = new Date(harvestDate);
        let diffTime = harvest - today;
        let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        if(diffDays > 0) {
            return diffDays + ' days until ready';
        } else if(diffDays === 0) {
            return 'Ready today!';
        } else {
            return 'Overdue by ' + Math.abs(diffDays) + ' days';
        }
    }
    </script>
</body>
</html>