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
$ok = $_GET['ok'] ?? '';

?>

<!DOCTYPE html>
<html lang="en>
<head>
    <meta charset="UTF-8">
    <title>Register - CCI IMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <h2>Register</h2>
    <p>Please select your role and fill in the registration details</p>

    <?php if ($err !== ''): ?>
        <p style="color: red;"><b><?=  htmlspecialchars($err) ?></b></p>
    <?php endif; ?>

    <form method="POST" action="../verify/register.php">
        <!-- Role Selection -->
        <label for="role">Register as:</label><br>
        <select name="role" id="role" required>
            <option value="student">Student</option>
            <option value="lecturer">Visiting Lecturer</option>
        </select>
        <br><br>
    
            <!-- ID Field -->
        <label for="user_id">ID:</label><br>
        <input
            type="text"
            id="user_id"
            name="id"
            required
            placeholder="Student ID / Lecturer ID"
        >
        <br><br>

        <!-- Name Field -->
        <label for="name">Full Name:</label><br>
        <input
            type="text"
            id="name"
            name="name"
            required
            placeholder="Enter your full name"
        >
        <br><br>

        <!-- Email Field -->
        <label for="email">Email:</label><br>
        <input
            type="email"
            id="email"
            name="email"
            required
            placeholder="example@uniten.edu.my"
        >
        <br><br>

        <!-- Password -->
        <label for="password">Password:</label><br>
        <input
            type="password"
            id="password"
            name="password"
            required
            minlength="6"
            placeholder="Minimum 6 characters"
        >
        <br><br>

        <!-- Confirm Password -->
        <label for="confirm_password">Confirm Password:</label><br>
        <input
            type="password"
            id="confirm_password"
            name="confirm_password"
            required
            minlength="6"
            placeholder="Re-enter password"
        >
        <br><br>

        <!-- Buttons -->
        <button type="submit">Register</button>
        <a href="login.php"><button type="button">Back to Login</button></a>
        <a href="index.php"><button type="button">Back to Home</button></a>
    </form>

    <hr>

    

</body>
</html>
</body>
</html>