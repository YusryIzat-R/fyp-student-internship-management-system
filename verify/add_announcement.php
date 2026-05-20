<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

if(!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'lecturer')) {
    header("Location: ../public/login.php");
    exit;
}

$back_page = "../dashboards/lecturer_announcement.php";
if($_SESSION['role'] == 'admin') {
    $back_page = "../dashboards/admin_announcement.php";
}

if($_SERVER["REQUEST_METHOD"] != "POST") {
    $_SESSION['error'] = "Invalid request method.";
    header("Location: $back_page");
    exit;
}

if(!isset($_POST["add_announcement"])) {
    $_SESSION['error'] = "Form was not submitted properly.";
    header("Location: $back_page");
    exit;
}

$title = mysqli_real_escape_string($conn, trim($_POST['title']));
$content = mysqli_real_escape_string($conn, trim($_POST['content']));
$posted_by = $_SESSION["login_id"];
$role = $_SESSION["role"];

if($title == "" || $content == "") {
    $_SESSION['error'] = "Please fill in all fields.";
    header("Location: $back_page");
    exit;
}

$sql = "INSERT INTO announcements (title, content, posted_by, role)
        VALUES ('$title', '$content', '$posted_by', '$role')";

$result = mysqli_query($conn, $sql);

if($result) {
    $_SESSION['success'] = "Announcement added successfully.";
    header("Location: $back_page");
    exit;
} else {
    $_SESSION['error'] = "Failed to add announcement.";
    header("Location: $back_page");
    exit;
}
?>