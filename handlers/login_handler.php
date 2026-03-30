<?php
require_once "../config/config.php";
require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars(trim($_POST['email']));
    $password = $_POST['password'];

    // Ensure at least one admin exists for login recovery
    $adminCount = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
    if ($adminCount == 0) {
        $defaultPass = password_hash('admin123', PASSWORD_DEFAULT);
        $createAdmin = $conn->prepare("INSERT INTO users (username, email, password, full_name, role, is_active, payment_status, payment_amount) VALUES (?, ?, ?, ?, ?, 1, 'paid', 0)");
        $createAdmin->execute(['admin', 'admin@agro.com', $defaultPass, 'Administrator', 'admin']);
        $_SESSION['success'] = 'Default admin created: admin@agro.com / admin123';
    }

    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            if (isset($user['is_active']) && $user['is_active'] == 0) {
                $_SESSION['error'] = 'Your account is blocked. Contact admin.';
                header("Location: ../login.php");
                exit();
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            
            // Redirect based on role
            if ($user['role'] == 'admin') {
                header("Location: ../admin/dashboard.php");
            } elseif ($user['role'] == 'farmer') {
                header("Location: ../farmer/dashboard.php");
            } else {
                header("Location: ../index.php");
            }
            exit();
        } else {
            $_SESSION['error'] = "Invalid email or password";
            header("Location: ../login.php");
            exit();
        }
    } catch(PDOException $e) {
        $_SESSION['error'] = "Login failed. Please try again.";
        header("Location: ../login.php");
        exit();
    }
} else {
    header("Location: ../login.php");
    exit();
}
?>