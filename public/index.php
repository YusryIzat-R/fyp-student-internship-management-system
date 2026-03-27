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
    <link rel="stylesheet" href="../assests/css/style.css">
</head>
<body>

    <div class="main-container">
        <header class="topbar">
            <div class="brand">CCI IMS</div>

            <div class="nav-actions">
                <a href="login.php" class="login-btn">
                    <span>LOGIN</span>
                </a>

                <a href="register.php" class="register-btn">REGISTER</a>
            </div>
        </header>
        
        <section class="content-section">
            <div class="left-panel">
                <h1>
                    CCI<br>
                    INTERNSHIP<br>
                    MANAGEMENT<br>
                    SYSTEM
                </h1>
            </div>

            <div class="right-panel">
                <p>
                    WELCOME<br>
                    TO<br>
                    NEW<br>
                    CCI<br>
                    INTERNSHIP<br>
                    MANAGEMENT<br>
                    SYSTEM!
                </p>
            </div>
        </section>
    </div>
    
</body>
</html>
