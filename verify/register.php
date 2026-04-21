<?php

require_once "../config/db.php";

/* Check request methods */
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo "<h3 style='color: red;'>Invalid request, Please try again later.</h3>";
    echo "<a href='../public/register.php'>Back to Register</a>";
    exit;
}

/* Get Form Data */
$role = $_POST["role"];
$login_id = trim($_POST["login_id"]);
$full_name = trim($_POST["full_name"]);
$email = trim($_POST["email"]);
$password = $_POST["password"];
$confirm_password = $_POST["confirm_password"];

/* Check for role selected */
if ($role != "student" && $role != "lecturer") {
    echo "<h3 style='color: red;'>Invalid role selected!!!</h3>";
    echo "<a href='../public/register.php'>Back to Register</a>";
    exit;
}

/* Error for empty fields */
if ($login_id == "" || $full_name == "" || $email == "" || $password == "" || $confirm_password == "") {
    echo "<h3 style='color: red;'>Please fill in all the fields!!!</h3>";
    echo "<a href='../public/register.php'>Back to Register</a>";
    exit;
}

/* Check if password and confirm password match */
if ($password != $confirm_password) {
    echo "<h3 style='color: red;'>Passwords do not match!!!</h3>";
    echo "<a href='../public/register.php'>Back to Register</a>";
    exit;
}

/* Check for existing user ID in the database */
$sql = "SELECT * FROM users WHERE login_id = '$login_id'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    echo "<h3 style='color: red;'>This ID is already registered!!!</h3>";
    echo "<a href='../public/register.php'>Back to Register</a>";
    exit;
}

/* Insert into users table first */
$sql2 = "INSERT INTO users (login_id, password, role, status)
         VALUES ('$login_id', '$password', '$role', 1)";

if (mysqli_query($conn, $sql2)) {
    $user_id = mysqli_insert_id($conn);

    /* If role is student, insert into students table */
    if ($role == "student") {
        $student_no = $login_id;

        $sql3 = "INSERT INTO students (user_id, student_no, full_name, email, program, assigned_lecturer_id)
                 VALUES ('$user_id', '$student_no', '$full_name', '$email', NULL, NULL)";

        if (mysqli_query($conn, $sql3)) {
            echo "<h3 style='color: green;'>Student Registration successful!!!</h3>";
            echo "<a href='../public/login.php'>Go to Login</a>";
        } else {
            echo "<h3 style='color: red;'>Error occured while saving student details!</h3>";
            echo "<a href='../public/register.php'>Back to Register</a>";
        }
    }

    /* If role is lecturer, insert into lecturers table */
    if ($role == "lecturer") {
        $lecturer_no = $login_id;

        $sql4 = "INSERT INTO lecturers (user_id, lecturer_no, full_name, email, department)
                 VALUES ('$user_id', '$lecturer_no', '$full_name', '$email', NULL)";

        if (mysqli_query($conn, $sql4)) {
            echo "<h3 style='color: green;'>Lecturer Registration successful!!!</h3>";
            echo "<a href='../public/login.php'>Go to Login</a>";
        } else {
            echo "<h3 style='color: red;'>Error occured while saving lecturer details!</h3>";
            echo "<a href='../public/register.php'>Back to Register</a>";
        }
    }

} else {
    echo "<h3 style='color: red;'>Error occured while registering user!</h3>";
    echo "<a href='../public/register.php'>Back to Register</a>";
}

?>