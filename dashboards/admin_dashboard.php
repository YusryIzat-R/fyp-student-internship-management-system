<?php
session_start();

if(!isset($_SESSION["role"]) || $_SESSION["role"] != "admin") {
    header("Location: ../public/login.php");
    exit;
}

$admin_id = $_SESSION["user_id"] ?? "";
$admin_name = $_SESSION["name"] ?? "";  
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Admin Dashboard - CCI IMS</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">;
    </head>
    <body>
        <h1>Program Coordinator (Admin) Dashboard</h1>
        <p>Welcome, <b><?php echo $admin_name;?></b></p>
        <p>User ID: <b><?php echo $admin_id;?></b></p>

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

    <a href="../public/logout.php">
        <button style="padding:8px 15px; font-size:14px; color:red;">Logout</button>
    </a>

    
    </body>
</html>