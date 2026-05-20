<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../public/login.php");
    exit;
}

$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : $_SESSION['login_id'];

$sql = "SELECT students.*, users.status 
        FROM students
        LEFT JOIN users ON students.user_id = users.id
        ORDER BY students.full_name ASC";

$result = mysqli_query($conn, $sql);

if(!$result) {
    die("Query Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Management - CCI IMS</title>
    <link rel="stylesheet" href="../assets/css/dashboards.css">
</head>
<body>
<div class="wrapper">
    <aside class="sidebar">
        <h3>ADMIN MENU</h3>

        <nav class="menu">
            <a href="admin_dashboard.php" class="menu-item">Dashboard</a>
            <a href="admin_announcement.php" class="menu-item">Announcements</a>
            <a href="manage_lecturers.php" class="menu-item">Visiting Lecturer Management</a>
            <a href="student_management.php" class="menu-item is-active">Student Management</a>
            <a href="admin_required_submissions.php" class="menu-item">Required Submissions</a>
            <a href="admin_results.php" class="menu-item">Results</a>
            <a href="admin_help_requests.php" class="menu-item">Get Help</a>
            <a href="../verify/logout.php" class="menu-item">Logout</a>
        </nav>
    </aside>

    <main class="content">
        <h1>Student Management</h1>
        <p>Welcome, <b><?php echo $full_name; ?></b></p>
        <p>Manage student records and account status here.</p>

        <?php if(isset($_SESSION['error'])) { ?>
            <div class="alert error">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php } ?>

        <?php if(isset($_SESSION['success'])) { ?>
            <div class="alert success">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php } ?>

        <br>

        <a href="upload_student_list.php" class="btn-main">Upload Student List</a>

        <br><br>

        <h2>Student List</h2>

        <table border="1" cellpadding="0" cellspacing="10" style="width:100%; background:white;">
            <tr>
                <th>No</th>
                <th>Student No</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Program</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

            <?php
            if(mysqli_num_rows($result) > 0) {
                $no = 1;

                while($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $no . "</td>";
                    echo "<td>" . $row['student_no'] . "</td>";
                    echo "<td>" . $row['full_name'] . "</td>";
                    echo "<td>" . $row['email'] . "</td>";
                    echo "<td>" . $row['program'] . "</td>";

                    if($row['status'] == 1) {

                    echo "<td style='color: green;'>Active</td>";

                    echo "<td>
                        <a class='action-btn btn-deactivate' 
                            href='../verify/update_student_status.php?id=" . $row['user_id'] . "&status=0'
                            onclick='return confirm(\"Set this student as inactive?\");'>
                            Deactivate
                        </a>
                    </td>";

                    } else if($row['status'] == 0 && $row['user_id'] != NULL) {

                        echo "<td style='color: red;'>Inactive</td>";

                        echo "<td>
                            <a class='action-btn btn-activate' 
                                href='../verify/update_student_status.php?id=" . $row['user_id'] . "&status=1'
                                onclick='return confirm(\"Set this student as active?\");'>
                                Activate
                            </a>
                        </td>";

                    } else {

                        echo "<td style='color: orange;'>Not Registered</td>";
                        echo "<td>-</td>";

                    }

                    $no++;
                    
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No students found.</td></tr>";
            }
            ?>
        </table>
    </main>
</div>
</body>
</html>