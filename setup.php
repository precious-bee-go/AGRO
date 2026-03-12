<?php
/**
 * PreshyMarketplace - Comprehensive Database Setup & Seeding Utility
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/constants.php';

header('Content-Type: text/plain');
echo "=== PreshyMarketplace Database Setup ===\n\n";

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

echo "Creating database `" . DB_NAME . "`...\n";
$conn->query("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "`");
$conn->select_db(DB_NAME);

echo "Importing schema...\n";
$schema = file_get_contents(__DIR__ . '/db/agri_direct_schema.sql');
if ($conn->multi_query($schema)) {
    do {
        $conn->store_result();
    } while ($conn->next_result());
    echo "✅ Schema imported.\n";
}

echo "Seeding categories...\n";
$conn->query("INSERT IGNORE INTO categories (id, name, slug) VALUES 
(1, 'Vegetables', 'vegetables'),
(2, 'Fruits', 'fruits'),
(3, 'Grains', 'grains'),
(4, 'Legumes', 'legumes'),
(5, 'Dairy', 'dairy'),
(6, 'Tubers', 'tubers'),
(7, 'Spices & Herbs', 'spices-herbs')");

echo "Creating demo users...\n";
$pass = password_hash('password123', PASSWORD_DEFAULT);
$conn->query("INSERT IGNORE INTO users (id, name, email, password, role) VALUES (1, 'Demo Farmer', 'farmer@demo.com', '$pass', 'farmer')");
$conn->query("INSERT IGNORE INTO users (id, name, email, password, role) VALUES (2, 'Demo Buyer', 'buyer@demo.com', '$pass', 'buyer')");
$conn->query("INSERT IGNORE INTO users (id, name, email, password, role) VALUES (3, 'Platform Admin', 'admin@demo.com', '$pass', 'admin')");

$conn->query("INSERT IGNORE INTO profiles (user_id, farm_name, city, farm_story) VALUES (1, 'Preshy Organic Farm', 'Buea', 'Supplying the best organic crops in the region.')");
$conn->query("INSERT IGNORE INTO profiles (user_id, city) VALUES (2, 'Douala')");
$conn->query("INSERT IGNORE INTO profiles (user_id, city) VALUES (3, 'Buea')");

echo "Populating products and images...\n";
$products = [
    ['cat' => 1, 'name' => 'Organic Cabbage', 'price' => 500, 'unit' => 'Head', 'img' => 'images/cabage.jpg', 'meth' => 'organic'],
    ['cat' => 1, 'name' => 'Fresh Carrots', 'price' => 1000, 'unit' => 'Bundle', 'img' => 'images/carrot.jpg', 'meth' => 'traditional'],
    ['cat' => 1, 'name' => 'NJama Njama', 'price' => 300, 'unit' => 'Bundle', 'img' => 'images/njama njama.jpg', 'meth' => 'organic'],
    ['cat' => 1, 'name' => 'Green Beans', 'price' => 1200, 'unit' => 'Basket', 'img' => 'images/Green beens.jpg', 'meth' => 'organic'],
    ['cat' => 1, 'name' => 'Green Bell Pepper', 'price' => 1500, 'unit' => 'Basket', 'img' => 'images/Green pepper.png', 'meth' => 'traditional'],
    ['cat' => 1, 'name' => 'Fresh Celery', 'price' => 400, 'unit' => 'Bundle', 'img' => 'images/celery.jpg', 'meth' => 'traditional'],
    ['cat' => 1, 'name' => 'Spring Onions', 'price' => 300, 'unit' => 'Bundle', 'img' => 'images/spring onions.jpg', 'meth' => 'traditional'],

    ['cat' => 2, 'name' => 'Sweet Mangoes', 'price' => 100, 'unit' => 'Piece', 'img' => 'images/mango.jpg', 'meth' => 'traditional'],
    ['cat' => 2, 'name' => 'Watermelon', 'price' => 1500, 'unit' => 'Piece', 'img' => 'images/watermelon.jpg', 'meth' => 'traditional'],
    ['cat' => 2, 'name' => 'Fresh Pineapple', 'price' => 800, 'unit' => 'Piece', 'img' => 'images/painaple.jpg', 'meth' => 'traditional'],
    ['cat' => 2, 'name' => 'Papaya (Paw Paw)', 'price' => 1000, 'unit' => 'Piece', 'img' => 'images/paw paw.jpg', 'meth' => 'traditional'],

    ['cat' => 3, 'name' => 'Yellow Corn', 'price' => 5000, 'unit' => 'Bag', 'img' => 'images/corn.jpg', 'meth' => 'traditional'],
    ['cat' => 4, 'name' => 'Raw Groundnuts', 'price' => 8000, 'unit' => 'Bag', 'img' => 'images/groundnut.jpg', 'meth' => 'traditional'],

    ['cat' => 6, 'name' => 'White Yam', 'price' => 2000, 'unit' => 'Tuber', 'img' => 'images/yam.jpg', 'meth' => 'traditional'],
    ['cat' => 6, 'name' => 'Fresh Cassava', 'price' => 3000, 'unit' => 'Bag', 'img' => 'images/cassava.jpg', 'meth' => 'traditional'],
    ['cat' => 6, 'name' => 'Irish Potatoes', 'price' => 7500, 'unit' => 'Bag', 'img' => 'images/potato.jpg', 'meth' => 'traditional'],
    ['cat' => 6, 'name' => 'Sweet Potatoes', 'price' => 4000, 'unit' => 'Bag', 'img' => 'images/sweet potato.jpg', 'meth' => 'traditional'],

    ['cat' => 7, 'name' => 'Fresh Basil', 'price' => 250, 'unit' => 'Bundle', 'img' => 'images/baseli.jpg', 'meth' => 'organic'],
    ['cat' => 7, 'name' => 'Fresh Thyme', 'price' => 250, 'unit' => 'Bundle', 'img' => 'images/thyme.jpg', 'meth' => 'organic'],
];

foreach ($products as $p) {
    $pName = $conn->real_escape_string($p['name']);
    $conn->query("INSERT INTO products (farmer_id, category_id, name, description, price_per_unit, unit, cultivation_method, status) 
                  VALUES (1, {$p['cat']}, '$pName', 'Freshly grown $pName from our farm.', {$p['price']}, '{$p['unit']}', '{$p['meth']}', 'published')");
    $pid = $conn->insert_id;
    if ($pid) {
        $conn->query("INSERT INTO product_images (product_id, image_path, is_primary) VALUES ($pid, '{$p['img']}', 1)");
        $hDate = date('Y-m-d', strtotime('+' . rand(5, 25) . ' days'));
        $conn->query("INSERT INTO product_batches (product_id, batch_name, quantity_available, harvest_date, booking_deadline) 
                      VALUES ($pid, 'Batch 1', 100, '$hDate', '" . date('Y-m-d', strtotime($hDate . ' -3 days')) . "')");
    }
}

echo "✅ Setup Complete!\n";
echo "URL: " . APP_URL . "\n";
echo "Buyer: buyer@demo.com / password123\n";
echo "Farmer: farmer@demo.com / password123\n";
echo "Admin: admin@demo.com / password123\n";
$conn->close();
?>