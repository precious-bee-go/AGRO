<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            // Updated to use 'name' as seen in Register.php
            echo "Login successful! Welcome " . (isset($row['name']) ? $row['name'] : 'User');
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No user found with that email.";
    }
}
$conn->close();
?>