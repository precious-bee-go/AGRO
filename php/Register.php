<?php
include "db_connect.php";

if (isset($_POST['register'])) {

    // Get form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Hash password for security
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert into database
    $sql = "INSERT INTO users (name, email, password, role)
            VALUES ('$name', '$email', '$hashedPassword', '$role')";

    if (mysqli_query($conn, $sql)) {
        echo "Registration successful";
        // later we will redirect to login
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
