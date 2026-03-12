<?php require_once ROOT_PATH . '/views/layout/header.php'; ?>

<div class="dashboard-header animate-up">
    <h1><i class="fas fa-user-shield"></i> Admin Control Panel</h1>
    <div class="header-actions">
        <a href="<?php echo APP_URL; ?>/admin/users" class="btn btn-outline-sm"><i class="fas fa-users"></i> Manage
            Users</a>
        <a href="<?php echo APP_URL; ?>/shop" class="btn btn-primary-sm"><i class="fas fa-shopping-bag"></i> View
            Site</a>
    </div>
</div>

<div class="stats-grid">
    <div class="stat-card premium-glass animate-up">
        <div class="stat-icon"><i class="fas fa-users"></i></div>
        <div class="stat-info">
            <h3>Total Users</h3>
            <p class="stat-value">
                <?php echo $stats['users']; ?>
            </p>
        </div>
    </div>
    <div class="stat-card premium-glass animate-up delay-1">
        <div class="stat-icon"><i class="fas fa-shopping-cart"></i></div>
        <div class="stat-info">
            <h3>Total Orders</h3>
            <p class="stat-value">
                <?php echo $stats['orders']; ?>
            </p>
        </div>
    </div>
    <div class="stat-card premium-glass animate-up delay-2">
        <div class="stat-icon"><i class="fas fa-carrot"></i></div>
        <div class="stat-info">
            <h3>Total Products</h3>
            <p class="stat-value">
                <?php echo $stats['products']; ?>
            </p>
        </div>
    </div>
    <div class="stat-card premium-glass animate-up delay-3">
        <div class="stat-icon"><i class="fas fa-money-bill-wave"></i></div>
        <div class="stat-info">
            <h3>Revenue</h3>
            <p class="stat-value">
                <?php echo number_format($stats['revenue'], 0); ?> frs
            </p>
        </div>
    </div>
</div>

<div class="dashboard-content-grid">
    <!-- Recent Users -->
    <div class="section-card animate-up delay-2">
        <div class="section-header">
            <h2><i class="fas fa-user-plus"></i> Recent Users</h2>
        </div>
        <div class="dashboard-list">
            <?php if (is_object($recentUsers) && $recentUsers->num_rows > 0): ?>
                <?php while ($user = $recentUsers->fetch_assoc()): ?>
                    <div class="dashboard-item premium-glass">
                        <div class="item-info">
                            <h3>
                                <?php echo $user['name']; ?>
                            </h3>
                            <p class="sub-text">
                                <?php echo $user['email']; ?>
                            </p>
                        </div>
                        <span class="role-badge role-<?php echo $user['role']; ?>">
                            <?php echo ucfirst($user['role']); ?>
                        </span>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state premium-glass">
                    <p>No users registered yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="section-card animate-up delay-3">
        <div class="section-header">
            <h2><i class="fas fa-receipt"></i> Recent Orders</h2>
        </div>
        <div class="dashboard-list">
            <?php if (is_object($recentOrders) && $recentOrders->num_rows > 0): ?>
                <?php while ($order = $recentOrders->fetch_assoc()): ?>
                    <div class="dashboard-item premium-glass">
                        <div class="item-info">
                            <h3>Order #
                                <?php echo $order['id']; ?>
                            </h3>
                            <p class="sub-text">By:
                                <?php echo $order['buyer_name']; ?>
                            </p>
                        </div>
                        <div class="item-meta">
                            <p class="price-val">
                                <?php echo number_format($order['total_amount'], 0); ?> frs
                            </p>
                            <span class="status-pill status-<?php echo strtolower($order['order_status']); ?>">
                                <?php echo ucfirst($order['order_status']); ?>
                            </span>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state premium-glass">
                    <p>No orders placed yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2.5rem;
        padding-top: 2rem;
    }

    .dashboard-header h1 {
        font-size: 2rem;
        margin: 0;
    }

    .header-actions {
        display: flex;
        gap: 1rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }

    .stat-card {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        padding: 1.5rem;
    }

    .stat-icon {
        font-size: 2.2rem;
        color: var(--primary-color);
        background: rgba(var(--primary-rgb, 46, 204, 113), 0.1);
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 15px;
    }

    .stat-info h3 {
        font-size: 0.9rem;
        color: #7f8c8d;
        margin-bottom: 0.2rem;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
    }

    .dashboard-content-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2.5rem;
    }

    .section-header {
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.8rem;
    }

    .section-header h2 {
        font-size: 1.2rem;
        margin: 0;
    }

    .dashboard-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .dashboard-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.2rem;
        text-align: left;
    }

    .item-info h3 {
        font-size: 1rem;
        margin: 0;
    }

    .sub-text {
        font-size: 0.8rem;
        color: #7f8c8d;
    }

    .role-badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .role-admin {
        background: #fee2e2;
        color: #991b1b;
    }

    .role-farmer {
        background: #dcfce7;
        color: #166534;
    }

    .role-buyer {
        background: #dbeafe;
        color: #1e40af;
    }

    .item-meta {
        text-align: right;
    }

    .price-val {
        font-weight: 700;
        margin-bottom: 0.2rem;
    }

    .status-pill {
        font-size: 0.7rem;
        padding: 2px 8px;
        border-radius: 10px;
        text-transform: uppercase;
    }

    @media (max-width: 900px) {
        .dashboard-content-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php require_once ROOT_PATH . '/views/layout/footer.php'; ?>