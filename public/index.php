<?php 

session_start();

if (isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'student':
            header("Location: ../dashboards/student_dashboard.php");
            exit;
        
        case 'lecturer':
            header("Location: ../dashboards/lecturer_dashboard.php");
            exit;
        
        case 'admin':
            header("Location: ../dashboards/admin_dashboard.php");
            exit;
    }
}

?>

<!DOCTYPE html> 
<html lang=en>
<head>
    <meta charset="UTF-8">
    <title>CCI Internship Management System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <div class="main-container">
        <header class="topbar">
            <div class="brand">
                CCI IMS
                <span class="brand-sub">Internship System</span>
            </div>
        </header>
        
        <section class="content-section">
            <div class="left-panel">
                <p class="eyebrow">College of Computing and Informatics</p>
                <h1>Internship Management System</h1>
                <p class="hero-text">
                    Manage student internship registration, lecturer supervision, and admin monitoring in one simple system.
                </p>

                <div class="hero-actions">
                <a href="login.php" class="primary-btn">Login</a>
                <a href="register.php" class="secondary-btn">Create Account</a>
                </div>
            </div>

            <div class="right-panel">
                <h2>Welcome to CCI IMS</h2>
                <p>
                    A simple platform designed to support internship management for students, lecturers, and administrators.
                </p>
            </div>
        </section>
    </div>
</body>
</html>
