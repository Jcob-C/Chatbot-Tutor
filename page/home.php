<?php
require_once '../utils/CleanerFunctions.php';
require_once '../utils/PageBlocker.php';
require_once '../utils/TutorSessionSystem.php';
require_once '../utils/database/Users.php';
require_once '../utils/database/TutorSessions.php';
require_once '../utils/database/Topics.php';

session_start();
loginBlock();
redirectAdmin();
checkPost();

$limit = 5;
$sessionsPage = isset($_GET['sessions_page']) ? (int)$_GET['sessions_page'] : 1;
$topicsPage = isset($_GET['topics_page']) ? (int)$_GET['topics_page'] : 1;
$sessionsData = getLatestUserSessions($_SESSION['userID'], $limit, $sessionsPage);
$topicsData = getTopics($limit, $topicsPage);
$nickname = getNickname($_SESSION['userID']);

function checkPost() {
    if (isset($_POST['logout'])) {
        resetSession();
        headTo('login.php');
    }
    if (isset($_POST['startSession'])) {
        startNewSession($_POST['startSession']);
    }
    clearPost();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - TutorChat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/theme.css">
    <link rel="stylesheet" href="../assets/popupMessage.css">
</head>
<body>
    <div class="container py-5">
        <!-- Header with Branding -->
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold text-white">
                <i class="bi bi-chat-dots-fill text-brand"></i> TutorChat
            </h1>
        </div>

        <!-- User Info Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-dark bg-opacity-75 shadow-lg border-0 rounded-3">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <h2 class="card-title fw-bold mb-2 text-white">Welcome back, 
                                    <a href="settings.php" class="link-brand fw-semibold text-decoration-none">
                                        <?php echo htmlspecialchars($nickname); ?>
                                    </a>
                                </h2>
                            </div>
                            <div class="mt-3 mt-md-0">
                                <button type="button" class="btn btn-brand btn-lg fw-semibold" onclick="showFeedbackForm()">
                                    <i class="bi bi-chat-square-text"></i> Feedback
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Topics and Past Sessions Side by Side -->
        <div class="row">
            <!-- Available Topics -->
            <div class="col-12 col-lg-6 mb-4">
                <div class="card bg-dark bg-opacity-75 shadow-lg border-0 rounded-3 h-100">
                    <div class="card-header bg-transparent border-0 p-4">
                        <h3 class="fw-bold mb-0 text-brand">
                            <i class="bi bi-book"></i> Available Topics
                        </h3>
                    </div>
                    <div class="card-body p-4">
                        <?php if (empty($topicsData['topics'])): ?>
                            <div class="text-center text-white-50 py-5">
                                <i class="bi bi-inbox display-1"></i>
                                <p class="mt-3 fs-5">No topics available at the moment.</p>
                            </div>
                        <?php else: ?>
                            <div class="row g-3">
                                <?php foreach ($topicsData['topics'] as $topic): ?>
                                    <div class="col-12">
                                        <div class="card bg-black bg-opacity-50 border border-secondary border-opacity-25 rounded-3">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <h5 class="fw-bold text-white mb-2"><?php echo htmlspecialchars($topic['title']); ?></h5>
                                                        <p class="text-white-50 mb-2 small"><?php echo htmlspecialchars($topic['descr']); ?></p>
                                                        <small class="text-white-50">
                                                            <i class="bi bi-people"></i> <?php echo $topic['clicks']; ?> sessions completed
                                                        </small>
                                                    </div>
                                                    <div class="ms-3">
                                                        <form method="post">
                                                            <button name="startSession" value="<?= $topic['id'] ?>" type="submit" class="btn btn-brand">
                                                                <i class="bi bi-play-circle"></i> Start
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Topics Pagination -->
                            <?php if ($topicsData['has_prev'] || $topicsData['has_next']): ?>
                                <div class="mt-4">
                                    <nav>
                                        <ul class="pagination justify-content-center mb-0">
                                            <?php if ($topicsData['has_prev']): ?>
                                                <li class="page-item">
                                                    <a class="page-link link-brand bg-dark border-secondary" href="?topics_page=<?php echo $topicsPage - 1; ?><?php echo isset($_GET['sessions_page']) ? '&sessions_page='.$_GET['sessions_page'] : ''; ?>">
                                                        <i class="bi bi-chevron-left"></i> Previous
                                                    </a>
                                                </li>
                                            <?php endif; ?>

                                            <?php if ($topicsData['has_next']): ?>
                                                <li class="page-item">
                                                    <a class="page-link link-brand bg-dark border-secondary" href="?topics_page=<?php echo $topicsPage + 1; ?><?php echo isset($_GET['sessions_page']) ? '&sessions_page='.$_GET['sessions_page'] : ''; ?>">
                                                        Next <i class="bi bi-chevron-right"></i>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </nav>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Past Sessions -->
            <div class="col-12 col-lg-6 mb-4">
                <div class="card bg-dark bg-opacity-75 shadow-lg border-0 rounded-3 h-100">
                    <div class="card-header bg-transparent border-0 p-4">
                        <h3 class="fw-bold mb-0 text-brand">
                            <i class="bi bi-clock-history"></i> Past Sessions
                        </h3>
                    </div>
                    <div class="card-body p-4">
                        <?php if (empty($sessionsData['sessions'])): ?>
                            <div class="text-center text-white-50 py-5">
                                <i class="bi bi-inbox display-1"></i>
                                <p class="mt-3 fs-5">No sessions yet. Start learning to see your progress here!</p>
                            </div>
                        <?php else: ?>
                            <div class="row g-3">
                                <?php foreach ($sessionsData['sessions'] as $session): ?>
                                    <?php 
                                        $improvement = $session['post_score'] - $session['pre_score'];
                                        $improvementClass = $improvement > 0 ? 'text-success' : ($improvement < 0 ? 'text-danger' : 'text-white-50');
                                    ?>
                                    <div class="col-12">
                                        <div class="card bg-black bg-opacity-50 border border-secondary border-opacity-25 rounded-3">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between align-items-start flex-wrap">
                                                    <div class="flex-grow-1 mb-3 mb-md-0">
                                                        <h5 class="fw-bold text-white mb-2"><?php echo htmlspecialchars($session['topic_title']); ?></h5>
                                                        <p class="text-white-50 mb-2 small">
                                                            <i class="bi bi-calendar"></i> <?php echo date('M j, Y g:i A', strtotime($session['concluded'])); ?>
                                                        </p>
                                                        <div class="d-flex gap-3 flex-wrap">
                                                            <span class="text-white-50 small">
                                                                Pre: <span class="badge bg-secondary"><?php echo $session['pre_score'] * 5; ?>%</span>
                                                            </span>
                                                            <span class="text-white-50 small">
                                                                Post: <span class="badge bg-primary"><?php echo $session['post_score'] * 5; ?>%</span>
                                                            </span>
                                                            <span class="text-white-50 small">
                                                                Improvement: <span class="fw-bold <?php echo $improvementClass; ?>">
                                                                    <?php echo $improvement > 0 ? '+' : ''; ?><?php echo $improvement * 5; ?>%
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-sm btn-outline-light" onclick="viewSession(<?php echo $session['id']; ?>)">
                                                            <i class="bi bi-eye"></i> View
                                                        </button>
                                                        <form method="post">
                                                            <button type="submit" name="startSession" value="<?= $session['topic_id'] ?>" class="btn btn-sm btn-brand" onclick="redoSession(<?php echo $session['topic_id']; ?>)">
                                                                <i class="bi bi-arrow-repeat"></i> Start New
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Sessions Pagination -->
                            <?php if ($sessionsData['has_prev'] || $sessionsData['has_next']): ?>
                                <div class="mt-4">
                                    <nav>
                                        <ul class="pagination justify-content-center mb-0">
                                            <?php if ($sessionsData['has_prev']): ?>
                                                <li class="page-item">
                                                    <a class="page-link link-brand bg-dark border-secondary" href="?sessions_page=<?php echo $sessionsPage - 1; ?><?php echo isset($_GET['topics_page']) ? '&topics_page='.$_GET['topics_page'] : ''; ?>">
                                                        <i class="bi bi-chevron-left"></i> Previous
                                                    </a>
                                                </li>
                                            <?php endif; ?>

                                            <?php if ($sessionsData['has_next']): ?>
                                                <li class="page-item">
                                                    <a class="page-link link-brand bg-dark border-secondary" href="?sessions_page=<?php echo $sessionsPage + 1; ?><?php echo isset($_GET['topics_page']) ? '&topics_page='.$_GET['topics_page'] : ''; ?>">
                                                        Next <i class="bi bi-chevron-right"></i>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </nav>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-5">
            <small class="text-white-50">© <?= date('Y'); ?> TutorChat — Your AI Learning Companion</small>
        </div>
    </div>

    <!-- Feedback Modal -->
    <div class="modal fade" id="feedbackModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark border border-secondary">
                <div class="modal-header border-secondary pb-0">
                    <h5 class="modal-title fw-bold text-brand">
                        <i class="bi bi-chat-square-text"></i> Send Feedback
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="feedbackForm" onsubmit="return submitFeedback(event)">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="feedbackTitle" class="form-label fw-semibold text-white">Title</label>
                            <input type="text" class="form-control bg-white border-secondary" id="feedbackTitle" required placeholder="Brief summary of your feedback">
                        </div>
                        <div class="mb-3">
                            <label for="feedbackDescription" class="form-label fw-semibold text-white">Description</label>
                            <textarea class="form-control bg-white border-secondary" id="feedbackDescription" rows="5" required placeholder="Tell us more about your feedback..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-secondary pt-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-brand fw-semibold">
                            <i class="bi bi-send"></i> Submit Feedback
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../utils/popupmessages/front.js"></script>
    <script>
        // Initialize feedback modal
        let feedbackModal;
        
        document.addEventListener('DOMContentLoaded', function() {
            feedbackModal = new bootstrap.Modal(document.getElementById('feedbackModal'));
        });

        function showFeedbackForm() {
            feedbackModal.show();
        }

        function submitFeedback(event) {
            event.preventDefault();
            
            const title = document.getElementById('feedbackTitle').value;
            const description = document.getElementById('feedbackDescription').value;
            
            // TODO: Replace with actual AJAX call to submit feedback
            
            feedbackModal.hide();
            displayPopupMessage('Thank you for your feedback! We appreciate your input.');
            document.getElementById('feedbackForm').reset();
            
            return false;
        }

        // Utility function to escape HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>
</html>