<?php
session_start();
require_once '../config/db.php';

if(!isset($_SESSION['role']) || ($_SESSION['role'] != "admin" && $_SESSION['role'] != "lecturer")) {
    header("Location: ../public/login.php");
    exit;
}

$back_page = "../dashboards/lecturer_announcement.php";
if($_SESSION['role'] == "admin") {
    $back_page = "../dashboards/admin_announcement.php";
}

if(!isset($_GET['id'])) {
    echo "<p style='color:red;'>Announcement ID not found.</p>";
    echo "<a href='$back_page'>Back to Announcements</a>";
    exit;
}

$id = $_GET['id'];
$login_id = $_SESSION["login_id"];
$role = $_SESSION["role"];

/* Admin can delete any announcement, lecturer can only delete their own announcement */
if($role == "admin") {
    $sql = "DELETE FROM announcements WHERE announcement_id='$id'";
} else {
    $sql = "DELETE FROM announcements WHERE announcement_id='$id' AND posted_by='$login_id'";
}

$result = mysqli_query($conn, $sql);

if($result && mysqli_affected_rows($conn) > 0) {
    header("Location: $back_page");
    exit;
} else {
    echo "<p style='color:red;'>Failed to delete announcement. Please try again.</p>";
    echo "<a href='$back_page'>Back to Announcements</a>";
    exit;
}
?>
