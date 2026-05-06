<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

$back_page = "../dashboards/student_management.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../public/login.php");
    exit;
}

if(!isset($_GET['id']) || !isset($_GET['status'])) {
    $_SESSION['error'] = "Invalid request.";
    header("Location: $back_page");
    exit;
}

$user_id = $_GET['id'];
$status = $_GET['status'];

if($status != 0 && $status != 1) {
    $_SESSION['error'] = "Invalid status value.";
    header("Location: $back_page");
    exit;
}

$sql = "UPDATE users SET status = '$status' WHERE id = '$user_id'";
$result = mysqli_query($conn, $sql);

if($result) {
    $_SESSION['success'] = "Student status updated successfully.";
} else {
    $_SESSION['error'] = "Failed to update student status.";
}

header("Location: $back_page");
exit;
?>