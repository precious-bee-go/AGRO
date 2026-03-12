<?php require_once ROOT_PATH . '/views/layout/header.php'; ?>

<section class="about-hero hero-bg-overlay animate-up">
    <div class="container">
        <h1>Our Story & Mission</h1>
        <p>Connecting the soil to your table through innovation and community support.</p>
    </div>
</section>

<section class="about-story">
    <div class="container">
        <div class="about-grid">
            <div class="about-text animate-up">
                <h2 class="section-title">What is PreshyMarketplace?</h2>
                <p>PreshyMarketplace is more than just an e-commerce platform; it's a bridge between the hardworking
                    farmers of our region and the families who value fresh, organic, and locally-sourced food.</p>
                <p>Born from the need to support local agriculture, we've built a system that empowers farmers to sell
                    their crops even before they are harvested, ensuring they have the financial security to grow the
                    best products possible.</p>
            </div>
            <div class="about-image premium-glass animate-up delay-1">
                <img src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=800&q=80"
                    alt="Beautiful Farm" class="img-fluid rounded">
            </div>
        </div>
    </div>
</section>

<section class="about-values premium-glass">
    <div class="container">
        <h2 class="section-title">Why We Are Different</h2>
        <div class="values-grid">
            <div class="value-item animate-up">
                <div class="value-icon"><i class="fas fa-hourglass-half"></i></div>
                <h3>Time-Based Booking</h3>
                <p>We allow you to book and secure your favorite crops while they are still growing. No more rushing to
                    the market—your food is reserved just for you.</p>
            </div>
            <div class="value-item animate-up delay-1">
                <div class="value-icon"><i class="fas fa-handshake"></i></div>
                <h3>Fair Farmer Pay</h3>
                <p>By cutting out middlemen, we ensure that a larger portion of your payment goes directly into the
                    hands of the people who grew your food.</p>
            </div>
            <div class="value-item animate-up delay-2">
                <div class="value-icon"><i class="fas fa-gem"></i></div>
                <h3>Quality Transparency</h3>
                <p>With our grading system (Premium, Grade A, Grade B) and organic certifications, you know exactly what
                    you're bringing into your kitchen.</p>
            </div>
        </div>
    </div>
</section>

<section class="about-cta">
    <div class="container animate-up">
        <h2>Ready to support local?</h2>
        <p>Join thousands of buyers and farmers today.</p>
        <div class="hero-btns" style="margin-top: 2rem;">
            <a href="<?php echo APP_URL; ?>/shop" class="btn btn-primary">Start Shopping</a>
            <a href="<?php echo APP_URL; ?>/auth/register" class="btn btn-secondary">Join as a Farmer</a>
        </div>
    </div>
</section>


<?php require_once ROOT_PATH . '/views/layout/footer.php'; ?>