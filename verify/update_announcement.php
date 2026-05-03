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
    echo "<p style='color:red;'>Invalid request method.</p>";
    echo "<a href='$back_page'>Back to Announcements</a>";
    exit;
}

if(!isset($_POST["update_announcement"])) {
    echo "<p style='color:red;'>Form was not submitted properly!</p>";
    echo "<a href='$back_page'>Back to Announcements</a>";
    exit;
}

$announcement_id = $_POST["announcement_id"];
$title = trim($_POST["title"]);
$content = trim($_POST["content"]);
$login_id = $_SESSION["login_id"];
$role = $_SESSION["role"];

if($announcement_id == "" || $title == "" || $content == "") {
    echo "<p style='color:red;'>Please fill in all fields!</p>";
    echo "<a href='$back_page'>Back to Announcements</a>";
    exit;
}

if($role == "admin") {
    $sql = "UPDATE announcements SET title='$title', content='$content' WHERE announcement_id='$announcement_id'";
} else {
    $sql = "UPDATE announcements SET title='$title', content='$content' WHERE announcement_id='$announcement_id' AND posted_by='$login_id'";
}

$result = mysqli_query($conn, $sql);

if($result && mysqli_affected_rows($conn) > 0) {
    header("Location: $back_page");
    exit;
} else {
    echo "<p style='color:red;'>Failed to update announcement / you are not the owner of this announcement.</p>";
    echo "<a href='$back_page'>Back to Announcements</a>";
    exit;
}
?>