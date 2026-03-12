<?php require_once ROOT_PATH . '/views/layout/header.php'; ?>

<section class="product-details">
    <div class="details-grid">
        <div class="product-images glass">
            <div class="main-image-placeholder">
                <?php
                $imgFile = !empty($product['image_path']) ? $product['image_path'] : 'images/cabage.jpg';
                ?>
                <img src="<?php echo APP_URL . '/' . $imgFile; ?>" alt="<?php echo $product['name']; ?>"
                    class="product-img">
                <span class="cultivation-label">
                    <?php echo $product['cultivation_method']; ?>
                </span>
            </div>
        </div>

        <div class="product-info glass">
            <h1>
                <?php echo $product['name']; ?>
            </h1>
            <p class="farmer-name">Grown by: <strong>
                    <?php echo $product['farmer_name']; ?>
                </strong></p>
            <span class="badge">
                <?php echo $product['category_name']; ?>
            </span>

            <?php if (isset($rating) && is_array($rating) && $rating['total'] > 0): ?>
                <div class="rating-summary">
                    <span class="stars">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="<?php echo ($i <= round($rating['average'])) ? 'fas' : 'far'; ?> fa-star"></i>
                        <?php endfor; ?>
                    </span>
                    <span class="count">(<?php echo $rating['total']; ?> Reviews)</span>
                </div>
            <?php endif; ?>

            <div class="price-box">
                <span class="current-price">
                    <?php echo number_format($product['price_per_unit'], 0); ?> frs
                </span>
                <span class="unit">per
                    <?php echo $product['unit']; ?>
                </span>
            </div>

            <div class="description">
                <h3>Description</h3>
                <p>
                    <?php echo $product['description'] ?? 'No description provided by the farmer.'; ?>
                </p>
            </div>

            <div class="booking-section">
                <h3>Select Batch & Harvest</h3>
                <?php if (isset($batches) && is_object($batches) && $batches->num_rows > 0): ?>
                    <div class="batch-list">
                        <?php while ($batch = $batches->fetch_assoc()): ?>
                            <div class="batch-card <?php echo ($batch['quantity_available'] <= 0) ? 'sold-out' : ''; ?>">
                                <div class="batch-header">
                                    <span class="batch-name">
                                        <?php echo $batch['batch_name']; ?>
                                    </span>
                                    <span class="grade-badge">
                                        <?php echo $batch['quality_grade']; ?>
                                    </span>
                                </div>
                                <div class="batch-footer">
                                    <p>Harvest: <strong>
                                            <?php echo date('M d, Y', strtotime($batch['harvest_date'])); ?>
                                        </strong></p>
                                    <div class="countdown" data-date="<?php echo $batch['harvest_date']; ?>">
                                        Loading countdown...
                                    </div>
                                </div>

                                <?php if ($batch['quantity_available'] > 0): ?>
                                    <?php if (isset($_SESSION['user_id'])): ?>
                                        <form action="<?php echo APP_URL; ?>/cart/add" method="POST" class="add-to-cart-form">
                                            <input type="hidden" name="batch_id" value="<?php echo $batch['id']; ?>">
                                            <div class="qty-input">
                                                <input type="number" name="quantity" value="1" min="1"
                                                    max="<?php echo $batch['quantity_available']; ?>">
                                                <button type="submit" class="btn btn-primary">Book Now</button>
                                            </div>
                                        </form>
                                    <?php else: ?>
                                        <div class="guest-cta">
                                            <a href="<?php echo APP_URL; ?>/auth/login" class="btn btn-outline-sm btn-block">Login
                                                to Book</a>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <p class="sold-out-msg">SOLD OUT</p>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="no-batches">No active batches available for booking at this time.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="details-bottom-grid">
        <div class="reviews-section glass">
            <h3>Customer Reviews</h3>

            <?php if (isset($_SESSION['user_id'])): ?>
                <form action="<?php echo APP_URL; ?>/shop/addReview" method="POST" class="review-form">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <div class="rating-input">
                        <label>Your Rating:</label>
                        <select name="rating" required>
                            <option value="5">5 - Excellent</option>
                            <option value="4">4 - Very Good</option>
                            <option value="3">3 - Good</option>
                            <option value="2">2 - Fair</option>
                            <option value="1">1 - Poor</option>
                        </select>
                    </div>
                    <textarea name="comment" placeholder="Share your experience with this product..." required></textarea>
                    <button type="submit" class="btn btn-primary-sm">Submit Review</button>
                </form>
            <?php endif; ?>

            <div class="reviews-list">
                <?php if (is_object($reviews) && $reviews->num_rows > 0): ?>
                    <?php while ($rev = $reviews->fetch_assoc()): ?>
                        <div class="review-item">
                            <div class="review-header">
                                <strong><?php echo $rev['buyer_name']; ?></strong>
                                <span class="stars">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="<?php echo ($i <= $rev['rating']) ? 'fas' : 'far'; ?> fa-star"></i>
                                    <?php endfor; ?>
                                </span>
                            </div>
                            <p><?php echo htmlspecialchars($rev['comment']); ?></p>
                            <small><?php echo date('M d, Y', strtotime($rev['created_at'])); ?></small>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="empty-text">No reviews yet. Be the first to share your experience!</p>
                <?php endif; ?>
            </div>
        </div>

        <aside class="farmer-other-items glass">
            <h3>More from <?php echo $product['farmer_name']; ?></h3>
            <div class="mini-grid">
                <?php if (is_object($farmerItems) && $farmerItems->num_rows > 0): ?>
                    <?php while ($fItem = $farmerItems->fetch_assoc()): ?>
                        <a href="<?php echo APP_URL; ?>/shop/details?id=<?php echo $fItem['id']; ?>" class="mini-card">
                            <img src="<?php echo APP_URL . '/' . ($fItem['image_path'] ?? 'images/cabage.jpg'); ?>" alt="">
                            <div class="mini-info">
                                <h4><?php echo $fItem['name']; ?></h4>
                                <p><?php echo number_format($fItem['price_per_unit'], 0); ?> frs</p>
                            </div>
                        </a>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="empty-text">No other products listed.</p>
                <?php endif; ?>
            </div>
        </aside>
    </div>
</section>

<style>
    .details-bottom-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
        margin-top: 2rem;
    }

    .rating-summary {
        margin: 1rem 0;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #f1c40f;
    }

    .rating-summary .count {
        color: #7f8c8d;
        font-size: 0.9rem;
    }

    .review-form {
        margin-bottom: 2rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .rating-input {
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .review-form textarea {
        width: 100%;
        height: 100px;
        padding: 1rem;
        border-radius: 10px;
        border: 1px solid #ddd;
        margin-bottom: 1rem;
    }

    .review-item {
        padding: 1rem 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.02);
    }

    .review-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }

    .stars {
        color: #f1c40f;
        font-size: 0.8rem;
    }

    .mini-grid {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-top: 1rem;
    }

    .mini-card {
        display: flex;
        gap: 15px;
        text-decoration: none;
        color: inherit;
        transition: transform 0.2s;
    }

    .mini-card:hover {
        transform: translateX(5px);
    }

    .mini-card img {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        object-fit: cover;
    }

    .mini-info h4 {
        font-size: 0.95rem;
        margin-bottom: 2px;
    }

    .mini-info p {
        font-size: 0.85rem;
        color: var(--primary-color);
        font-weight: 700;
    }

    @media (max-width: 768px) {
        .details-bottom-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    // Countdown Timer Logic
    function updateCountdowns() {
        const timers = document.querySelectorAll('.countdown');
        timers.forEach(timer => {
            const targetDate = new Date(timer.dataset.date).getTime();
            const now = new Date().getTime();
            const distance = targetDate - now;

            if (distance < 0) {
                timer.innerHTML = "HARVEST READY";
                return;
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));

            timer.innerHTML = `${days}d ${hours}h ${minutes}m`;
        });
    }

    setInterval(updateCountdowns, 60000);
    updateCountdowns();
</script>

<?php require_once ROOT_PATH . '/views/layout/footer.php'; ?>