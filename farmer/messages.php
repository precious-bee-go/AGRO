<?php
require_once "../config/config.php";
require_once "../config/database.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'farmer') {
    header("Location: ../login.php");
    exit();
}

$farmer_id = $_SESSION['user_id'];

// Load messages for logged-in farmer
$stmt = $conn->prepare("SELECT * FROM messages WHERE farmer_id = ? ORDER BY created_at DESC");
$stmt->execute([$farmer_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Messages - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include "../includes/navbar.php"; ?>
    <div class="container mt-4">
        <h2>Messages from Customers</h2>

        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <?php if (count($messages) === 0): ?>
            <div class="alert alert-info">No customer messages yet.</div>
        <?php else: ?>
            <div class="accordion" id="messagesAccordion">
                <?php foreach($messages as $msg): ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading<?php echo $msg['id']; ?>">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $msg['id']; ?>" aria-expanded="false" aria-controls="collapse<?php echo $msg['id']; ?>">
                                #<?php echo $msg['id']; ?> - <?php echo htmlspecialchars($msg['sender_name']); ?> (<?php echo htmlspecialchars($msg['sender_email']); ?>) - <?php echo date('d M Y H:i', strtotime($msg['created_at'])); ?>
                            </button>
                        </h2>
                        <div id="collapse<?php echo $msg['id']; ?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $msg['id']; ?>" data-bs-parent="#messagesAccordion">
                            <div class="accordion-body">
                                <p><strong>Message:</strong> <?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>

                                <?php if (!empty($msg['reply'])): ?>
                                    <p><strong>Your Reply:</strong> <?php echo nl2br(htmlspecialchars($msg['reply'])); ?></p>
                                    <p><em>Replied at: <?php echo date('d M Y H:i', strtotime($msg['replied_at'])); ?></em></p>
                                <?php endif; ?>

                                <form action="../handlers/reply_handler.php" method="POST">
                                    <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                    <div class="mb-3">
                                        <label for="reply<?php echo $msg['id']; ?>" class="form-label">Reply</label>
                                        <textarea class="form-control" id="reply<?php echo $msg['id']; ?>" name="reply" rows="3"><?php echo htmlspecialchars($msg['reply']); ?></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Send Reply</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>