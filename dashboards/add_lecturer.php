<?php
session_start();

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../public/login.php");
    exit;
}

$full_name = "";
if(isset($_SESSION['full_name'])) {
    $full_name = $_SESSION['full_name'];
} else {
    $full_name = $_SESSION['login_id'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Lecturer - CCI IMS</title>
    <link rel="stylesheet" href="../assets/css/dashboards.css">
</head>
<body>
    <div class="wrapper">
        <aside class="sidebar">
            <h3>Admin Menu</h3>

            <nav class="menu">
                <a href="admin_dashboard.php" class="menu-item">Dashboard</a>
                <a href="admin_announcement.php" class="menu-item">Announcements</a>
                <a href="manage_student_assignments.php" class="menu-item">Student Assignments</a>
                <a href="manage_lecturers.php" class="menu-item is-active">Manage Lecturers</a>
                <a href="#" class="menu-item">Results</a>
                <a href="#" class="menu-item">Get Help</a>
                <a href="../verify/logout.php" class="menu-item">Logout</a>
            </nav>
        </aside>
        
        <main class="content">
            <h1>Add New Lecturer</h1>
            <p>Welcome, <b><?php echo $full_name; ?></b></p>
            <p>Register a new Visiting Lecturer Account Here</p>

            <br>

            <form action="../verify/add_lecturer.php" method="POST">
                <label for="lecturer_no">Lecturer No:</label><br>
                <input type="text" name="lecturer_no" id="lecturer_no" required style="width: 400px; padding: 8px; margin-bottom: 15px;">
                <br><br>

                <label for="full_name">Full Name:</label><br>
                <input type="text" name="full_name" id="full_name" required style="width: 400px; padding: 8px; margin-bottom: 15px;">
                <br><br>

                <label for="email">Email:</label><br>
                <input type="email" name="email" id="email" required style="width: 400px; padding: 8px; margin-bottom: 15px;">
                <br><br>

                <label for="password">Password:</label><br>
                <input type="text" name="password" id="password" required style="width: 400px; padding: 8px; margin-bottom: 15px;" placeholder="Example: 123456">
                <br><br>

                <label for="confirm_password">Confirm Password:</label><br>
                <input type="text" name="confirm_password" id="confirm_password" required style="width: 400px; padding: 8px;" placeholder="Re-enter Password">
                <br><br>

                <button type="submit" name="add_lecturer">Add Lecturer</button>
                <a href="manage_lecturers.php" style="margin-left: 10px;">Cancel</a>
            </form>
        </main>
    </div>
</body>
</html>