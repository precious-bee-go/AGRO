<?php require_once ROOT_PATH . '/views/layout/header.php'; ?>

<section class="cart-section">
    <h1>Your Shopping Cart</h1>

    <?php if (!empty($items)): ?>
        <div class="cart-grid">
            <div class="cart-items glass">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product / Batch</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td>
                                    <strong>
                                        <?php echo $item['name']; ?>
                                    </strong><br>
                                    <small>Batch:
                                        <?php echo $item['batch_name']; ?>
                                    </small>
                                </td>
                                <td>
                                    <?php echo $item['cart_qty']; ?>
                                    <?php echo $item['unit']; ?>
                                </td>
                                <td>
                                    <?php echo number_format($item['price_per_unit'], 0); ?> frs
                                </td>
                                <td>
                                    <?php echo number_format($item['subtotal'], 0); ?> frs
                                </td>
                                <td>
                                    <a href="<?php echo APP_URL; ?>/cart/remove?id=<?php echo $item['id']; ?>"
                                        class="text-danger">&times;</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <aside class="cart-summary glass">
                <h3>Order Summary</h3>
                <div class="summary-row">
                    <span>Total Amount</span>
                    <span>
                        <?php echo number_format($total, 0); ?> frs
                    </span>
                </div>
                <div class="summary-row highlight">
                    <span>Booking Amount (<?php echo BOOKING_PERCENTAGE * 100; ?>%)</span>
                    <span>
                        <?php echo number_format($advance, 0); ?> frs
                    </span>
                </div>
                <div class="summary-row">
                    <span>Payable on Delivery</span>
                    <span>
                        <?php echo number_format($total - $advance, 0); ?> frs
                    </span>
                </div>

                <hr>

                <form action="<?php echo APP_URL; ?>/order/checkout" method="POST">
                    <div class="form-group">
                        <label>Delivery Address</label>
                        <textarea name="address" required placeholder="Enter your full address..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Proceed to Booking</button>
                </form>
            </aside>
        </div>
    <?php else: ?>
        <div class="empty-cart glass">
            <p>Your cart is empty.</p>
            <a href="<?php echo APP_URL; ?>/shop" class="btn btn-primary">Go to Shop</a>
        </div>
    <?php endif; ?>
</section>

<?php require_once ROOT_PATH . '/views/layout/footer.php'; ?>