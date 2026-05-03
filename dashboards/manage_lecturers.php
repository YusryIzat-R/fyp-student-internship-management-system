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

/* Get all lecturers with total assigned students */
$sql = "SELECT lecturers.id, lecturers.lecturer_no, lecturers.full_name, lecturers.email, lecturers.department,
        COUNT(students.id) AS total_students
        FROM lecturers
        LEFT JOIN students ON lecturers.id = students.assigned_lecturer_id
        GROUP BY lecturers.id, lecturers.lecturer_no, lecturers.full_name, lecturers.email, lecturers.department
        ORDER BY lecturers.lecturer_no ASC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Lecturers - CCI IMS</title>
    <link rel="stylesheet" href="../assets/css/dashboards.css">
</head>
<body>
    <div class="wrapper">
        <aside class="sidebar">
            <h3>Admin Menu</h3>

            <nav class="menu">
                <a href="admin_dashboard.php" class="menu-item">Dashboard</a>
                <a href="admin_announcement.php" class="menu-item">Announcements</a>
                <a href="manage_student_assignments.php" class="menu-item">Student Assignments</a>
                <a href="manage_lecturers.php" class="menu-item is-active">Manage Lecturers</a>
                <a href="#" class="menu-item">Results</a>
                <a href="#" class="menu-item">Get Help</a>
                <a href="../verify/logout.php" class="menu-item">Logout</a>
            </nav>
        </aside>

        <main class="content">
            <h1>Manage Lecturers</h1>
            <p>Welcome, <b><?php echo $full_name; ?></b></p>
            <p>View and manage visiting lecturer records here.</p>

            <br>

            <a href="add_lecturer.php" style="display: inline-block; padding: 10px 16px; background-color: #3bba9c; color: white; text-decoration: none; border-radius: 5px; margin-bottom: 20px;">
                Add New Lecturer
            </a>

            <h2>Lecturer List</h2>

            <?php
            if($result && mysqli_num_rows($result) > 0) {
                echo "<table border='1' cellpadding='10' cellspacing='0' style='background: white; border-collapse: collapse; width: 100%;'>";
                echo "<tr>";
                echo "<th>Lecturer No</th>";
                echo "<th>Full Name</th>";
                echo "<th>Email</th>";
                echo "<th>Department</th>";
                echo "<th>Assigned Students</th>";
                echo "<th>Action</th>";
                echo "</tr>";

                while($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['lecturer_no'] . "</td>";
                    echo "<td>" . $row['full_name'] . "</td>";
                    echo "<td>" . $row['email'] . "</td>";
                    echo "<td>" . $row['department'] . "</td>";
                    echo "<td>" . $row['total_students'] . "</td>";

                    echo "<td>";
                    echo "<a href='edit_lecturer.php?id=" . $row['id'] . "' style='display: inline-block; padding: 8px 14px; background-color: #3bba9c; color: white; text-decoration: none; border-radius: 5px; margin-right: 10px;'>Edit</a>";

                    echo "<a href='../verify/delete_lecturer.php?id=" . $row['id'] . "' style='display: inline-block; padding: 8px 14px; background-color: #dc3545; color: white; text-decoration: none; border-radius: 5px;' onclick='return confirm(\"Are you sure you want to delete this lecturer?\");'>Delete</a>";
                    echo "</td>";

                    echo "</tr>";
                }

                echo "</table>";
            } else {
                echo "<p>No lecturers found.</p>";
            }
            ?>
        </main>
    </div>
</body>
</html>