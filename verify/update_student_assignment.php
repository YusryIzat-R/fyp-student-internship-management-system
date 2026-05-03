<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../public/login.php");
    exit;
}

/* Check Request Method */
if($_SERVER["REQUEST_METHOD"] != "POST") {
    echo "<p style='color:red;'>Invalid request method.</p>";
    echo "<a href='../public/manage_student_assignments.php'>Back to Lecturer Assignments</a>";
    exit;
}

/* Check submit button */
if(!isset($_POST['update_student_assignment'])) {
    echo "<p style='color:red;'>Form was not submitted properly.</p>";
    echo "<a href='../dashboards/manage_student_assignments.php'>Back to Lecturer Assignments</a>";
    exit;
}

/* Get Form Data */
$student_id = trim($_POST['student_id']);
$lecturer_id = trim($_POST['lecturer_id']);

/* Check empty fields */
if($student_id == "" || $lecturer_id == "") {
    echo "<p style='color:red;'>Please select a lecturer first!</p>";
    echo "<a href='../dashboards/manage_student_assignments.php'>Back to Lecturer Assignments</a>";
    exit;
}

/* Check for student existence */
$student_check_sql = "SELECT * FROM students WHERE id = '$student_id'";
$student_check_result = mysqli_query($conn, $student_check_sql);

if(!$student_check_result || mysqli_num_rows($student_check_result) == 0) {
    echo "<p style='color:red;'>Student not found!</p>";
    echo "<a href='../dashboards/manage_student_assignments.php'>Back to Lecturer Assignments</a>";
    exit;
}

/* Check for lecturer existence */
$lecturer_check_sql = "SELECT * FROM lecturers WHERE id = '$lecturer_id'";
$lecturer_check_result = mysqli_query($conn, $lecturer_check_sql);

if(!$lecturer_check_result || mysqli_num_rows($lecturer_check_result) == 0) {
    echo "<p style='color:red;'>Lecturer not found!</p>";
    echo "<a href='../dashboards/manage_student_assignments.php'>Back to Lecturer Assignments</a>";
    exit;
}

/* Update the student assignment */
$sql = "UPDATE students SET assigned_lecturer_id = '$lecturer_id' WHERE id = '$student_id'";
$result = mysqli_query($conn, $sql);

if($result) {
    header("Location: ../dashboards/manage_student_assignments.php");
    exit;
} else {
    echo "<p style='color:red;'>Failed to assign lecturer. Please try again later.</p>";
    echo "<a href='../dashboards/manage_student_assignments.php'>Back to Lecturer Assignments</a>";
    exit;
}
?>