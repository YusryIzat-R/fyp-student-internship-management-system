<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../public/login.php");
    exit;
}

$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : $_SESSION['login_id'];

$sql = "SELECT students.student_no,
            students.full_name AS student_name,
            students.email,
            students.program,
            lecturers.full_name AS lecturer_name,
            results.grade,
            results.feedback,
            results.released_at
        FROM students
        LEFT JOIN lecturers ON students.assigned_lecturer_id = lecturers.id
        LEFT JOIN results ON students.student_no = results.student_id
        ORDER BY students.student_no ASC";

$result = mysqli_query($conn, $sql);

if(!$result) {
    die("Query Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<head>
    <meta charset="wrapper">
    <title>Student Results - CCI IMS</title>
    <link rel="stylesheet" href="../assets/css/dashboards.css">
</head>
<body>
    <div class="wrapper">
        <aside class="sidebar">
            <h3>Admin Menu</h3>

            <nav class="menu">
                <a href="../dashboards/admin_dashboard.php" class="menu-item">Dashboard</a>
                <a href="../dashboards/admin_announcement.php" class="menu-item">Announcements</a>
                <a href="../dashboards/manage_lecturers.php" class="menu-item">Visiting Lecturer Management</a>
                <a href="../dashboards/student_management.php" class="menu-item">Student Management</a>
                <a href="../dashboards/admin_required_submissions.php" class="menu-item">Required Submissions</a>
                <a href="../dashboards/admin_results.php" class="menu-item is-active">Results</a>
                <a href="../dashboards/admin_help_requests.php" class="menu-item">Get Help</a>
                <a href="../public/logout.php" class="menu-item">Logout</a>
            </nav>
        </aside>

        <main class="content">
            <h1>Student Results</h1>
            <p>Welcome, <b><?php echo $full_name; ?></b></p>
            <p>View students' internship results and lecturer feedbacks below:</p>

            <br>

            <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; background: white; border-collapse: collapse;">
                <tr>
                    <th>No</th>
                    <th>Student No</th>
                    <th>Student Name</th>
                    <th>Program</th>
                    <th>Assigned Lecturer</th>
                    <th>Result</th>
                    <th>Feedback</th>
                    <th>Released At</th>
                </tr>

                <?php 
                if(mysqli_num_rows($result) > 0){
                    $no = 1;

                    while($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $no . "</td>";
                        echo "<td>" . $row['student_no'] . "</td>";
                        echo "<td>" . $row['student_name'] . "</td>";
                        echo "<td>" . $row['program'] . "</td>";

                        if($row['lecturer_name'] != "") {
                            echo "<td>" . $row['lecturer_name'] . "</td>";
                        } else {
                            echo "<td style='color: orange;'>Not Assigned</td>";
                        }

                        if($row['grade'] != "") {
                            echo "<td>" . $row['grade'] . "</td>";
                        } else {
                            echo "<td style='color: red;'>Not Released Yet.</td>";
                        }

                        echo "<td>" . $row['feedback'] . "</td>";
                        echo "<td>" . $row['released_at'] . "</td>";
                        echo "</tr>";

                        $no++;
                    }
                } else {
                    echo "<tr><td colspan='8'>No Students found.</td></tr>";
                }
                ?>
            </table>
        </main>
    </div>
</body>
</html>