<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../database/Users.php';
require_once __DIR__ . '/../utils/PopupMessage/.php';
require_once __DIR__ . '/../utils/PageBlocker.php';

session_start();
$conn = new mysqli(host, user, pass, db);
redirectUnauthorized($conn);
redirectAdmin();
$userID = $_SESSION['loggedinUserID'];
$currentNickname = getNickname($conn, $userID);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'change_password':
                $oldPassword = $_POST['old_password'] ?? '';
                $newPassword = $_POST['new_password'] ?? '';
                $repeatPassword = $_POST['repeat_password'] ?? '';
                
                if (empty($oldPassword) || empty($newPassword) || empty($repeatPassword)) {
                    setPopupMessage('All password fields are required');
                } elseif ($newPassword !== $repeatPassword) {
                    setPopupMessage('New passwords do not match');
                } else {
                    $currentHash = getHashedPasswordByID($conn, $userID);
                    if (password_verify($oldPassword, $currentHash)) {
                        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
                        if (updatePassword($conn, $userID, $newHash)) {
                            setPopupMessage('Password changed successfully');
                        } else {
                            setPopupMessage('Failed to update password');
                        }
                    } else {
                        setPopupMessage('Current password is incorrect');
                    }
                }
                header('Location: settings.php');
                exit();
                break;
                
            case 'change_nickname':
                $newNickname = trim($_POST['nickname'] ?? '');
                
                if (empty($newNickname)) {
                    setPopupMessage('Nickname cannot be empty');
                } else {
                    if (updateNickname($conn, $userID, $newNickname)) {
                        setPopupMessage('Nickname updated successfully');
                    } else {
                        setPopupMessage('Failed to update nickname');
                    }
                }
                header('Location: settings.php');
                exit();
                break;
                
            case 'deactivate_account':
                $password = $_POST['deactivate_password'] ?? '';
                
                if (empty($password)) {
                    setPopupMessage('Password is required to deactivate account');
                    header('Location: settings.php');
                    exit();
                } else {
                    $currentHash = getHashedPasswordByID($conn, $userID);
                    if (password_verify($password, $currentHash)) {
                        if (deactivateUser($conn, $userID)) {
                            session_destroy();
                            header('Location: login.php?deactivated=1');
                            exit();
                        } else {
                            setPopupMessage('Failed to deactivate account');
                            header('Location: settings.php');
                            exit();
                        }
                    } else {
                        setPopupMessage('Incorrect password');
                        header('Location: settings.php');
                        exit();
                    }
                }
                break;
                
            case 'logout':
                $_SESSION = [];
                session_destroy();
                header('Location: login.php');
                exit();
                break;
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - TutorChat</title>
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
                    <a href="history.php" class="text-decoration-none"><i class="bi bi-clock-history me-1"></i>History</a>
                    <a href="settings.php" class="text-decoration-none fw-bold"><i class="bi bi-person-circle me-1"></i>Settings</a>
                </nav>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h3 class="mb-4">Account Settings</h3>

                <?php displayPopupMessage(); ?>

                <!-- Logout Card -->
                <div class="card">
                    <h5 class="mb-3"><i class="bi bi-box-arrow-right me-2"></i>Sign Out</h5>
                    <p class="text-muted mb-3">End your current session and return to the login page.</p>
                    <form method="POST" id="logoutForm">
                        <input type="hidden" name="action" value="logout">
                        <button type="submit" class="btn btn-secondary"><i class="bi bi-box-arrow-right me-1"></i>Log Out</button>
                    </form>
                </div>

                <!-- Change Nickname Card -->
                <div class="card">
                    <h5 class="mb-3"><i class="bi bi-pencil-square me-2"></i>Change Nickname</h5>
                    <form method="POST" id="nicknameForm">
                        <input type="hidden" name="action" value="change_nickname">
                        <div class="mb-3">
                            <label for="nickname" class="form-label">New Nickname</label>
                            <input type="text" class="form-control" id="nickname" name="nickname" value="<?php echo htmlspecialchars($currentNickname); ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i>Update Nickname</button>
                    </form>
                </div>

                <!-- Change Password Card -->
                <div class="card">
                    <h5 class="mb-3"><i class="bi bi-lock-fill me-2"></i>Change Password</h5>
                    <form method="POST" id="passwordForm">
                        <input type="hidden" name="action" value="change_password">
                        <div class="mb-3">
                            <label for="old_password" class="form-label">Current Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="old_password" name="old_password" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('old_password')">
                                    <i class="bi bi-eye" id="old_password_icon"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password')">
                                    <i class="bi bi-eye" id="new_password_icon"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="repeat_password" class="form-label">Repeat New Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="repeat_password" name="repeat_password" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('repeat_password')">
                                    <i class="bi bi-eye" id="repeat_password_icon"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-shield-check me-1"></i>Change Password</button>
                    </form>
                </div>

                <!-- Deactivate Account Card -->
                <div class="card">
                    <h5 class="mb-3 text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Deactivate Account</h5>
                    <p class="text-muted mb-3">Warning: This action will deactivate your account. You will need to contact support to reactivate it.</p>
                    <form method="POST" id="deactivateForm" onsubmit="return confirm('Are you sure you want to deactivate your account? This action cannot be undone without contacting support.');">
                        <input type="hidden" name="action" value="deactivate_account">
                        <div class="mb-3">
                            <label for="deactivate_password" class="form-label">Enter Password to Confirm</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="deactivate_password" name="deactivate_password" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('deactivate_password')">
                                    <i class="bi bi-eye" id="deactivate_password_icon"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-danger"><i class="bi bi-x-circle me-1"></i>Deactivate Account</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(inputId + '_icon');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }
    </script>
</body>
</html>