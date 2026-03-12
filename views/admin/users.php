<?php require_once ROOT_PATH . '/views/layout/header.php'; ?>

<div class="dashboard-header animate-up">
    <h1><i class="fas fa-users-cog"></i> User Management</h1>
    <a href="<?php echo APP_URL; ?>/admin/dashboard" class="btn btn-outline-sm"><i class="fas fa-arrow-left"></i> Back
        to Dashboard</a>
</div>

<div class="user-management-table premium-glass animate-up delay-1">
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Joined</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (is_object($users) && $users->num_rows > 0): ?>
                <?php while ($user = $users->fetch_assoc()): ?>
                    <tr>
                        <td>#
                            <?php echo $user['id']; ?>
                        </td>
                        <td>
                            <div class="user-info-cell">
                                <img src="<?php echo APP_URL; ?>/assets/images/<?php echo $user['profile_pic']; ?>" alt=""
                                    class="avatar-sm">
                                <span>
                                    <?php echo $user['name']; ?>
                                </span>
                            </div>
                        </td>
                        <td>
                            <?php echo $user['email']; ?>
                        </td>
                        <td>
                            <span class="role-badge role-<?php echo $user['role']; ?>">
                                <?php echo ucfirst($user['role']); ?>
                            </span>
                        </td>
                        <td>
                            <?php echo date('M d, Y', strtotime($user['created_at'])); ?>
                        </td>
                        <td>
                            <div class="action-btns">
                                <button class="btn-icon text-primary"><i class="fas fa-edit"></i></button>
                                <button class="btn-icon text-danger"><i class="fas fa-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">No users found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<style>
    .user-management-table {
        padding: 1rem;
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .data-table th {
        padding: 1.2rem 1rem;
        border-bottom: 2px solid rgba(0, 0, 0, 0.05);
        color: #7f8c8d;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .data-table td {
        padding: 1.2rem 1rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.02);
        vertical-align: middle;
    }

    .user-info-cell {
        display: flex;
        align-items: center;
        gap: 0.8rem;
    }

    .avatar-sm {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
    }

    .action-btns {
        display: flex;
        gap: 0.5rem;
    }

    .btn-icon {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 1rem;
        transition: opacity 0.2s;
    }

    .btn-icon:hover {
        opacity: 0.7;
    }

    .role-badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .role-admin {
        background: #fee2e2;
        color: #991b1b;
    }

    .role-farmer {
        background: #dcfce7;
        color: #166534;
    }

    .role-buyer {
        background: #dbeafe;
        color: #1e40af;
    }
</style>

<?php require_once ROOT_PATH . '/views/layout/footer.php'; ?>