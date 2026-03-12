<?php require_once ROOT_PATH . '/views/layout/header.php'; ?>

<section class="form-section">
    <div class="form-card glass">
        <h2>List New Product</h2>
        <p>Fill in the details about your crop to start taking bookings.</p>

        <form action="<?php echo APP_URL; ?>/farmer/addProduct" method="POST" class="main-form">
            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Product Name</label>
                    <input type="text" id="name" name="name" required placeholder="e.g. Organic Tomatoes">
                </div>

                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id" required>
                        <?php while ($cat = $categories->fetch_assoc()): ?>
                            <option value="<?php echo $cat['id']; ?>">
                                <?php echo $cat['name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="price">Price (per unit)</label>
                    <input type="number" step="0.01" id="price" name="price" required placeholder="0.00">
                </div>

                <div class="form-group">
                    <label for="unit">Unit</label>
                    <select id="unit" name="unit" required>
                        <option value="kg">Kilogram (kg)</option>
                        <option value="quintal">Quintal</option>
                        <option value="crate">Crate</option>
                        <option value="piece">Piece</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="method">Cultivation Method</label>
                    <select id="method" name="method" required>
                        <option value="traditional">Traditional</option>
                        <option value="organic">Organic</option>
                        <option value="hydroponic">Hydroponic</option>
                        <option value="greenhouse">Greenhouse</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="quantity">Initial Quantity Available</label>
                    <input type="number" step="0.01" id="quantity" name="quantity" required placeholder="100.00">
                </div>

                <div class="form-group">
                    <label for="harvest_date">Expected Harvest Date</label>
                    <input type="date" id="harvest_date" name="harvest_date" required>
                </div>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4"
                    placeholder="Tell buyers about your crop..."></textarea>
            </div>

            <div class="form-actions">
                <a href="<?php echo APP_URL; ?>/farmer/dashboard" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary">List Product</button>
            </div>
        </form>
    </div>
</section>

<?php require_once ROOT_PATH . '/views/layout/footer.php'; ?>