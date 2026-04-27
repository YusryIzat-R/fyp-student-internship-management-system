<?php
session_start();
require_once '../config/db.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../public/login.php");
    exit;
}

/* Get request method */
if($_SERVER["REQUEST_METHOD"] != "POST"){
    echo "<p style='color:red;'>Invalid request method.</p>";
    echo "<a href='../dashboards/add_lecturer.php'>Back to Add Lecturer</a>";
    exit;
}

/* Check if form is submitted properly */
if(!isset($_POST["add_lecturer"])) {
    echo "<p style='color:red;'>Form was not submitted properly.</p>";
    echo "<a href='../dashboards/add_lecturer.php'>Back to Add Lecturer</a>";
    exit;
}

/* Get form data */
$lecturer_no = trim($_POST["lecturer_no"]);
$full_name = trim($_POST["full_name"]);
$email = trim($_POST["email"]);
$password = trim($_POST["password"]);
$confirm_password = trim($_POST["confirm_password"]);

/* Check for empty fields */
if($lecturer_no == "" || $full_name == "" || $email == "" || $password == "" || $confirm_password == ""){
    echo "<p style='color:red;'>Please fill in all fields first!!!</p>";
    echo "<a href='../dashboards/add_lecturer.php'>Back to Add Lecturer</a>";
    exit;
}

/* Check password match */
if($password != $confirm_password) {
    echo "<p style='color:red;'>Password and confirm password do not match.</p>";
    echo "<a href='../dashboards/add_lecturer.php'>Back to Add Lecturer</a>";
    exit;
}

/* Check if lecturer_no already exists in users table */
$user_check_sql = "SELECT * FROM users WHERE login_id = '$lecturer_no'";
$user_check_result = mysqli_query($conn, $user_check_sql);

if($user_check_result && mysqli_num_rows($user_check_result) > 0) {
    echo "<p style='color:red;'>This lecturer number already exists in the users table.</p>";
    echo "<a href='../dashboards/add_lecturer.php'>Back to Add Lecturer</a>";
    exit;
}

/* Check if lecturer_no already exists in lecturers table */
$lecturer_check_sql = "SELECT * FROM lecturers WHERE lecturer_no = '$lecturer_no'";
$lecturer_check_result = mysqli_query($conn, $lecturer_check_sql);

if($lecturer_check_result && mysqli_num_rows($lecturer_check_result) > 0) {
    echo "<p style='color:red;'>This lecturer number already exists in the lecturers table.</p>";
    echo "<a href='../dashboards/add_lecturer.php'>Back to Add Lecturer</a>";
    exit;
}

/* Check if email already exists in lecturers table */
$email_check_sql = "SELECT * FROM lecturers WHERE email = '$email'";
$email_check_result = mysqli_query($conn, $email_check_sql);

if($email_check_result && mysqli_num_rows($email_check_result) > 0) {
    echo "<p style='color:red;'>This email is already used by another lecturer.</p>";
    echo "<a href='../dashboards/add_lecturer.php'>Back to Add Lecturer</a>";
    exit;
}

/* Insert into users table first */
$user_sql = "INSERT INTO users (login_id, password, role, status) 
             VALUES ('$lecturer_no', '$password', 'lecturer', 1)";
$user_result = mysqli_query($conn, $user_sql);

if(!$user_result) {
    echo "<p style='color:red;'>Failed to create lecturer account.</p>";
    echo "<a href='../dashboards/add_lecturer.php'>Back to Add Lecturer</a>";
    exit;
}

/* Get the inserted user id */
$user_id = mysqli_insert_id($conn);

/* Insert into lecturers table */
$lecturer_sql = "INSERT INTO lecturers (user_id, lecturer_no, full_name, email) 
                 VALUES ('$user_id', '$lecturer_no', '$full_name', '$email')";
$lecturer_result = mysqli_query($conn, $lecturer_sql);

if($lecturer_result){
    header("Location: ../dashboards/manage_lecturers.php");
    exit;
} else {
    /* Delete the user account if lecturer details failed to register */
    $delete_user_sql = "DELETE FROM users WHERE id = '$user_id'";
    mysqli_query($conn, $delete_user_sql);

    echo "<p style='color:red;'>Failed to register new lecturer details.</p>";
    echo "<a href='../dashboards/add_lecturer.php'>Back to Add Lecturer</a>";
    exit;
}
?>