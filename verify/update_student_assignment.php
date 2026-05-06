<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

$back_page = "../dashboards/manage_student_assignments.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../public/login.php");
    exit;
}

if($_SERVER["REQUEST_METHOD"] != "POST") {
    $_SESSION['error'] = "Invalid request method.";
    header("Location: $back_page");
    exit;
}

if(!isset($_POST['update_student_assignment'])) {
    $_SESSION['error'] = "Form was not submitted properly.";
    header("Location: $back_page");
    exit;
}

$student_id = trim($_POST['student_id']);
$lecturer_id = trim($_POST['lecturer_id']);

if($student_id == "" || $lecturer_id == "") {
    $_SESSION['error'] = "Please select a lecturer first.";
    header("Location: $back_page");
    exit;
}

$student_check_sql = "SELECT * FROM students WHERE id = '$student_id'";
$student_check_result = mysqli_query($conn, $student_check_sql);

if(!$student_check_result || mysqli_num_rows($student_check_result) == 0) {
    $_SESSION['error'] = "Student not found.";
    header("Location: $back_page");
    exit;
}

$lecturer_check_sql = "SELECT * FROM lecturers WHERE id = '$lecturer_id'";
$lecturer_check_result = mysqli_query($conn, $lecturer_check_sql);

if(!$lecturer_check_result || mysqli_num_rows($lecturer_check_result) == 0) {
    $_SESSION['error'] = "Lecturer not found.";
    header("Location: $back_page");
    exit;
}

$sql = "UPDATE students 
        SET assigned_lecturer_id = '$lecturer_id' 
        WHERE id = '$student_id'";

$result = mysqli_query($conn, $sql);

if($result) {
    $_SESSION['success'] = "Lecturer assigned to student successfully.";
    header("Location: $back_page");
    exit;
} else {
    $_SESSION['error'] = "Failed to assign lecturer. Please try again later.";
    header("Location: $back_page");
    exit;
}
?>