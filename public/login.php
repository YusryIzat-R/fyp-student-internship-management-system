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
        <title>Login - CCI IMS</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../assets/css/login.css">
    </head>
    <body>
        <div class="login-page-container">
            <div class="login-left-panel">
                <h1>WELCOME TO CCI-IMS!</h1>
                <h3>User Login</h3>
                <p>Enter your details to login</p>


                <form method="POST" action="../verify/login.php">

                    <?php if(isset($_SESSION['error'])) { ?>
                        <div class="alert error">
                            <?php 
                                echo $_SESSION['error']; 
                                unset($_SESSION['error']);
                            ?>
                        </div>
                    <?php } ?>

                    <?php if(isset($_SESSION['success'])) { ?>
                        <div class="alert success">
                            <?php 
                                echo $_SESSION['success']; 
                                unset($_SESSION['success']);
                            ?>
                        </div>
                    <?php } ?>

                    <label for="role">Login as:</label>
                    <select name="role" id="role" required>
                        <option>-- Select a role --</option>
                        <option value="student">Student</option>
                        <option value="lecturer">Visiting Lecturer</option>
                        <option value="admin">Admin</option>
                    </select>

                    <br><br>

                    <label for="login_id">Login ID:</label>
                    <input type="text" id="login_id" name="login_id" required placeholder="Enter your login ID">
                    <br><br>

                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required placeholder="Please enter your password">
                    <br><br>

                    <div class="login-buttons">
                        <button type="submit">LOGIN</button>
                        <a href="index.php" class="back-btn">BACK</a>
                    </div>
                </form>
            </div>
            <div class="login-right-panel">
            </div>
        </div>
    </body>
</html>