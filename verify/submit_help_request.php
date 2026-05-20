<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

$help_page = "../dashboards/student_get_help.php";

/** Redirect page if session not belongs to students */
if(!isset($_SESSION['role']) || $_SESSION['role'] != "student") {
    header("Location: ../public/login.php");
    exit;
}

if($_SERVER["REQUEST_METHOD"] != "POST") {
    $_SESSION['error'] = "Invalid request method.";
    header("Location: $help_page");
    exit;
}

/** validatiion for error message if form not submitted properly */
if(!isset($_POST['submit_help_request'])) {
    $_SESSION['error'] = "Form was not submitted properly.";
    header("Location: $help_page");
    exit;
}

$student_no = $_SESSION['student_no'];
$category = trim($_POST['category']);
$message = trim($_POST['message']);

/** Error message/validation if fields not filled in */
if($category == "" || $message == "") {
    $_SESSION['error'] = "Please fill in all fields.";
    header("Location: $help_page");
    exit;
}

/** Insert data/complaints into database */
$sql = "INSERT INTO help_ticket 
        (student_id, category, message, status)
        VALUES 
        ('$student_no', '$category', '$message', 'submitted')";

$result = mysqli_query($conn, $sql);

if($result) {
    $_SESSION['success'] = "Help request submitted successfully.";
    header("Location: $help_page");
    exit;
} else {
    $_SESSION['error'] = "Failed to submit help request.";
    header("Location: $help_page");
    exit;
}
?>