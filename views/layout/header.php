<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $title ?? APP_NAME; ?>
    </title>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <header class="main-header premium-nav">
        <nav class="container">
            <a href="<?php echo APP_URL; ?>" class="logo">Preshy<span>Marketplace</span></a>

            <button class="mobile-menu-toggle" aria-label="Toggle Menu">
                <i class="fas fa-bars"></i>
            </button>

            <div class="nav-wrapper">
                <ul class="nav-links">
                    <li><a href="<?php echo APP_URL; ?>/shop">Shop</a></li>
                    <?php if (isset($_SESSION['user_role'])): ?>
                        <li>
                            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                                <a href="<?php echo APP_URL; ?>/admin/dashboard">Admin Dashboard</a>
                            <?php elseif ($_SESSION['user_role'] === 'farmer'): ?>
                                <a href="<?php echo APP_URL; ?>/farmer/dashboard">Farmer Dashboard</a>
                            <?php else: ?>
                                <a href="<?php echo APP_URL; ?>/order/history">My Orders</a>
                            <?php endif; ?>
                        </li>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item-special">
                            <a href="<?php echo APP_URL; ?>/notifications" class="nav-icon" title="Notifications">
                                <i class="far fa-bell"></i>
                            </a>
                        </li>
                        <li class="nav-item-special">
                            <a href="<?php echo APP_URL; ?>/cart/show" class="nav-icon" title="Cart">
                                <i class="fas fa-shopping-cart"></i>
                                <span
                                    class="cart-count"><?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?></span>
                            </a>
                        </li>
                        <li><a href="<?php echo APP_URL; ?>/dashboard">Dashboard</a></li>
                        <li><a href="<?php echo APP_URL; ?>/auth/logout" class="btn btn-outline-sm">Logout</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo APP_URL; ?>/auth/register">Register</a></li>
                        <li><a href="<?php echo APP_URL; ?>/auth/login">Login</a></li>
                        <li class="about-getstarted-stack">
                            <a href="<?php echo APP_URL; ?>/home/about" class="nav-link">About us</a>
                            <a href="<?php echo APP_URL; ?>/auth/register" class="btn btn-primary-sm">Get Started</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </header>
    <main class="container">
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const toggle = document.querySelector('.mobile-menu-toggle');
                const nav = document.querySelector('.nav-wrapper');
                if (toggle && nav) {
                    toggle.addEventListener('click', function () {
                        nav.classList.toggle('active');
                        const icon = this.querySelector('i');
                        if (icon.classList.contains('fa-bars')) {
                            icon.classList.replace('fa-bars', 'fa-times');
                        } else {
                            icon.classList.replace('fa-times', 'fa-bars');
                        }
                    });
                }
            });
        </script>