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

/* Check if student exists in students table */
$student_check_sql = "SELECT * FROM students WHERE student_no = '$login_id'";
$student_check_result = mysqli_query($conn, $student_check_sql);

if(!$student_check_result || mysqli_num_rows($student_check_result) == 0) {
    $_SESSION['error'] = "Student not found in system. Please contact admin.";
    header("Location: ../public/register.php");
    exit();
}

$student_data = mysqli_fetch_assoc($student_check_result);

if($student_data['user_id'] != NULL) {
    $_SESSION['error'] = "This student number is already linked to an account.";
    header("Location: ../public/register.php");
    exit();
}

/* Check if user account already exists */
$sql = "SELECT * FROM users WHERE login_id = '$login_id'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $_SESSION['error'] = "This student number is already registered.";
    header("Location: ../public/register.php");
    exit();
}

/* Insert into users table */
$sql2 = "INSERT INTO users (login_id, password, role, status)
         VALUES ('$login_id', '$password', '$role', 1)";

if (mysqli_query($conn, $sql2)) {
    $user_id = mysqli_insert_id($conn);

    /* Link existing student record with new user account */
    $sql3 = "UPDATE students 
             SET user_id = '$user_id',
                 full_name = '$full_name',
                 email = '$email'
             WHERE student_no = '$login_id'";

    if (mysqli_query($conn, $sql3) && mysqli_affected_rows($conn) > 0) {
        $_SESSION['success'] = "Student registration successful. Please login.";
        header("Location: ../public/login.php");
        exit();
    } else {
        $_SESSION['error'] = "Error occurred while linking student account.";
        header("Location: ../public/register.php");
        exit();
    }

} else {
    $_SESSION['error'] = "Error occurred while registering student.";
    header("Location: ../public/register.php");
    exit();
}
?>