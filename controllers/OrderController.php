<?php
namespace Controllers;

use Core\Controller;
use Models\Order;
use Models\Product;

class OrderController extends Controller
{
    private $orderModel;

    public function __construct()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('auth/login');
        }
        $this->orderModel = new Order();
    }

    public function checkout()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SESSION['cart'])) {
            $productModel = new Product();
            $cartData = ['items' => [], 'total' => 0];

            foreach ($_SESSION['cart'] as $batchId => $qty) {
                $sql = "SELECT b.*, p.price_per_unit FROM product_batches b JOIN products p ON b.product_id = p.id WHERE b.id = ?";
                $item = $productModel->query($sql, [$batchId], "i")->fetch_assoc();
                if ($item) {
                    $item['cart_qty'] = $qty;
                    $cartData['items'][] = $item;
                    $cartData['total'] += $qty * $item['price_per_unit'];
                }
            }

            $orderId = $this->orderModel->create($_SESSION['user_id'], $cartData, $_POST['address']);

            if ($orderId) {
                unset($_SESSION['cart']);
                $this->redirect('order/success?id=' . $orderId);
            } else {
                die("Checkout failed. Please try again.");
            }
        }
    }

    public function success()
    {
        $id = $_GET['id'] ?? null;
        $this->view('order/success', ['title' => 'Booking Confirmed', 'orderId' => $id]);
    }

    public function history()
    {
        $orders = $this->orderModel->getByUser($_SESSION['user_id'], $_SESSION['user_role']);
        $this->view('order/history', ['title' => 'Your Orders', 'orders' => $orders]);
    }
}
?>