<?php require_once ROOT_PATH . '/views/layout/header.php'; ?>

<section class="auth-section hero-bg-overlay">
    <div class="auth-card premium-glass animate-up">
        <h2>Welcome Back</h2>
        <p>Login to your account to continue.</p>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo APP_URL; ?>/auth/login" method="POST" class="auth-form">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required placeholder="john@example.com">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="••••••••">
            </div>

            <div class="form-options">
                <label class="remember-me">
                    <input type="checkbox" name="remember"> Remember me
                </label>
                <a href="#" class="forgot-link">Forgot Password?</a>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>

        <p class="auth-footer">Don't have an account? <a href="<?php echo APP_URL; ?>/auth/register">Sign up here</a>
        </p>
    </div>
</section>

<?php require_once ROOT_PATH . '/views/layout/footer.php'; ?>