<?php 

session_start();

if (isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'student':
            header("Location: ../dashboards/student_dashboard.php");
            exit;

        case 'lecturer':
            header("Location: ../dashboards/lecturer_dashboard.php");
            exit;

        case 'admin':
            header("Location: ../dashboards/admin_dashboard.php");
            exit;
    }
}

$err = $_GET['err'] ?? '';
$registered = $_GET['registered'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - CCI IMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <h2>Login</h2>
    <p>Please enter your login details</p>

    <?php if($registered === '1'): ?>
        <p style="color: green;">Registration successful! Please log in.</p>
    <?php endif; ?>

    <?php if($err === ''): ?>
        <p style="color: red;"><b><?=  htmlspecialchars($err) ?></b></p>
    <?php endif; ?>

    <form method="POST" action="../verify/login.php">
        <!-- Role Selection -->
        <label for="role">Login as:</label>
        <select name="role" id="role" required>
            <option value="student">Student</option>
            <option value="lecturer"> VisitingLecturer</option>
            <option value="admin">Program Coordinator</option>
        </select>
        <br><br>

        <!-- ID -->
        <label for="id">ID:</label>
        <input type="text" id="id" name="id" required placeholder="Student ID / Lecturer ID / Admin ID">
        <br><br>

        <!-- PASSWORD -->
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required placeholder="Enter your password">
        <br><br>

        <! -- FORGOT PASSWORD LINK -->
        <div style="margin-bottom: 10px;">
            <a href="forgot_password.php" style="font-size: 14px;">Forgot Password?</a>
        </div>
        
        <!-- BUTTONS -->
        <button type="submit">Login</button>
        <a href="register.php">
            <button type="button">Register</button>
        </a>

        <a href="index.php">
            <button type="button">Back</button>
        </a>
    </form>
</body>
</html>