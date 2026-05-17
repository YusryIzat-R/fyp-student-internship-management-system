<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

if(!isset($_SESSION['role']) || $_SESSION['role'] != "student") {
    header("Location: ../public/login.php");
    exit;
}

$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : $_SESSION['login_id'];
$student_no = $_SESSION['student_no'];

/** Get student's data */
$student_sql = "SELECT * FROM students WHERE student_no = '$student_no'";
$student_result = mysqli_query($conn, $student_sql);

if(!$student_result || mysqli_num_rows($student_result) == 0) {
    die("Student record not found.");
}

$student = mysqli_fetch_assoc($student_result);
$assigned_lecturer_id = $student['assigned_lecturer_id'];

$resource_result = NULL;
$lecturer = NULL;

if($assigned_lecturer_id != NULL) {

    /** Get assigned lecturer's data */
    $lecturer_sql = "SELECT * FROM lecturers WHERE id = '$assigned_lecturer_id'";
    $lecturer_result = mysqli_query($conn, $lecturer_sql);

    if($lecturer_result && mysqli_num_rows($lecturer_result) > 0) {
        $lecturer = mysqli_fetch_assoc($lecturer_result);
        $lecturer_no = $lecturer['lecturer_no'];
    
        /** Get resources uploaded by assigned lecturer */
        $resource_sql = "SELECT * FROM resources 
                         WHERE lecturer_id = '$lecturer_no'
                         ORDER BY created_at DESC";
                         
        $resource_result = mysqli_query($conn, $resource_sql);

        if(!$resource_result) {
            die("Query error: " . mysqli_error($conn));
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Internship Resources - CCI IMS</title>
    <link rel="stylesheet" href="../assets/css/dashboards.css">
</head>
<body>
    <div class="wrapper">
        <aside class="sidebar">
            <h3>Student Menu</h3>

            <nav class="menu">
                <a href="../dashboards/student_dashboard.php" class="menu-item">Dashboard</a>
                <a href="../dashboards/student_announcement.php" class="menu-item">Announcements</a>
                <a href="../dashboards/student_resource.php" class="menu-item is-active">Resources</a>
                <a href="../dashboards/student_assigned_lecturer.php" class="menu-item">My Lecturer</a>
                <a href="../dashboards/student_presentation_booking.php" class="menu-item">Presentation Booking</a>
                <a href="#" class="menu-item">My Result</a>
                <a href="#" class="menu-item">Get Help</a>
                <a href="../verify/logout.php" class="menu-item">Logout</a>
            </nav>
        </aside>

        <main class="content">
            <h1></h1>
            <p>Welcome, <b><?php echo $full_name; ?></b></p>
            <p>View and download internship templates and documents uploaded by your assigned lecturer.</p>

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

        <?php if($assigned_lecturer_id == NULL) { ?>

            <p style="color:red;">You have not been assigned to a lecturer yet. Please contact admin.</p>

        <?php } else if($lecturer == null) { ?>

            <p style="color:red;">Assigned lecturer record not found.</p>

        <?php } else { ?>
        
            <p><b>Assigned Lecturer: </b><?php echo $lecturer['full_name']; ?> (<?php echo $lecturer['lecturer_no']; ?>)</p>

            <br>

            <h2>Available Resources</h2>

            <div class="resource-list">
                <?php
                if($resource_result && mysqli_num_rows($resource_result) > 0) {
                    while($row = mysqli_fetch_assoc($resource_result)) {
                        ?>

                        <div class="resource-card">
                            <h3><?php echo $row['title']; ?></h3>
                            <p><b>Category:</b><?php echo $row['created_at']; ?></p>
                            <p><b>Uploaded At:</b><?php echo $row['created_at']; ?></p>

                            <div class="resource-actions">
                                <a href="../<?php echo $row['file_path']; ?>"
                                 target="_blank"
                                 class="resource-btn download-btn">
                                 Download / View
                                </a>

                                <a href="student_resource_submission.php?resource_id=<?php echo $row['resource_id']; ?>"
                                    class="resource-btn edit-btn">
                                    Submit
                                </a>
                            </div>
                        </div>

                <?php
                    }
                } else {
                    echo "<p>No resources available yet.</p>";
                }
                ?>
            </div>

            <?php } ?>

        </main>
    </div>
</body>
</html>