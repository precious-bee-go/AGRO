<?php require_once ROOT_PATH . '/views/layout/header.php'; ?>

<div class="dashboard-header">
    <h1>Farmer Dashboard</h1>
    <a href="<?php echo APP_URL; ?>/farmer/addProduct" class="btn btn-primary">+ Add New Product</a>
</div>

<div class="stats-grid">
    <div class="stat-card premium-glass animate-up">
        <div class="stat-icon"><i class="fas fa-leaf"></i></div>
        <div class="stat-info">
            <h3>Active Listings</h3>
            <p class="stat-value"><?php echo $stats['active_listings']; ?></p>
        </div>
    </div>
    <div class="stat-card premium-glass animate-up delay-1">
        <div class="stat-icon"><i class="fas fa-shopping-basket"></i></div>
        <div class="stat-info">
            <h3>Incoming Orders</h3>
            <p class="stat-value"><?php echo $stats['incoming_orders']; ?></p>
        </div>
    </div>
    <div class="stat-card premium-glass animate-up delay-2">
        <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
        <div class="stat-info">
            <h3>Ready for Harvest</h3>
            <p class="stat-value"><?php echo $stats['harvest_ready']; ?></p>
        </div>
    </div>
</div>

<section class="dashboard-content-grid">
    <div class="listings-section animate-up delay-1">
        <div class="section-header">
            <h2>Your Product Listings</h2>
        </div>
        <div class="dashboard-list">
            <?php if (is_object($products) && $products->num_rows > 0): ?>
                <?php while ($row = $products->fetch_assoc()): ?>
                    <div class="dashboard-item premium-glass">
                        <div class="item-info">
                            <h3><?php echo $row['name']; ?></h3>
                            <span class="badge"><?php echo $row['category_name']; ?></span>
                            <p class="price"><?php echo number_format($row['price_per_unit'], 0); ?> frs /
                                <?php echo $row['unit']; ?></p>
                        </div>
                        <div class="item-actions">
                            <a href="#" class="btn btn-outline-sm"><i class="fas fa-edit"></i></a>
                            <a href="#" class="btn btn-outline-sm"><i class="fas fa-boxes"></i></a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state premium-glass">
                    <p>No products listed yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="orders-section animate-up delay-2">
        <div class="section-header">
            <h2>Recent Orders</h2>
        </div>
        <div class="dashboard-list">
            <?php if (is_object($recentOrders) && $recentOrders->num_rows > 0): ?>
                <?php while ($order = $recentOrders->fetch_assoc()): ?>
                    <div class="dashboard-item premium-glass">
                        <div class="item-info">
                            <h3>Order #<?php echo $order['id']; ?></h3>
                            <p class="buyer-name">By: <strong><?php echo $order['buyer_name']; ?></strong></p>
                            <p class="order-total"><?php echo number_format($order['total_amount'], 0); ?> frs</p>
                        </div>
                        <div class="item-status">
                            <span class="order-status status-<?php echo strtolower($order['order_status']); ?>">
                                <?php echo ucfirst($order['order_status']); ?>
                            </span>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state premium-glass">
                    <p>No incoming orders yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<style>
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-top: 2rem;
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
        padding: 2rem;
    }

    .stat-icon {
        font-size: 2.5rem;
        color: var(--primary-color);
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        line-height: 1;
    }

    .dashboard-content-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
    }

    .section-header {
        margin-bottom: 1.5rem;
        text-align: left;
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
        padding: 1.5rem;
        text-align: left;
    }

    .item-info h3 {
        font-size: 1.1rem;
        margin-bottom: 0.2rem;
    }

    .buyer-name {
        font-size: 0.9rem;
        color: #666;
    }

    .order-status {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-booked {
        background: #e3f2fd;
        color: #1976d2;
    }

    @media (max-width: 900px) {
        .dashboard-content-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php require_once ROOT_PATH . '/views/layout/footer.php'; ?>