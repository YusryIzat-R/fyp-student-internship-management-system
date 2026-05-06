<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

if(!isset($_SESSION['role']) || ($_SESSION['role'] != "admin" && $_SESSION['role'] != "lecturer")) {
    header("Location: ../public/login.php");
    exit;
}

$back_page = "../dashboards/lecturer_announcement.php";
if($_SESSION['role'] == "admin") {
    $back_page = "../dashboards/admin_announcement.php";
}

if(!isset($_GET['id'])) {
    $_SESSION['error'] = "Announcement ID not found.";
    header("Location: $back_page");
    exit;
}

$id = $_GET['id'];
$login_id = $_SESSION["login_id"];
$role = $_SESSION["role"];

if($role == "admin") {
    $sql = "DELETE FROM announcements WHERE announcement_id='$id'";
} else {
    $sql = "DELETE FROM announcements WHERE announcement_id='$id' AND posted_by='$login_id' AND role='lecturer'";
}

$result = mysqli_query($conn, $sql);

if($result && mysqli_affected_rows($conn) > 0) {
    $_SESSION['success'] = "Announcement deleted successfully.";
    header("Location: $back_page");
    exit;
} else {
    $_SESSION['error'] = "Failed to delete announcement.";
    header("Location: $back_page");
    exit;
}
?>