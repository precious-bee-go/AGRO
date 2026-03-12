<?php require_once ROOT_PATH . '/views/layout/header.php'; ?>

<section class="catalog-header">
    <div class="container">
        <h1 class="animate-up">Explore Fresh Harvests</h1>
        <p class="animate-up delay-1">Book your favorite crops directly from local farms and secure the best prices.</p>

        <div class="search-container animate-up delay-2">
            <form action="<?php echo APP_URL; ?>/shop" method="GET" class="search-form">
                <input type="text" name="q" placeholder="Search for cabbage, mangoes, etc..."
                    value="<?php echo htmlspecialchars($filters['q']); ?>">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
            </form>
        </div>
    </div>
</section>

<div class="catalog-layout">
    <aside class="filters-sidebar premium-glass animate-up">
        <h3>Filter Products</h3>
        <form action="<?php echo APP_URL; ?>/shop" method="GET">
            <input type="hidden" name="q" value="<?php echo htmlspecialchars($filters['q']); ?>">

            <div class="filter-group">
                <label>Category</label>
                <select name="category">
                    <option value="">All Categories</option>
                    <?php while ($cat = $categories->fetch_assoc()): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo ($filters['category'] == $cat['id']) ? 'selected' : ''; ?>>
                            <?php echo $cat['name']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="filter-group">
                <label>Cultivation Method</label>
                <select name="method">
                    <option value="">All Methods</option>
                    <option value="organic" <?php echo ($filters['method'] === 'organic') ? 'selected' : ''; ?>>Organic
                    </option>
                    <option value="hydroponic" <?php echo ($filters['method'] === 'hydroponic') ? 'selected' : ''; ?>>
                        Hydroponic</option>
                    <option value="traditional" <?php echo ($filters['method'] === 'traditional') ? 'selected' : ''; ?>>
                        Traditional</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Price Range</label>
                <select name="price">
                    <option value="">Any Price</option>
                    <option value="0-5000" <?php echo ($filters['price'] === '0-5000') ? 'selected' : ''; ?>>0 - 5,000 frs
                    </option>
                    <option value="5000-15000" <?php echo ($filters['price'] === '5000-15000') ? 'selected' : ''; ?>>5,000
                        - 15,000 frs</option>
                    <option value="15000+" <?php echo ($filters['price'] === '15000+') ? 'selected' : ''; ?>>15,000+ frs
                    </option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Apply Filters</button>
            <?php if (!empty($filters['q']) || !empty($filters['method']) || !empty($filters['category']) || !empty($filters['price'])): ?>
                <a href="<?php echo APP_URL; ?>/shop" class="btn btn-link btn-block">Clear All</a>
            <?php endif; ?>
        </form>
    </aside>

    <main class="product-catalog">
        <div class="product-grid">
            <?php if (is_object($products) && $products->num_rows > 0): ?>
                <?php while ($row = $products->fetch_assoc()): ?>
                    <div class="product-card premium-glass">
                        <div class="card-img-placeholder">
                            <?php
                            $imgFile = !empty($row['image_path']) ? $row['image_path'] : 'images/cabage.jpg';
                            ?>
                            <img src="<?php echo APP_URL . '/' . $imgFile; ?>" alt="<?php echo $row['name']; ?>"
                                class="product-img">
                            <span class="cultivation-badge">
                                <?php echo $row['cultivation_method']; ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <h3>
                                <?php echo $row['name']; ?>
                            </h3>
                            <p class="category">
                                <?php echo $row['category_name']; ?>
                            </p>
                            <p class="price">
                                <?php echo number_format($row['price_per_unit'], 0); ?> frs /
                                <?php echo $row['unit']; ?>
                            </p>

                            <?php if ($row['earliest_harvest']): ?>
                                <p class="harvest-alert">
                                    <i class="far fa-clock"></i> Ready in: <strong>
                                        <?php echo date_diff(date_create('today'), date_create($row['earliest_harvest']))->format('%a Days'); ?>
                                    </strong>
                                </p>
                            <?php endif; ?>

                            <a href="<?php echo APP_URL; ?>/shop/details?id=<?php echo $row['id']; ?>"
                                class="btn btn-primary btn-block">Book Now</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="empty-state">No products found. Adjust your filters or try again later!</p>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php require_once ROOT_PATH . '/views/layout/footer.php'; ?>