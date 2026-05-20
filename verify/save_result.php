<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

$grading_page = "../dashboards/lecturer_grading.php";

/** make sure the session belongs to a lecturer */
if(!isset($_SESSION['role']) || $_SESSION['role'] != "lecturer") {
    header("Location: ../public/login.php");
    exit;
}

if($_SERVER["REQUEST_METHOD"] != "POST") {
    $_SESSION['error'] = "Invalid request method.";
    header("Location: $grading_page");
    exit;
}

/** Validate if the form was submitted properly */
if(!isset($_POST['save_result'])) {
    $_SESSION['error'] = "Form was not submitted properly.";
    header("Location: $grading_page");
    exit;
}

$student_no = trim($_POST['student_no']);
$grade = trim($_POST['grade']);
$feedback = trim($_POST['feedback']);

$lecturer_no = $_SESSION['login_id'];

$back_page = "../dashboards/grade_student.php?student_no=$student_no";

/** Validate input for result (pass or fail) */
if($student_no == "" || $grade == "") {
    $_SESSION['error'] = "Please select result.";
    header("Location: $back_page");
    exit;
}

/** Validate the selected grade */
if($grade != "Pass" && $grade != "Fail") {
    $_SESSION['error'] = "Invalid result selected.";
    header("Location: $back_page");
    exit;
}

/* Check lecturer record */
$lecturer_sql = "SELECT * FROM lecturers WHERE lecturer_no = '$lecturer_no' LIMIT 1";
$lecturer_result = mysqli_query($conn, $lecturer_sql);

if(!$lecturer_result || mysqli_num_rows($lecturer_result) == 0) {
    $_SESSION['error'] = "Lecturer record not found.";
    header("Location: $grading_page");
    exit;
}

$lecturer = mysqli_fetch_assoc($lecturer_result);
$lecturer_id = $lecturer['id'];

/* Check student belongs to this lecturer */
$student_sql = "SELECT * FROM students 
                WHERE student_no = '$student_no'
                AND assigned_lecturer_id = '$lecturer_id'
                LIMIT 1";

$student_result = mysqli_query($conn, $student_sql);

if(!$student_result || mysqli_num_rows($student_result) == 0) {
    $_SESSION['error'] = "Student not found or not assigned to you.";
    header("Location: $grading_page");
    exit;
}

/* Check existing result */
$check_sql = "SELECT * FROM results
              WHERE student_id = '$student_no'
              AND lecturer_id = '$lecturer_no'
              LIMIT 1";

$check_result = mysqli_query($conn, $check_sql);

if($check_result && mysqli_num_rows($check_result) > 0) {

    $update_sql = "UPDATE results
                   SET grade = '$grade',
                       feedback = '$feedback',
                       released_at = NOW()
                   WHERE student_id = '$student_no'
                   AND lecturer_id = '$lecturer_no'";

    if(mysqli_query($conn, $update_sql)) {
        $_SESSION['success'] = "Result updated successfully.";
        header("Location: $back_page");
        exit;
    } else {
        $_SESSION['error'] = "Failed to update result.";
        header("Location: $back_page");
        exit;
    }

} else {

    $insert_sql = "INSERT INTO results (student_id, lecturer_id, grade, feedback, released_at)
                   VALUES ('$student_no', '$lecturer_no', '$grade', '$feedback', NOW())";

    if(mysqli_query($conn, $insert_sql)) {
        $_SESSION['success'] = "Result saved successfully.";
        header("Location: $back_page");
        exit;
    } else {
        $_SESSION['error'] = "Failed to save result.";
        header("Location: $back_page");
        exit;
    }
}
?>