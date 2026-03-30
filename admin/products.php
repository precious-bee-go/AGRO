<?php
require_once "../config/config.php";
require_once "../config/database.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Get all products with farmer info
$stmt = $conn->query("SELECT p.*, u.username, u.full_name 
                      FROM products p 
                      LEFT JOIN users u ON p.farmer_id = u.id 
                      ORDER BY p.created_at DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate stats
$total_products = count($products);
$in_stock = 0;
$total_value = 0;
$categories = [];

foreach($products as $p) {
    if($p['quantity'] > 0) $in_stock++;
    $total_value += ($p['price'] * $p['quantity']);
    $categories[] = $p['category'];
}
$unique_categories = count(array_unique($categories));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products - Agro E-Commerce</title>
    
    <!-- Your existing CSS files -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/product.css">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* Admin layout - sits below your header */
        .admin-wrapper {
            display: flex;
            min-height: calc(100vh - 80px);
            background: #f4f6f9;
        }
        
        /* Sidebar */
        .admin-sidebar {
            width: 250px;
            background: #2c3e50;
            color: white;
        }
        
        .admin-sidebar .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .admin-sidebar .sidebar-header h3 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
        }
        
        .admin-sidebar .sidebar-header i {
            color: #28a745;
            margin-right: 10px;
        }
        
        .admin-sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .admin-sidebar li {
            margin: 2px 0;
        }
        
        .admin-sidebar a {
            display: block;
            padding: 12px 20px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        
        .admin-sidebar a:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: #28a745;
        }
        
        .admin-sidebar a.active {
            background: rgba(40, 167, 69, 0.2);
            color: white;
            border-left-color: #28a745;
        }
        
        .admin-sidebar i {
            margin-right: 10px;
            width: 20px;
        }
        
        .admin-sidebar .back-site {
            margin-top: 30px;
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 15px;
        }
        
        .admin-sidebar .back-site a {
            color: #ffc107;
        }
        
        /* Main content */
        .admin-content {
            flex: 1;
            padding: 25px;
        }
        
        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
        }
        
        .content-header h1 {
            margin: 0;
            font-size: 1.8rem;
            color: #2c3e50;
        }
        
        .content-header h1 i {
            color: #28a745;
            margin-right: 10px;
        }
        
        .btn-add {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-add:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40,167,69,0.3);
        }
        
        .btn-add i {
            margin-right: 8px;
        }
        
        /* Stats cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 24px;
        }
        
        .stat-blue { background: #e3f2fd; color: #1976d2; }
        .stat-green { background: #e8f5e9; color: #28a745; }
        .stat-cyan { background: #e0f7fa; color: #17a2b8; }
        .stat-yellow { background: #fff3e0; color: #ff9800; }
        
        .stat-info h3 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 700;
            color: #2c3e50;
        }
        
        .stat-info p {
            margin: 5px 0 0;
            color: #6c757d;
        }
        
        /* Table */
        .table-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .table-header h2 {
            margin: 0;
            font-size: 1.2rem;
            color: #2c3e50;
        }
        
        .table-header h2 i {
            color: #28a745;
            margin-right: 10px;
        }
        
        .search-box {
            display: flex;
            align-items: center;
            background: #f8f9fa;
            border-radius: 20px;
            padding: 5px 15px;
            border: 1px solid #e9ecef;
        }
        
        .search-box i {
            color: #6c757d;
            margin-right: 8px;
        }
        
        .search-box input {
            border: none;
            background: transparent;
            padding: 8px 0;
            width: 250px;
            outline: none;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background: #f8f9fa;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #2c3e50;
            border-bottom: 2px solid #e9ecef;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            color: #495057;
        }
        
        tr:hover {
            background: #f8f9fa;
        }
        
        /* Product image */
        .product-img {
            width: 50px;
            height: 50px;
            border-radius: 5px;
            object-fit: cover;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .product-img i {
            font-size: 24px;
            color: #28a745;
        }
        
        /* Status badges */
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .status-available {
            background: #e8f5e9;
            color: #28a745;
        }
        
        .status-out_of_stock {
            background: #ffebee;
            color: #dc3545;
        }
        
        .category-badge {
            background: #e3f2fd;
            color: #1976d2;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
        }
        
        .quantity-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .quantity-high {
            background: #e8f5e9;
            color: #28a745;
        }
        
        .quantity-low {
            background: #fff3e0;
            color: #ff9800;
        }
        
        .quantity-none {
            background: #ffebee;
            color: #dc3545;
        }
        
        /* Action buttons */
        .btn-icon {
            width: 35px;
            height: 35px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
            margin: 0 3px;
        }
        
        .btn-edit {
            background: #e3f2fd;
            color: #1976d2;
        }
        
        .btn-edit:hover {
            background: #1976d2;
            color: white;
        }
        
        .btn-delete {
            background: #ffebee;
            color: #dc3545;
        }
        
        .btn-delete:hover {
            background: #dc3545;
            color: white;
        }
        
        .btn-view {
            background: #e8f5e9;
            color: #28a745;
            border: none;
            padding: 5px 12px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-view:hover {
            background: #28a745;
            color: white;
        }
        
        @media (max-width: 768px) {
            .admin-wrapper {
                flex-direction: column;
            }
            
            .admin-sidebar {
                width: 100%;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .table-header {
                flex-direction: column;
                gap: 10px;
            }
            
            .search-box {
                width: 100%;
            }
            
            .search-box input {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<!-- YOUR EXACT HEADER - NOTHING CHANGED -->


<!-- Admin Section - Appears below your header -->
<div class="admin-wrapper">
    <!-- Sidebar -->
    <div class="admin-sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-leaf"></i> Admin Panel</h3>
        </div>
        <ul>
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="products.php" class="active"><i class="fas fa-box"></i> Products</a></li>
            <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            
            <li class="back-site"><a href="<?php echo BASE_URL; ?>"><i class="fas fa-arrow-left"></i> Back to Site</a></li>
        </ul>
    </div>
    
    <!-- Main Content -->
    <div class="admin-content">
        <div class="content-header">
            <h1><i class="fas fa-box"></i> Manage Products</h1>
            
        </div>
        
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon stat-blue">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $total_products; ?></h3>
                    <p>Total Products</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon stat-green">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $in_stock; ?></h3>
                    <p>In Stock</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon stat-cyan">
                    <i class="fas fa-tags"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $unique_categories; ?></h3>
                    <p>Categories</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon stat-yellow">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo number_format($total_value, 0); ?> FCFA</h3>
                    <p>Total Value</p>
                </div>
            </div>
        </div>
        
        <!-- Products Table -->
        <div class="table-container">
            <div class="table-header">
                <h2><i class="fas fa-list"></i> Products List</h2>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Search products...">
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Product</th>
                        <th>Farmer</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Category</th>
                        <th>Status</th>
                        
                    </tr>
                </thead>
                <tbody id="productsTable">
                    <?php if(count($products) > 0): ?>
                        <?php foreach($products as $product): ?>
                        <tr>
                            <td><strong>#<?php echo $product['id']; ?></strong></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    
                                    <strong><?php echo htmlspecialchars($product['name']); ?></strong>
                                </div>
                            </td>
                            <td>
                                <i class="fas fa-user-circle" style="color: #28a745; margin-right: 5px;"></i>
                                <?php echo htmlspecialchars($product['full_name'] ?? $product['username'] ?? 'Unknown'); ?>
                            </td>
                            <td>
                                <strong style="color: #28a745;"><?php echo number_format($product['price'], 0); ?> FCFA</strong>
                                <div style="font-size: 0.8rem; color: #6c757d;">/<?php echo $product['unit']; ?></div>
                            </td>
                            <td>
                                <?php
                                $qty_class = 'quantity-high';
                                $qty_text = 'In Stock';
                                if($product['quantity'] <= 0) {
                                    $qty_class = 'quantity-none';
                                    $qty_text = 'Out of Stock';
                                } elseif($product['quantity'] < 10) {
                                    $qty_class = 'quantity-low';
                                    $qty_text = 'Low Stock';
                                }
                                ?>
                                <span class="quantity-badge <?php echo $qty_class; ?>">
                                    <?php echo $product['quantity']; ?> <?php echo $product['unit']; ?>
                                </span>
                                <div style="font-size: 0.75rem; color: #6c757d; margin-top: 3px;">
                                    <?php echo $qty_text; ?>
                                </div>
                            </td>
                            <td>
                                <span class="category-badge">
                                    <i class="fas fa-tag" style="margin-right: 3px;"></i>
                                    <?php echo $product['category']; ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $product['status']; ?>">
                                    <i class="fas fa-<?php echo $product['status'] == 'available' ? 'check-circle' : 'times-circle'; ?>" style="margin-right: 3px;"></i>
                                    <?php echo ucfirst(str_replace('_', ' ', $product['status'])); ?>
                                </span>
                            </td>
                            
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 50px;">
                                <i class="fas fa-box-open" style="font-size: 50px; color: #28a745; margin-bottom: 15px;"></i>
                                <h4>No Products Found</h4>
                                <p style="color: #6c757d;">Click the "Add Product" button to create your first product.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Search functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
    let searchText = this.value.toLowerCase();
    let rows = document.querySelectorAll('#productsTable tr');
    
    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchText) ? '' : 'none';
    });
});

function addProduct() {
    alert('Add Product - Redirect to add product page');
    // window.location.href = 'add-product.php';
}

function editProduct(id) {
    alert('Edit product: ' + id);
    // window.location.href = 'edit-product.php?id=' + id;
}

function deleteProduct(id) {
    if(confirm('Are you sure you want to delete this product?')) {
        alert('Delete product: ' + id);
        // window.location.href = 'delete-product.php?id=' + id;
    }
}

function viewProduct(id) {
    alert('View product: ' + id);
    // window.location.href = 'view-product.php?id=' + id;
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>