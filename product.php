<?php
require_once "config/config.php";
require_once "config/database.php";

$category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include "includes/navbar.php"; ?>
    
    <div class="container mt-4">
        <div class="row">
            <!-- Sidebar Filters -->
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Filter Products</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET">
                            <div class="mb-3">
                                <label>Search</label>
                                <input type="text" name="search" class="form-control" value="<?php echo $search; ?>">
                            </div>
                            <div class="mb-3">
                                <label>Category</label>
                                <select name="category" class="form-control">
                                    <option value="">All Categories</option>
                                    <?php
                                    $categories = ['Vegetables', 'Fruits', 'Grains', 'Dairy', 'Organic', 'Seeds'];
                                    foreach($categories as $cat) {
                                        $selected = ($category == $cat) ? 'selected' : '';
                                        echo "<option value='$cat' $selected>$cat</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success w-100">Apply Filters</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Products Grid -->
            <div class="col-md-9">
                <h2 class="mb-4">Agricultural Products</h2>
                <div class="row">
                    <?php
                    $sql = "SELECT p.*, u.full_name as farmer_name FROM products p 
                            JOIN users u ON p.farmer_id = u.id 
                            WHERE p.status != 'deleted'";
                    $params = [];
                    
                    if(!empty($category)) {
                        $sql .= " AND p.category = ?";
                        $params[] = $category;
                    }
                    
                    if(!empty($search)) {
                        $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
                        $params[] = "%$search%";
                        $params[] = "%$search%";
                    }
                    
                    $sql .= " ORDER BY p.created_at DESC";
                    
                    $stmt = $conn->prepare($sql);
                    $stmt->execute($params);
                    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if(count($products) > 0) {
                        foreach($products as $product) {
                            ?>
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <img src="<?php echo $product['image'] ? 'uploads/products/'.$product['image'] : 'https://via.placeholder.com/300x200?text=Product'; ?>" 
                                         class="card-img-top" alt="<?php echo $product['name']; ?>" style="height: 200px; object-fit: cover;">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo $product['name']; ?></h5>
                                        <p class="card-text text-success fw-bold">FCFA<?php echo number_format($product['price'], 2); ?> per <?php echo $product['unit']; ?></p>
                                        <p class="card-text"><small class="text-muted">Farmer: <?php echo $product['farmer_name']; ?></small></p>
                                        <p class="card-text"><?php echo substr($product['description'], 0, 100); ?>...</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <?php if($product['status'] == 'sold' || $product['quantity'] <= 0): ?>
                                                <span class="badge bg-danger">Sold Out</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">In Stock: <?php echo $product['quantity']; ?> <?php echo $product['unit']; ?></span>
                                            <?php endif; ?>
                                            
                                            <a href="product_detail.php?id=<?php echo $product['id']; ?>" class="btn btn-success btn-sm">
                                                <i class="fas fa-eye"></i> View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<div class="col-12"><div class="alert alert-info">No products found.</div></div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    
    <?php include "includes/footer.php"; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>