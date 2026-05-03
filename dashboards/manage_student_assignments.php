<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

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

/* Get all students with assigned lecturer name if available */
$sql = "SELECT students.id, students.student_no, students.full_name, students.email, students.program,
        students.assigned_lecturer_id,
        lecturers.full_name AS lecturer_name,
        lecturers.lecturer_no
        FROM students
        LEFT JOIN lecturers ON students.assigned_lecturer_id = lecturers.id
        ORDER BY students.student_no ASC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Student Assignments - CCI IMS</title>
    <link rel="stylesheet" href="../assets/css/dashboards.css">
</head>
<body>
    <div class="wrapper">
        <aside class="sidebar">
            <h3>Admin Menu</h3>

            <nav class="menu">
                <a href="admin_dashboard.php" class="menu-item">Dashboard</a>
                <a href="admin_announcement.php" class="menu-item">Announcements</a>
                <a href="manage_student_assignments.php" class="menu-item is-active">Student Assignments</a>
                <a href="manage_lecturers.php" class="menu-item">Manage Lecturers</a>
                <a href="#" class="menu-item">Results</a>
                <a href="#" class="menu-item">Get Help</a>
                <a href="../verify/logout.php" class="menu-item">Logout</a>
            </nav>
        </aside>

        <main class="content">
            <h1>Student to Lecturer Assignment</h1>
            <p>Welcome, <b><?php echo $full_name; ?></b></p>
            <p>Assign students to visiting lecturers here.</p>

            <br>

            <h2>Student List</h2>

            <?php
            if($result && mysqli_num_rows($result) > 0) {
                echo "<table border='1' cellpadding='10' cellspacing='0' style='background: white; border-collapse: collapse; width: 100%;'>";
                echo "<tr>";
                echo "<th>Student No</th>";
                echo "<th>Full Name</th>";
                echo "<th>Email</th>";
                echo "<th>Program</th>";
                echo "<th>Assigned Lecturer</th>";
                echo "<th>Action</th>";
                echo "</tr>";

                while($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['student_no'] . "</td>";
                    echo "<td>" . $row['full_name'] . "</td>";
                    echo "<td>" . $row['email'] . "</td>";
                    echo "<td>" . $row['program'] . "</td>";

                    if($row['assigned_lecturer_id'] != NULL) {
                        echo "<td>" . $row['lecturer_no'] . " - " . $row['lecturer_name'] . "</td>";
                    } else {
                        echo "<td style='color:red;'>Not Assigned Yet</td>";
                    }

                    echo "<td>";
                    echo "<a href='assign_lecturer.php?id=" . $row['id'] . "' style='display: inline-block; padding: 8px 14px; background-color: #3bba9c; color: white; text-decoration: none; border-radius: 5px;'>";
                    
                    if($row['assigned_lecturer_id'] != NULL) {
                        echo "Change Lecturer";
                    } else {
                        echo "Assign Lecturer";
                    }

                    echo "</a>";
                    echo "</td>";
                    echo "</tr>";
                }

                echo "</table>";
            } else {
                echo "<p>No students found.</p>";
            }
            ?>
        </main>
    </div>
</body>
</html>