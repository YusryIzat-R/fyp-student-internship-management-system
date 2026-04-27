<?php
session_start();
require_once '../config/db.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../public/login.php");
    exit;
}

if(!isset($_GET['id'])) {
    echo "<p style='color:red;'>Student ID not found.</p>";
    echo "<a href='manage_studemtnt_assignments.php>Back to Lecturer Assignments</a>";
    exit;
}

$student_id = $_GET['id'];

/* Get selected student */
$student_sql = "SELECT * FROM students WHERE id = '$student_id'";
$student_result = mysqli_query($conn, $student_sql);

if(!$student_result || mysqli_num_rows($student_result) == 0) {
    echo "<p style='color:red;'>Student not found.</p>";
    echo "<a href='manage_student_assignments.php'>Back to Lecturer Assignments</a>";
    exit;
}

$student = mysqli_fetch_assoc($student_result);

/* Get all lecturers */
$lecturer_sql = "SELECT * FROM lecturers ORDER BY lecturer_no ASC";
$lecturer_result = mysqli_query($conn, $lecturer_sql);

$full_name = "";
if(isset($_SESSION['full_name'])) {
    $full_name = $_SESSION['full_name'];
} else {
    $full_name = $_SESSION['login_id'];
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Assign Lecturer - CCI IMS</title>
        <link rel="stylesheet" href="../assets/css/dashboards.css">
    </head>
    <body>
        <div class="wrapper">
            <aside class="sidebar">
                <h3>Admin Menu</h3>

                <nav class="menu">
                    <a href="admin_dashboard.php" class="menu-item">Dashboard</a>
                    <a href="admin_announcement.php" class="menu-item">Announcements</a>
                    <a href="manage_student_assignments.php" class="menu-item is-active">Lecturer Assignments</a>
                    <a href="manage_lecturers.php" class="menu-item">Manage Lecturers</a>
                    <a href="#" class="menu-item">Results</a>
                    <a href="#" class="menu-item">Get help</a>
                    <a href="../verify/logout.php" class="menu-item">Logout</a>
                </nav>
            </aside>

            <main class="content">
                <h1>Assign Visiting Lecturer</h1>
                <p>Welcome, <b><?php echo $full_name; ?></b></p>
                <p>Select a Visiting Lecturer for this student.</p>

                <br>

                <div style="background: white; padding: 15px; margin-bottom: 20px; border: 1px solid #ccc;">
                    <p><b>Student No:</b> <?php echo $student['student_no']; ?></p>
                    <p><b>Full Name:</b> <?php echo $student['full_name']; ?></p>
                    <p><b>Email:</b> <?php echo $student['email']; ?></p>
                    <p><b>Program:</b> <?php echo $student['program']?></p>
                </div>

                <form action="../verify/update_student_assignment.php" method="POST">
                    <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">

                    <label for="lecturer_id">Select Visiting Lecturer:</label><br>
                    <select name="lecturer_id" id="lecturer_id" required style="width: 400px; padding: 8px;">
                        <option value="">-- Select Lecturer --</option>

                        <?php
                        if($lecturer_result && mysqli_num_rows($lecturer_result) > 0) {
                            while($lecturer = mysqli_fetch_assoc($lecturer_result)) {
                                echo "<option value='" . $lecturer['id'] . "'";

                                if($student['assigned_lecturer_id'] == $lecturer['id']) {
                                    echo " selected";
                                }

                                echo ">";
                                echo $lecturer['lecturer_no'] . " - " . $lecturer['full_name'];
                                echo "</option>";
                            }
                        }
                        ?>
                    </select>

                    <br><br>

                    <button type="submit" name="update_student_assignment">Save Assignment</button>
                    <a href="manage_student_assignments.php" style="margin-left: 10px;">Cancel</a>
                </form>
            </main>
        </div>
    </body>
</html>