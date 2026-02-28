<?php
declare(strict_types=1);
session_start();

if(($_SESSION["role"] ?? "") !== "Lecturer") {
    header("Location: ../public/login.php?err=Unauthorized access. Please login as a lecturer.");
    exit;
}

$lecturer_id = $_SESSION["uid" ?? ""];

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Lecturer Dashboard - CCI IMS</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>

    <h1>Visiting Lecturer Dashboard</h1>
    <p>Welcome, <b><?=  htmlspecialchars($lecturer_id) ?></b></p>

    <hr>

    <h3>Menu</h3>
    <ul>
        <li><a href="#">Home</a></li>
        <li><a href="#">Announcements</a></li>
        <li><a href="#">Resources</a></li>
        <li><a href="#">My Students</a></li>
        <li><a href="#">Presentation</a></li>
        <li><a href="#">Grading</a></li>
    </ul>

    <hr>

    <a href="../public/logout.php">Logout</a>
    </body>
</html>