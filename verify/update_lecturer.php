<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../public/login.php");
    exit;
}

/* Check for rqueset method */
if($_SERVER["REQUEST_METHOD"] != "POST") {
    echo "<p style='color:red;'>Invalid request method.</p>";
    echo "<a href='../dashboards/manage_lecturers.php'>Back</a>";
    exit;
} 

/* Check submit */
if(!isset($_POST["update_lecturer"])) {
    echo "<p style='color:red;'>Form not submitted properly.</p>";
    echo "<a href='../dashboards/manage_lecturers.php'>Back</a>";
    exit;
}

/* Get form data */
$lecturer_id = trim($_POST["lecturer_id"]);
$user_id = trim($_POST["user_id"]);
$lecturer_no = trim($_POST["lecturer_no"]);
$full_name = trim($_POST["full_name"]);
$email = trim($_POST["email"]);
$department = trim($_POST["department"]);

/* Check empty fields */
if($lecturer_id == "" || $user_id == "" || $lecturer_no == "" || $full_name == "" || $email == "") {
    echo "<p style='color:red;'>Please fill all required fields.</p>";
    echo "<a href='../dashboards/manage_lecturers.php'>Back</a>";
    exit;
}

/* Check duplicate lecturer_no (exclude current record) */
$check_sql = "SELECT * FROM lecturers 
              WHERE lecturer_no = '$lecturer_no' 
              AND id != '$lecturer_id'";
$check_result = mysqli_query($conn, $check_sql);

if($check_result && mysqli_num_rows($check_result) > 0) {
    echo "<p style='color:red;'>Lecturer number already exists.</p>";
    echo "<a href='../dashboards/manage_lecturers.php'>Back</a>";
    exit;
}

/* Check duplicate email */
$email_check_sql = "SELECT * FROM lecturers 
                    WHERE email = '$email' 
                    AND id != '$lecturer_id'";
$email_check_result = mysqli_query($conn, $email_check_sql);

if($email_check_result && mysqli_num_rows($email_check_result) > 0) {
    echo "<p style='color:red;'>Email already used.</p>";
    echo "<a href='../dashboards/manage_lecturers.php'>Back</a>";
    exit;
}

/* Update lecturers table */
$update_lecturer_sql = "UPDATE lecturers 
                        SET lecturer_no = '$lecturer_no',
                            full_name = '$full_name',
                            email = '$email',
                            department = '$department'
                        WHERE id = '$lecturer_id'";

$lecturer_result = mysqli_query($conn, $update_lecturer_sql);

if(!$lecturer_result) {
    echo "<p style='color:red;'>Failed to update lecturer details.</p>";
    echo "<a href='../dashboards/manage_lecturers.php'>Back</a>";
    exit;
}

/* Update users table (IMPORTANT) */
$update_user_sql = "UPDATE users 
                    SET login_id = '$lecturer_no'
                    WHERE id = '$user_id'";

$user_result = mysqli_query($conn, $update_user_sql);

if(!$user_result) {
    echo "<p style='color:red;'>Failed to update login ID.</p>";
    echo "<a href='../dashboards/manage_lecturers.php'>Back</a>";
    exit;
}

/* Success */
header("Location: ../dashboards/manage_lecturers.php");
exit;
?>