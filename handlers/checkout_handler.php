<?php
require_once "../config/config.php";
require_once "../config/database.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $payment_method = $_POST['payment_method'];
    $shipping_address = htmlspecialchars($_POST['shipping_address']);
    
    try {
        $conn->beginTransaction();
        
        // Get cart items
        $stmt = $conn->prepare("SELECT c.*, p.price, p.farmer_id 
                                FROM cart c 
                                JOIN products p ON c.product_id = p.id 
                                WHERE c.user_id = ?");
        $stmt->execute([$user_id]);
        $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if(empty($cart_items)) {
            throw new Exception("Cart is empty");
        }
        
        // Calculate total
        $total = 0;
        foreach($cart_items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        // Create order
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, payment_method, shipping_address) 
                                VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $total, $payment_method, $shipping_address]);
        $order_id = $conn->lastInsertId();
        
        // Add order items and update product quantities
        foreach($cart_items as $item) {
            // Add to order items
            $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) 
                                    VALUES (?, ?, ?, ?)");
            $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
            
            // Update product quantity
            $stmt = $conn->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
            $stmt->execute([$item['quantity'], $item['product_id']]);
        }
        
        // Clear cart
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->execute([$user_id]);
        
        $conn->commit();
        
        $_SESSION['success'] = "Order placed successfully! Order ID: #" . $order_id;
        header("Location: ../customer/orders.php");
        exit();
        
    } catch(Exception $e) {
        $conn->rollBack();
        $_SESSION['error'] = "Checkout failed: " . $e->getMessage();
        header("Location: ../checkout.php");
        exit();
    }
}
?>