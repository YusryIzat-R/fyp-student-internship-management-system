<?php
session_start();

/* Check if user logged in as a student */

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'student') {
    header("Location: ../public/login.php");
    exit;
}

/* Get Session data */
$student_id = $_SESSION['user_id'];
$student_name = $_SESSION['name'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard - CCI IMS</title>
</head>

<body>
    <p>Welcome,<b><?php echo $student_name; ?></b></p>
    <p>ID: <b><?php echo $student_id; ?></b></p>

    <hr>

    <h3>Menu</h3>

    <ul>
        <li><a href="#">Home</a></li>
        <li><a href="#">Announcements</a></li>
        <li><a href="#">Resources</a></li>
        <li><a href="#">My Lecturer</a></li>
        <li><a href="#">Presentation</a></li>
        <li><a href="#">My Result</a></li>
        <li><a href="#">Get Help</a></li>
    </ul>

    <hr>
        <a href="../public/logout.php">
            <button style="padding:7px 12px; font-size:14px; color:red;">
                Logout
            </button>
        </a>
</body>
</html>