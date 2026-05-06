<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

$upload_page = "../dashboards/upload_student_list.php";
$student_page = "../dashboards/student_management.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../public/login.php");
    exit;
}

if($_SERVER["REQUEST_METHOD"] != "POST") {
    $_SESSION['error'] = "Invalid request method.";
    header("Location: $upload_page");
    exit;
}

if(!isset($_POST['upload_student_list'])) {
    $_SESSION['error'] = "Form was not submitted properly.";
    header("Location: $upload_page");
    exit;
}

if(!isset($_FILES['student_file']) || $_FILES['student_file']['error'] != 0) {
    $_SESSION['error'] = "Please upload a valid CSV file.";
    header("Location: $upload_page");
    exit;
}

$file_name = $_FILES['student_file']['name'];
$file_tmp = $_FILES['student_file']['tmp_name'];
$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

if($file_ext != "csv") {
    $_SESSION['error'] = "Only CSV files are allowed.";
    header("Location: $upload_page");
    exit;
}

$file = fopen($file_tmp, "r");

if(!$file) {
    $_SESSION['error'] = "Failed to open uploaded file.";
    header("Location: $upload_page");
    exit;
}

/* Skip first row/header */
fgetcsv($file);

$added = 0;
$skipped = 0;
$failed = 0;

while(($data = fgetcsv($file)) !== FALSE) {

    if(count($data) < 4) {
        $failed++;
        continue;
    }

    $student_no = trim($data[0]);
    $full_name = trim($data[1]);
    $email = trim($data[2]);
    $program = trim($data[3]);

    if($student_no == "" || $full_name == "" || $email == "" || $program == "") {
        $failed++;
        continue;
    }

    /* Check duplicate in students table */
    $check_student_sql = "SELECT * FROM students WHERE student_no = '$student_no'";
    $check_student_result = mysqli_query($conn, $check_student_sql);

    if($check_student_result && mysqli_num_rows($check_student_result) > 0) {
        $skipped++;
        continue;
    }

    /* Insert into students table only */
    $student_sql = "INSERT INTO students (user_id, student_no, full_name, email, program, assigned_lecturer_id)
                    VALUES (NULL, '$student_no', '$full_name', '$email', '$program', NULL)";

    $student_result = mysqli_query($conn, $student_sql);

    if($student_result) {
        $added++;
    } else {
        $failed++;
    }
}

fclose($file);

$_SESSION['success'] = "Upload completed. Added: $added, Skipped: $skipped, Failed: $failed.";
header("Location: $student_page");
exit;
?>