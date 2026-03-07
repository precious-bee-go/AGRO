<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['user_type'] = $row['role']; // Using 'role' column from database

            // Redirect based on user type
            if ($row['role'] == 'farmer') {
                header("Location: farmer_dashboard.php");
            } elseif ($row['role'] == 'buyer') {
                header("Location: shop.php");
            } elseif ($row['role'] == 'admin') {
                header("Location: admin.php");
            } else {
                echo "Invalid user type.";
            }
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No user found with that email.";
    }
}
$conn->close();
?>