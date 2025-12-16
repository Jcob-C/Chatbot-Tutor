<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../database/TutorSessions.php';
require_once __DIR__ . '/../utils/PageBlocker.php';
require_once __DIR__ . '/../utils/TutorSessionSystem.php';

$conn = new mysqli(host,user,pass,db);
session_start();
redirectUnauthorized($conn);
redirectAdmin();
redirectFromQuiz();

$questions = $_SESSION['ongoingTutorSession']['quiz']['quiz'];
$quizTitle = $_SESSION['ongoingTutorSession']['topic'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['skip_quiz'])) {
        resetTutorSession();
        header('Location: learn.php'); 
        exit;
    }
    
    if (isset($_POST['submit_quiz'])) {
        $score = 0;
        $totalQuestions = count($questions);
        
        foreach ($questions as $index => $question) {
            $userAnswer = isset($_POST['question_' . $index]) ? $_POST['question_' . $index] : '';
            if ($userAnswer === $question['answer']) {
                $score++;
            }
        }
        
        $percentage = ($score / $totalQuestions) * 100;
        $sessionId = $_SESSION['ongoingTutorSession']['id'];
        updateSessionQuiz($conn, $sessionId, $percentage);
        resetTutorSession();
        header('Location: learn.php'); 
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TutorChat - <?php echo htmlspecialchars($quizTitle); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/Style.css">
    <link rel="stylesheet" href="../assets/PopupMessage.css">
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger shadow-sm">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?php echo $error; ?>
                    </div>
                <?php else: ?>
                    <!-- Header Card -->
                    <div class="card shadow-sm mb-4 border-0">
                        <div class="card-body text-center py-4">
                            <div class="mb-3">
                                <i class="bi bi-clipboard-check-fill text-primary" style="font-size: 3rem;"></i>
                            </div>
                            <h2 class="card-title text-primary mb-2"><?php echo htmlspecialchars($quizTitle); ?></h2>
                            <p class="text-muted mb-0">
                                <i class="bi bi-info-circle me-1"></i>
                                Answer all <?php echo count($questions); ?> questions and submit to see your results
                            </p>
                            <br>
                            <form method="post">
                                <button type="submit" 
                                        name="skip_quiz" 
                                        class="btn btn-outline-secondary btn-lg w-100">
                                    <i class="bi bi-skip-forward-fill me-2"></i>Skip Quiz
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <form method="POST" action="">
                        <!-- Questions -->
                        <?php foreach ($questions as $index => $question): ?>
                            <div class="card shadow-sm mb-3 border-0">
                                <div class="card-body">
                                    <div class="d-flex align-items-start mb-3">
                                        <span class="badge bg-primary rounded-circle p-2 me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-size: 1rem;">
                                            <?php echo ($index + 1); ?>
                                        </span>
                                        <div class="flex-grow-1">
                                            <h5 class="mb-0"><?php echo htmlspecialchars($question['question']); ?></h5>
                                        </div>
                                    </div>
                                    
                                    <div class="ms-5">
                                        <?php foreach ($question['choices'] as $choiceIndex => $choice): ?>
                                            <input type="radio" class="btn-check" 
                                                name="question_<?php echo $index; ?>" 
                                                id="q<?php echo $index; ?>_choice<?php echo $choiceIndex; ?>" 
                                                value="<?php echo htmlspecialchars($choice); ?>" 
                                                autocomplete="off" required>
                                            <label class="btn btn-outline-primary btn-lg w-100 mb-2" 
                                                for="q<?php echo $index; ?>_choice<?php echo $choiceIndex; ?>">
                                                <?php echo htmlspecialchars($choice); ?>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <!-- Action Buttons -->
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <button type="submit" 
                                        name="submit_quiz" 
                                        class="btn btn-primary btn-lg w-100">
                                    <i class="bi bi-check-circle-fill me-2"></i>Submit Quiz
                                </button>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../utils/PopupMessage.js"></script>
    
    <style>
        .hover-shadow:hover {
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
            border-color: #0d6efd !important;
        }
        .form-check-input:checked ~ .form-check-label {
            font-weight: 500;
        }
    </style>
</body>
</html>