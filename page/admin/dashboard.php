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

function checkPost() {
    if (isset($_POST['logout'])) {
        resetSession();
        headTo('../login.php');
    }
    clearPost();
}

$conn = getConnection();
$stats = getDashboardStats($conn);
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - TutorChat</title>
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
            <a href="dashboard.php" class="nav-link active">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="user_management.php" class="nav-link">
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
            <h2>Dashboard Overview</h2>
            <p class="text-muted">Welcome back, <?= cleanHTML($_SESSION['nick'] ?? 'Admin') ?>!</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-info">
                    <h3><?= $stats['total_users'] ?></h3>
                    <p>Total Users</p>
                    <small><?= $stats['active_users'] ?> active</small>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">📚</div>
                <div class="stat-info">
                    <h3><?= $stats['total_topics'] ?></h3>
                    <p>AI Literacy Topics</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">💬</div>
                <div class="stat-info">
                    <h3><?= $stats['total_sessions'] ?></h3>
                    <p>Total Sessions</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">📝</div>
                <div class="stat-info">
                    <h3><?= $stats['total_feedback'] ?></h3>
                    <p>Feedback Entries</p>
                </div>
            </div>

            <div class="stat-card highlight">
                <div class="stat-icon">📈</div>
                <div class="stat-info">
                    <h3><?= $stats['avg_improvement'] > 0 ? '+' : '' ?><?= $stats['avg_improvement'] ?></h3>
                    <p>Avg Score Improvement</p>
                    <small>Pre-test to Post-test</small>
                </div>
            </div>
        </div>

        <div class="quick-actions">
            <h3>Quick Actions</h3>
            <div class="action-grid">
                <a href="user_management.php" class="action-card">
                    <h4><i class="bi bi-people"></i> Manage Users</h4>
                    <p>View, enable, disable, or delete user accounts</p>
                </a>

                <a href="topic_management.php" class="action-card">
                    <h4><i class="bi bi-book"></i> Manage Topics</h4>
                    <p>Create, edit, or remove AI literacy topics</p>
                </a>

                <a href="feedback_monitoring.php" class="action-card">
                    <h4><i class="bi bi-chat-left-quote"></i> View Feedback</h4>
                    <p>Monitor and review user feedback submissions</p>
                </a>

                <a href="test_reports.php" class="action-card">
                    <h4><i class="bi bi-bar-chart"></i> Test Reports</h4>
                    <p>Analyze user test scores and learning progress</p>
                </a>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../../assets/js/front.js"></script>
</body>
</html>