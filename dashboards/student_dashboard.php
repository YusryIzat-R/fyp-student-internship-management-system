<?php
declare(strict_types=1);
session_start();

if (($_SESSION['role'] ?? '') !== 'student') {
    header("Location: ../public/login.php?err=Unauthorized access. Please login as a student.");
    exit;
}

$student_id = $_SESSION["uid" ?? ""];

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Student Dashboard - CCI IMS</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <h1>Student Dashboard</h1>
        <p>Welcome, <b><?= htmlspecialchars($student_id) ?></b></p>

        <hr>
        <h3>Menu</h3>
        <ul>
            <li><a href="#">Home</a></li>
            <li><a href="#">Announcements</a></li>
            <li><a href="#">Resources</a></li>
            <li><a href="#">My Lecturer</a></li>
            <li><a href="#">Presentation</a></li>
            <li><a href="#">My Result</a></li>
            <li><a href="#">Get Help</a></li>
        </ul>

        <hr>

        <a href="../public/logout.php">Logout</a>
    </body>
</html>