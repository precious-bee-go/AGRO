<?php
namespace Controllers;

use Core\Controller;
use Models\Product;

class FarmerController extends Controller
{
    private $productModel;

    public function __construct()
    {
        // Ensure user is farmer
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'farmer') {
            $this->redirect('auth/login');
        }
        $this->productModel = new Product();
    }

    public function dashboard()
    {
        $orderModel = new \Models\Order();
        $products = $this->productModel->getByFarmer($_SESSION['user_id']);
        $stats = $orderModel->getFarmerStats($_SESSION['user_id']);
        $recentOrders = $orderModel->getByUser($_SESSION['user_id'], 'farmer');

        $this->view('farmer/dashboard', [
            'title' => 'Farmer Dashboard',
            'products' => $products,
            'stats' => $stats,
            'recentOrders' => $recentOrders
        ]);
    }

    public function addProduct()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productData = [
                'farmer_id' => $_SESSION['user_id'],
                'category_id' => $_POST['category_id'],
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'price_per_unit' => $_POST['price'],
                'unit' => $_POST['unit'],
                'cultivation_method' => $_POST['method'],
                'status' => 'published'
            ];

            $productId = $this->productModel->create($productData);

            if ($productId) {
                // Add initial batch
                $batchData = [
                    'product_id' => $productId,
                    'batch_name' => 'First Harvest',
                    'quantity' => $_POST['quantity'],
                    'harvest_date' => $_POST['harvest_date'],
                    'booking_deadline' => date('Y-m-d', strtotime($_POST['harvest_date'] . ' -7 days')),
                    'quality_grade' => 'grade_a'
                ];
                $this->productModel->addBatch($batchData);
                $this->redirect('farmer/dashboard');
            }
        } else {
            $categories = $this->productModel->getCategories();
            $this->view('farmer/add_product', [
                'title' => 'Add New Product',
                'categories' => $categories
            ]);
        }
    }
}
?>