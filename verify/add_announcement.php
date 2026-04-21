<?php
session_start();
require_once '../config/db.php';

if(!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'lecturer')) {
    header("Location: ../public/login.php");
    exit;
}

$back_page = "../dashboards/lecturer_announcement.php";
if($_SESSION['role'] == 'admin') {
    $back_page = "../dashboards/admin_announcement.php";
}

/* Check request method */
if($_SERVER["REQUEST_METHOD"] != "POST") {
    echo "<p style='color:red;'>Invalid request method.</p>";
    echo "<a href='$back_page'>Back to Announcements</a>";
    exit;
}

/* Check submitted data */
if(!isset($_POST["add_announcement"])) {
    echo "<p style='color:red;'>Form was not submitted properly.</p>";
    echo "<a href='$back_page'>Back to Announcements</a>";
    exit;
}

/* Get form data */
$title = trim($_POST["title"]);
$content = trim($_POST["content"]);
$posted_by = $_SESSION["login_id"];
$role = $_SESSION["role"];

/* Check empty fields */
if($title == "" || $content == "") {
    echo "<p style='color:red;'>Please fill in all fields!</p>";
    echo "<a href='$back_page'>Back to Announcements</a>";
    exit;
}

/* Insert into announcements table */
$sql = "INSERT INTO announcements (title, content, posted_by, role)
        VALUES ('$title', '$content', '$posted_by', '$role')";

$result = mysqli_query($conn, $sql);

if($result) {
    header("Location: $back_page");
    exit;
} else {
    echo "<p style='color:red;'>Failed to add announcement.</p>";
    echo "<a href='$back_page'>Back to Announcements</a>";
    exit;
}
?>