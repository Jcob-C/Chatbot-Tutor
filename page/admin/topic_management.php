<?php
require_once __DIR__ . '/../../utils/database/adminFunction.php';
require_once __DIR__ . '/../../utils/CleanerFunctions.php';
require_once __DIR__ . '/../../utils/PageBlocker.php';
require_once __DIR__ . '/../../utils/popupmessages/back.php';
require_once __DIR__ . '/../../config/db.php';

session_start();
loginBlock();
redirectLearner();
checkPost();

$conn = getConnection();

function checkPost() {
    global $conn;
    
    if (isset($_POST['logout'])) {
        resetSession();
        headTo('../login.php');
    }
    
    if (isset($_POST['create_topic'])) {
        $result = createTopic($_POST['title'] ?? '', $_POST['descr'] ?? '');
        setNewPopupMessage($result['message']);
        if ($result['success']) {
            headTo($_SERVER['PHP_SELF']);
        }
    }
    
    if (isset($_POST['update_topic']) && isset($_POST['topic_id'])) {
        $result = updateTopic($conn, (int)$_POST['topic_id'], $_POST['title'] ?? '', $_POST['descr'] ?? '');
        setNewPopupMessage($result['message']);
        if ($result['success']) {
            headTo($_SERVER['PHP_SELF']);
        }
    }
    
    if (isset($_POST['delete_topic']) && isset($_POST['topic_id'])) {
        $result = deleteTopic($conn, (int)$_POST['topic_id']);
        if ($result) {
            setNewPopupMessage('Topic deleted successfully!');
        } else {
            setNewPopupMessage('Failed to delete topic.');
        }
        headTo($_SERVER['PHP_SELF']);
    }
    
    clearPost();
}

$conn = getConnection();
$topics = getAllTopicsAdmin($conn);

$editing_topic = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editing_topic = getTopicById($conn, (int)$_GET['edit']);
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Topic Management - TutorChat Admin</title>
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
            <a href="user_management.php" class="nav-link">
                <i class="bi bi-people"></i> User Management
            </a>
            <a href="topic_management.php" class="nav-link active">
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
            <h2><i class="bi bi-book text-brand"></i> Topic Management</h2>
            <p class="text-muted">Create, edit, and manage AI literacy topics</p>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>
                    <i class="bi bi-<?= $editing_topic ? 'pencil-square' : 'plus-circle' ?>"></i>
                    <?= $editing_topic ? 'Edit Topic' : 'Create New Topic' ?>
                </h3>
            </div>
            <div class="card-body">
                <form method="POST">
                    <?php if ($editing_topic): ?>
                        <input type="hidden" name="topic_id" value="<?= (int)$editing_topic['id'] ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-card-heading"></i> Topic Title *
                        </label>
                        <input type="text" name="title" class="form-control" required maxlength="255" 
                               value="<?= $editing_topic ? cleanHTML($editing_topic['title']) : '' ?>" 
                               placeholder="e.g., Understanding AI Bias">
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-text-paragraph"></i> Description *
                        </label>
                        <textarea name="descr" class="form-control" rows="5" required 
                                  placeholder="Detailed description of the topic..."><?= $editing_topic ? cleanHTML($editing_topic['descr']) : '' ?></textarea>
                    </div>

                    <div class="form-actions">
                        <?php if ($editing_topic): ?>
                            <button type="submit" name="update_topic" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Update Topic
                            </button>
                            <a href="topic_management.php" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        <?php else: ?>
                            <button type="submit" name="create_topic" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Create Topic
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>All Topics (<?= count($topics) ?>)</h3>
            </div>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Total Clicks</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($topics)): ?>
                            <tr><td colspan="5" class="text-center">No topics available. Create one above!</td></tr>
                        <?php else: ?>
                            <?php foreach ($topics as $topic): ?>
                                <tr>
                                    <td><?= (int)$topic['id'] ?></td>
                                    <td><strong><?= cleanHTML($topic['title']) ?></strong></td>
                                    <td class="text-truncate" style="max-width: 400px;"><?= cleanHTML($topic['descr']) ?></td>
                                    <td>
                                        <span class="badge badge-secondary">
                                            <i class="bi bi-mouse"></i> <?= (int)$topic['clicks'] ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="?edit=<?= (int)$topic['id'] ?>" class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="topic_id" value="<?= (int)$topic['id'] ?>">
                                            <button type="submit" name="delete_topic" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Delete this topic? All associated sessions will also be deleted.')">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../../utils/popupmessages/front.js"></script>
</body>
</html>