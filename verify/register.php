<?php

require_once "../config/db.php";

/* Check request methods */
if($_SERVER["REQUEST_METHOD"] != "POST") {
    echo"<h3 style='color: red;'>Invalid request, Please try again later.</h3>";
    echo"<a href='../public/register.php'>Back to Register</a>";
    exit;
}

/* Get Form Data */
$role = $_POST["role"];
$user_id = $_POST["user_id"];
$name = $_POST["name"];
$email = $_POST["email"];
$password = $_POST["password"];
$confirm_password = $_POST["confirm_password"];

/* Check for role selected */
if($role != "student" && $role != "lecturer") {
    echo"<h3 style='color: red;'>Invalid role selected!!!</h3>";
    echo"<a href='../public/register.php'>Back to Register</a>";
    exit;
}

/* Error for empty fields */
if($user_id == "" || $name == "" || $email == "" || $password == "" || $confirm_password == "") {
    echo"<h3 style='color: red;'>Please fill in all the fields!!!</h3>";
    echo"<a href='../public/register.php'>Back to Register</a>";
    exit;
}

/* Check if password and confirm password match */
if($password != $confirm_password) {
    echo"<h3 style='color: red;'>Passwords do not match!!!</h3>";
    echo"<a href='../public/register.php'>Back to Register</a>";
    exit;
}

/* Check for existing user ID in the database */
$sql = "SELECT * FROM users WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) > 0) {
    echo"<h3 style='color: red;'>User ID already exists!!!</h3>";
    echo"<a href='../public/register.php'>Back to Register</a>";
    exit;
}

/* Check for existing email in the database */
$sql2 = "SELECT * FROM users WHERE email = '$email'";
$result2 = mysqli_query($conn, $sql2);

if(mysqli_num_rows($result) > 0) {
    echo"<h3 style='color: red;'>Email already exists!!!</h3>";
    echo"<a href='../public/register.php'>Back to Register</a>";
    exit;
}

/* Insert new user into the database */
$sql3 = "INSERT INTO users (user_id, role, name, email, password, created_at)
VALUES ('$user_id', '$role', '$name', '$email', '$password', NOW())";

if(mysqli_query($conn, $sql3)) {
    echo"<h3 style='color: green;'>Registration successful!!!</h3>";
    echo"<a href='../public/login.php'>Go to Login</a>";
} else {
    echo"<h3 style='color: red;'>Error occurred while registering!!!</h3>";
    echo"<a href='../public/register.php'>Back to Register</a>";
}

?>