<?php
require_once __DIR__ . '/../../utils/database/adminFunction.php';
require_once __DIR__ . '/../../utils/CleanerFunctions.php';
require_once __DIR__ . '/../../utils/PageBlocker.php';
require_once __DIR__ . '/../../utils/popupmessages/back.php';

session_start();
loginBlock();
redirectLearner();
checkPost();

function checkPost() {
    global $conn;
    
    if (isset($_POST['logout'])) {
        resetSession();
        headTo('../login.php');
    }
    
    if (isset($_POST['toggle_user']) && isset($_POST['user_id'])) {
        $result = toggleUserActivation($conn, (int)$_POST['user_id']);
        if ($result) {
            setNewPopupMessage('User status updated successfully!');
        } else {
            setNewPopupMessage('Failed to update user status.');
        }
        headTo($_SERVER['PHP_SELF'] . '?' . http_build_query($_GET));
    }
    
    if (isset($_POST['delete_user']) && isset($_POST['user_id'])) {
        $result = deleteUser($conn, (int)$_POST['user_id']);
        if ($result) {
            setNewPopupMessage('User deleted successfully!');
        } else {
            setNewPopupMessage('Failed to delete user.');
        }
        headTo($_SERVER['PHP_SELF'] . '?' . http_build_query($_GET));
    }
    
    clearPost();
}

$conn = getConnection();
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$search = $_GET['search'] ?? '';
$data = getAllUsers($conn, $page, 20, $search);
$users = $data['users'];
$total_pages = $data['total_pages'];
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - TutorChat Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../assets/theme.css">
    <link rel="stylesheet" href="../../assets/admin_bootstrap_theme.css">
    <link rel="stylesheet" href="../../assets/popupMessage.css">
</head>
<body>
<?php displayPopupMessage(); ?>

<div class="admin-container">
    <aside class="admin-sidebar">
        <h1 class="admin-title">
            <i class="bi bi-chat-dots-fill text-brand"></i> TutorChat Admin
        </h1>
        <nav class="admin-nav">
            <a href="dashboard.php" class="nav-link">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="user_management.php" class="nav-link active">
                <i class="bi bi-people"></i> User Management
            </a>
            <a href="topic_management.php" class="nav-link">
                <i class="bi bi-book"></i> Topic Management
            </a>
            <a href="feedback_monitoring.php" class="nav-link">
                <i class="bi bi-chat-left-quote"></i> Feedback
            </a>
            <a href="test_reports.php" class="nav-link">
                <i class="bi bi-bar-chart"></i> Test Reports
            </a>
            <hr class="sidebar-divider">
            <form method="post">
                <button type="submit" name="logout" class="btn-logout">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </button>
            </form>
        </nav>
    </aside>

    <main class="admin-content">
        <div class="page-header">
            <h2><i class="bi bi-people text-brand"></i> User Management</h2>
            <p class="text-muted">Manage user accounts and permissions</p>
        </div>

        <div class="card">
            <div class="card-header">
                <h3><i class="bi bi-search"></i> Search Users</h3>
            </div>
            <div class="card-body">
                <form method="GET" class="search-form">
                    <input type="text" name="search" class="form-control" placeholder="Search by email or nickname..." value="<?= cleanHTML($search) ?>">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Search
                    </button>
                    <?php if (!empty($search)): ?>
                        <a href="user_management.php" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Clear
                        </a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>All Users (<?= $data['total'] ?>)</h3>
            </div>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Nickname</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr><td colspan="6" class="text-center">No users found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= (int)$user['id'] ?></td>
                                    <td><?= cleanHTML($user['email']) ?></td>
                                    <td><?= cleanHTML($user['nick']) ?></td>
                                    <td>
                                        <?php if ($user['acc_role'] === 'admin'): ?>
                                            <span class="badge badge-primary">
                                                <i class="bi bi-shield-check"></i> Admin
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">
                                                <i class="bi bi-person"></i> Learner
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($user['activated']): ?>
                                            <span class="badge badge-success">
                                                <i class="bi bi-check-circle"></i> Active
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">
                                                <i class="bi bi-x-circle"></i> Disabled
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($user['acc_role'] !== 'admin'): ?>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="user_id" value="<?= (int)$user['id'] ?>">
                                                <button type="submit" name="toggle_user" class="btn btn-sm btn-warning" onclick="return confirm('Toggle user activation status?')">
                                                    <?= $user['activated'] ? 'Disable' : 'Enable' ?>
                                                </button>
                                            </form>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="user_id" value="<?= (int)$user['id'] ?>">
                                                <button type="submit" name="delete_user" class="btn btn-sm btn-danger" onclick="return confirm('Permanently delete this user? This action cannot be undone.')">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted"><i class="bi bi-lock"></i> Protected</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pages > 1): ?>
            <div class="card-footer">
                <nav class="pagination-wrapper">
                    <ul class="pagination">
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">
                                <i class="bi bi-chevron-left"></i> Previous
                            </a>
                        </li>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>">
                                Next <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../../utils/popupmessages/front.js"></script>
</body>
</html>