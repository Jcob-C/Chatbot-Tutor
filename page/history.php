<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../database/TutorSessions.php';

session_start();

// Function to initialize database connection
function initDB() {
    $conn = new mysqli(host, user, pass, db);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

// Function to get page data
function getPageData($conn, $userID) {
    $limit = 10;
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    
    try {
        $data = getLatestUserSessions($conn, $userID, $limit, $page);
        return [
            'sessions' => $data['sessions'],
            'has_prev' => $data['has_prev'],
            'has_next' => $data['has_next'],
            'current_page' => $data['page'],
            'error_message' => null
        ];
    } catch (Exception $e) {
        return [
            'sessions' => [],
            'has_prev' => false,
            'has_next' => false,
            'current_page' => 1,
            'error_message' => "Failed to load sessions. Please try again." . $e->getMessage()
        ];
    }
}

// Initialize
$conn = initDB();
$pageData = getPageData($conn, $_SESSION['loggedinUserID']);

// Extract variables for template
$sessions = $pageData['sessions'];
$has_prev = $pageData['has_prev'];
$has_next = $pageData['has_next'];
$current_page = $pageData['current_page'];
$error_message = $pageData['error_message'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session History - TutorChat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/Style.css">
    <link rel="stylesheet" href="../assets/PopupMessage.css">
</head>
<body>
    <header class="bg-white py-3 mb-4 shadow-sm">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center gap-3">
                <h1 class="h3 mb-0 text-nowrap"><i class="bi bi-chat-dots-fill icon-primary"></i> TutorChat</h1>
                <nav class="d-flex flex-wrap gap-3 align-items-center">
                    <a href="learn.php" class="text-decoration-none"><i class="bi bi-book me-1"></i>Learn</a>
                    <a href="history.php" class="text-decoration-none fw-bold"><i class="bi bi-clock-history me-1"></i>History</a>
                    <a href="feedback.php" class="text-decoration-none"><i class="bi bi-chat-square-text me-1"></i>Feedback</a>
                    <a href="settings.php" class="text-decoration-none"><i class="bi bi-person-circle me-1"></i>Settings</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="container mb-5">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h4 mb-0">Latest Sessions</h2>
                </div>

                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <?php if (empty($sessions)): ?>
                    <div class="card text-center py-5">
                        <i class="bi bi-clock-history icon-xl icon-primary mb-3"></i>
                        <h3 class="h5 mb-2">No Sessions Yet</h3>
                        <p class="text-muted mb-3">You haven't completed any tutoring sessions.</p>
                        <a href="learn.php" class="btn btn-primary">Start Learning</a>
                    </div>
                <?php else: ?>
                    <div class="row g-3">
                        <?php foreach ($sessions as $session): ?>
                            <div class="col-12">
                                <a href="session.php?id=<?php echo $session['id']; ?>" class="text-decoration-none">
                                    <div class="card hover-shadow">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h3 class="h5 mb-2">
                                                    <i class="bi bi-journal-text icon-primary me-2"></i>
                                                    <?php echo htmlspecialchars($session['topic_title']); ?>
                                                </h3>
                                                <p class="text-muted mb-2 small">
                                                    <i class="bi bi-calendar3 me-1"></i>
                                                    <?php 
                                                        $date = new DateTime($session['concluded']);
                                                        echo $date->format('F j, Y \a\t g:i A'); 
                                                    ?>
                                                </p>
                                                <?php if ($session['quiz_score'] !== null): ?>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="badge bg-success">
                                                            <i class="bi bi-check-circle me-1"></i>Quiz Score: <?php echo $session['quiz_score']; ?>%
                                                        </span>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">
                                                        <i class="bi bi-hourglass-split me-1"></i>No Quiz Taken
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-end">
                                                <i class="bi bi-chevron-right icon-primary"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <?php if ($has_prev): ?>
                            <a href="?page=<?php echo $current_page - 1; ?>" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-chevron-left"></i> Prev
                            </a>
                        <?php else: ?>
                            <button class="btn btn-outline-secondary btn-sm" disabled>
                                <i class="bi bi-chevron-left"></i> Prev
                            </button>
                        <?php endif; ?>

                        <small class="text-muted">Page <span><?php echo $current_page; ?></span></small>

                        <?php if ($has_next): ?>
                            <a href="?page=<?php echo $current_page + 1; ?>" class="btn btn-outline-secondary btn-sm">
                                Next <i class="bi bi-chevron-right"></i>
                            </a>
                        <?php else: ?>
                            <button class="btn btn-outline-secondary btn-sm" disabled>
                                Next <i class="bi bi-chevron-right"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../utils/PopupMessage/.js"></script>
    <?php if (isset($error_message)): ?>
    <script>
        displayPopupMessage('<?php echo addslashes($error_message); ?>');
    </script>
    <?php endif; ?>
</body>
</html>