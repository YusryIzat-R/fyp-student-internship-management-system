<?php
session_start();

if(isset($_SESSION['role'])) {

    if($_SESSION['role'] == "student") {
        header("Location: ../dashboards/student_dashboard.php");
        exit;
    }
    if($_SESSION['role'] == "lecturer") {
        header("Location: ../dashboards/lecturer_dashboard.php");
        exit;
    }
    if($_SESSION['role'] == "admin") {
        header("Location: ../dashboards/admin_dashboard.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - CCI IMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assests/css/register.css">
</head>
<body>

    <div class="register-page-container">

        <div class="register-left-panel">
            <form method="POST" action="../verify/register.php">
                <label for="name">Enter Username:</label>
                <input type="text" id="name" name="name" placeholder="Enter your full name">

                <label for="user_id">Enter ID:</label>
                <input type="text" id="user_id" name="user_id" placeholder="Enter your ID">

                <label for="email">Enter Email:</label>
                <input type="email" id="email" name="email" placeholder="example@uniten.edu.my">

                <label for="password">Enter Password:</label>
                <input type="password" id="password" name="password" placeholder="Create a password">

                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter your password">

                <label for="role">Select Role:</label>
                <select name="role" id="role">
                    <option value="student">Student</option>
                    <option value="lecturer">Visiting Lecturer</option>
                </select>

                <div class="register-buttons">
                    <a href="login.php" class="back-login-btn">BACK TO LOGIN</a>
                    <button type="submit">REGISTER</button>
                </div>
            </form>
        </div>

        <div class="register-right-section">
            <div class="register-info-box">
                <h2>
                    CCIIMS<br>
                    CCI<br>
                    INTERNSHIP<br>
                    MANAGEMENT<br>
                    SYSTEM
                </h2>

                <p>
                    PLEASE ENTER YOUR DETAILS<br>
                    TO REGISTER
                </p>
            </div>
        </div>

    </div>

</body>
</html>