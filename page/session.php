<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../database/TutorSessions.php';

session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedinUserID'])) {
    header("Location: login.php");
    exit();
}

// Function to initialize database connection
function initDB() {
    $conn = new mysqli(host, user, pass, db);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

// Get session ID from URL
$session_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($session_id <= 0) {
    header("Location: history.php");
    exit();
}

// Initialize database and get session
$conn = initDB();
$session_data = getSession($conn, $session_id);

// Check if session exists and belongs to user
if (empty($session_data) || $session_data[0]['user_id'] != $_SESSION['loggedinUserID']) {
    header("Location: history.php");
    exit();
}

$session = $session_data[0];
$transcript_data = json_decode($session['transcript'], true);
$transcript = isset($transcript_data['transcript']) ? $transcript_data['transcript'] : [];

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($session['topic_title']); ?> - TutorChat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/Style.css">
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
                <!-- Session Header -->
                <div class="card mb-4">
                    <h2 class="h4 mb-3">
                        <i class="bi bi-journal-text icon-primary me-2"></i>
                        <?php echo htmlspecialchars($session['topic_title']); ?>
                    </h2>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong><i class="bi bi-calendar3 me-1"></i> Date:</strong>
                                <?php 
                                    $date = new DateTime($session['concluded']);
                                    echo $date->format('F j, Y \a\t g:i A'); 
                                ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong><i class="bi bi-trophy me-1"></i> Quiz Score:</strong>
                                <?php if ($session['quiz_score'] !== null): ?>
                                    <span class="badge bg-success"><?php echo $session['quiz_score']; ?>%</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">No Quiz Taken</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Topic Plan -->
                <div class="container">
                    <div class="row">
                        <div class="col card mb-4" style="height: 600px;">
                            <h3 class="h5 mb-3">
                                <i class="bi bi-lightbulb icon-primary me-2"></i>
                                Learning Plan
                            </h3>
                            <div class="overflow-auto" style="height: 100%;" data-bs-spy="scroll" data-bs-offset="0" tabindex="0">
                                <?php echo $session['topic_plan']; ?>
                            </div>
                        </div>

                        <!-- Conversation Transcript -->
                        <div class="col card" style="height: 600px;">
                            <h3 class="h5 mb-3">
                                <i class="bi bi-chat-left-text icon-primary me-2"></i>
                                Conversation Transcript
                            </h3>
                            
                            <?php if (!empty($transcript) && is_array($transcript)): ?>
                                <div class="overflow-auto" style="height: 100%;" data-bs-spy="scroll" data-bs-offset="0" tabindex="0">
                                    <?php foreach ($transcript as $message): ?>
                                        <?php if (isset($message['isUser']) && isset($message['message'])): ?>
                                            <?php if ($message['isUser']): ?>
                                                <!-- User message - right aligned -->
                                                <div class="mb-3 d-flex justify-content-end">
                                                    <div class="p-3 bg-primary text-white rounded" data-mw="75%">
                                                        <div class="d-flex align-items-start gap-2">
                                                            <div class="flex-grow-1">
                                                                <strong class="d-block mb-1">You</strong>
                                                                <?php if (isset($message['timestamp'])): ?>
                                                                    <small class="d-block mb-1 opacity-75">
                                                                        <?php 
                                                                            $timestamp = new DateTime($message['timestamp']);
                                                                            echo $timestamp->format('g:i A'); 
                                                                        ?>
                                                                    </small>
                                                                <?php endif; ?>
                                                                <div><?php echo nl2br(htmlspecialchars($message['message'])); ?></div>
                                                            </div>
                                                            <i class="bi bi-person-circle icon-lg"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <!-- Tutor message - left aligned -->
                                                <div class="mb-3 d-flex justify-content-start">
                                                    <div class="p-3 bg-light rounded" data-mw="75%">
                                                        <div class="d-flex align-items-start gap-2">
                                                            <i class="bi bi-robot icon-primary icon-lg"></i>
                                                            <div class="flex-grow-1">
                                                                <strong class="d-block mb-1">Tutor</strong>
                                                                <?php if (isset($message['timestamp'])): ?>
                                                                    <small class="text-muted d-block mb-1">
                                                                        <?php 
                                                                            $timestamp = new DateTime($message['timestamp']);
                                                                            echo $timestamp->format('g:i A'); 
                                                                        ?>
                                                                    </small>
                                                                <?php endif; ?>
                                                                <div><?php echo $message['message']; ?></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">No conversation transcript available.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>