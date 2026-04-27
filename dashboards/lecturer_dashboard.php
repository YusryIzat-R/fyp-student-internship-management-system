<?php
session_start();

if(!isset($_SESSION['role']) || $_SESSION['role'] != "lecturer") {
    header("Location: ../public/login.php");
    exit;
}

$full_name = "";
if(isset($_SESSION['full_name'])) {
    $full_name = $_SESSION['full_name'];
} else {
    $full_name = $_SESSION['login_id'];
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Lecturer Dashboard - CCI IMS</title>
        <link rel="stylesheet" href="../assets/css/dashboards.css">
    </head>

    <body>
        <div class="wrapper">
            <aside class="sidebar">
                <h3>Lecturer Menu</h3>

                <nav class="menu">
                    <a href="../dashboards/lecturer_dashboard.php" class="menu-item is-active">Dashboard</a>
                    <a href="../dashboards/lecturer_announcement.php" class="menu-item">Announcements</a>
                    <a href="#" class="menu-item">Resources</a>
                    <a href="../dashboards/lecturer_assigned_students.php" class="menu-item">My Students</a>
                    <a href="#" class="menu-item">Presentation Bookings</a>
                    <a href="#" class="menu-item">Grading</a>
                    <a href="../verify/logout.php" class="menu-item">Logout</a>
                </nav> 
            </aside>

            <main class="content">
                <h1>Welcome, <?php echo $full_name; ?></h1>
                <p>You are logged in as <b><?php echo $_SESSION['role']; ?></b>.</p>
                <p>Login ID: <?php echo $_SESSION['login_id']; ?></p>

                <?php if(isset($_SESSION['lecturer_no'])) {
                    ?> <p>Lecturer No <?php echo $_SESSION['lecturer_no']; ?></p>
                <?php  }?>

                <?php if(isset($_SESSION['department'])) { 
                    ?> <p>Department: <?php echo $_SESSION['department']; ?></p>
                <?php } ?>
                <p>This is the Lecturer Dashboard for the CCI IMS</p>
            </main>
        </div>
    </body>
</html>