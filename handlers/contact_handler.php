<?php
require_once "../config/config.php";
require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $message = htmlspecialchars(trim($_POST['message']));
    $farmer_id = intval($_POST['farmer_id'] ?? 0);

    if (!$name || !$email || !$message || $farmer_id <= 0) {
        $_SESSION['error'] = "Please fill all fields correctly and select a farmer.";
        header("Location: ../contact.php");
        exit();
    }

    // Determine recipient farmer
    $farmerStmt = $conn->prepare("SELECT id, full_name, username, email FROM users WHERE id = ? AND role = 'farmer' AND is_active = 1");
    $farmerStmt->execute([$farmer_id]);
    $farmer = $farmerStmt->fetch(PDO::FETCH_ASSOC);

    if (!$farmer) {
        $_SESSION['error'] = "Selected farmer not found. Please try again.";
        header("Location: ../contact.php");
        exit();
    }

    // Store message for records with reply fields
    try {
        $conn->exec("CREATE TABLE IF NOT EXISTS messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            sender_name VARCHAR(100) NOT NULL,
            sender_email VARCHAR(150) NOT NULL,
            farmer_id INT NOT NULL,
            message TEXT NOT NULL,
            reply TEXT NULL,
            replied_at TIMESTAMP NULL DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (farmer_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $insert = $conn->prepare("INSERT INTO messages (sender_name, sender_email, farmer_id, message) VALUES (?, ?, ?, ?)");
        $insert->execute([$name, $email, $farmer_id, $message]);

        // TODO: Optionally send notice email to farmer
        // mail($farmer['email'], "New customer message", "You have a new message.", "From: no-reply@agro-ecommerce.com");

        $_SESSION['success'] = "Message submitted to {$farmer['full_name']} successfully. They can reply soon.";

    } catch (PDOException $e) {
        $_SESSION['error'] = "Message could not be sent: " . $e->getMessage();
    }

    header("Location: ../contact.php");
    exit();
}

header("Location: ../contact.php");
exit();
?>