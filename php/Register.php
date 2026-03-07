<?php
include "db_connect.php";

if (isset($_POST['register'])) {

    // Get form data
    // Handle name from either 'name' or separate first/last fields
    if (isset($_POST['First-name']) && isset($_POST['Last-name'])) {
        $name = mysqli_real_escape_string($conn, $_POST['First-name'] . " " . $_POST['Last-name']);
    } else {
        $name = mysqli_real_escape_string($conn, $_POST['name'] ?? 'User');
    }

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $role = mysqli_real_escape_string($conn, $_POST['role'] ?? 'buyer');

    // Check if email already exists
    $check_sql = "SELECT id FROM users WHERE email = '$email'";
    $check_result = mysqli_query($conn, $check_sql);
    if (mysqli_num_rows($check_result) > 0) {
        echo "Error: Email already registered. <a href='../html/Login.html'>Login here</a>";
        exit();
    }

    // Hash password for security
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert into database
    $sql = "INSERT INTO users (name, email, password, role)
            VALUES ('$name', '$email', '$hashedPassword', '$role')";

    if (mysqli_query($conn, $sql)) {
        echo "Registration successful. <a href='../html/Login.html'>Login here</a>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>