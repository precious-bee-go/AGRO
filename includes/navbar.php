<?php
// Get cart count for customer
$cart_count = 0;
if(isset($_SESSION['user_id']) && $_SESSION['role'] == 'customer') {
    require_once __DIR__ . "/../config/database.php";
    $stmt = $conn->prepare("SELECT COUNT(*) FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $cart_count = $stmt->fetchColumn();
}

// Determine current path for correct linking
$current_file = basename($_SERVER['PHP_SELF']);
$current_folder = basename(dirname($_SERVER['PHP_SELF']));

// Set the base path prefix
$base_prefix = '';
if(in_array($current_folder, ['admin', 'farmer', 'customer'])) {
    $base_prefix = '../';
}

// Special case for when we're in a subfolder of these directories
if(strpos($current_folder, '/') !== false) {
    $parts = explode('/', $current_folder);
    if(in_array($parts[0], ['admin', 'farmer', 'customer'])) {
        $base_prefix = str_repeat('../', count($parts));
    }
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-success sticky-top">
    <div class="container">
        <!-- Brand Logo -->
        <a class="navbar-brand fw-bold" href="<?php echo $base_prefix; ?>index.php">
            <i class="fas fa-leaf"></i> <?php echo SITE_NAME; ?>
        </a>
        
        <!-- Mobile Toggle Button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Navbar Links -->
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_file == 'index.php' ? 'active' : ''; ?>" 
                       href="<?php echo $base_prefix; ?>index.php">
                        <i class="fas fa-home"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_file == 'product.php' ? 'active' : ''; ?>" 
                       href="<?php echo $base_prefix; ?>product.php">
                        <i class="fas fa-seedling"></i> Products
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button" 
                       data-bs-toggle="dropdown">
                        <i class="fas fa-list"></i> Categories
                    </a>
                    <ul class="dropdown-menu">
                        <?php
                        $categories = ['Vegetables', 'Fruits', 'Grains', 'Dairy', 'Organic', 'Seeds'];
                        foreach($categories as $cat) {
                            echo '<li><a class="dropdown-item" href="' . $base_prefix . 'product.php?category=' . $cat . '"> ' . $cat . '</a></li>';
                        }
                        ?>
                    </ul>
                </li>
            </ul>
            
            <!-- Search Bar -->
            <form class="d-flex mx-auto" action="<?php echo $base_prefix; ?>product.php" method="GET" style="width: 300px;">
                <div class="input-group">
                    <input class="form-control" type="search" name="search" placeholder="Search products..." 
                           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button class="btn btn-light" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
            
            <!-- Right Side Navigation -->
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <!-- Cart Icon for Customers -->
                    <?php if($_SESSION['role'] == 'customer'): ?>
                        <li class="nav-item">
                            <a class="nav-link position-relative" href="<?php echo $base_prefix; ?>cart.php">
                                <i class="fas fa-shopping-cart"></i> Cart
                                <?php if($cart_count > 0): ?>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        <?php echo $cart_count; ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <!-- User Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" 
                           role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i>
                            <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <!-- Dashboard Link Based on Role -->
                            <?php if($_SESSION['role'] == 'admin'): ?>
                                <li>
                                    <a class="dropdown-item" href="<?php 
                                        if($current_folder == 'admin') {
                                            echo 'dashboard.php';
                                        } else {
                                            echo $base_prefix . 'admin/dashboard.php';
                                        }
                                    ?>">
                                        <i class="fas fa-tachometer-alt"></i> Admin Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo $base_prefix; ?>admin/users.php">
                                        <i class="fas fa-users"></i> Manage Users
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo $base_prefix; ?>admin/products.php">
                                        <i class="fas fa-box"></i> Manage Products
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo $base_prefix; ?>admin/orders.php">
                                        <i class="fas fa-shopping-cart"></i> Manage Orders
                                    </a>
                                </li>
                            <?php elseif($_SESSION['role'] == 'farmer'): ?>
                                <li>
                                    <a class="dropdown-item" href="<?php 
                                        if($current_folder == 'farmer') {
                                            echo 'dashboard.php';
                                        } else {
                                            echo $base_prefix . 'farmer/dashboard.php';
                                        }
                                    ?>">
                                        <i class="fas fa-tachometer-alt"></i> Farmer Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?php 
                                        if($current_folder == 'farmer') {
                                            echo 'dashboard.php#add-product';
                                        } else {
                                            echo $base_prefix . 'farmer/dashboard.php#add-product';
                                        }
                                    ?>">
                                        <i class="fas fa-plus-circle"></i> Add Product
                                    </a>
                                </li>
                            <?php else: ?>
                                <li>
                                    <a class="dropdown-item" href="<?php 
                                        if($current_folder == 'customer') {
                                            echo 'dashboard.php';
                                        } else {
                                            echo $base_prefix . 'customer/dashboard.php';
                                        }
                                    ?>">
                                        <i class="fas fa-tachometer-alt"></i> My Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo $base_prefix; ?>customer/orders.php">
                                        <i class="fas fa-truck"></i> My Orders
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="<?php echo $base_prefix; ?>logout.php">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <!-- Guest Navigation -->
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_prefix; ?>login.php">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-light text-success px-3 rounded-pill ms-2" href="<?php echo $base_prefix; ?>register.php">
                            <i class="fas fa-user-plus"></i> Register
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Add Font Awesome if not already included -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
.navbar {
    box-shadow: 0 2px 10px rgba(253, 247, 247, 0.1);
    padding: 15px 0;
}
.navbar-brand {
    font-size: 1.5rem;
}
.nav-link {
    font-weight: 500;
    transition: all 0.3s;
    padding: 8px 15px !important;
}
.nav-link:hover {
    transform: translateY(-2px);
}
.nav-link.active {
    font-weight: 600;
    border-bottom: 2px solid white;
}
.dropdown-menu {
    border: none;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    border-radius: 8px;
    min-width: 200px;
}
.dropdown-item {
    padding: 8px 20px;
    transition: all 0.3s;
}
.dropdown-item:hover {
    background-color: #f8f9fa;
    transform: translateX(5px);
}
.btn-light {
    background-color: white;
    border: none;
}
.btn-light:hover {
    background-color: #e8edf1;
}
</style>