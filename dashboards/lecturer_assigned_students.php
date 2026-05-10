<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

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

$login_id = $_SESSION['login_id'];

/* Get lecturer record first */
$lecturer_sql = "SELECT * FROM lecturers WHERE lecturer_no = '$login_id' LIMIT 1";
$lecturer_result = mysqli_query($conn, $lecturer_sql);

if(!$lecturer_result || mysqli_num_rows($lecturer_result) == 0) {
    echo "<p style='color: red;'>Lecturer's record not found.</p>";
    echo "<a href='lecturer_dashboard.php'>Back to Dashboard</a>";
    exit;
}

$lecturer = mysqli_fetch_assoc($lecturer_result);
$lecturer_id = $lecturer['id'];

/* Get students assigned to the lecturer assigned */
$student_sql = "SELECT * FROM students WHERE assigned_lecturer_id = '$lecturer_id' ORDER BY student_no ASC";
$student_result = mysqli_query($conn, $student_sql);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>My Assigned Students - CCI IMS</title>
        <link rel="stylesheet" href="../assets/css/dashboards.css">
    </head>
    <body>
        <div class="wrapper">
            <aside class="sidebar">
                <h3>Lecturer Menu</h3>

                <nav class="menu">
                    <a href="lecturer_dashboard.php" class="menu-item">Dashboard</a>
                    <a href="lecturer_announcement.php" class="menu-item">Announcements</a>
                    <a href="lecturer_resources.php" class="menu-item">Internship Resources Management</a>
                    <a href="lecturer_assigned_students.php" class="menu-item is-active">My Students</a>
                    <a href="lecturer_presentation_booking.php" class="menu-item">Presentation Timeslot Management</a>
                    <a href="#" class="menu-item">Grading</a>
                    <a href="../verify/logout.php" class="menu-item">Logout</a>
                </nav>
            </aside>

            <main class="content">
                <h1>My Assigned Students</h1>
                <p>Welcome, <b><?php echo $full_name; ?></b></p>
                <p>Below is the list of students assigned to you:</p>

                <br>

                <?php
                if($student_result && mysqli_num_rows($student_result) > 0) {
                    echo "<table border='1' cellpadding='10' cellspacing='0' style'background: white; border-collapse: collapse; width: 100%;'>";
                    echo "<tr>";
                    echo "<th>Student No</th>";
                    echo "<th>Full Name</th>";
                    echo "<th>Email</th>";
                    echo "<th>Program</th>";
                    echo "</tr>";

                    while($student = mysqli_fetch_assoc($student_result)) {
                        echo "<tr>";
                        echo "<td>" . $student['student_no'] . "</td>";
                        echo "<td>" . $student['full_name'] . "</td>";
                        echo "<td>" . $student['email'] . "</td>";
                        echo "<td>" . $student['program'] . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>No students have been assigned to you yet at the moment.</p>";
                }
                ?>
            </main>
        </div>
    </body>
</html>