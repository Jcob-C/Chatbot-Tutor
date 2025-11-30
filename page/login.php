<?php
require_once '../utils/CleanerFunctions.php';
require_once '../utils/popupmessages/back.php';
require_once '../utils/database/Users.php';

session_start();
checkPost();
displayPopupMessage();

function checkPost() {
    if (isset($_POST['login'])) {
        login();
    }
    clearPost();
}

function login() {
    $cleanEmail = trim($_POST['email']);
    $cleanPassword = trim($_POST['password']);

    if (password_verify($cleanPassword, getHashedPassword($cleanEmail))) {
        $_SESSION = [];
        $_SESSION['userID'] = getUserID($cleanEmail);

        if (!checkActivated($_SESSION['userID'])) {
            setNewPopupMessage("Deactivated Account!");
            return;
        }

        if (getUserRole($_SESSION['userID']) === 'admin') {
            headTo("admin/dashboard.php");
        }
        else {
            headTo("home.php");
        }
    }
    else {
        setNewPopupMessage("Invalid Login!");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Theme + Popup -->
    <link rel="stylesheet" href="../assets/theme.css">
    <link rel="stylesheet" href="../assets/popupMessage.css">

    <link rel="icon" type="image/svg+xml" href="../assets/icon.svg">
</head>

<body class="d-flex align-items-center justify-content-center min-vh-100">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">

            <!-- Branding -->
            <div class="text-center mb-4">
                <h1 class="display-4 fw-bold text-white">
                    <i class="bi bi-chat-dots-fill text-brand"></i> TutorChat
                </h1>
            </div>

            <!-- Login Card -->
            <div class="card shadow-lg border-0 rounded-3 bg-dark bg-opacity-75">
                <div class="card-body p-4 p-md-5">

                    <h2 class="card-title fw-bold text-center mb-4 text-white">Log In</h2>

                    <!-- Form -->
                    <form method="post">

                        <!-- Email -->
                        <div class="mb-4">
                            <label class="form-label text-white">Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="bi bi-envelope-fill text-brand"></i>
                                </span>
                                <input type="text" class="form-control" placeholder="Enter your email" name="email" required>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <label class="form-label text-white">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="bi bi-lock-fill text-brand"></i>
                                </span>
                                <input type="password" class="form-control" placeholder="Enter your password" name="password" required>
                            </div>
                        </div>

                        <!-- Login Button -->
                        <div class="d-grid">
                            <button type="submit" name="login" class="btn btn-brand btn-lg fw-semibold">
                                Log In
                            </button>
                        </div>

                    </form>

                    <!-- Register link -->
                    <div class="text-center mt-4">
                        <a href="register.php" class="link-brand fw-semibold">Don't have an account? Register</a>
                    </div>

                </div>
            </div>

            <p class="text-center small text-white-50 mt-4">
                © <?= date('Y') ?> TutorChat. All rights reserved.
            </p>

        </div>
    </div>
</div>


<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
