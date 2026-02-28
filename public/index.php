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
</head>
<body>
    <h1>CCI Internship Management System</h1>
    <p>Welcome to the CCI Internship Management System!</p>

    <hr>

    <div>
        <a href="login.php">
            <button type="button">Login</button>
        </a>

        <a href="register.php">
            <button type="button">Register</button>
        </a>
    </div>
</body>
</html>