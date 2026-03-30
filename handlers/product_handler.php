<?php
require_once "../config/config.php";
require_once "../config/database.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'farmer') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';
    
    if($action == 'add') {
        $name = htmlspecialchars($_POST['name']);
        $category = htmlspecialchars($_POST['category']);
        $description = htmlspecialchars($_POST['description']);
        $price = floatval($_POST['price']);
        $quantity = intval($_POST['quantity']);
        $unit = htmlspecialchars($_POST['unit']);
        $farmer_id = $_SESSION['user_id'];
        
        // NEW: Harvest information
        $harvest_date = !empty($_POST['harvest_date']) ? $_POST['harvest_date'] : null;
        $ready_status = $_POST['ready_status'] ?? 'cultivating';
        $estimated_ready_time = !empty($_POST['estimated_ready_time']) ? htmlspecialchars($_POST['estimated_ready_time']) : null;
        
        // Handle image upload
        $image = '';
        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $target_dir = "../uploads/products/";
            if(!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $image = time() . '_' . basename($_FILES['image']['name']);
            $target_file = $target_dir . $image;
            move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
        }
        
        $stmt = $conn->prepare("INSERT INTO products (farmer_id, name, category, description, price, quantity, unit, image, harvest_date, ready_status, estimated_ready_time) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if($stmt->execute([$farmer_id, $name, $category, $description, $price, $quantity, $unit, $image, $harvest_date, $ready_status, $estimated_ready_time])) {
            $_SESSION['success'] = "Product added successfully!";
        } else {
            $_SESSION['error'] = "Failed to add product.";
        }
        header("Location: ../farmer/dashboard.php");
        exit();
    }
    
    elseif($action == 'update') {
        $id = intval($_POST['id']);
        $name = htmlspecialchars($_POST['name']);
        $category = htmlspecialchars($_POST['category']);
        $description = htmlspecialchars($_POST['description']);
        $price = floatval($_POST['price']);
        $quantity = intval($_POST['quantity']);
        $unit = htmlspecialchars($_POST['unit']);
        
        // NEW: Harvest information
        $harvest_date = !empty($_POST['harvest_date']) ? $_POST['harvest_date'] : null;
        $ready_status = $_POST['ready_status'] ?? 'cultivating';
        $estimated_ready_time = !empty($_POST['estimated_ready_time']) ? htmlspecialchars($_POST['estimated_ready_time']) : null;
        
        $sql = "UPDATE products SET name=?, category=?, description=?, price=?, quantity=?, unit=?, harvest_date=?, ready_status=?, estimated_ready_time=?";
        $params = [$name, $category, $description, $price, $quantity, $unit, $harvest_date, $ready_status, $estimated_ready_time];
        
        // Handle image upload
        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $target_dir = "../uploads/products/";
            $image = time() . '_' . basename($_FILES['image']['name']);
            $target_file = $target_dir . $image;
            move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
            $sql .= ", image=?";
            $params[] = $image;
        }
        
        $sql .= " WHERE id=? AND farmer_id=?";
        $params[] = $id;
        $params[] = $_SESSION['user_id'];
        
        $stmt = $conn->prepare($sql);
        if($stmt->execute($params)) {
            $_SESSION['success'] = "Product updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update product.";
        }
        header("Location: ../farmer/dashboard.php");
        exit();
    }
    
    // NEW: Update status only
    elseif($action == 'update_status') {
        $id = intval($_POST['id']);
        $ready_status = $_POST['ready_status'];
        $estimated_ready_time = !empty($_POST['estimated_ready_time']) ? htmlspecialchars($_POST['estimated_ready_time']) : null;
        
        $stmt = $conn->prepare("UPDATE products SET ready_status=?, estimated_ready_time=? WHERE id=? AND farmer_id=?");
        if($stmt->execute([$ready_status, $estimated_ready_time, $id, $_SESSION['user_id']])) {
            $_SESSION['success'] = "Harvest status updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update status.";
        }
        header("Location: ../farmer/dashboard.php");
        exit();
    }
    
    elseif($action == 'delete') {
        $id = intval($_POST['id']);
        
        $stmt = $conn->prepare("DELETE FROM products WHERE id=? AND farmer_id=?");
        if($stmt->execute([$id, $_SESSION['user_id']])) {
            $_SESSION['success'] = "Product deleted successfully!";
        } else {
            $_SESSION['error'] = "Failed to delete product.";
        }
        header("Location: ../farmer/dashboard.php");
        exit();
    }
}
?>