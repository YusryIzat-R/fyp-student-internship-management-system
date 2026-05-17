<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

$resource_page = "../dashboards/student_resource.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "student") {
    header("Location: ../public/login.php");
    exit;
}

if($_SERVER["REQUEST_METHOD"] != "POST") {
    $_SESSION['error'] = "Invalid request method. Please try again!";
    header("Location: $resource_page");
    exit;
}

if(!isset($_POST['upload_submission'])) {
    $_SESSION['error'] = "Form was not submitted properly.";
    header("Location: $resource_page");
    exit;
}

if(!isset($_POST['resource_id'])) {
    $_SESSION['error'] = "Resource ID not found.";
    header("Location: $resource_page");
    exit;
}

$resource_id = trim($_POST['resource_id']);
$submission_id = "";

if(isset($_POST['submission_id'])) {
    $submission_id = trim($_POST['submission_id']);
}

$student_no = $_SESSION['student_no'];

$back_page = "../dashboards/student_resource_submission.php?resource_id=$resource_id";

if(!isset($_FILES['submission_file']) || $_FILES['submission_file']['error'] != 0) {
    $_SESSION['error'] = "Please upload a valid file format!";
    header("Location: $back_page");
    exit;
}

$file_name = $_FILES['submission_file']['name'];
$file_tmp = $_FILES['submission_file']['tmp_name'];
$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

$allowed_ext = array("pdf", "doc", "docx", "ppt", "pptx");

if(!in_array($file_ext, $allowed_ext)) {
    $_SESSION['error'] = "Only PDF, Word, and PowerPoint files are allowed for submission.";
    header("Location: $back_page");
    exit;
}

/** Check student's existence */
$student_sql = "SELECT * FROM students WHERE student_no = '$student_no'";
$student_result = mysqli_query($conn, $student_sql);

if(!$student_result || mysqli_num_rows($student_result) == 0) {
    $_SESSION['error'] = "Student not found.";
    header("Location: $resource_page");
    exit;
}

/** Check resource's existence */
$resource_sql = "SELECT * FROM resources WHERE resource_id = '$resource_id'";
$resource_result = mysqli_query($conn, $resource_sql);

if(!$resource_result || mysqli_num_rows($resource_result) == 0) {
    $_SESSION['error'] = "Resource not found.";
    header("Location: $resource_page");
    exit;
}

$resource = mysqli_fetch_assoc($resource_result);
$submission_type = $resource['category'];

$upload_folder = "../uploads/submissions/";

if(!is_dir($upload_folder)) {
    mkdir($upload_folder, 0777, true);
}

$new_file_name = time() . "_" . $file_name;
$target_path = $upload_folder . $new_file_name;

if(!move_uploaded_file($file_tmp, $target_path)) {
    $_SESSION['error'] = "Failed to upload submission file. Please try again.";
    header("Location: $back_page");
    exit;
}

$file_path = "uploads/submissions/" . $new_file_name;

/* Reupload existing submission */
if($submission_id != "") {
    $check_sql = "SELECT * FROM submissions 
                  WHERE submission_id = '$submission_id' 
                  AND student_id = '$student_no' 
                  AND resource_id = '$resource_id'";

    $check_result = mysqli_query($conn, $check_sql);

    if(!$check_result || mysqli_num_rows($check_result) == 0) {
        $_SESSION['error'] = "No submission record found.";
        header("Location: $back_page");
        exit;
    }

    $old_submission = mysqli_fetch_assoc($check_result);
    $old_file_path = "../" . $old_submission['file_path'];

    if(file_exists($old_file_path)) {
        unlink($old_file_path);
    }

    $update_sql = "UPDATE submissions
                   SET file_path = '$file_path',
                       status = 'pending',
                       lecturer_comment = NULL,
                       updated_at = NOW()
                   WHERE submission_id = '$submission_id'
                   AND student_id = '$student_no'
                   AND resource_id = '$resource_id'";

    if(mysqli_query($conn, $update_sql)) {
        $_SESSION['success'] = "Submission reuploaded successfully!";
        header("Location: $back_page");
        exit;
    } else {
        $_SESSION['error'] = "Failed to update submission.";
        header("Location: $back_page");
        exit;
    }

} else {
    $insert_sql = "INSERT INTO submissions
                   (student_id, resource_id, submission_type, file_path, status, submitted_at)
                   VALUES 
                   ('$student_no', '$resource_id', '$submission_type', '$file_path', 'pending', NOW())";

    if(mysqli_query($conn, $insert_sql)) {
        $_SESSION['success'] = "Submission uploaded successfully.";
        header("Location: $back_page");
        exit;
    } else {
        $_SESSION['error'] = "Failed to save submission! Please try again.";
        header("Location: $back_page");
        exit;
    }
}
?>