<?php
require_once "config/config.php";
require_once "config/database.php";

// Load active farmers for message routing
$farmerStmt = $conn->query("SELECT id, full_name, username FROM users WHERE role = 'farmer' AND is_active = 1 ORDER BY full_name ASC");
$farmers = $farmerStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include "includes/navbar.php"; ?>
    <div class="container mt-5">
        <h1 class="mb-4">Contact Us</h1>
        <div class="row">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Get in touch</h5>
                        <p class="card-text">Need support? Have questions about your order or listing? Send us a message and we will respond in 24 hours.</p>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-map-marker-alt"></i> Pk 12, Douala</li>
                            <li><i class="fas fa-phone"></i> +237 6 71 49 70 96</li>
                            <li><i class="fas fa-envelope"></i> atechongprecious@gmail.com</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Send us a message</h5>
                        <form action="handlers/contact_handler.php" method="POST">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="farmer" class="form-label">Send to Farmer</label>
                                <select class="form-select" id="farmer" name="farmer_id" required>
                                    <option value="">Choose a farmer</option>
                                    <?php foreach($farmers as $farmer): ?>
                                        <option value="<?php echo $farmer['id']; ?>"><?php echo htmlspecialchars($farmer['full_name'] ?: $farmer['username']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-success">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include "includes/footer.php"; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>