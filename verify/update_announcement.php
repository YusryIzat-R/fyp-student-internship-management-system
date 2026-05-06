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

if($_SERVER['REQUEST_METHOD'] != "POST") {
    $_SESSION['error'] = "Invalid request method.";
    header("Location: $back_page");
    exit;
}

if(!isset($_POST["update_announcement"])) {
    $_SESSION['error'] = "Form was not submitted properly.";
    header("Location: $back_page");
    exit;
}

$announcement_id = $_POST["announcement_id"];
$title = trim($_POST["title"]);
$content = trim($_POST["content"]);
$login_id = $_SESSION["login_id"];
$role = $_SESSION["role"];

if($announcement_id == "" || $title == "" || $content == "") {
    $_SESSION['error'] = "Please fill in all fields.";
    header("Location: $back_page");
    exit;
}

if($role == "admin") {
    $sql = "UPDATE announcements 
            SET title='$title', content='$content' 
            WHERE announcement_id='$announcement_id'";
} else {
    $sql = "UPDATE announcements 
            SET title='$title', content='$content' 
            WHERE announcement_id='$announcement_id' 
            AND posted_by='$login_id' 
            AND role='lecturer'";
}

$result = mysqli_query($conn, $sql);

if($result && mysqli_affected_rows($conn) > 0) {
    $_SESSION['success'] = "Announcement updated successfully.";
    header("Location: $back_page");
    exit;
} else {
    $_SESSION['error'] = "Failed to update announcement or no changes were made.";
    header("Location: $back_page");
    exit;
}
?>