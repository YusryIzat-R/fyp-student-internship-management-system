<?php
session_start();

if(!isset($_SESSION['role']) || $_SESSION['role'] != "student") {
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
        <title>Student Dashboard - CCI IMS</title>
        <link rel="stylesheet" href="../assets/css/dashboards.css">
    </head>

    <body>
        <div class="wrapper">
            <aside class="sidebar">
                <h3>Student Menu</h3>

                <nav class="menu">
                    <a href="../dashboards/student_dashboard.php" class="menu-item is-active">Dashboard</a>
                    <a href="../dashboards/student_announcement.php" class="menu-item">Announcements</a>
                    <a href="../dashboards/student_resource.php" class="menu-item">Resources</a>
                    <a href="../dashboards/student_assigned_lecturer.php" class="menu-item">My Lecturer</a>
                    <a href="../dashboards/student_presentation_booking.php" class="menu-item">Presentation Booking</a>
                    <a href="../dashboards/student_result.php" class="menu-item">My Result</a>
                    <a href="../dashboards/student_get_help.php" class="menu-item">Get Help</a>
                    <a href="../verify/logout.php" class="menu-item">Logout</a>
                </nav>
            </aside>

            <main class="content">
                <h1>Welcome, <?php echo $full_name;?></h1>
                <p>You are logged in as <b><?php echo $_SESSION['role']; ?></b>.</p>
                <p>Login ID: <?php echo $_SESSION['login_id']; ?></p>
                <p>This is the Student Dashboard for the CCI IMS</p>
            </main>
        </div>
    </body>
</html>