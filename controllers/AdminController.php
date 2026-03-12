<?php
namespace Controllers;

use Core\Controller;
use Models\User;
use Models\Order;
use Models\Product;

class AdminController extends Controller
{
    private $userModel;
    private $orderModel;
    private $productModel;

    public function __construct()
    {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            $this->redirect('auth/login');
        }
        $this->userModel = new User();
        $this->orderModel = new Order();
        $this->productModel = new Product();
    }

    public function dashboard()
    {
        // Fetch general stats
        $totalUsers = $this->userModel->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
        $totalOrders = $this->orderModel->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
        $totalProducts = $this->productModel->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
        $totalRevenue = $this->orderModel->query("SELECT SUM(total_amount) as revenue FROM orders")->fetch_assoc()['revenue'] ?? 0;

        // Recent users
        $recentUsers = $this->userModel->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5");

        // Recent orders
        $recentOrders = $this->orderModel->query("SELECT o.*, u.name as buyer_name FROM orders o JOIN users u ON o.buyer_id = u.name ORDER BY o.created_at DESC LIMIT 5");
        // Fix: JOIN users u ON o.buyer_id = u.id (typo in my thought, let me check the query)
        $recentOrders = $this->orderModel->query("SELECT o.*, u.name as buyer_name FROM orders o JOIN users u ON o.buyer_id = u.id ORDER BY o.created_at DESC LIMIT 5");

        $this->view('admin/dashboard', [
            'title' => 'Admin Control Panel',
            'stats' => [
                'users' => $totalUsers,
                'orders' => $totalOrders,
                'products' => $totalProducts,
                'revenue' => $totalRevenue
            ],
            'recentUsers' => $recentUsers,
            'recentOrders' => $recentOrders
        ]);
    }

    public function users()
    {
        $users = $this->userModel->query("SELECT * FROM users ORDER BY created_at DESC");
        $this->view('admin/users', [
            'title' => 'Manage Users',
            'users' => $users
        ]);
    }
}
?>