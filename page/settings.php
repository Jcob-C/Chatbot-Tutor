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
</head>
<body>

<div class="container py-5">
    <!-- Branding -->
    <div class="text-center mb-4">
        <h1 class="display-4 fw-bold text-white">
            <i class="bi bi-chat-dots-fill text-brand"></i> TutorChat
        </h1>
        <p class="text-white-50">Manage your account settings</p>
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
            <div class="card shadow-lg border-0 rounded-3 mb-4">
                <div class="card-body p-4">
                    <h2 class="card-title fw-bold mb-4">Profile Information</h2>
                    <form>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nickname</label>
                                <input type="text" class="form-control" value="JohnDoe">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" class="form-control" value="johndoe@example.com" disabled>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-body p-4">
                    <h2 class="card-title fw-bold mb-4">Change Password</h2>
                    <form>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Current Password</label>
                            <input type="password" class="form-control" placeholder="Enter current password">
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">New Password</label>
                                <input type="password" class="form-control" placeholder="New password">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Confirm Password</label>
                                <input type="password" class="form-control" placeholder="Confirm password">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-brand btn-lg fw-semibold w-100">
                            Update Password
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column: Actions -->
        <div class="col-lg-4">
            <div class="card shadow-lg border-0 rounded-3 mb-4">
                <div class="card-body p-4 text-center">
                    <i class="bi bi-box-arrow-right text-brand mb-3" style="font-size: 3rem;"></i>
                    <h3 class="h5 fw-bold mb-3">Session</h3>
                    <button class="btn btn-outline-dark btn-lg fw-semibold w-100">
                        Log Out
                    </button>
                </div>
            </div>

            <div class="card shadow-lg border-0 rounded-3 border-danger">
                <div class="card-body p-4 text-center">
                    <i class="bi bi-exclamation-triangle text-danger mb-3" style="font-size: 3rem;"></i>
                    <h3 class="h5 fw-bold mb-3 text-danger">Danger Zone</h3>
                    <form>
                        <div class="mb-3 text-start">
                            <label class="form-label fw-semibold small">Confirm with password</label>
                            <input type="password" class="form-control" placeholder="Enter password">
                        </div>
                        <button type="submit" class="btn btn-danger btn-lg fw-semibold w-100" onclick="return confirm('Are you sure you want to deactivate your account? This action cannot be undone.');">
                            Deactivate Account
                        </button>
                    </form>
                    <small class="text-muted d-block mt-2">This action is permanent</small>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>