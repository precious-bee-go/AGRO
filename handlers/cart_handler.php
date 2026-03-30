<?php
require_once "../config/config.php";
require_once "../config/database.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        echo json_encode(['success' => false, 'message' => 'Please login first']);
        exit();
    } else {
        header("Location: ../login.php");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';
    $user_id = $_SESSION['user_id'];
    
    if($action == 'add') {
        $product_id = intval($_POST['product_id']);
        $quantity = intval($_POST['quantity']);
        
        // Check product availability
        $stmt = $conn->prepare("SELECT quantity, status FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if(!$product) {
            $_SESSION['error'] = "Product not found";
            header("Location: ../product.php");
            exit();
        }
        
        // Cast to int to ensure proper comparison
        $available_qty = intval($product['quantity']);
        
        // Check if product is sold or out of stock
        if($product['status'] == 'sold' || $available_qty <= 0) {
            $_SESSION['error'] = "This product is sold out";
            header("Location: ../product.php");
            exit();
        }
        
        if($quantity > $available_qty) {
            $_SESSION['error'] = "Only {$available_qty} items available";
            header("Location: ../product.php");
            exit();
        }
        
        // Check if product already in cart
        $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($existing) {
            // Update quantity
            $new_quantity = $existing['quantity'] + $quantity;
            $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
            $stmt->execute([$new_quantity, $existing['id']]);
            $_SESSION['success'] = "Cart updated!";
        } else {
            // Add new item
            $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $product_id, $quantity]);
            $_SESSION['success'] = "Product added to cart!";
        }
        
        header("Location: ../cart.php");
        exit();
    }
    
    elseif($action == 'update') {
        $cart_id = intval($_POST['cart_id']);
        $quantity = intval($_POST['quantity']);
        
        if($quantity > 0) {
            // Check product availability
            $stmt = $conn->prepare("SELECT p.quantity FROM cart c JOIN products p ON c.product_id = p.id WHERE c.id = ?");
            $stmt->execute([$cart_id]);
            $product_qty = $stmt->fetchColumn();
            
            if($quantity <= $product_qty) {
                $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
                $stmt->execute([$quantity, $cart_id, $user_id]);
                $_SESSION['success'] = "Cart updated!";
            } else {
                $_SESSION['error'] = "Only $product_qty items available";
            }
        } else {
            // Remove if quantity is 0
            $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
            $stmt->execute([$cart_id, $user_id]);
            $_SESSION['success'] = "Item removed from cart";
        }
        
        header("Location: ../cart.php");
        exit();
    }
    
    elseif($action == 'remove') {
        $cart_id = intval($_POST['cart_id']);
        
        $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $stmt->execute([$cart_id, $user_id]);
        
        $_SESSION['success'] = "Item removed from cart";
        header("Location: ../cart.php");
        exit();
    }
    
    elseif($action == 'clear') {
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->execute([$user_id]);
        
        $_SESSION['success'] = "Cart cleared";
        header("Location: ../cart.php");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>