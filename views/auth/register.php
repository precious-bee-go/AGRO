<?php require_once ROOT_PATH . '/views/layout/header.php'; ?>

<section class="auth-section hero-bg-overlay">
    <div class="auth-card premium-glass animate-up">
        <h2>Create Account</h2>
        <p>Join the AgriDirect community today.</p>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo APP_URL; ?>/auth/register" method="POST" class="auth-form">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required placeholder="John Doe">
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required placeholder="john@example.com">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="••••••••">
            </div>

            <div class="form-group">
                <label>Register as:</label>
                <div class="role-selector">
                    <label class="role-option">
                        <input type="radio" name="role" value="buyer" checked>
                        <span>Buyer</span>
                    </label>
                    <label class="role-option">
                        <input type="radio" name="role" value="farmer">
                        <span>Farmer</span>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Sign Up</button>
        </form>

        <p class="auth-footer">Already have an account? <a href="<?php echo APP_URL; ?>/auth/login">Login here</a></p>
    </div>
</section>

<?php require_once ROOT_PATH . '/views/layout/footer.php'; ?>