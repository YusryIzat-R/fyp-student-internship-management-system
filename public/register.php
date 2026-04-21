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
    if($_SESSION['role'] == "admins") {
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
        <link rel="stylesheet" href="../assets/css/register.css">
    </head>
<body>

    <div class="register-page-container">
        <div class="register-left-panel">
            <form method="POST" action="../verify/register.php">
                <label for="full_name">Enter your Full Name:</label>
                <input type="text" id="full_name" name="full_name" placeholder="Enter your full name" required>

                <label for="role">Select Role:</label>
                <select name="role" id="role" required>
                    <option value="student">Student</option>
                    <option value="lecturer">Lecturer</option>
                </select>

                <label for="login_id" id="id_label">Enter Student No:</label>
                <input type="text" id="login_id" name="login_id" placeholder="Enter your student number" required>

                <label for="email">Enter your Email:</label>
                <input type="email" id="email" name="email" placeholder="Example@uniten.edu.my" required>

                <label for="password">Create Your Password:</label>
                <input type="password" id="password" name="password" placeholder="Create a password" required>

                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_passowrd" name="confirm_password" placeholder="Re-enter your password" required>

                <div class="register-buttons">
                    <a href="login.php" class="back-login-btn">BACK TO LOGIN</a>
                    <button type="submit">REGISTER</button>
                </div>
            </form>
        </div>

                <div class="register-right-section">
                    <div class="register-info-box">
                        <h2>
                            CCI IMS <br>
                            CCI <br>
                            INTERNSHIP <br>
                            MANAGEMENT <br>
                            SYSTEM <br>
                        </h2>

                        <p>
                            PLEASE ENTER YOUR DETAILS <br>
                            TO REGISTER
                        </p>
                    </div>
                </div>
            
        </div>

        <script>
            const roleSelect = document.getElementById("role");
            const idLabel = document.getElementById("id_label");
            const loginIdInput = document.getElementById("login_id");

            function updateIdField() {
                if (roleSelect.value === "student") {
                    idLabel.textContent = "Enter Student No:";
                    loginIdInput.placeholder = "Enter your student number";
                } else if (roleSelect.value === "lecturer") {
                    idLabel.textContent = "Enter Lecturer ID:";
                    loginIdInput.placeholder = "Enter your lecturer ID";
                }
            }

            roleSelect.addEventListener("change", updateIdField);

            // Run once when page loads
            updateIdField();
        </script>

</body>
</html>