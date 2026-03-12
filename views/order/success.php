<?php require_once ROOT_PATH . '/views/layout/header.php'; ?>

<section class="success-section glass">
    <div class="success-icon">✓</div>
    <h1>Booking Confirmed!</h1>
    <p>Your order #<strong>
            <?php echo $orderId; ?>
        </strong> has been successfully booked.</p>
    <p>The farmer has been notified and will prepare your harvest. You can track the status in your order history.</p>

    <div class="success-actions">
        <a href="<?php echo APP_URL; ?>/order/history" class="btn btn-primary">View My Orders</a>
        <a href="<?php echo APP_URL; ?>/shop" class="btn btn-outline">Back to Shop</a>
    </div>
</section>

<style>
    .success-section {
        max-width: 600px;
        margin: 60px auto;
        text-align: center;
        padding: 60px;
    }

    .success-icon {
        font-size: 4rem;
        color: var(--primary-color);
        margin-bottom: 20px;
    }

    .success-actions {
        margin-top: 40px;
        display: flex;
        justify-content: center;
        gap: 20px;
    }
</style>

<?php require_once ROOT_PATH . '/views/layout/footer.php'; ?>