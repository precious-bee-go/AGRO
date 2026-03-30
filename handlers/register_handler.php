<?php
require_once "../config/config.php";
require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars(trim($_POST['username']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = htmlspecialchars(trim($_POST['full_name']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $address = htmlspecialchars(trim($_POST['address']));
    $role = $_POST['role'];
    
    $errors = [];
    
    // Validation
    if(empty($username)) $errors[] = "Username is required";
    if(empty($email)) $errors[] = "Email is required";
    if(empty($password)) $errors[] = "Password is required";
    if($password != $confirm_password) $errors[] = "Passwords do not match";
    if(strlen($password) < 6) $errors[] = "Password must be at least 6 characters";
    
    // Check if user exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if($stmt->rowCount() > 0) {
        $errors[] = "Username or email already exists";
    }
    
    if(empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, full_name, phone, address, role) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
        if($stmt->execute([$username, $email, $hashed_password, $full_name, $phone, $address, $role])) {
            $_SESSION['success'] = "Registration successful! Please login.";
            header("Location: ../login.php");
            exit();
        } else {
            $errors[] = "Registration failed. Please try again.";
        }
    }
    
    if(!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: ../register.php");
        exit();
    }
}
?>