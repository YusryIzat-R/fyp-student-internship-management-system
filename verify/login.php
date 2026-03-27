<?php
session_start();
require_once '../config/db.php';

/* Check for the form submitted */

if($_SERVER["REQUEST_METHOD"] != "POST") {
    echo "<p style='color:red;'> Invalid reuest </p>";
    echo "<a href='../public/login.php'>Back to Login Page</a>";
    exit;
}

/* Get the Form Data */

$role = $_POST["role"];
$user_id = $_POST["user_id"];
$password = $_POST["password"];

/* Check for empty fields */

if($role == "" || $user_id == "" || $password == "") {
    echo "<p style='color:red'> Please fill all of the fields first!!</p>";
    echo "<a href='../public/login.php'>Back to Login Again</a>";
    exit;
}

/* Check user existence in the database */

$sql = "SELECT * FROM users WHERE user_id ='$user_id' AND role='$role'";
$result = mysqli_query($conn, $sql);

/* error for no users found */
if(mysqli_num_rows($result) == 0) {
    echo "<p style='color:red;'> User does not exist! Please register if you haven't registered yet!</p>";
    echo "<a href='../public/login.php'>Login Again</a>";
    exit;
}

/* fetch user data */

$user = mysqli_fetch_assoc($result);

/* check password for the users */

if($password != $user["password"]) {
    echo "<p style='color:red;'> Incorrect Password! Please try again.</p>";
    echo "<a href='../public/login.php'>Login Again</a>";
    exit;
}

/* Successful login */

$_SESSION["user_id"] = $user["user_id"];
$_SESSION["role"] = $user["role"];
$_SESSION["name"] = $user["name"];

/* Page redirection based on the user role */
if($role == "student") {
    header("Location: ../dashboards/student_dashboard.php");
}
else if($role == "lecturer") {
    header("Location: ../dashboards/lecturer_dashboard.php");
} 
else {
    header("Location: ../dashboards/admin_dashboard.php");
}

exit;

?>