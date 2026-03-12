<?php
namespace Controllers;

use Core\Controller;
use Models\Product;

class CartController extends Controller
{
    public function add()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('auth/login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $batchId = $_POST['batch_id'];
            $qty = (float) $_POST['quantity'];

            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            // Check if batch already in cart
            if (isset($_SESSION['cart'][$batchId])) {
                $_SESSION['cart'][$batchId] += $qty;
            } else {
                $_SESSION['cart'][$batchId] = $qty;
            }

            $this->redirect('cart/show');
        }
    }

    public function show()
    {
        $productModel = new Product();
        $cartItems = [];
        $total = 0;

        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $batchId => $qty) {
                $sql = "SELECT b.*, p.name, p.price_per_unit, p.unit 
                        FROM product_batches b 
                        JOIN products p ON b.product_id = p.id 
                        WHERE b.id = ?";
                $item = $productModel->query($sql, [$batchId], "i")->fetch_assoc();
                if ($item) {
                    $item['cart_qty'] = $qty;
                    $item['subtotal'] = $qty * $item['price_per_unit'];
                    $cartItems[] = $item;
                    $total += $item['subtotal'];
                }
            }
        }

        $this->view('cart/index', [
            'title' => 'Your Shopping Cart',
            'items' => $cartItems,
            'total' => $total,
            'advance' => $total * BOOKING_PERCENTAGE
        ]);
    }

    public function remove()
    {
        $batchId = $_GET['id'] ?? null;
        if ($batchId && isset($_SESSION['cart'][$batchId])) {
            unset($_SESSION['cart'][$batchId]);
        }
        $this->redirect('cart/show');
    }
}
?>