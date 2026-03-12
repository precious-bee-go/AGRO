<?php
namespace Controllers;

use Core\Controller;
use Models\Product;

class ShopController extends Controller
{
    private $productModel;

    public function __construct()
    {
        $this->productModel = new Product();
    }

    public function index()
    {
        $search = $_GET['q'] ?? '';
        $method = $_GET['method'] ?? '';
        $priceRange = $_GET['price'] ?? '';
        $categoryId = $_GET['category'] ?? '';

        $params = [];
        $types = "";

        $sql = "SELECT p.*, c.name as category_name, 
                (SELECT MIN(harvest_date) FROM product_batches WHERE product_id = p.id) as earliest_harvest,
                (SELECT SUM(quantity_available) FROM product_batches WHERE product_id = p.id) as total_qty,
                i.image_path as image_path
                FROM products p 
                JOIN categories c ON p.category_id = c.id
                LEFT JOIN product_images i ON p.id = i.product_id AND i.is_primary = 1
                WHERE p.status = 'published'";

        if (!empty($search)) {
            $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $types .= "ss";
        }

        if (!empty($method)) {
            $sql .= " AND p.cultivation_method = ?";
            $params[] = $method;
            $types .= "s";
        }

        if (!empty($categoryId)) {
            $sql .= " AND p.category_id = ?";
            $params[] = $categoryId;
            $types .= "i";
        }

        if (!empty($priceRange)) {
            if ($priceRange === '0-5000') {
                $sql .= " AND p.price_per_unit <= 5000";
            } elseif ($priceRange === '5000-15000') {
                $sql .= " AND p.price_per_unit BETWEEN 5000 AND 15000";
            } elseif ($priceRange === '15000+') {
                $sql .= " AND p.price_per_unit > 15000";
            }
        }

        $sql .= " ORDER BY p.created_at DESC";

        if (!empty($params)) {
            $products = $this->productModel->query($sql, $params, $types);
        } else {
            $products = $this->productModel->query($sql);
        }

        $categories = $this->productModel->getCategories();

        $this->view('shop/index', [
            'title' => 'Shop Products',
            'products' => $products,
            'categories' => $categories,
            'filters' => [
                'q' => $search,
                'method' => $method,
                'price' => $priceRange,
                'category' => $categoryId
            ]
        ]);
    }

    public function details()
    {
        $id = $_GET['id'] ?? null;
        if (!$id)
            $this->redirect('shop');

        $sql = "SELECT p.*, c.name as category_name, u.name as farmer_name, i.image_path
                FROM products p 
                JOIN categories c ON p.category_id = c.id
                JOIN users u ON p.farmer_id = u.id
                LEFT JOIN product_images i ON p.id = i.product_id AND i.is_primary = 1
                WHERE p.id = ?";

        $product = $this->productModel->query($sql, [$id], "i")->fetch_assoc();

        if (!$product)
            $this->redirect('shop');

        // Get batches for this product
        $batches = $this->productModel->query("SELECT * FROM product_batches WHERE product_id = ? AND harvest_date >= CURDATE()", [$id], "i");

        // Get reviews
        $reviews = $this->productModel->getReviews($id);
        $ratingInfo = $this->productModel->getAverageRating($id);

        // Get more from this farmer
        $farmerItems = $this->productModel->query("SELECT p.*, i.image_path FROM products p 
                                                  LEFT JOIN product_images i ON p.id = i.product_id AND i.is_primary = 1
                                                  WHERE p.farmer_id = ? AND p.id != ? AND p.status = 'published' LIMIT 4",
            [$product['farmer_id'], $id],
            "ii"
        );

        $this->view('shop/details', [
            'title' => $product['name'],
            'product' => $product,
            'batches' => $batches,
            'reviews' => $reviews,
            'rating' => $ratingInfo,
            'farmerItems' => $farmerItems
        ]);
    }

    public function addReview()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
            $data = [
                'buyer_id' => $_SESSION['user_id'],
                'product_id' => $_POST['product_id'],
                'rating' => $_POST['rating'],
                'comment' => $_POST['comment']
            ];
            $this->productModel->addReview($data);
            $this->redirect('shop/details?id=' . $_POST['product_id']);
        } else {
            $this->redirect('auth/login');
        }
    }
}
?>