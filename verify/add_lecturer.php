<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

$add_page = "../dashboards/add_lecturer.php";
$manage_page = "../dashboards/manage_lecturers.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../public/login.php");
    exit;
}

if($_SERVER["REQUEST_METHOD"] != "POST"){
    $_SESSION['error'] = "Invalid request method.";
    header("Location: $add_page");
    exit;
}

if(!isset($_POST["add_lecturer"])) {
    $_SESSION['error'] = "Form was not submitted properly.";
    header("Location: $add_page");
    exit;
}

$lecturer_no = trim($_POST["lecturer_no"]);
$full_name = trim($_POST["full_name"]);
$email = trim($_POST["email"]);
$password = trim($_POST["password"]);
$confirm_password = trim($_POST["confirm_password"]);

if($lecturer_no == "" || $full_name == "" || $email == "" || $password == "" || $confirm_password == ""){
    $_SESSION['error'] = "Please fill in all fields first.";
    header("Location: $add_page");
    exit;
}

if($password != $confirm_password) {
    $_SESSION['error'] = "Password and confirm password do not match.";
    header("Location: $add_page");
    exit;
}

$user_check_sql = "SELECT * FROM users WHERE login_id = '$lecturer_no'";
$user_check_result = mysqli_query($conn, $user_check_sql);

if($user_check_result && mysqli_num_rows($user_check_result) > 0) {
    $_SESSION['error'] = "This lecturer number already exists in the users table.";
    header("Location: $add_page");
    exit;
}

$lecturer_check_sql = "SELECT * FROM lecturers WHERE lecturer_no = '$lecturer_no'";
$lecturer_check_result = mysqli_query($conn, $lecturer_check_sql);

if($lecturer_check_result && mysqli_num_rows($lecturer_check_result) > 0) {
    $_SESSION['error'] = "This lecturer number already exists in the lecturers table.";
    header("Location: $add_page");
    exit;
}

$email_check_sql = "SELECT * FROM lecturers WHERE email = '$email'";
$email_check_result = mysqli_query($conn, $email_check_sql);

if($email_check_result && mysqli_num_rows($email_check_result) > 0) {
    $_SESSION['error'] = "This email is already used by another lecturer.";
    header("Location: $add_page");
    exit;
}

$user_sql = "INSERT INTO users (login_id, password, role, status) 
             VALUES ('$lecturer_no', '$password', 'lecturer', 1)";
$user_result = mysqli_query($conn, $user_sql);

if(!$user_result) {
    $_SESSION['error'] = "Failed to create lecturer account.";
    header("Location: $add_page");
    exit;
}

$user_id = mysqli_insert_id($conn);

$lecturer_sql = "INSERT INTO lecturers (user_id, lecturer_no, full_name, email) 
                 VALUES ('$user_id', '$lecturer_no', '$full_name', '$email')";
$lecturer_result = mysqli_query($conn, $lecturer_sql);

if($lecturer_result){
    $_SESSION['success'] = "Lecturer added successfully.";
    header("Location: $manage_page");
    exit;
} else {
    $delete_user_sql = "DELETE FROM users WHERE id = '$user_id'";
    mysqli_query($conn, $delete_user_sql);

    $_SESSION['error'] = "Failed to register new lecturer details.";
    header("Location: $add_page");
    exit;
}
?>