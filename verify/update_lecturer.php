<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

$manage_page = "../dashboards/manage_lecturers.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../public/login.php");
    exit;
}

if($_SERVER["REQUEST_METHOD"] != "POST") {
    $_SESSION['error'] = "Invalid request method.";
    header("Location: $manage_page");
    exit;
} 

if(!isset($_POST["update_lecturer"])) {
    $_SESSION['error'] = "Form not submitted properly.";
    header("Location: $manage_page");
    exit;
}

$lecturer_id = trim($_POST["lecturer_id"]);
$user_id = trim($_POST["user_id"]);
$lecturer_no = trim($_POST["lecturer_no"]);
$full_name = trim($_POST["full_name"]);
$email = trim($_POST["email"]);
$department = trim($_POST["department"]);

if($lecturer_id == "" || $user_id == "" || $lecturer_no == "" || $full_name == "" || $email == "") {
    $_SESSION['error'] = "Please fill all required fields.";
    header("Location: $manage_page");
    exit;
}

$check_sql = "SELECT * FROM lecturers 
              WHERE lecturer_no = '$lecturer_no' 
              AND id != '$lecturer_id'";
$check_result = mysqli_query($conn, $check_sql);

if($check_result && mysqli_num_rows($check_result) > 0) {
    $_SESSION['error'] = "Lecturer number already exists.";
    header("Location: $manage_page");
    exit;
}

$email_check_sql = "SELECT * FROM lecturers 
                    WHERE email = '$email' 
                    AND id != '$lecturer_id'";
$email_check_result = mysqli_query($conn, $email_check_sql);

if($email_check_result && mysqli_num_rows($email_check_result) > 0) {
    $_SESSION['error'] = "Email already used.";
    header("Location: $manage_page");
    exit;
}

$update_lecturer_sql = "UPDATE lecturers 
                        SET lecturer_no = '$lecturer_no',
                            full_name = '$full_name',
                            email = '$email',
                            department = '$department'
                        WHERE id = '$lecturer_id'";

$lecturer_result = mysqli_query($conn, $update_lecturer_sql);

if(!$lecturer_result) {
    $_SESSION['error'] = "Failed to update lecturer details.";
    header("Location: $manage_page");
    exit;
}

$update_user_sql = "UPDATE users 
                    SET login_id = '$lecturer_no'
                    WHERE id = '$user_id'";

$user_result = mysqli_query($conn, $update_user_sql);

if(!$user_result) {
    $_SESSION['error'] = "Failed to update login ID.";
    header("Location: $manage_page");
    exit;
}

$_SESSION['success'] = "Lecturer updated successfully.";
header("Location: $manage_page");
exit;
?>