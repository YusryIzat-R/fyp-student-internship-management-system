<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

$manage_page = "../dashboards/manage_lecturers.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../public/login.php");
    exit;
}

if(!isset($_GET['id'])) {
    $_SESSION['error'] = "Lecturer ID not found.";
    header("Location: $manage_page");
    exit;
}

$lecturer_id = $_GET['id'];

$lecturer_sql = "SELECT * FROM lecturers WHERE id = '$lecturer_id'";
$lecturer_result = mysqli_query($conn, $lecturer_sql);

if(!$lecturer_result || mysqli_num_rows($lecturer_result) == 0) {
    $_SESSION['error'] = "Lecturer not found.";
    header("Location: $manage_page");
    exit;
}

$lecturer = mysqli_fetch_assoc($lecturer_result);
$user_id = $lecturer['user_id'];

$student_check_sql = "SELECT * FROM students WHERE assigned_lecturer_id = '$lecturer_id'";
$student_check_result = mysqli_query($conn, $student_check_sql);

if($student_check_result && mysqli_num_rows($student_check_result) > 0) {
    $_SESSION['error'] = "This lecturer cannot be deleted because students are still assigned to this lecturer.";
    header("Location: $manage_page");
    exit;
}

$delete_lecturer_sql = "DELETE FROM lecturers WHERE id = '$lecturer_id'";
$delete_lecturer_result = mysqli_query($conn, $delete_lecturer_sql);

if(!$delete_lecturer_result) {
    $_SESSION['error'] = "Failed to delete lecturer details.";
    header("Location: $manage_page");
    exit;
}

$delete_user_sql = "DELETE FROM users WHERE id = '$user_id'";
$delete_user_result = mysqli_query($conn, $delete_user_sql);

if($delete_user_result) {
    $_SESSION['success'] = "Lecturer deleted successfully.";
    header("Location: $manage_page");
    exit;
} else {
    $_SESSION['error'] = "Lecturer details deleted, but failed to delete lecturer login account.";
    header("Location: $manage_page");
    exit;
}
?>