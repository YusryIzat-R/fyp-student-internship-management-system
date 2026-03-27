<?php
session_start();

/* Check if user logged in as a lecturer */
if(!isset($_SESSION["role"]) || $_SESSION["role"] != "lecturer") {
    header("Location: ../public/login.php");
    exit;
}

/* Get Session data */
$lecturer_id = $_SESSION["user_id"] ?? "";
$lecturer_name = $_SESSION["name"] ?? "";
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

        <p>Welcome, <b><?php echo $lecturer_name; ?></b></p>
        <p>ID: <b><?php echo $lecturer_id; ?></b></p>

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

<a href="../public/logout.php">
    <button style="padding:8px 15px; font-size:14px; color:red;">
        Logout
    </button>
</a>

</body>
</html>
