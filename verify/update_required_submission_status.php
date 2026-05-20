<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

$back_page = "../dashboards/admin_required_submissions.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../public/login.php");
    exit;
}

if(!isset($_GET['id']) || !isset($_GET['status'])) {
    $_SESSION['error'] = "Invalid request.";
    header("Location: $back_page");
    exit;
}

$requirement_id = $_GET['id'];
$status = $_GET['status'];

if($status != "active" && $status != "inactive") {
    $_SESSION['error'] = "Invalid status.";
    header("Location: $back_page");
    exit;
}

/** Update required submissions status in the database */
$sql = "UPDATE required_submissions
        SET status = '$status'
        WHERE requirement_id = '$requirement_id'";

$result = mysqli_query($conn, $sql);

/** Success or Error message for the updating status operation */
if($result) {
    $_SESSION['success'] = "Required submission status updated successfully.";
    header("Location: $back_page");
    exit;
} else {
    $_SESSION['error'] = "Failed to update status.";
    header("Location: $back_page");
    exit;
}
?>