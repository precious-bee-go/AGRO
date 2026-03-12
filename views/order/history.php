<?php require_once ROOT_PATH . '/views/layout/header.php'; ?>

<section class="history-section">
    <h1 class="animate-up">Your Order History</h1>

    <div class="orders-list">
        <?php if (is_object($orders) && $orders->num_rows > 0): ?>
            <?php while ($order = $orders->fetch_assoc()): ?>
                <div class="order-item premium-glass animate-up">
                    <div class="order-header">
                        <div>
                            <span class="order-id"><i class="fas fa-hashtag"></i> ID-<?php echo $order['id']; ?></span>
                            <span class="order-date"><i class="far fa-calendar-alt"></i>
                                <?php echo date('M d, Y', strtotime($order['created_at'])); ?></span>
                        </div>
                        <span class="order-status status-<?php echo strtolower($order['order_status']); ?>">
                            <?php echo ucfirst($order['order_status']); ?>
                        </span>
                    </div>

                    <div class="order-details">
                        <div class="detail-group">
                            <label>Total Amount</label>
                            <p><?php echo number_format($order['total_amount'], 0); ?> frs</p>
                        </div>
                        <div class="detail-group">
                            <label>Advance Paid</label>
                            <p><?php echo number_format($order['advance_amount'], 0); ?> frs</p>
                        </div>
                        <div class="detail-group">
                            <label>Due on Delivery</label>
                            <p><?php echo number_format($order['remaining_amount'], 0); ?> frs</p>
                        </div>
                        <div class="detail-group">
                            <label>Delivery Address</label>
                            <p><i class="fas fa-map-marker-alt"></i> <?php echo $order['delivery_address']; ?></p>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state premium-glass animate-up">
                <p>No orders found.</p>
                <a href="<?php echo APP_URL; ?>/shop" class="btn btn-primary-sm">Start Shopping</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
    .order-item {
        margin-bottom: 20px;
        padding: 20px;
    }

    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid var(--light-grey);
        padding-bottom: 15px;
        margin-bottom: 15px;
    }

    .order-id {
        font-weight: 700;
        color: var(--primary-color);
        margin-right: 10px;
    }

    .order-status {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .status-booked {
        background: #e3f2fd;
        color: #1976d2;
    }

    .status-growing {
        background: #f1f8e9;
        color: #558b2f;
    }

    .status-delivered {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .order-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 20px;
    }

    .detail-group label {
        font-size: 0.75rem;
        color: #7f8c8d;
        text-transform: uppercase;
    }

    .detail-group p {
        font-weight: 600;
    }
</style>

<?php require_once ROOT_PATH . '/views/layout/footer.php'; ?>