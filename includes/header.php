<?php
require_once __DIR__ . "/../config/config.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Agro E-Commerce</title>

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/product.css">
</head>
<body>

<header class="main-header">
    <div class="logo">
        <a href="<?php echo BASE_URL; ?>">🌾 AgroMarket</a>
    </div>

    <nav>
       <ul>
    <li><a href="<?php echo BASE_URL; ?>">Home</a></li>
    <li><a href="<?php echo BASE_URL; ?>customer/products.php">Products</a></li>
    <li><a href="<?php echo BASE_URL; ?>cart.php">Cart</a></li>

    <?php if (isset($_SESSION['user_id'])): ?>

        <li>Hi, <?php echo $_SESSION['username']; ?></li>

        <?php if ($_SESSION['role'] == 'farmer'): ?>
            <li><a href="<?php echo BASE_URL; ?>farmer/dashboard.php">Farmer Dashboard</a></li>
        <?php endif; ?>

        <?php if ($_SESSION['role'] == 'admin'): ?>
            <li><a href="<?php echo BASE_URL; ?>admin/dashboard.php">Admin Dashboard</a></li>
        <?php endif; ?>

        <?php if ($_SESSION['role'] == 'buyer'): ?>
            <li><a href="<?php echo BASE_URL; ?>customer/orders.php">My Orders</a></li>
        <?php endif; ?>

        <li><a href="<?php echo BASE_URL; ?>logout.php">Logout</a></li>

    <?php else: ?>

        <li><a href="<?php echo BASE_URL; ?>login.php">Login</a></li>
        <li><a href="<?php echo BASE_URL; ?>register.php">Register</a></li>

    <?php endif; ?>
</ul>
    </nav>
</header>

<main class="container">