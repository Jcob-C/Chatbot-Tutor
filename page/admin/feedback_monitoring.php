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
    
    if (isset($_POST['delete_feedback']) && isset($_POST['feedback_id'])) {
        $result = deleteFeedback($conn, (int)$_POST['feedback_id']);
        if ($result) {
            setNewPopupMessage('Feedback deleted successfully!');
        } else {
            setNewPopupMessage('Failed to delete feedback.');
        }
        headTo($_SERVER['PHP_SELF'] . '?' . http_build_query($_GET));
    }
    
    clearPost();
}

$conn = getConnection();
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$data = getAllFeedback($conn, $page, 20);
$feedback_list = $data['feedback'];
$total_pages = $data['total_pages'];
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Monitoring - TutorChat Admin</title>
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
            <a href="topic_management.php" class="nav-link">
                <i class="bi bi-book"></i> Topic Management
            </a>
            <a href="feedback_monitoring.php" class="nav-link active">
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
            <h2><i class="bi bi-chat-left-quote text-brand"></i> Feedback Monitoring</h2>
            <p class="text-muted">Review user feedback and suggestions</p>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>All Feedback (<?= $data['total'] ?>)</h3>
            </div>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>User</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($feedback_list)): ?>
                            <tr><td colspan="6" class="text-center">No feedback submitted yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($feedback_list as $feedback): ?>
                                <tr>
                                    <td><?= (int)$feedback['id'] ?></td>
                                    <td class="small">
                                        <i class="bi bi-calendar"></i>
                                        <?= cleanHTML(date("M d, Y", strtotime($feedback['created']))) ?>
                                    </td>
                                    <td>
                                        <strong><?= cleanHTML($feedback['nick']) ?></strong><br>
                                        <small class="text-muted">
                                            <i class="bi bi-envelope"></i> <?= cleanHTML($feedback['email']) ?>
                                        </small>
                                    </td>
                                    <td><strong><?= cleanHTML($feedback['title']) ?></strong></td>
                                    <td class="text-truncate" style="max-width: 300px;"><?= cleanHTML($feedback['descr']) ?></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-info" onclick="showFullFeedback(<?= (int)$feedback['id'] ?>)">
                                            <i class="bi bi-eye"></i> View
                                        </button>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="feedback_id" value="<?= (int)$feedback['id'] ?>">
                                            <button type="submit" name="delete_feedback" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Delete this feedback?')">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <tr id="feedback-detail-<?= (int)$feedback['id'] ?>" class="feedback-detail" style="display:none;">
                                    <td colspan="6">
                                        <div class="feedback-full">
                                            <h4><i class="bi bi-chat-left-quote"></i> <?= cleanHTML($feedback['title']) ?></h4>
                                            <p>
                                                <strong><i class="bi bi-person"></i> From:</strong> 
                                                <?= cleanHTML($feedback['nick']) ?> (<?= cleanHTML($feedback['email']) ?>)
                                            </p>
                                            <p>
                                                <strong><i class="bi bi-calendar"></i> Date:</strong> 
                                                <?= cleanHTML(date("F d, Y g:i A", strtotime($feedback['created']))) ?>
                                            </p>
                                            <p><strong><i class="bi bi-chat-text"></i> Message:</strong></p>
                                            <div class="feedback-message"><?= nl2br(cleanHTML($feedback['descr'])) ?></div>
                                            <button type="button" class="btn btn-sm btn-secondary" onclick="hideFullFeedback(<?= (int)$feedback['id'] ?>)">
                                                <i class="bi bi-x-circle"></i> Close
                                            </button>
                                        </div>
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
                            <a class="page-link" href="?page=<?= $page - 1 ?>">
                                <i class="bi bi-chevron-left"></i> Previous
                            </a>
                        </li>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page + 1 ?>">
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
<script>
function showFullFeedback(id) {
    document.getElementById('feedback-detail-' + id).style.display = 'table-row';
}

function hideFullFeedback(id) {
    document.getElementById('feedback-detail-' + id).style.display = 'none';
}
</script>
</body>
</html>