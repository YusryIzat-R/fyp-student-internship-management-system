<?php
session_start();
require_once '../config/db.php';

/* Check for the form submitted */ 
if($_SERVER["REQUEST_METHOD"] != "POST") {
    echo "<p style='color:red;'>Invalid request method.</p>";
    echo "<a href='../public/login.php'>Back to Login</a>";
    exit;
}

/* Get The Form Data*/
$role = trim($_POST['role']);
$login_id = trim($_POST['login_id']);
$password = trim($_POST['password']);

/* Check for the fields*/
if($role == "" || $login_id == "" || $password == "") {
    echo "<p style='color:red;'>Please fill in all fields first!</p>";
    echo "<a href='../public/login.php'>Back to Login</a>";
    exit;
} 

/* Check users existence in the database */
$sql = "SELECT * FROM users WHERE login_id = '$login_id' AND role = '$role'";
$result = mysqli_query($conn, $sql);

/* Error if no user found */
if(mysqli_num_rows($result) == 0) {
    echo "<p style='color:red;'>User does not exist or wrong role selected!</p>";
    echo "<a href='../public/login.php'>Login Again</a>";
    exit;
}

$user = mysqli_fetch_assoc($result);

/*check account status*/
if($user['status'] != 1) {
    echo "<p style='color:red;'>Your account is inactive. Please contact admin.</p>";
    echo "<a href='../public/login.php'>Back to Login Page</a>";
    exit;
}

/* check pasword */
if($password != $user['password']) {
    echo "<p style='color:red;'>Incorrect password!</p>";
    echo "<a href='../public/login.php'>Login Again</a>";
    exit;
}

/* Successful login */
$_SESSION["user_id"] = $user['id'];
$_SESSION["login_id"] = $user['login_id'];
$_SESSION["role"] = $user['role'];

/* Get full name and other details based on the user role */
if($role == "student") {

    $student_sql = "SELECT * FROM students WHERE user_id = '{$user['id']}'";
    $student_result = mysqli_query($conn, $student_sql);

    if(mysqli_num_rows($student_result) > 0) {
        $student = mysqli_fetch_assoc($student_result);
        
        $_SESSION["full_name"] = $student['full_name'];
        $_SESSION["email"] = $student["email"];
        $_SESSION["student_no"] = $student["student_no"];
        $_SESSION["assigned_lecturer_id"] = $student["assigned_lecturer_id"];
    }

    header("Location: ../dashboards/student_dashboard.php");
    exit;

} else if($role == "lecturer") {

    $lecturer_sql = "SELECT * FROM lecturers WHERE user_id = '{$user['id']}'";
    $lecturer_result = mysqli_query($conn, $lecturer_sql);

    if(mysqli_num_rows($lecturer_result) > 0) {
        $lecturer = mysqli_fetch_assoc($lecturer_result);

        $_SESSION["full_name"] = $lecturer["full_name"];
        $_SESSION["email"] = $lecturer["email"];
        $_SESSION["lecturer_no"] = $lecturer["lecturer_no"];
    }

    header("Location: ../dashboards/lecturer_dashboard.php");
    exit;
  
} else if($role == "admin") {

    $admin_sql = "SELECT * FROM admins WHERE user_id = '{$user['id']}'";
    $admin_result = mysqli_query($conn, $admin_sql);

    if(mysqli_num_rows($admin_result) > 0) {
        $admin = mysqli_fetch_assoc($admin_result);

        $_SESSION["full_name"] = $admin["full_name"];
        $_SESSION["email"] = $admin["email"];
    }

    header("Location: ../dashboards/admin_dashboard.php");
    exit;
}
else {
    echo "<p style='color:red;'>Invalid role.</p>";
    echo "<a href='../public/login.php'>Back to Login Page</a>";
    exit;
}
?>
