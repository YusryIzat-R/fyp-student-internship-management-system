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
    $full_name = $_SESSION['full_name'];
} else {
    $full_name = $_SESSION ['login_id'];
}

$login_id = $_SESSION['login_id'];

/* Get student record first */
$student_sql = "SELECT * FROM students WHERE student_no = '$login_id' LIMIT 1";
$student_result = mysqli_query($conn, $student_sql);

if(!$student_result || mysqli_num_rows($student_result) == 0) {
    echo "<p style='color: red;'>Student's record not found.</p>";
    echo "<a href='student_dashboard.php'>Back to Dashboard</a>";
    exit;
}

$student = mysqli_fetch_assoc($student_result);

/* Check for assigned lecturer existence */
if($student["assigned_lecturer_id"] == NULL) {
    $assigned_lecturer = null;
} else {
    $lecturer_id = $student["assigned_lecturer_id"];
    $lecturer_sql = "SELECT * FROM lecturers WHERE id = '$lecturer_id' LIMIT 1";
    $lecturer_result = mysqli_query($conn, $lecturer_sql);

    
    if($lecturer_result && mysqli_num_rows($lecturer_result) > 0) {
        $assigned_lecturer = mysqli_fetch_assoc($lecturer_result);
    } else {
        $assigned_lecturer = null;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>My Visiting Lecturer - CCI IMS</title>
        <link rel="stylesheet" href="../assets/css/dashboards.css">
    </head>
    <body>
        <div class="wrapper">
            <aside class="sidebar">
                <h3>Student Menu</h3>

                <nav class="menu">
                    <a href="../dashboards/student_dashboard.php" class="menu-item">Dashboards</a>
                    <a href="../dashboards/student_announcement.php" class="menu-item">Announcements</a>
                    <a href="#" class="menu-item">Resources</a>
                    <a href="../dashboards/student_assigned_lecturer.php" class="menu-item is-active">My Lecturer</a>
                    <a href="#" class="menu-item">Presentation Booking</a>
                    <a href="#" class="menu-item">My Result</a>
                    <a href="#" class="menu-item">Get Help</a>
                    <a href="../verify/logout.php" class="menu-item">Logout</a>
                </nav>
            </aside>

            <main class="content">
                <h1>My Visiting Lecturer</h1>
                <p>Welcome, <b><?php echo $full_name; ?></b></p>
                <p>Below is your assigned visiting lecturer information.</p>

                <br>

                <?php if($assigned_lecturer != null) { ?>
                    <div style="background: white; padding: 20px; border: 1px solid #ccc; max-width: 600px;">
                        <p><b>Lecturer No:</b><?php echo $assigned_lecturer['lecturer_no']; ?></p>
                        <p><b>Full Name:</b> <?php echo $assigned_lecturer['full_name']; ?></p>
                        <p><b>Email:</b> <?php echo $assigned_lecturer['email']; ?></p>
                        <p><b>Department:</b><?php echo $assigned_lecturer['department']; ?></p>
                    </div>
                <?php } else { ?>
                    <p style="color: red;">You have not been assigned to a visiting lecturer yet.</p>
                <?php } ?>
            </main>
        </div>
    </body>
</html>