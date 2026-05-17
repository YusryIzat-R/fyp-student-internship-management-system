<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

if(!isset($_SESSION['role']) || $_SESSION['role'] != "student") {
    header("Location: ../public/login.php");
    exit;
}

$full_name = "";
if(isset($_SESSION['full_name'])) {
    $full_name = $_SESSION["full_name"];
} else {
    $full_name = $_SESSION["login_id"];
}

$sql = "SELECT * FROM announcements ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Student Announcements</title>
        <link rel="stylesheet" href="../assets/css/dashboards.css">
    </head>
    <body>
        <div class="wrapper">
            <aside class="sidebar">
                <h3>Student Menu</h3>

                <nav class="menu">
                    <a href="student_dashboard.php" class="menu-item">Dashboard</a>
                    <a href="student_announcement.php" class="menu-item is-active">Announcements</a>
                    <a href="student_resource.php" class="menu-item">Resources</a>
                    <a href="student_assigned_lecturer.php" class="menu-item">My Lecturer</a>
                    <a href="student_presentation_booking.php" class="menu-item">Presentation Booking</a>
                    <a href="#" class="menu-item">My Result</a>
                    <a href="#" class="menu-item">Get Help</a>
                    <a href="../verify/logout.php" class="menu-item">Logout</a>
                </nav>
            </aside>

            <main class="content">
                <h1>Announcements</h1>
                <p>Welcome, <b><?php echo $full_name; ?></b></p>
                <p>View the latest announcements from admin and lecturers here.</p>

                <br>

                <h2>Announcement List</h2>

                <?php
                if($result && mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        echo "<div style='background: white; padding: 15px; margin-bottom: 15px; border: 1px solid #ccc;'>";
                        echo "<h3>" . $row['title'] . "</h3>";
                        echo "<p>" . $row['content'] . "</p>";
                        echo "<p><b>Posted By:</b>" . $row['posted_by'] . "</p>";
                        echo "<p><b>Role:</b>" . $row['role'] . "</p>";
                        echo "<p><b>Created At:</b>" . $row['created_at'] . "</p>";
                        echo "</div>";
                    }
                    } else {
                        echo "<p>No announcements available.</p>";
                    }
                ?>
            </main>
        </div>
    </body>
</html>
