<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons (Required) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Theme + Popup -->
    <link rel="stylesheet" href="../assets/theme.css">
    <link rel="stylesheet" href="../assets/popupMessage.css">
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

            <!-- Registration Card -->
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-body p-4 p-md-5">

                    <h2 class="card-title fw-bold text-center mb-4">Register</h2>

                    <!-- Login link -->
                    <div class="text-center mb-3">
                        <a href="login.php" class="link-brand fw-semibold">Already have an account? Log in</a>
                    </div>

                    <!-- Form -->
                    <form method="post" id="registerForm" class="mt-3">

                        <!-- Email -->
                        <div class="mb-4">
                            <label class="form-label">Account Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="bi bi-envelope-fill text-brand"></i>
                                </span>
                                <input type="text" class="form-control" placeholder="Enter your email" name="email">
                            </div>
                        </div>

                        <!-- Verification Code (input + send code button combined) -->
                        <div class="mb-4">
                            <label class="form-label">Verification Code</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="bi bi-shield-lock-fill text-brand"></i>
                                </span>

                                <input type="text" class="form-control" placeholder="Enter verification code" name="code">

                                <button type="button" class="btn btn-brand d-flex align-items-center gap-1 px-3"
                                    onclick="requestVerificationCode()">
                                    <i class="bi bi-send-fill"></i>
                                    Get Code
                                </button>
                            </div>
                        </div>

                        <!-- Nickname -->
                        <div class="mb-4">
                            <label class="form-label">Your Nickname</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="bi bi-person-fill text-brand"></i>
                                </span>
                                <input type="text" class="form-control" placeholder="Choose a nickname" name="nickname">
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <label class="form-label">Account Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="bi bi-lock-fill text-brand"></i>
                                </span>
                                <input type="password" class="form-control" placeholder="Enter password" name="password">
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label class="form-label">Confirm Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="bi bi-lock-fill text-brand"></i>
                                </span>
                                <input type="password" class="form-control" placeholder="Re-enter password" name="confirmPassword">
                            </div>
                        </div>

                        <!-- Register Button -->
                        <div class="d-grid">
                            <button type="button" class="btn btn-brand btn-lg fw-semibold"
                                onclick="submitRegisterForm()">
                                Register
                            </button>
                        </div>

                    </form>

                </div>
            </div>

            <p class="text-center small text-white-50 mt-4">
                © <?= date('Y'); ?> TutorChat — Your AI Learning Companion
            </p>

        </div>
    </div>
</div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="../utils/popupmessages/front.js"></script>
    <script>
        const registerForm = document.getElementById('registerForm');

        async function requestVerificationCode() {
            const email = registerForm.email.value.trim();

            if (!email) {
                displayPopupMessage("Please enter your email first.");
                return;
            }

            try {
                const response = await fetch("../api/EmailVerification.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "email=" + encodeURIComponent(email)
                });

                const data = await response.text();
                displayPopupMessage(data);
            } 
            catch (err) {
                displayPopupMessage("Error requesting verification code.");
                console.error(err);
            }
        }

        async function submitRegisterForm() {
            const formData = new FormData(registerForm);

            if (formData.get("password").trim().length < 8) {
                displayPopupMessage("Password must contain at least 8 characters.");
                return;
            }

            if (formData.get("password").trim() !== formData.get("confirmPassword").trim()) {
                displayPopupMessage("Passwords do not match.");
                return;
            }

            try {
                const response = await fetch("../api/Register.php", {
                    method: "POST",
                    body: formData
                });

                const data = await response.text();
                displayPopupMessage(data);

                if (data.toLowerCase().includes("success")) {
                    registerForm.reset();
                }
            } 
            catch (err) {
                displayPopupMessage("Registration failed.");
                console.error(err);
            }
        }
    </script>
</body>
</html>