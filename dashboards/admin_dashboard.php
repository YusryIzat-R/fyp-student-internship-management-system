<?php
session_start();

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
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
        <title>Admin Dashboard - CCI IMS</title>
        <link rel="stylesheet" href="../assets/css/dashboards.css">
    </head>

    <body>
        <div class="wrapper">
            <aside class="sidebar">
                <h3>Admin Menu</h3>

                <nav class="menu">
                    <a href="../dashboards/admin_dashboard.php" class="menu-item is-active">Dashboard</a>
                    <a href="../dashboards/admin_announcement.php" class="menu-item">Announcements</a>
                    <a href="../dashboards/manage_lecturers.php" class="menu-item">Visitng Lecturer Management</a>
                    <a href="../dashboards/student_management.php" class="menu-item">Student Management</a>
                    <a href="#" class="menu-item">Results</a>
                    <a href="#" class="menu-item">Get Help</a>
                    <a href="../public/logout.php" class="menu-item">Logout</a>
                </nav>
            </aside>

            <main class="content">
                <h1>Welcome, <?php echo $full_name; ?></h1>
                <p>You are logged in as <b><?php echo $_SESSION['role'];?></b>.</p>
                <p>Login ID: <?php echo $_SESSION['login_id']; ?></p>
                <p>This is the Admin Dashboard for the CCI IMS</p>
            </main>
        </div>
    </body>
</html>