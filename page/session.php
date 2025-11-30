<?php
require_once __DIR__ . '/../utils/CleanerFunctions.php';
require_once __DIR__ . '/../utils/PageBlocker.php';
require_once __DIR__ . '/../utils/database/Topics.php';
require_once __DIR__ . '/../utils/database/TutorSessions.php';

session_start();
loginBlock();
redirectAdmin();

// Get session ID from URL
$sessionId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($sessionId === 0) {
    header('Location: dashboard.php');
    exit();
}

// Fetch session data
$sessionData = getSession($sessionId);

if (empty($sessionData)) {
    header('Location: dashboard.php');
    exit();
}

$session = $sessionData[0];
$messages = json_decode($session['messages'], true);

function calculateImprovement($x, $y) {
    return (int) ((($y - $x) / $x) * 100);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Overview - TutorChat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/theme.css">
    <link rel="stylesheet" href="../assets/popupMessage.css">
</head>
<body>
    <div class="container py-5">
        <!-- Branding -->
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold text-white">
                <i class="bi bi-chat-dots-fill text-brand"></i> TutorChat
            </h1>
        </div>

        <!-- Back Button -->
        <div class="mb-4">
            <a href="home.php" class="btn btn-brand btn-lg fw-semibold">
                <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>

        <!-- Page Heading -->
        <div class="mb-5 text-center">
            <h1 class="display-6 fw-bold text-white">Session Overview</h1>
        </div>

        <!-- Main Content Row -->
        <div class="row g-4">
            <!-- Left Column: Details and Scores -->
            <div class="col-lg-4">
                <!-- Session Details Card -->
                <div class="card shadow-lg border-0 rounded-3 mb-4 bg-dark bg-opacity-75">
                    <div class="card-body p-4 text-white">
                        <h2 class="card-title fw-bold mb-4">
                            <i class="bi bi-info-circle text-brand"></i> Session Details
                        </h2>
                        <div class="mb-4">
                            <p class="mb-2 fs-5 fw-semibold">Topic:</p>
                            <p class="text-white-50 fs-4"><?php echo htmlspecialchars(getTopicTitle($session['topic_id'])); ?></p>
                        </div>
                        <div>
                            <p class="mb-2 fs-5 fw-semibold">Concluded:</p>
                            <p class="text-white-50 fs-4"><?php echo date('F j, Y g:i A', strtotime($session['concluded'])); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Scores Card -->
                <div class="card shadow-lg border-0 rounded-3 mb-4 bg-dark bg-opacity-75">
                    <div class="card-body p-4 text-white">
                        <h2 class="card-title fw-bold mb-4">
                            <i class="bi bi-graph-up text-brand"></i> Performance
                        </h2>
                        <div class="mb-3">
                            <div class="p-3 bg-dark bg-opacity-75 rounded-3">
                                <p class="mb-2 fw-semibold text-white">Pre-Assessment</p>
                                <h3 class="display-5 fw-bold text-brand"><?php echo htmlspecialchars($session['pre_score'] * 5); ?>%</h3>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="p-3 bg-dark bg-opacity-75 rounded-3">
                                <p class="mb-2 fw-semibold text-white">Post-Assessment</p>
                                <h3 class="display-5 fw-bold text-brand"><?php echo htmlspecialchars($session['post_score'] * 5); ?>%</h3>
                            </div>
                        </div>
                        <?php
                        $improvement = calculateImprovement($session['pre_score'], $session['post_score']);
                        $improvementClass = $improvement > 0 ? 'text-success' : ($improvement < 0 ? 'text-danger' : 'text-white-50');
                        $improvementIcon = $improvement > 0 ? 'bi-arrow-up' : ($improvement < 0 ? 'bi-arrow-down' : 'bi-dash');
                        ?>
                        <div class="text-center mt-3">
                            <p class="mb-0 <?php echo $improvementClass; ?> fw-semibold fs-5">
                                <i class="bi <?php echo $improvementIcon; ?>"></i>
                                <?php echo $improvement >= 0 ? '+' : ''; ?><?php echo $improvement; ?>% Improvement
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Chat Transcript -->
            <div class="col-lg-8">
                <div class="card shadow-lg border-0 rounded-3 h-100 bg-dark bg-opacity-75">
                    <div class="card-body d-flex flex-column p-4 text-white">
                        <h2 class="card-title fw-bold mb-4">
                            <i class="bi bi-chat-text text-brand"></i> Chat Transcript
                        </h2>
                        <div class="chat-container p-3 bg-dark bg-opacity-75 rounded-3 flex-grow-1" style="max-height: 700px; overflow-y: auto;">
                            <?php foreach ($messages as $msg): ?>
                                <?php
                                $isUser = $msg['role'] === 'user';
                                $bgClass = $isUser ? 'bg-dark bg-opacity-90 text-white' : 'bg-primary bg-opacity-25 text-white';
                                $alignClass = $isUser ? 'ms-auto' : 'me-auto';
                                $icon = $isUser ? 'bi-person-fill' : 'bi-robot';
                                $iconColor = $isUser ? 'text-secondary' : 'text-brand';
                                ?>
                                <div class="mb-3 d-flex <?php echo $isUser ? 'justify-content-end' : 'justify-content-start'; ?>">
                                    <div class="<?php echo $bgClass; ?> p-3 rounded-3 shadow-sm <?php echo $alignClass; ?>" style="max-width: 75%;">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="bi <?php echo $icon; ?> <?php echo $iconColor; ?> me-2"></i>
                                            <strong class="me-2"><?php echo $isUser ? 'Student' : 'TutorChat'; ?></strong>
                                            <small class="text-white-50"><?php echo date('g:i A', strtotime($msg['timestamp'])); ?></small>
                                        </div>
                                        <p class="mb-0"><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="text-center mt-5">
            <small class="text-white-50">© <?= date('Y') ?> TutorChat. All rights reserved.</small>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
