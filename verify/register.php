<?php
session_start();

require_once "../config/db.php";
/** @var mysqli $conn */

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    $_SESSION['error'] = "Invalid request. Please try again.";
    header("Location: ../public/register.php");
    exit();
}

$role = "student";
$login_id = trim($_POST["login_id"]);
$full_name = trim($_POST["full_name"]);
$email = trim($_POST["email"]);
$password = $_POST["password"];
$confirm_password = $_POST["confirm_password"];

if ($login_id == "" || $full_name == "" || $email == "" || $password == "" || $confirm_password == "") {
    $_SESSION['error'] = "Please fill in all the fields.";
    header("Location: ../public/register.php");
    exit();
}

if ($password != $confirm_password) {
    $_SESSION['error'] = "Passwords do not match.";
    header("Location: ../public/register.php");
    exit();
}

$sql = "SELECT * FROM users WHERE login_id = '$login_id'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $_SESSION['error'] = "This student number is already registered.";
    header("Location: ../public/register.php");
    exit();
}

$sql2 = "INSERT INTO users (login_id, password, role, status)
         VALUES ('$login_id', '$password', '$role', 1)";

if (mysqli_query($conn, $sql2)) {
    $user_id = mysqli_insert_id($conn);
    $student_no = $login_id;

    $sql3 = "INSERT INTO students (user_id, student_no, full_name, email, program, assigned_lecturer_id)
             VALUES ('$user_id', '$student_no', '$full_name', '$email', NULL, NULL)";

    if (mysqli_query($conn, $sql3)) {
        $_SESSION['success'] = "Student registration successful. Please login.";
        header("Location: ../public/login.php");
        exit();
    } else {
        $_SESSION['error'] = "Error occurred while saving student details.";
        header("Location: ../public/register.php");
        exit();
    }

} else {
    $_SESSION['error'] = "Error occurred while registering student.";
    header("Location: ../public/register.php");
    exit();
}
?>