<?php
session_start();

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../public/login.php");
    exit;
}

$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : $_SESSION['login_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Student List - CCI IMS</title>
    <link rel="stylesheet" href="../assets/css/dashboards.css">
</head>
<body>
<div class="wrapper">
    <aside class="sidebar">
        <h3>ADMIN MENU</h3>

        <nav class="menu">
            <a href="admin_dashboard.php" class="menu-item">Dashboard</a>
            <a href="admin_announcement.php" class="menu-item">Announcements</a>
            <a href="manage_lecturers.php" class="menu-item">Visiting Lecturer Management</a>
            <a href="student_management.php" class="menu-item is-active">Student Management</a>
            <a href="#" class="menu-item">Results</a>
            <a href="#" class="menu-item">Get Help</a>
            <a href="../verify/logout.php" class="menu-item">Logout</a>
        </nav>
    </aside>

    <main class="content">
        <h1>Upload Student List</h1>
        <p>Welcome, <b><?php echo $full_name; ?></b></p>
        <p>Upload a CSV file to add multiple students into the system.</p>

        <?php if(isset($_SESSION['error'])) { ?>
            <div class="alert error">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php } ?>

        <?php if(isset($_SESSION['success'])) { ?>
            <div class="alert success">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php } ?>

        <br>

        <form action="../verify/upload_student_list.php" method="POST" enctype="multipart/form-data">
            <label for="student_file">Select CSV File:</label><br><br>
            <input type="file" name="student_file" id="student_file" accept=".csv" required>
            <br><br>

            <button type="submit" name="upload_student_list">Upload</button>
            <a href="student_management.php" style="margin-left: 10px;">Cancel</a>
        </form>

        <br><hr><br>

        <h3>CSV Format Required:</h3>
        <p>Your CSV file should contain this information of students:</p>

        <pre>
student_no,full_name,email,program
DCS12345,Ali Bin Abu,ali@student.uniten.edu.my,Diploma in Computer Science
        </pre>
    </main>
</div>
</body>
</html>