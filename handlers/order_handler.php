<?php
require_once "../config/config.php";
require_once "../config/database.php";

if(!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];
    $order_id = intval($_POST['order_id'] ?? 0);

    if($order_id <= 0) {
        $_SESSION['error'] = "Invalid order ID.";
        header("Location: ../index.php");
        exit();
    }

    // Get order details
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$order) {
        $_SESSION['error'] = "Order not found.";
        header("Location: ../index.php");
        exit();
    }

    // Check permissions - admin can update any order, customers can only update their own orders
    if($_SESSION['role'] != 'admin' && $order['user_id'] != $_SESSION['user_id']) {
        $_SESSION['error'] = "You don't have permission to modify this order.";
        header("Location: ../index.php");
        exit();
    }

    if($action == 'cancel_order') {
        // Only allow cancellation if order is not delivered
        if($order['status'] == 'delivered') {
            $_SESSION['error'] = "Cannot cancel a delivered order.";
            header("Location: " . ($_SESSION['role'] == 'admin' ? "../admin/orders.php" : "../customer/orders.php"));
            exit();
        }

        try {
            $conn->beginTransaction();

            // Update order status to cancelled
            $stmt = $conn->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ?");
            $stmt->execute([$order_id]);

            // Get order items and restore inventory
            $stmt = $conn->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = ?");
            $stmt->execute([$order_id]);
            $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach($order_items as $item) {
                // Restore product quantity
                $stmt = $conn->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ?");
                $stmt->execute([$item['quantity'], $item['product_id']]);

                // Check if product should be unmarked as sold
                $stmt = $conn->prepare("SELECT quantity FROM products WHERE id = ?");
                $stmt->execute([$item['product_id']]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);

                // If quantity is now > 0, change status from 'sold' to 'available'
                if($product && $product['quantity'] > 0) {
                    $stmt = $conn->prepare("UPDATE products SET status = 'available' WHERE id = ?");
                    $stmt->execute([$item['product_id']]);
                }
            }

            $conn->commit();

            $_SESSION['success'] = "Order cancelled successfully. Product inventory has been restored.";
            header("Location: " . ($_SESSION['role'] == 'admin' ? "../admin/orders.php" : "../customer/orders.php"));
            exit();

        } catch(Exception $e) {
            $conn->rollBack();
            $_SESSION['error'] = "Failed to cancel order: " . $e->getMessage();
            header("Location: " . ($_SESSION['role'] == 'admin' ? "../admin/orders.php" : "../customer/orders.php"));
            exit();
        }
    }

    elseif($action == 'update_status' && $_SESSION['role'] == 'admin') {
        $new_status = $_POST['status'] ?? '';

        if(!in_array($new_status, ['pending', 'processing', 'delivered', 'cancelled'])) {
            $_SESSION['error'] = "Invalid status.";
            header("Location: ../admin/orders.php");
            exit();
        }

        // If changing TO cancelled, restore inventory
        if($new_status == 'cancelled' && $order['status'] != 'cancelled') {
            try {
                $conn->beginTransaction();

                // Update order status
                $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
                $stmt->execute([$new_status, $order_id]);

                // Get order items and restore inventory
                $stmt = $conn->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = ?");
                $stmt->execute([$order_id]);
                $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach($order_items as $item) {
                    // Restore product quantity
                    $stmt = $conn->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ?");
                    $stmt->execute([$item['quantity'], $item['product_id']]);

                    // Check if product should be unmarked as sold
                    $stmt = $conn->prepare("SELECT quantity FROM products WHERE id = ?");
                    $stmt->execute([$item['product_id']]);
                    $product = $stmt->fetch(PDO::FETCH_ASSOC);

                    // If quantity is now > 0, change status from 'sold' to 'available'
                    if($product && $product['quantity'] > 0) {
                        $stmt = $conn->prepare("UPDATE products SET status = 'available' WHERE id = ?");
                        $stmt->execute([$item['product_id']]);
                    }
                }

                $conn->commit();
                $_SESSION['success'] = "Order status updated to {$new_status}. Product inventory restored.";

            } catch(Exception $e) {
                $conn->rollBack();
                $_SESSION['error'] = "Failed to update order status: " . $e->getMessage();
            }
        }
        // If changing FROM cancelled to something else, deduct inventory again
        elseif($order['status'] == 'cancelled' && $new_status != 'cancelled') {
            try {
                $conn->beginTransaction();

                // Update order status
                $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
                $stmt->execute([$new_status, $order_id]);

                // Get order items and deduct inventory again
                $stmt = $conn->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = ?");
                $stmt->execute([$order_id]);
                $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach($order_items as $item) {
                    // Deduct product quantity again
                    $stmt = $conn->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
                    $stmt->execute([$item['quantity'], $item['product_id']]);

                    // Check if product should be marked as sold
                    $stmt = $conn->prepare("SELECT quantity FROM products WHERE id = ?");
                    $stmt->execute([$item['product_id']]);
                    $product = $stmt->fetch(PDO::FETCH_ASSOC);

                    // If quantity is now <= 0, change status to 'sold'
                    if($product && $product['quantity'] <= 0) {
                        $stmt = $conn->prepare("UPDATE products SET status = 'sold' WHERE id = ?");
                        $stmt->execute([$item['product_id']]);
                    }
                }

                $conn->commit();
                $_SESSION['success'] = "Order status updated to {$new_status}. Product inventory adjusted.";

            } catch(Exception $e) {
                $conn->rollBack();
                $_SESSION['error'] = "Failed to update order status: " . $e->getMessage();
            }
        }
        else {
            // Simple status update without inventory changes
            $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $stmt->execute([$new_status, $order_id]);
            $_SESSION['success'] = "Order status updated to {$new_status}.";
        }

        header("Location: ../admin/orders.php");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>