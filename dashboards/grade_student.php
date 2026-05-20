<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

if(!isset($_SESSION['role']) || $_SESSION['role'] != "lecturer") {
    header("Location: ../public/login.php");
    exit;
}

if(!isset($_GET['student_no'])) {
    $_SESSION['error'] = "Student Number not found for grading.";
    header("Location: lecturer_grading.php");
    exit;
}

$student_no = $_GET['student_no'];
$lecturer_no = $_SESSION['login_id'];

$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : $_SESSION['login_id'];

/** Get lecturer's record and information */
$lecturer_sql = "SELECT * FROM lecturers 
                 WHERE lecturer_no = '$lecturer_no'
                 LIMIT 1";

$lecturer_result = mysqli_query($conn, $lecturer_sql);

if(!$lecturer_result || mysqli_num_rows($lecturer_result) == 0) {
    $_SESSION['error'] = "No lecturer's record and informations found";
    header("Location: lecturer_grading.php");
    exit;
}

$lecturer = mysqli_fetch_assoc($lecturer_result);
$lecturer_id = $lecturer['id'];

/** Get student's information */
$student_sql = "SELECT * FROM students
                WHERE student_no = '$student_no'
                AND assigned_lecturer_id = '$lecturer_id'
                LIMIT 1";

$student_result = mysqli_query($conn, $student_sql);

if(!$student_result || mysqli_num_rows($student_result) == 0) {
    $_SESSION['error'] = "No student's record found!";
    header("Location: lecturer_grading.php");
    exit;
}

$student = mysqli_fetch_assoc($student_result);

/** Count total resources */
$total_resource_sql = "SELECT COUNT(*) AS total_resources
                       FROM resources
                       WHERE lecturer_id = '$lecturer_id'";

$total_resource_result = mysqli_query($conn, $total_resource_sql);
$total_resource_row = mysqli_fetch_assoc($total_resource_result);
$total_resources = $total_resource_row['total_resources'];

/** Count active required submissions */
$total_required_sql = "SELECT COUNT(*) AS total_required
                       FROM required_submissions
                       WHERE status = 'active'";

$total_required_result = mysqli_query($conn, $total_required_sql);
$total_required_row = mysqli_fetch_assoc($total_required_result);
$total_required = $total_required_row['total_required'];

/** Count approved required submissions */
$approved_sql = "SELECT COUNT(*) AS approved_submissions
                 FROM submissions
                 INNER JOIN required_submissions
                 ON submissions.submission_type = required_submissions.submission_type
                 WHERE submissions.student_id = '$student_no'
                 AND submissions.status = 'approved'
                 AND required_submissions.status = 'active'";

$approved_result = mysqli_query($conn, $approved_sql);
$approved_row = mysqli_fetch_assoc($approved_result);
$approved_submissions = $approved_row['approved_submissions'];

$submission_progress = $approved_submissions . " / " . $total_required . " Approved";

/** Get latest presentation booking */
$booking_sql = "SELECT * FROM presentation_booking
                WHERE student_id = '" . $student['id'] . "'
                AND lecturer_id = '$lecturer_id'
                ORDER BY created_at DESC
                LIMIT 1";

$booking_result = mysqli_query($conn, $booking_sql);

$presentation_status = "No Bookings";

if($booking_result && mysqli_num_rows($booking_result) > 0) {
    $booking = mysqli_fetch_assoc($booking_result);
    $presentation_status = ucfirst($booking['status']);
}

/** Check if there's existing result */
$result_sql = "SELECT  * FROM results
               WHERE student_id = '$student_no'
               AND lecturer_id = '$lecturer_id'
               LIMIT 1";

$result_result = mysqli_query($conn, $result_sql);

$existing_result = null;

if($result_result && mysqli_num_rows($result_result) > 0) {
    $existing_result = mysqli_fetch_assoc($result_result);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Grade Student - CCI IMS</title>
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
                <a href="lecturer_assigned_students.php" class="menu-item">My Students</a>
                <a href="lecturer_presentation_booking.php" class="menu-item">Presentation Timeslot Management</a>
                <a href="lecturer_grading.php" class="menu-item is-active">Grading</a>
                <a href="../verify/logout.php" class="menu-item">Logout</a>
            </nav>
        </aside>

        <main class="content">
            <h1>Grade Students</h1>
            <p>Welcome, <b><?php echo $full_name; ?></b></p>

            <br>

            <a href="lecturer_grading.php" class="btn-main">
                ← Back to Grade Page
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

            <div class="resource-card">
                <h2><?php echo $student['full_name']; ?></h2>
                <p><b>Student No:</b><?php echo $student['student_no']; ?></p>
                <p><b>Submission Progess:</b><?php echo $submission_progress; ?></p>
                <p><b>Presentation Status:</b><?php echo $presentation_status; ?></p>

            </div>

            <br>

            <?php
            if($total_required > 0 &&
                $approved_submissions == $total_required && 
                $presentation_status == "Accepted") {
            ?> 

            <form action="../verify/save_result.php" method="POST">

                <input type="hidden" name="student_no" value="<?php echo $student_no; ?>">
                <label for="grade"><b>Grade:</b></label>
                <br><br>

                <select name="grade" id="grade" required>
                    <option value="">-- Select Result --</option>

                    <?php 
                    $grades = array("Pass", "Fail");

                    foreach($grades as $grade) {
                        $selected = "";
                        if($existing_result != null && $existing_result['grade'] == $grade) {
                            $selected = "selected";
                        }
                        echo "<option value='$grade' $selected>$grade</option>";
                    }
                    ?>
                </select>

                <br><br>

                <label for="feedback"><b>Feedback:</b></label>
                <br><br>

                <textarea name="feedback" id="feedback" rows="6" style="width: 100%;" placeholder="Enter feedback for the student.."><?php
                if($existing_result != null) {
                    echo $existing_result['feedback'];
                }
                ?></textarea>

                <br><br>

                <button type="submit" name="save_result">
                    Save Result
                </button>

            </form>

                <?php } else { ?>

                    <div class="alert error">
                        This student is not eligible for grading yet.
                    </div>

                <?php } ?>

        </main>
    </div>
</body>
</html>