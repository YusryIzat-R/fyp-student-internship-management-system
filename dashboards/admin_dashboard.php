<?php
declare(strict_types=1);
session_start();

if(($_SESSION["role"] ?? "") !== "admin") {
    header("Location: ../public/login.php?err=Unauthorized access. Please login as an admin.");
    exit;
}

$admin_id = $_SESSION["uid"] ?? "";
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Admin Dashboard - CCI IMS</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <h1> Program Coordinator (Admin) Dashboard</h1>
        <p>Welcome, <b><?= htmlspecialchars($admin_id) ?></b></p>
        
        <hr>

        <h2>Menu</h2>
        <ul>
            <li><a href="#">Home</a></li>
            <li><a href="#">Announcements</a></li>
            <li><a href="#">Assignments</a></li>
            <li><a href="#">Results</a></li>
            <li><a href="#">Get Help</a></li>
        </ul>

        <hr>

        <a href="../public/logout.php">Logout</a>
    </body>
</html>