<?php
require_once '../utils/CleanerFunctions.php';
require_once '../utils/PageBlocker.php';
require_once '../utils/database/Users.php';
require_once '../utils/popupmessages/back.php';

session_start();
loginBlock();
redirectAdmin();
checkPost();
displayPopupMessage();

function checkPost() {
    if (isset($_POST['updateNickname'])) {
        updateNickname($_SESSION['userID'], trim($_POST['nickname']));
    }
    if (isset($_POST['updatePassword'])) {
        $oldPassword = trim($_POST['currentPassword']);
        $password = trim($_POST['newPassword']);
        $confirmPassword = trim($_POST['confirmNewPassword']);

        if (!password_verify($oldPassword, getHashedPasswordWithID($_SESSION['userID']))) {
            setNewPopupMessage("Incorrect Password!");
        }
        elseif (strlen($password) < 8) {
            setNewPopupMessage("New password must be 8 characters or longer!");
        }
        elseif ($password !== $confirmPassword) {
            setNewPopupMessage("Confirm password is not identical!");
        }
        elseif (updatePassword($_SESSION['userID'], $password)) {
            setNewPopupMessage("Password Updated successfully!");
        } 
        else {
            setNewPopupMessage("Password Update failed!");
        }
    }
    if (isset($_POST['deactivate'])) {
        $password = trim($_POST['password']);
        if (password_verify($password, getHashedPasswordWithID($_SESSION['userID']))) {
            if (deactivateUser($_SESSION['userID'])) {
                headTo('login.php');
            }
            else {
                setNewPopupMessage("Deactivation Failed!");
            }
        }
        else {
            setNewPopupMessage("Incorrect Password!");
        }
    }
    if (isset($_POST['logout'])) {
        resetSession();
    }
    clearPost();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TutorChat - User Settings</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons & Theme -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link rel="stylesheet" href="../assets/theme.css">
<link rel="stylesheet" href="../assets/popupMessage.css">
<link rel="icon" type="image/svg+xml" href="../assets/icon.svg">
</head>
<body>

<div class="container py-5">
    <!-- Branding -->
    <div class="text-center mb-4">
        <h1 class="display-4 fw-bold text-white">
            <i class="bi bi-chat-dots-fill text-brand"></i> TutorChat
        </h1>
    </div>
    
    <!-- Back Button -->
    <div class="mb-4">
        <a href="home.php" class="btn btn-brand btn-lg fw-semibold">
            <i class="bi bi-arrow-left me-2"></i>Back
        </a>
    </div>

    <!-- Settings Container -->
    <div class="row justify-content-center g-4">
        
        <!-- Left Column: Profile & Password -->
        <div class="col-lg-8">
            <div class="card bg-dark bg-opacity-75 shadow-lg border-0 rounded-3 mb-4">
                <div class="card-body p-4">
                    <h2 class="card-title fw-bold mb-4 text-white">Profile Information</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-white">Nickname</label>
                            <form method="post">
                                <input type="text" name="nickname" class="form-control mb-3" value="<?= getNickname($_SESSION['userID']) ?>" required>
                                <button type="submit" name="updateNickname" class="btn btn-brand btn-lg fw-semibold w-100"> Update Nickname </button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-white">Email</label>
                            <input type="email" class="form-control" value="<?= getEmail($_SESSION['userID']) ?>" disabled>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bg-dark bg-opacity-75 shadow-lg border-0 rounded-3">
                <div class="card-body p-4">
                    <h2 class="card-title fw-bold mb-4 text-white">Change Password</h2>
                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-white">Current Password</label>
                            <input type="password" name="currentPassword" class="form-control" placeholder="Enter current password">
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-white">New Password</label>
                                <input type="password" name="newPassword" class="form-control" placeholder="New password">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-white">Confirm Password</label>
                                <input type="password" name="confirmNewPassword" class="form-control" placeholder="Confirm password">
                            </div>
                        </div>
                        <button type="submit" name="updatePassword" class="btn btn-brand btn-lg fw-semibold w-100">
                            Update Password
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column: Actions -->
        <div class="col-lg-4">
            <div class="card bg-dark bg-opacity-75 shadow-lg border-0 rounded-3 mb-4">
                <div class="card-body p-4 text-center">
                    <i class="bi bi-box-arrow-right text-brand mb-3" style="font-size: 3rem;"></i>
                    <form method="post">
                        <button type="submit" name="logout" class="btn btn-outline-dark btn-lg fw-semibold w-100 text-white">
                            Log Out
                        </button>
                    </form>
                </div>
            </div>

            <div class="card bg-dark bg-opacity-75 shadow-lg border-0 rounded-3 border-danger">
                <div class="card-body p-4 text-center">
                    <i class="bi bi-exclamation-triangle text-danger mb-3" style="font-size: 3rem;"></i>
                    <form method="post">
                        <div class="mb-3 text-start">
                            <label class="form-label fw-semibold small text-white">Confirm with password</label>
                            <input type="password" name="password" required class="form-control" placeholder="Enter password">
                        </div>
                        <button type="submit" name="deactivate" class="btn btn-danger btn-lg fw-semibold w-100">
                            Deactivate Account
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<p class="text-center small text-white-50 mt-4">
    © <?= date('Y') ?> TutorChat. All rights reserved.
</p>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>