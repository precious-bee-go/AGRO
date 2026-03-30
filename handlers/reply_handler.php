<?php
require_once "../config/config.php";
require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'farmer') {
        header('Location: ../login.php');
        exit();
    }

    $message_id = intval($_POST['message_id'] ?? 0);
    $reply = trim($_POST['reply'] ?? '');
    $farmer_id = $_SESSION['user_id'];

    if ($message_id <= 0 || $reply === '') {
        $_SESSION['error'] = 'Invalid message or empty reply.';
        header('Location: ../farmer/messages.php');
        exit();
    }

    $stmt = $conn->prepare('SELECT * FROM messages WHERE id = ? AND farmer_id = ?');
    $stmt->execute([$message_id, $farmer_id]);
    $message = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$message) {
        $_SESSION['error'] = 'Message not found.';
        header('Location: ../farmer/messages.php');
        exit();
    }

    $update = $conn->prepare('UPDATE messages SET reply = ?, replied_at = NOW() WHERE id = ? AND farmer_id = ?');
    if ($update->execute([$reply, $message_id, $farmer_id])) {
        $_SESSION['success'] = 'Reply saved successfully.';
    } else {
        $_SESSION['error'] = 'Failed to save reply.';
    }

    header('Location: ../farmer/messages.php');
    exit();
}

header('Location: ../farmer/messages.php');
exit();