<?php
require_once "config/config.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 80px 0;
        }
        .feature-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
        .team-member {
            text-align: center;
            margin-bottom: 30px;
        }
        .team-member img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #28a745;
        }
    </style>
</head>
<body>
    <?php include "includes/navbar.php"; ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="display-4 fw-bold mb-4">About <?php echo SITE_NAME; ?></h1>
                    <p class="lead mb-4">
                        Connecting farmers directly with consumers for fresh, quality agricultural products.
                        We believe in fair trade, sustainable farming, and supporting local communities.
                    </p>
                    <a href="register.php" class="btn btn-light btn-lg">Join Our Community</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <h2 class="mb-4">Our Mission</h2>
                    <p class="lead mb-4">
                        To create a transparent and fair marketplace that empowers farmers and provides consumers
                        with access to fresh, locally-grown products at competitive prices.
                    </p>
                    <ul class="list-unstyled">
                        <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> Support local farmers and sustainable agriculture</li>
                        <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> Ensure fair prices for both farmers and consumers</li>
                        <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> Promote healthy, fresh food choices</li>
                        <li class="mb-3"><i class="fas fa-check-circle text-success me-2"></i> Build strong community connections</li>
                    </ul>
                </div>
                <div class="col-lg-6">
                    <img src="assets/images/welcome_page.png" alt="Fresh Farm Products" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-12">
                    <h2>Why Choose Us?</h2>
                    <p class="lead">Experience the difference with our farmer-to-consumer marketplace</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-leaf fa-3x text-success mb-3"></i>
                            <h5 class="card-title">Fresh & Local</h5>
                            <p class="card-text">Get products directly from local farmers, ensuring maximum freshness and supporting your community.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-handshake fa-3x text-success mb-3"></i>
                            <h5 class="card-title">Fair Trade</h5>
                            <p class="card-text">We ensure farmers get fair compensation for their hard work and dedication to quality farming.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-shield-alt fa-3x text-success mb-3"></i>
                            <h5 class="card-title">Quality Assured</h5>
                            <p class="card-text">All products go through quality checks to ensure you receive only the best agricultural products.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-5">
        <div class="container">
            <div class="row text-center mb-5">
                <div class="col-12">
                    <h2>How It Works</h2>
                    <p class="lead">Simple steps to connect farmers with consumers</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 text-center mb-4">
                    <div class="mb-3">
                        <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem; font-weight: bold;">1</div>
                    </div>
                    <h5>Farmers Register</h5>
                    <p>Farmers create their profiles and list their products with details about harvest times and availability.</p>
                </div>
                <div class="col-md-4 text-center mb-4">
                    <div class="mb-3">
                        <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem; font-weight: bold;">2</div>
                    </div>
                    <h5>Customers Browse</h5>
                    <p>Customers can browse products, view farmer information, and place orders directly through our platform.</p>
                </div>
                <div class="col-md-4 text-center mb-4">
                    <div class="mb-3">
                        <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem; font-weight: bold;">3</div>
                    </div>
                    <h5>Fresh Delivery</h5>
                    <p>Orders are fulfilled by farmers and delivered fresh to customers, completing the direct farm-to-table connection.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact CTA -->
    <section class="py-5 bg-success text-white">
        <div class="container text-center">
            <h2 class="mb-4">Ready to Get Started?</h2>
            <p class="lead mb-4">Join our growing community of farmers and consumers today!</p>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <a href="register.php?role=farmer" class="btn btn-light btn-lg me-3">
                        <i class="fas fa-tractor me-2"></i>Register as Farmer
                    </a>
                </div>
                <div class="col-md-6 mb-3">
                    <a href="register.php?role=customer" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-shopping-cart me-2"></i>Register as Customer
                    </a>
                </div>
            </div>
        </div>
    </section>

    <?php include "includes/footer.php"; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>