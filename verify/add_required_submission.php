<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

$back_page = "../dashboards/admin_required_submissions.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../public/login.php");
    exit;
}

if($_SERVER["REQUEST_METHOD"] != "POST") {
    $_SESSION['error'] = "Invalid request method.";
    header("Location: $back_page");
    exit;
}

/** Validate (error message) if form for required submissions not submitted properly */
if(!isset($_POST['add_required_submission'])) {
    $_SESSION['error'] = "Form was not submitted properly.";
    header("Location: $back_page");
    exit;
}

$submission_type = trim($_POST['submission_type']);
$description = trim($_POST['description']);

/** Validate if no submission type entered */
if($submission_type == "") {
    $_SESSION['error'] = "Please enter submission type.";
    header("Location: $back_page");
    exit;
}

/* Prevent duplicate required submission */
$check_sql = "SELECT * FROM required_submissions
              WHERE submission_type = '$submission_type'
              LIMIT 1";

$check_result = mysqli_query($conn, $check_sql);

if($check_result && mysqli_num_rows($check_result) > 0) {
    $_SESSION['error'] = "This submission requirement already exists.";
    header("Location: $back_page");
    exit;
}

$sql = "INSERT INTO required_submissions
        (submission_type, description, status)
        VALUES
        ('$submission_type', '$description', 'active')";

$result = mysqli_query($conn, $sql);

/** Success message and error message for the required submissions operation */
if($result) {
    $_SESSION['success'] = "Required submission added successfully.";
    header("Location: $back_page");
    exit;
} else {
    $_SESSION['error'] = "Failed to add required submission.";
    header("Location: $back_page");
    exit;
}
?>