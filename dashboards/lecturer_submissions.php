<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

if(!isset($_SESSION['role']) || $_SESSION['role'] != "lecturer") {
    header("Location: ../public/login.php");
    exit;
}

$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : $_SESSION['login_id'];
$lecturer_no = $_SESSION['login_id'];

$student_filter = "";

if(isset($_GET['student_no'])) {
    $student_no = $_GET['student_no'];
    $student_filter = "AND students.student_no = '$student_no'";
}

$sql = "SELECT submissions.*,
               students.student_no,
               students.full_name AS student_name,
               students.email,
               students.program,
               resources.title AS resource_title,
               resources.category
        FROM submissions
        INNER JOIN students ON submissions.student_id = students.student_no
        INNER JOIN resources ON submissions.resource_id = resources.resource_id
        WHERE resources.lecturer_id = '$lecturer_no'
        $student_filter
        ORDER BY submissions.submitted_at DESC";

$result = mysqli_query($conn, $sql);

if(!$result) {
    die("Query Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Submissions - CCI IMS</title>
    <link rel="stylesheet" href="../assets/css/dashboards.css">
</head>
<body>
<div class="wrapper">
    <aside class="sidebar">
        <h3>Lecturer Menu</h3>

        <nav class="menu">
            <a href="lecturer_dashboard.php" class="menu-item">Dashboard</a>
                <a href="lecturer_announcement.php" class="menu-item">Announcements</a>
                <a href="lecturer_resources.php" class="menu-item is-active">Internship Resources Management</a>
                <a href="lecturer_assigned_students.php" class="menu-item">My Students</a>
                <a href="lecturer_presentation_booking.php" class="menu-item">Presentation Timeslot Management</a>
                <a href="lecturer_grading.php" class="menu-item">Grading</a>
                <a href="../verify/logout.php" class="menu-item">Logout</a>
        </nav>
    </aside>

    <main class="content">
        <h1>Student Submissions</h1>
        <p>Welcome, <b><?php echo $full_name; ?></b></p>
        <p>Review internship document submissions from your students.</p>

        <br>

        <a href="lecturer_resources.php" class="btn-main">
            ← Back to Resources
        </a>

        <br><br>

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

        <table border="1" cellpadding="10" cellspacing="0" style="width:100%; background:white;">
            <tr>
                <th>No</th>
                <th>Student</th>
                <th>Resource</th>
                <th>Type</th>
                <th>Status</th>
                <th>Submitted At</th>
                <th>File</th>
                <th>Comment</th>
                <th>Action</th>
            </tr>

            <?php
            if(mysqli_num_rows($result) > 0) {
                $no = 1;

                while($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $no . "</td>";
                    echo "<td>";
                    echo $row['student_name'] . "<br>";
                    echo "<small>" . $row['student_no'] . "</small>";
                    echo "</td>";
                    echo "<td>" . $row['resource_title'] . "</td>";
                    echo "<td>" . $row['submission_type'] . "</td>";
                    echo "<td>" . ucfirst($row['status']) . "</td>";
                    echo "<td>" . $row['submitted_at'] . "</td>";
                    echo "<td><a href='../" . $row['file_path'] . "' target='_blank'>View / Download</a></td>";
                    echo "<td>" . $row['lecturer_comment'] . "</td>";

                    echo "<td>";

                    if($row['status'] == "pending") {
                        echo "<form action='../verify/review_submission.php' method='POST' style='margin-bottom:8px;'>";
                        echo "<input type='hidden' name='submission_id' value='" . $row['submission_id'] . "'>";
                        echo "<input type='hidden' name='review_action' value='approved'>";
                        echo "<button type='submit' name='review_submission' onclick='return confirm(\"Approve this submission?\");'>Approve</button>";
                        echo "</form>";

                        echo "<form action='../verify/review_submission.php' method='POST'>";
                        echo "<input type='hidden' name='submission_id' value='" . $row['submission_id'] . "'>";
                        echo "<input type='hidden' name='review_action' value='rejected'>";
                        echo "<input type='text' name='lecturer_comment' placeholder='Reason for rejection' required>";
                        echo "<br><br>";
                        echo "<button type='submit' name='review_submission' onclick='return confirm(\"Reject this submission?\");'>Reject</button>";
                        echo "</form>";
                    } else {
                        echo "-";
                    }

                    echo "</td>";
                    echo "</tr>";

                    $no++;
                }
            } else {
                echo "<tr><td colspan='9'>No submissions found.</td></tr>";
            }
            ?>
        </table>
    </main>
</div>
</body>
</html>