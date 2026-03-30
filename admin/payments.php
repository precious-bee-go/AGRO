<?php
require_once "../config/config.php";
require_once "../config/database.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Ensure required fields exist in users table (for legacy / incomplete setups)
try {
    $dbName = getenv('DB_DATABASE') ?: 'agro_ecommerce';
    $checkColumn = $conn->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'users' AND COLUMN_NAME = ?");

    $checkColumn->execute([$dbName, 'payment_status']);
    if($checkColumn->fetchColumn() == 0) {
        $conn->exec("ALTER TABLE users ADD COLUMN payment_status ENUM('pending','paid') NOT NULL DEFAULT 'pending'");
    }

    $checkColumn->execute([$dbName, 'payment_amount']);
    if($checkColumn->fetchColumn() == 0) {
        $conn->exec("ALTER TABLE users ADD COLUMN payment_amount DECIMAL(10,2) NOT NULL DEFAULT 1000.00");
    }

    $checkColumn->execute([$dbName, 'payment_date']);
    if($checkColumn->fetchColumn() == 0) {
        $conn->exec("ALTER TABLE users ADD COLUMN payment_date DATETIME NULL");
    }
} catch (PDOException $e) {
    // Schema updates failed (likely permissions); continue, but the page may still show incomplete data.
    // No crash here.
}

// Handle payment status updates
if(isset($_GET['action']) && isset($_GET['farmer_id'])) {
    $action = $_GET['action'];
    $farmer_id = intval($_GET['farmer_id']);

    if($action == 'mark_paid') {
        $stmt = $conn->prepare("UPDATE users SET payment_status = 'paid', payment_date = NOW() WHERE id = ? AND role = 'farmer'");
        if($stmt->execute([$farmer_id])) {
            $_SESSION['success'] = "Farmer payment marked as paid.";
        } else {
            $_SESSION['error'] = "Failed to update payment status.";
        }
    } elseif($action == 'mark_pending') {
        $stmt = $conn->prepare("UPDATE users SET payment_status = 'pending', payment_date = NULL WHERE id = ? AND role = 'farmer'");
        if($stmt->execute([$farmer_id])) {
            $_SESSION['success'] = "Farmer payment marked as pending.";
        } else {
            $_SESSION['error'] = "Failed to update payment status.";
        }
    }

    header("Location: payments.php");
    exit();
}

// Get all farmers with payment info
$stmt = $conn->prepare("SELECT id, username, full_name, email, payment_status, payment_amount, payment_date, created_at
                        FROM users WHERE role = 'farmer' ORDER BY created_at DESC");
$stmt->execute();
$farmers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate payment statistics
$total_farmers = count($farmers);
$paid_farmers = 0;
$pending_farmers = 0;
$total_revenue = 0;

foreach($farmers as $farmer) {
    if($farmer['payment_status'] == 'paid') {
        $paid_farmers++;
        $total_revenue += $farmer['payment_amount'];
    } else {
        $pending_farmers++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Payments - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-wrapper {
            display: flex;
            min-height: calc(100vh - 80px);
            background: #f4f6f9;
        }

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

        .payment-status {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .status-paid {
            background: #e8f5e9;
            color: #28a745;
        }

        .status-pending {
            background: #fff3e0;
            color: #ff9800;
        }

        .btn-action {
            width: 35px;
            height: 35px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
            margin: 0 3px;
        }

        .btn-mark-paid {
            background: #e8f5e9;
            color: #28a745;
        }

        .btn-mark-paid:hover {
            background: #28a745;
            color: white;
        }

        .btn-mark-pending {
            background: #fff3e0;
            color: #ff9800;
        }

        .btn-mark-pending:hover {
            background: #ff9800;
            color: white;
        }
    </style>
</head>
<body>
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
                <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                <li><a href="payments.php" class="active"><i class="fas fa-credit-card"></i> Farmer Payments</a></li>
                <li class="back-site"><a href="<?php echo BASE_URL; ?>"><i class="fas fa-arrow-left"></i> Back to Site</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="admin-content">
            <div class="content-header">
                <h1><i class="fas fa-credit-card"></i> Farmer Payments</h1>
            </div>

            <?php if(!empty($_SESSION['success'])): ?>
                <div style="margin-bottom: 15px; padding: 12px 20px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px;">
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <?php if(!empty($_SESSION['error'])): ?>
                <div style="margin-bottom: 15px; padding: 12px 20px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px;">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon stat-blue">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $total_farmers; ?></h3>
                        <p>Total Farmers</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon stat-green">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $paid_farmers; ?></h3>
                        <p>Paid Farmers</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon stat-yellow">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $pending_farmers; ?></h3>
                        <p>Pending Payments</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon stat-red">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo number_format($total_revenue, 0); ?> FCFA</h3>
                        <p>Total Revenue</p>
                    </div>
                </div>
            </div>

            <!-- Farmers Table -->
            <div class="table-container" style="background: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); overflow: hidden;">
                <div class="table-header" style="display: flex; justify-content: space-between; align-items: center; padding: 20px; border-bottom: 1px solid #e9ecef;">
                    <h2 style="margin: 0; font-size: 1.2rem; color: #2c3e50;"><i class="fas fa-list"></i> Farmer Payment Status</h2>
                </div>

                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa;">
                            <th style="padding: 15px; text-align: left; font-weight: 600; color: #2c3e50; border-bottom: 2px solid #e9ecef;">ID</th>
                            <th style="padding: 15px; text-align: left; font-weight: 600; color: #2c3e50; border-bottom: 2px solid #e9ecef;">Farmer</th>
                            <th style="padding: 15px; text-align: left; font-weight: 600; color: #2c3e50; border-bottom: 2px solid #e9ecef;">Email</th>
                            <th style="padding: 15px; text-align: left; font-weight: 600; color: #2c3e50; border-bottom: 2px solid #e9ecef;">Amount</th>
                            <th style="padding: 15px; text-align: left; font-weight: 600; color: #2c3e50; border-bottom: 2px solid #e9ecef;">Status</th>
                            <th style="padding: 15px; text-align: left; font-weight: 600; color: #2c3e50; border-bottom: 2px solid #e9ecef;">Payment Date</th>
                            <th style="padding: 15px; text-align: left; font-weight: 600; color: #2c3e50; border-bottom: 2px solid #e9ecef;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($farmers) > 0): ?>
                            <?php foreach($farmers as $farmer): ?>
                            <tr style="border-bottom: 1px solid #e9ecef;">
                                <td style="padding: 15px;"><strong>#<?php echo $farmer['id']; ?></strong></td>
                                <td style="padding: 15px;">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <i class="fas fa-user-circle" style="color: #28a745;"></i>
                                        <strong><?php echo htmlspecialchars($farmer['full_name'] ?? $farmer['username']); ?></strong>
                                    </div>
                                </td>
                                <td style="padding: 15px;"><?php echo htmlspecialchars($farmer['email']); ?></td>
                                <td style="padding: 15px;">
                                    <strong style="color: #28a745;"><?php echo number_format($farmer['payment_amount'], 0); ?> FCFA</strong>
                                </td>
                                <td style="padding: 15px;">
                                    <span class="payment-status status-<?php echo $farmer['payment_status']; ?>">
                                        <i class="fas fa-<?php echo $farmer['payment_status'] == 'paid' ? 'check-circle' : 'clock'; ?>" style="margin-right: 3px;"></i>
                                        <?php echo ucfirst($farmer['payment_status']); ?>
                                    </span>
                                </td>
                                <td style="padding: 15px;">
                                    <?php if($farmer['payment_date']): ?>
                                        <i class="far fa-calendar-alt" style="color: #6c757d; margin-right: 5px;"></i>
                                        <?php echo date('d M Y', strtotime($farmer['payment_date'])); ?>
                                    <?php else: ?>
                                        <span style="color: #6c757d;">Not paid</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 15px;">
                                    <?php if($farmer['payment_status'] == 'paid'): ?>
                                        <button class="btn-action btn-mark-pending" onclick="confirmAction('payments.php?action=mark_pending&farmer_id=<?php echo $farmer['id']; ?>', 'Mark this farmer\'s payment as pending?')" title="Mark as Pending">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    <?php else: ?>
                                        <button class="btn-action btn-mark-paid" onclick="confirmAction('payments.php?action=mark_paid&farmer_id=<?php echo $farmer['id']; ?>', 'Mark this farmer\'s payment as completed?')" title="Mark as Paid">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 50px;">
                                    <i class="fas fa-users" style="font-size: 50px; color: #28a745; margin-bottom: 15px;"></i>
                                    <h4>No Farmers Found</h4>
                                    <p style="color: #6c757d;">Farmers will appear here once they register.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function confirmAction(url, message) {
            if (confirm(message)) {
                window.location.href = url;
            }
        }
    </script>
</body>
</html>