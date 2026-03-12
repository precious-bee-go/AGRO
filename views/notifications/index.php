<?php require_once ROOT_PATH . '/views/layout/header.php'; ?>

<section class="notification-section">
    <div class="header-action">
        <h1>Notifications</h1>
        <a href="<?php echo APP_URL; ?>/notification/markRead" class="btn btn-outline btn-sm">Mark all as read</a>
    </div>

    <div class="notification-list">
        <?php if (is_object($notifications) && $notifications->num_rows > 0): ?>
            <?php while ($note = $notifications->fetch_assoc()): ?>
                <div class="notification-item glass <?php echo $note['type']; ?>">
                    <div class="note-content">
                        <h3>
                            <?php echo $note['title']; ?>
                        </h3>
                        <p>
                            <?php echo $note['message']; ?>
                        </p>
                        <span class="note-date">
                            <?php echo date('M d, H:i', strtotime($note['created_at'])); ?>
                        </span>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state glass">
                <p>You're all caught up! No new notifications.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<style>
    .header-action {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 40px 0 20px;
    }

    .notification-item {
        padding: 20px;
        margin-bottom: 15px;
        border-left: 4px solid var(--primary-color);
    }

    .notification-item.order {
        border-left-color: #3498db;
    }

    .notification-item.harvest {
        border-left-color: #e67e22;
    }

    .note-date {
        font-size: 0.75rem;
        color: #7f8c8d;
    }
</style>

<?php require_once ROOT_PATH . '/views/layout/footer.php'; ?>