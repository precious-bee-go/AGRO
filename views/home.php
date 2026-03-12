<?php require_once ROOT_PATH . '/views/layout/header.php'; ?>

<section class="hero dynamic-hero" id="hero-slider">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1 class="animate-up">Preshy Marketplace Delivered to Your <span>Doorstep</span></h1>
        <p class="animate-up delay-1">Support local farmers and get 100% organic products delivered once ready.</p>
        <div class="hero-btns animate-up delay-2">
            <a href="<?php echo APP_URL; ?>/shop" class="btn btn-primary">Shop Now</a>
            <a href="#" class="btn btn-secondary">Learn More</a>
        </div>
    </div>
</section>

<section class="features">
    <div class="container">
        <h2 class="section-title">Why Choose Preshy-Marketplace?</h2>
        <div class="feature-grid">
            <div class="feature-card premium-glass">
                <div class="feature-icon">🚜</div>
                <h3>Direct from Farm</h3>
                <p>We cut out the middleman so farmers earn more and you pay less.</p>
            </div>
            <div class="feature-card premium-glass">
                <div class="feature-icon">🌿</div>
                <h3>100% Organic</h3>
                <p>Certified organic products grown without harmful pesticides.</p>
            </div>
            <div class="feature-card premium-glass">
                <div class="feature-icon">🚚</div>
                <h3>Fast Delivery</h3>
                <p>From the soil to your kitchen in record time.</p>
            </div>
        </div>
    </div>
</section>

<section class="products dynamic-products">
    <div class="container">
        <h2 class="section-title">Fresh from the Field</h2>
        <p class="section-subtitle">Seasonal produce currently growing or ready for harvest.</p>

        <div class="product-grid">
            <?php if (isset($latestProducts) && is_object($latestProducts) && $latestProducts->num_rows > 0): ?>
                <?php while ($row = $latestProducts->fetch_assoc()): ?>
                    <div class="product-item premium-glass">
                        <div class="product-img-container">
                            <?php 
                                $imgFile = !empty($row['image_path']) ? $row['image_path'] : 'images/cabage.jpg'; 
                            ?>
                            <img src="<?php echo APP_URL . '/' . $imgFile; ?>" alt="<?php echo $row['name']; ?>"
                                class="product-img">
                        </div>
                        <div class="product-body">
                            <h4><?php echo $row['name']; ?></h4>
                            <p class="price"><?php echo number_format($row['price_per_unit'], 0); ?> frs /
                                <?php echo $row['unit']; ?></p>

                            <?php if ($row['earliest_harvest']): ?>
                                <div class="availability">
                                    <span class="clock-icon">🕒</span> Ready in:
                                    <strong><?php
                                    $diff = date_diff(date_create('today'), date_create($row['earliest_harvest']));
                                    echo $diff->format('%a Days');
                                    ?></strong>
                                </div>
                            <?php endif; ?>

                            <a href="<?php echo APP_URL; ?>/shop/details?id=<?php echo $row['id']; ?>" class="btn-sm">Book
                                Booking</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <!-- Hardcoded demo fallback if DB is empty -->
                <div class="product-item premium-glass">
                    <div class="product-img-container">
                        <img src="<?php echo APP_URL; ?>/images/cabage.jpg" alt="Fresh cabage" class="product-img">
                    </div>
                    <div class="product-body">
                        <h4>Organic Cabbage</h4>
                        <p class="price">5,000 frs / 1 Bag</p>
                        <div class="availability">
                            <span class="clock-icon">🕒</span> Ready in: <strong>2 Weeks</strong>
                        </div>
                        <a href="<?php echo APP_URL; ?>/shop" class="btn-sm">View in Shop</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
    // Dynamic Hero Slider Background
    const hero = document.getElementById('hero-slider');
    const images = [
        'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1600&q=80',
        'https://images.unsplash.com/photo-1523348837708-15d4a09cfac2?auto=format&fit=crop&w=1600&q=80',
        'https://images.unsplash.com/photo-1464226184884-fa280b87c399?auto=format&fit=crop&w=1600&q=80'
    ];
    let currentIdx = 0;

    function rotateHero() {
        currentIdx = (currentIdx + 1) % images.length;
        hero.style.backgroundImage = `linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('${images[currentIdx]}')`;
    }

    setInterval(rotateHero, 5000);
</script>

<?php require_once ROOT_PATH . '/views/layout/footer.php'; ?>