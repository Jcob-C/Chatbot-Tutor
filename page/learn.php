<?php

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TutorChat</title>
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
                    <a href="#" class="text-decoration-none"><i class="bi bi-book me-1"></i>Learn</a>
                    <a href="#" class="text-decoration-none"><i class="bi bi-speedometer2 me-1"></i>Analytics</a>
                    <a href="#" class="text-decoration-none"><i class="bi bi-person-circle me-1"></i>Settings</a>
                </nav>
            </div>
        </div>
    </header>   
    <div class="container-fluid px-4">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-warning bg-opacity-10 border-0">
                        <h2 class="h5 mb-0"><i class="bi bi-lightbulb text-warning me-2"></i>Learn Something New</h2>
                    </div>
                    <div class="card-body">
                        <div class="input-group input-group-sm mb-3">
                            <input type="text" class="form-control" placeholder="Search topics...">
                            <button class="btn btn-primary"><i class="bi bi-search"></i></button>
                        </div>
                        <div class="list-group list-group-flush mb-3">
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0 gap-2">
                                <span class="text-truncate">Mathematics Fundamentalsssssssssssssssssssssssssssss</span>
                                <a href="#" class="btn btn-primary btn-sm flex-shrink-0"><i class="bi bi-chat-dots me-1"></i>Chat</a>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0 gap-2">
                                <span class="text-truncate">Programming Basics</span>
                                <a href="#" class="btn btn-primary btn-sm flex-shrink-0"><i class="bi bi-chat-dots me-1"></i>Chat</a>
                            </div>
                            <div class="list-group-item px-0 border-primary">
                                <div class="d-flex gap-2">
                                    <input type="text" class="form-control form-control-sm" placeholder="Can't find what you're looking for?">
                                    <button class="btn btn-outline-primary btn-sm text-nowrap flex-shrink-0"><i class="bi bi-chat-dots me-1"></i>Chat</button>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-chevron-left"></i> Prev</button>
                            <small class="text-muted">Page 1 of 5</small>
                            <button class="btn btn-outline-secondary btn-sm">Next <i class="bi bi-chevron-right"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-success bg-opacity-10 border-0">
                        <h2 class="h5 mb-0"><i class="bi bi-check-circle text-success me-2"></i>Completed Topics</h2>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush mb-3">
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0 bg-light gap-2">
                                <div class="overflow-hidden">
                                    <div class="fw-medium text-truncate">Introduction to Physicssssssssssssssssssssss</div>
                                    <span class="badge bg-success mt-1">Score: 85%</span>
                                </div>
                                <a href="#" class="btn btn-outline-secondary btn-sm flex-shrink-0"><i class="bi bi-arrow-repeat me-1"></i>Revisit</a>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0 bg-light gap-2">
                                <div class="overflow-hidden">
                                    <div class="fw-medium text-truncate">World History</div>
                                    <span class="badge bg-success mt-1">Score: 92%</span>
                                </div>
                                <a href="#" class="btn btn-outline-secondary btn-sm flex-shrink-0"><i class="bi bi-arrow-repeat me-1"></i>Revisit</a>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <button class="btn btn-outline-secondary btn-sm"><i class="bi bi-chevron-left"></i> Prev</button>
                            <small class="text-muted">Page 1 of 3</small>
                            <button class="btn btn-outline-secondary btn-sm">Next <i class="bi bi-chevron-right"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>