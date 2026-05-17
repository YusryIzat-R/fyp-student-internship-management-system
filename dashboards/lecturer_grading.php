<?php
session_start();
require_once '../config/db,php';
/** @var mysqli $conn */

if(!isset($_SESSION['role']) || $_SESSION['role'] != "lecturer") {
    header("Location: ../public/login.php");
    exit;
}

$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : $_SESSION['login_id'];
$login_id = $_SESSION['login_id'];

/** Get lecturer record/information */
$lecturer_sql = "SELECT * FROM lecturers WHERE lecturer_no = '$login_id' LIMIT 1";
$lecturer_result = mysqli_query($conn, $lecturer_sql);

if(!$lecturer_result || mysqli_num_rows($lecturer_result) == 0) {
    $_SESSION['error'] = "No lecturer record found.";
    header("Location: lecturer_dashboard.php");
    exit;
}

$lecturer = mysqli_fetch_assoc($lecturer_result);
$lecturer_id = $lecturer['id'];

/** Get assigned students */
$student_sql = "SELECT * FROM students
                WHERE assigned_lecturer_id = '$lecturer_id'
                ORDER BY student_no ASC";

$student_result = mysqli_query($conn, $student_sql);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Grading - CCI IMS</title>
        <link rel="stylesheet" href="../assets/css/dashboards.css">
    </head>
    <body>
        <div class="wrapper">
            <div class="sidebar">
                <h3>Lecturer Menu</h3>

                <nav class="menu">
                    <a href="lecturer_dashboard.php" class="menu-item">Dashboard</a>
                    <a href="lecturer_announcement.php" class="menu-item">Announcements</a>
                    <a href="lecturer_resources.php" class="menu-item">Internship Resources Management</a>
                    <a href="lecturer_assigned_students.php" class="menu-item is-active">My Students</a>
                    <a href="lecturer_presentation_booking.php" class="menu-item">Presentation Timeslot Management</a>
                    <a href="lecturer_grading.php" class="menu-item">Grading</a>
                    <a href="../verify/logout.php" class="menu-item">Logout</a>
                </nav>
            </aside>

            <main class="content">
                <h1>Grading</h1>
                <p>Welcome, <b><?php echo $full_name; ?></b></p>
                <p>Grade your students' internship performance here.</p>

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

                <?php 
                if($student_result && mysqli_num_rows($student_result) > 0) {
                    echo "<table border='1' cellpadding='10' cellspacing='0' style='background: white; border-collapse:collapse; width:100%;'>";
                    echo "<tr>";
                    echo "<th>Student No.</th>";
                    echo "<th>Full Name</th>";
                    echo "<th>Submission Progress</th>";
                    echo "<th>Presentation Status</th>";
                    echo "<th>Grade Status</th>";
                    echo "<th>Action</th>";
                    echo "</tr>";

                    while($student = mysqli_fetch_assoc($student_result)) {
                        $student_no = $student['student_no'];
                        $student_id = $student['id'];

                        /** Count total submissions uploaded by lecturers */
                        $total_resource_sql = "SELECT COUNT(*) AS total_resources
                                               FROM resources
                                               WHERE lecturer_id = '$login_id'";
                        
                        $total_resource_result = mysqli_query($conn, $total_resource_sql);
                        $total_resource_row = mysqli_fetch_assoc($total_resource_result);
                        $total_resources = $total_resource_row['total_resources'];

                        /** Count approved submissions */
                        $approved_sql = "SELECT COUNT(*) AS approved_submissions
                                         FROM submissions
                                         INNER JOIN resources ON submissions.resource_id = resources.resource_id
                                         WHERE submissions.student_id = '$student_no'
                                         AND resources.lecturer_id = '$login_id'
                                         AND submissions.status = 'approved'";
                        $approved_result = mysqli_query($conn, $approved_sql);
                        $approved_row = mysqli_fetch_assoc($approved_result);
                        $approved_submissions = $approved_row['approved_submissions'];

                        $submission_progress = $approved_submissions . " / " . $total_resources . " Approved";

                        /* Get Presentation status */
                        $booking_sql = "SELECT * FROM presentation_booking
                                        WHERE student_id = '$student_no'
                                        AND lecturer_id = '$lecturer_id'
                                        ORDER BY created_at DESC
                                        LIMIT 1";

                        $booking_result = mysqli_query($conn, $booking_sql);

                        $presentation_status = "No Bookings";

                        if($booking_result && mysqli_num_rows($booking_result) > 0) {
                            $booking = mysqli_fetch_assoc($booking_result);
                            $presentation_status = ucfirst($booking['status']);
                        }

                        /** Check if student has been graded and have result */
                        $result_sql = "SELECT * FROM results
                                       WHERE student_id = '$student_no'
                                       AND lecturer_id = '$lecturer_id'
                                       LIMIT 1";
                        $result_check = mysqli_query($conn, $result_sql);

                        $grade_status = "Not Graded Yet";

                        if($result_check && mysqli_num_rows($result_check) > 0) {
                            $result_row = mysqli_fetch_assoc($result_check);
                            $grade_status = "Graded: (" . $result_row['grade'] . ")"; 
                        }

                        echo "<tr>";
                        echo "<td>" . $student_no . "</td>";
                        echo "<td>" . $student['full_name'] . "</td>";
                        echo "<td>" . $submission_progress . "</td>";
                        echo "<td>" . $presentation_status . "</td>";
                        echo "<td>" . $grade_status . "</td>";

                        echo "<td>";

                        if($result_check && mysqli_num_rows($result_check) > 0) {
                            echo "<a href='grade_student.php?student_no=" . $student_no . "'>View / Edit Grade</a>";
                        } else {
                            if($total_resources > 0 && $approved_submissions == $total_resources && $presentation_status == "Accepted") {
                                echo "<a href='grade_student.php?student_no=" . $student_no . "'>Grade Student</a>";
                            } else {
                                echo "<span style='color:red;'>Not eligible yet</span>";
                            }
                        }

                        echo "</td>";
                        echo "</tr>";
                    }

                    echo "</table>";
                } else {
                    echo "<p>No students assigned to you yet.</p>";
                }
                ?>
            </main>
        </div>
    </body>
</html>

            