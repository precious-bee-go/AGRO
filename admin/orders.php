<?php
require_once "../config/config.php";
require_once "../config/database.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Get all orders with user info
$stmt = $conn->query("SELECT o.*, u.username, u.full_name 
                      FROM orders o 
                      LEFT JOIN users u ON o.user_id = u.id 
                      ORDER BY o.order_date DESC");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate stats
$total_orders = count($orders);
$pending = 0;
$processing = 0;
$delivered = 0;
$cancelled = 0;
$total_revenue = 0;

foreach($orders as $order) {
    $total_revenue += $order['total_amount'];
    
    switch($order['status']) {
        case 'pending': $pending++; break;
        case 'processing': $processing++; break;
        case 'delivered': $delivered++; break;
        case 'cancelled': $cancelled++; break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Orders - Agro E-Commerce</title>
    
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
        
        /* Stats cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
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
        .stat-red { background: #ffebee; color: #dc3545; }
        
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
        
        /* Filter bar */
        .filter-bar {
            background: white;
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .filter-bar select {
            padding: 8px 15px;
            border: 1px solid #e9ecef;
            border-radius: 5px;
            outline: none;
            min-width: 150px;
        }
        
        .filter-bar input {
            flex: 1;
            padding: 8px 15px;
            border: 1px solid #e9ecef;
            border-radius: 5px;
            outline: none;
        }
        
        .filter-bar button {
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .filter-bar button:hover {
            background: #218838;
        }
        
        /* Table */
        .table-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        
        .table-header {
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
        
        /* Status badges */
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .status-pending {
            background: #fff3e0;
            color: #ff9800;
        }
        
        .status-processing {
            background: #e3f2fd;
            color: #1976d2;
        }
        
        .status-delivered {
            background: #e8f5e9;
            color: #28a745;
        }
        
        .status-cancelled {
            background: #ffebee;
            color: #dc3545;
        }
        
        .payment-badge {
            background: #f8f9fa;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.85rem;
            color: #6c757d;
        }
        
        /* Action buttons */
        .btn-view {
            background: #e8f5e9;
            color: #28a745;
            border: none;
            padding: 5px 15px;
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
            
            .filter-bar {
                flex-direction: column;
            }
            
            .filter-bar select,
            .filter-bar input,
            .filter-bar button {
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
            <li><a href="products.php"><i class="fas fa-box"></i> Products</a></li>
            <li><a href="orders.php" class="active"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            
            <li class="back-site"><a href="<?php echo BASE_URL; ?>"><i class="fas fa-arrow-left"></i> Back to Site</a></li>
        </ul>
    </div>
    
    <!-- Main Content -->
    <div class="admin-content">
        <div class="content-header">
            <h1><i class="fas fa-shopping-cart"></i> Manage Orders</h1>
        </div>
        
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon stat-blue">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $total_orders; ?></h3>
                    <p>Total Orders</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon stat-yellow">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $pending; ?></h3>
                    <p>Pending</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon stat-cyan">
                    <i class="fas fa-spinner"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $processing; ?></h3>
                    <p>Processing</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon stat-green">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $delivered; ?></h3>
                    <p>Delivered</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon stat-red">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $cancelled; ?></h3>
                    <p>Cancelled</p>
                </div>
            </div>
        </div>
        
        <!-- Filter Bar -->
        <div class="filter-bar">
            <select id="statusFilter">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="processing">Processing</option>
                <option value="delivered">Delivered</option>
                <option value="cancelled">Cancelled</option>
            </select>
            <input type="text" id="searchInput" placeholder="Search by order ID or customer...">
            <button onclick="filterOrders()">Apply Filter</button>
        </div>
        
        <!-- Orders Table -->
        <div class="table-container">
            <div class="table-header">
                <h2><i class="fas fa-list"></i> Orders List</h2>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="ordersTable">
                    <?php if(count($orders) > 0): ?>
                        <?php foreach($orders as $order): ?>
                        <tr>
                            <td><strong>#<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></strong></td>
                            <td>
                                <i class="fas fa-user-circle" style="color: #28a745; margin-right: 5px;"></i>
                                <?php echo htmlspecialchars($order['full_name'] ?? $order['username'] ?? 'Guest'); ?>
                            </td>
                            <td>
                                <strong style="color: #28a745;"><?php echo number_format($order['total_amount'], 0); ?> FCFA</strong>
                            </td>
                            <td>
                                <span class="payment-badge">
                                    <i class="fas fa-credit-card" style="margin-right: 3px;"></i>
                                    <?php echo strtoupper($order['payment_method']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $order['status']; ?>">
                                    <i class="fas fa-<?php 
                                        echo $order['status'] == 'delivered' ? 'check-circle' : 
                                            ($order['status'] == 'cancelled' ? 'times-circle' : 
                                            ($order['status'] == 'pending' ? 'clock' : 'spinner')); 
                                    ?>" style="margin-right: 3px;"></i>
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td>
                                <i class="far fa-calendar-alt" style="color: #6c757d; margin-right: 5px;"></i>
                                <?php echo date('d M Y', strtotime($order['order_date'])); ?>
                            </td>
                            <td>
                                <?php if($order['status'] != 'delivered' && $order['status'] != 'cancelled'): ?>
                                    <form method="POST" action="../handlers/order_handler.php" style="display: inline;">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                        <select name="status" onchange="this.form.submit()" class="form-select form-select-sm" style="width: auto; display: inline;">
                                            <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                            <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                            <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                    </form>
                                <?php else: ?>
                                    <span class="status-badge status-<?php echo $order['status']; ?>">
                                        <i class="fas fa-<?php 
                                            echo $order['status'] == 'delivered' ? 'check-circle' : 'times-circle'; 
                                        ?>" style="margin-right: 3px;"></i>
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 50px;">
                                <i class="fas fa-shopping-cart" style="font-size: 50px; color: #28a745; margin-bottom: 15px;"></i>
                                <h4>No Orders Found</h4>
                                <p style="color: #6c757d;">There are no orders in the system yet.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Filter functionality
document.getElementById('statusFilter').addEventListener('change', filterOrders);
document.getElementById('searchInput').addEventListener('keyup', filterOrders);

function filterOrders() {
    let status = document.getElementById('statusFilter').value.toLowerCase();
    let searchText = document.getElementById('searchInput').value.toLowerCase();
    let rows = document.querySelectorAll('#ordersTable tr');
    
    rows.forEach(row => {
        let statusCell = row.querySelector('td:nth-child(5) .status-badge')?.textContent.toLowerCase() || '';
        let text = row.textContent.toLowerCase();
        
        let statusMatch = status === '' || statusCell.includes(status);
        let searchMatch = searchText === '' || text.includes(searchText);
        
        row.style.display = statusMatch && searchMatch ? '' : 'none';
    });
}

function viewOrder(id) {
    alert('View order: ' + id);
    // window.location.href = 'order-details.php?id=' + id;
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>