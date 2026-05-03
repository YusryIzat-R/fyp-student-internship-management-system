<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../public/login.php");
    exit;
}

if(!isset($_GET['id'])) {
    echo "<p style='color:red;'>Lecturer ID not found.</p>";
    echo "<a href='../dashboards/manage_lecturers.php'>Back to Manage Lecturers</a>";
    exit;
}

$lecturer_id = $_GET['id'];

/* Check lecturer exists */
$lecturer_sql = "SELECT * FROM lecturers WHERE id = '$lecturer_id'";
$lecturer_result = mysqli_query($conn, $lecturer_sql);

if(!$lecturer_result || mysqli_num_rows($lecturer_result) == 0) {
    echo "<p style='color:red;'>Lecturer not found.</p>";
    echo "<a href='../dashboards/manage_lecturers.php'>Back to Manage Lecturers</a>";
    exit;
}

$lecturer = mysqli_fetch_assoc($lecturer_result);
$user_id = $lecturer['user_id'];

/* Check if lecturer still has assigned students */
$student_check_sql = "SELECT * FROM students WHERE assigned_lecturer_id = '$lecturer_id'";
$student_check_result = mysqli_query($conn, $student_check_sql);

if($student_check_result && mysqli_num_rows($student_check_result) > 0) {
    echo "<p style='color:red;'>This lecturer cannot be deleted because students are still assigned to this lecturer.</p>";
    echo "<a href='../dashboards/manage_lecturers.php'>Back to Manage Lecturers</a>";
    exit;
}

/* Delete lecturer profile first */
$delete_lecturer_sql = "DELETE FROM lecturers WHERE id = '$lecturer_id'";
$delete_lecturer_result = mysqli_query($conn, $delete_lecturer_sql);

if(!$delete_lecturer_result) {
    echo "<p style='color:red;'>Failed to delete lecturer details.</p>";
    echo "<a href='../dashboards/manage_lecturers.php'>Back to Manage Lecturers</a>";
    exit;
}

/* Delete lecturer login account */
$delete_user_sql = "DELETE FROM users WHERE id = '$user_id'";
$delete_user_result = mysqli_query($conn, $delete_user_sql);

if($delete_user_result) {
    header("Location: ../dashboards/manage_lecturers.php");
    exit;
} else {
    echo "<p style='color:red;'>Lecturer details deleted, but failed to delete lecturer login account.</p>";
    echo "<a href='../dashboards/manage_lecturers.php'>Back to Manage Lecturers</a>";
    exit;
}
?>