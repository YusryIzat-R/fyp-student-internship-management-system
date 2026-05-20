<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

if(!isset($_SESSION['role']) || $_SESSION['role'] != "student") {
    header("Location: ../public/login.php");
    exit;
}

$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : $_SESSION['login_id'];
$student_no = $_SESSION['student_no'];

$sql = "SELECT results.*, lecturers.full_name AS lecturer_name
        FROM results
        LEFT JOIN lecturers ON results.lecturer_id = lecturers.lecturer_no
        WHERE results.student_id = '$student_no'
        LIMIT 1";

$result = mysqli_query($conn, $sql);

if(!$result) {
    die("Query Error: " . mysqli_error($conn));
}

$row = null;

if(mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>My Result - CCI IMS</title>
        <link rel="stylesheet" href="../assets/css/dashboards.css">
    </head>
    <body>
        <div class="wrapper">
            <aside class="sidebar">
                <h3>Student Menu</h3>

                <nav class="menu">
                    <a href="../dashboards/student_dashboard.php" class="menu-item">Dashboard</a>
                    <a href="../dashboards/student_announcement.php" class="menu-item">Announcements</a>
                    <a href="../dashboards/student_resource.php" class="menu-item">Resources</a>
                    <a href="../dashboards/student_assigned_lecturer.php" class="menu-item">My Lecturer</a>
                    <a href="../dashboards/student_presentation_booking.php" class="menu-item">Presentation Booking</a>
                    <a href="../dashboards/student_result.php" class="menu-item is-active">My Result</a>
                    <a href="../dashboards/student_get_help.php" class="menu-item">Get Help</a>
                    <a href="../verify/logout.php" class="menu-item">Logout</a>
                </nav>
            </aside>

            <main class="content">
                <h1>My Result</h1>
                <p>Welcome, <b><?php echo $full_name; ?></b></p>
                <p>View your internship result and lecturer feedbacks here.</p>

                <br>

                <?php if($row == null) { ?>
                    <div class="alert error">
                        Your result has not been released yet.
                    </div>

                <?php } else { ?>
                    <div class="resource-card">
                        <h2>Internship Result</h2>

                        <p><b>Student No: </b><?php echo $student_no; ?></p>
                        <p><b>Result: </b><?php echo $row['grade']; ?></p>
                        <p><b>Lecturer: </b><?php echo $row['lecturer_name']; ?></p>
                        <p><b></b><?php echo $row['released_at']; ?></p>

                        <br>

                        <p><b>Feedback: </b></p>
                        <p><?php echo $row['feedback']; ?></p>
                    </div>

                <?php } ?>
            </main>
        </div>
    </body>
</html>