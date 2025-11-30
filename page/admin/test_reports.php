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
    if (isset($_POST['logout'])) {
        resetSession();
        headTo('../login.php');
    }
    clearPost();
}

$conn = getConnection();
$overall_stats = getOverallStats($conn);
$topic_reports = getTopicReports($conn);

$viewing_topic = null;
$topic_sessions = [];
if (isset($_GET['topic_id']) && is_numeric($_GET['topic_id'])) {
    $viewing_topic = (int)$_GET['topic_id'];
    $topic_sessions = getTopicSessionDetails($conn, $viewing_topic);
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Reports - TutorChat Admin</title>
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
            <a href="feedback_monitoring.php" class="nav-link">
                <i class="bi bi-chat-left-quote"></i> Feedback
            </a>
            <a href="test_reports.php" class="nav-link active">
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
            <h2><i class="bi bi-bar-chart text-brand"></i> User Test Score Reports</h2>
            <p class="text-muted">Analyze learning progress and topic performance</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📊</div>
                <div class="stat-info">
                    <h3><?= $overall_stats['total_sessions'] ?></h3>
                    <p>Total Sessions</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">📝</div>
                <div class="stat-info">
                    <h3><?= $overall_stats['avg_pre_score'] ?></h3>
                    <p>Avg Pre-Test Score</p>
                    <small>Out of 5</small>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">✅</div>
                <div class="stat-info">
                    <h3><?= $overall_stats['avg_post_score'] ?></h3>
                    <p>Avg Post-Test Score</p>
                    <small>Out of 5</small>
                </div>
            </div>

            <div class="stat-card highlight">
                <div class="stat-icon">📈</div>
                <div class="stat-info">
                    <h3><?= $overall_stats['avg_improvement'] > 0 ? '+' : '' ?><?= $overall_stats['avg_improvement'] ?></h3>
                    <p>Avg Improvement</p>
                    <small>Pre to Post</small>
                </div>
            </div>
        </div>

        <?php if ($viewing_topic): ?>
            <div class="card">
                <div class="card-header">
                    <h3><i class="bi bi-file-earmark-text"></i> Session Details for Topic #<?= $viewing_topic ?></h3>
                    <a href="test_reports.php" class="btn btn-sm btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to All Topics
                    </a>
                </div>
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Session ID</th>
                                <th>User</th>
                                <th>Pre-Test Score</th>
                                <th>Post-Test Score</th>
                                <th>Improvement</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($topic_sessions)): ?>
                                <tr><td colspan="6" class="text-center">No sessions found for this topic.</td></tr>
                            <?php else: ?>
                                <?php foreach ($topic_sessions as $session): ?>
                                    <?php $improvement = (int)$session['post_score'] - (int)$session['pre_score']; ?>
                                    <tr>
                                        <td><?= (int)$session['id'] ?></td>
                                        <td>
                                            <strong><?= cleanHTML($session['nick']) ?></strong><br>
                                            <small class="text-muted">
                                                <i class="bi bi-envelope"></i> <?= cleanHTML($session['email']) ?>
                                            </small>
                                        </td>
                                        <td><?= (int)$session['pre_score'] ?> / 5</td>
                                        <td><?= (int)$session['post_score'] ?> / 5</td>
                                        <td>
                                            <?php if ($improvement > 0): ?>
                                                <span class="badge badge-success">
                                                    <i class="bi bi-arrow-up"></i> +<?= $improvement ?>
                                                </span>
                                            <?php elseif ($improvement < 0): ?>
                                                <span class="badge badge-danger">
                                                    <i class="bi bi-arrow-down"></i> <?= $improvement ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">
                                                    <i class="bi bi-dash"></i> 0
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="small">
                                            <i class="bi bi-calendar"></i>
                                            <?= cleanHTML(date("M d, Y g:i A", strtotime($session['concluded']))) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-header">
                    <h3><i class="bi bi-graph-up"></i> Performance by Topic</h3>
                </div>
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Topic</th>
                                <th>Total Sessions</th>
                                <th>Avg Pre-Test</th>
                                <th>Avg Post-Test</th>
                                <th>Avg Improvement</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($topic_reports)): ?>
                                <tr><td colspan="6" class="text-center">No session data available yet.</td></tr>
                            <?php else: ?>
                                <?php foreach ($topic_reports as $report): ?>
                                    <tr>
                                        <td><strong><?= cleanHTML($report['title']) ?></strong></td>
                                        <td>
                                            <span class="badge badge-secondary">
                                                <i class="bi bi-people"></i> <?= (int)$report['session_count'] ?>
                                            </span>
                                        </td>
                                        <td><?= number_format($report['avg_pre_score'] ?? 0, 2) ?></td>
                                        <td><?= number_format($report['avg_post_score'] ?? 0, 2) ?></td>
                                        <td>
                                            <?php 
                                            $improvement = $report['avg_improvement'] ?? 0;
                                            $color = $improvement > 0 ? 'success' : ($improvement < 0 ? 'danger' : 'secondary');
                                            $icon = $improvement > 0 ? 'arrow-up' : ($improvement < 0 ? 'arrow-down' : 'dash');
                                            ?>
                                            <span class="badge badge-<?= $color ?>">
                                                <i class="bi bi-<?= $icon ?>"></i>
                                                <?= $improvement > 0 ? '+' : '' ?><?= number_format($improvement, 2) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($report['session_count'] > 0): ?>
                                                <a href="?topic_id=<?= (int)$report['id'] ?>" class="btn btn-sm btn-info">
                                                    <i class="bi bi-eye"></i> View Sessions
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">No data</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../../utils/popupmessages/front.js"></script>
</body>
</html>