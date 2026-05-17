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
                    echo "<table border='1' cellpadding='10' cellspacing='1' style='background: white; border-collapse: collapse; width: 100%;'>";
                    echo "<tr>";
                    echo "<th>Student No</th>";
                    echo "<th>Full Name</th>";
                    echo "<th>Email</th>";
                    echo "<th>Program</th>";
                    echo "<th>Submission Progress</th>";
                    echo "<th>Presentation Status</th>";
                    echo "<th>Action</th>";
                    echo "</tr>";

                    while($student = mysqli_fetch_assoc($student_result)) {

                    $student_no = $student['student_no'];
                    $student_id = $student['id'];

                    /** Count total resources uploaded by the students */
                    $total_resource_sql = "SELECT COUNT(*) AS total_resources
                                           FROM resources
                                           WHERE lecturer_id = '$login_id'";

                    $total_resource_result = mysqli_query($conn, $total_resource_sql);
                    $total_resource_row = mysqli_fetch_assoc($total_resource_result);
                    $total_resources = $total_resource_row['total_resources'];

                    /** Count approved submissions by students */
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

                    /** Get latest presentation booking status */
                    $booking_sql = "SELECT * FROM presentation_booking
                                    WHERE student_id = '$student_id'
                                    ORDER BY created_at DESC
                                    LIMIT 1";

                    $booking_result = mysqli_query($conn, $booking_sql);

                    if($booking_result && mysqli_num_rows($booking_result) > 0) {
                        $booking = mysqli_fetch_assoc($booking_result);
                        $presentation_status = ucfirst($booking['status']);
                    } else {
                        $presentation_status = "No Bookings";
                    }

                        echo "<tr>";
                        echo "<td>" . $student['student_no'] . "</td>";
                        echo "<td>" . $student['full_name'] . "</td>";
                        echo "<td>" . $student['email'] . "</td>";
                        echo "<td>" . $student['program'] . "</td>";
                        echo "<td>" . $submission_progress . "</td>";
                        echo "<td>" . $presentation_status . "</td>";
                        
                        echo "<td>
                                <a href='lecturer_submissions.php?student_no=" . $student_no . "'>
                                    View Submissions
                                </a>
                                <br><br>
                                <a href='lecturer_presentation_booking.php?student_no=" . $student_no . "'>
                                    View Booking
                                </a>
                            </td>";

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