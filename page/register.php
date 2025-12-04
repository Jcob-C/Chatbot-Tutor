<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TutorChat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/Style.css">
    <link rel="stylesheet" href="../assets/PopupMessage.css">
    <style>
        .sidebar-scroll {
            max-height: 100vh;  
            overflow-y: auto;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row min-vh-100 justify-content-center">
            <div class="col-12 col-md-8 col-lg-9 d-flex justify-content-center align-items-center bg-light order-1 order-md-1 mb-4 mb-md-0">
                <div class="text-center px-4">
                    <br>
                    <h1>Welcome to <i class="bi bi-chat-dots-fill icon-primary"></i> TutorChat</h1>
                    <p class="lead">Your personal tutoring assistant, available 24/7.</p>
                </div>
            </div>
            <div class="col-12 col-md-4 col-lg-3 bg-white shadow d-flex flex-column justify-content-start p-4 order-2 order-md-2 sidebar-scroll">
                <h2 class="mb-4 d-flex align-items-center gap-2 justify-content-center">Register</h2>
                <form method="post" id="registerForm">
                    <label for="email" class="form-label">Email</label>
                    <div class="mb-3">
                        <input name="email" type="email" id="email" class="form-control" placeholder="Enter your email" required>
                    </div>
                    <div class="mb-3">
                        <label for="verificationCode" class="form-label">Verification Code</label>
                        <div class="input-group">
                            <input name="verificationCode" type="text" id="verificationCode" class="form-control" placeholder="Enter code" required>
                            <button type="button" id="sendCode" class="btn btn-outline-primary">Send Code</button>
                        </div>
                    </div>
                    <label for="nickname" class="form-label">Nickname</label>
                    <div class="mb-3">
                        <input name="nickname" type="text" id="nickname" class="form-control" placeholder="Enter your nickname" required>
                    </div>
                    <label for="password" class="form-label">Password</label>
                    <div class="mb-3 position-relative d-flex align-items-center">
                        <input name="password" type="password" id="password" class="form-control me-2" placeholder="Enter your password" required>
                        <button type="button" id="togglePassword" class="btn btn-outline-secondary">
                            <i class="bi bi-eye-fill"></i>
                        </button>
                    </div>
                    <label for="confirmPassword" class="form-label">Confirm Password</label>
                    <div class="mb-3 position-relative d-flex align-items-center">
                        <input name="confirmPassword" type="password" id="confirmPassword" class="form-control me-2" placeholder="Confirm your password" required>
                        <button type="button" id="toggleConfirmPassword" class="btn btn-outline-secondary">
                            <i class="bi bi-eye-fill"></i>
                        </button>
                    </div>
                    <br>
                    <button name="register" type="submit" class="btn btn-primary w-100">Register</button>
                </form>
                <p class="mt-3 text-center">
                    Already have an account? 
                    <a href="login.php" class="fw-bold">Log In</a>
                </p>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../utils/PopupMessage/.js"></script>
    <script>
        document.getElementById('sendCode').addEventListener('click', requestVerificationCode);

        async function requestVerificationCode() {
            const email = registerForm.email.value.trim();

            if (!email) {
                displayPopupMessage("Please enter your email first.");
                return;
            }

            try {
                displayPopupMessage("Sending Code...");

                const response = await fetch("../api/RequestVerificationCode.php", {
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

        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', () => {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            togglePassword.innerHTML = type === 'password' ? '<i class="bi bi-eye-fill"></i>' : '<i class="bi bi-eye-slash-fill"></i>';
        });

        const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
        const confirmPassword = document.querySelector('#confirmPassword');

        toggleConfirmPassword.addEventListener('click', () => {
            const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPassword.setAttribute('type', type);
            toggleConfirmPassword.innerHTML = type === 'password' ? '<i class="bi bi-eye-fill"></i>' : '<i class="bi bi-eye-slash-fill"></i>';
        });

        const registerForm = document.getElementById('registerForm');

        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitRegisterForm();
        });

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
                const response = await fetch("../api/SubmitUserRegisterForm.php", {
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