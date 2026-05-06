<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../public/login.php");
    exit;
}

if(!isset($_GET['id'])) {
    $_SESSION['error'] = "Lecturer ID not found or missing.";
    header("Location: manage_lecturers.php");
    exit;
}

$lecturer_id = $_GET['id'];

$sql = "SELECT * FROM lecturers WHERE id = '$lecturer_id'";
$result = mysqli_query($conn, $sql);

if(!$result || mysqli_num_rows($result) == 0) {
    $_SESSION['error'] = "Lecturer not found.";
    header("Location: manage_lecturers.php");
    exit;
}

$lecturer = mysqli_fetch_assoc($result);

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
    <title>Edit Lecturer - CCI IMS</title>
    <link rel="stylesheet" href="../assets/css/dashboards.css">
</head>
<body>
    <div class="wrapper">
        <aside class="sidebar">
            <h3>Admin Menu</h3>

            <nav class="menu">
                <a href="admin_dashboard.php" class="menu-item">Dashboard</a>
                <a href="admin_announcement.php" class="menu-item">Announcements</a>
                <a href="manage_lecturers.php" class="menu-item is-active">Visiting Lecturer Management</a>
                <a href="../dashboards/student_management.php" class="menu-item">Student Management</a>
                <a href="#" class="menu-item">Results</a>
                <a href="#" class="menu-item">Get Help</a>
                <a href="../verify/logout.php" class="menu-item">Logout</a>
            </nav>
        </aside>

        <main class="content">
            <h1>Edit Lecturer</h1>
            <p>Welcome, <b><?php echo $full_name; ?></b></p>
            <p>Update visiting lecturer details here.</p>

            <?php if(isset($_SESSION['error'])) { ?>
                <div class="alert error">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php } ?>

            <br>

            <form action="../verify/update_lecturer.php" method="POST">
                <input type="hidden" name="lecturer_id" value="<?php echo $lecturer['id']; ?>">
                <input type="hidden" name="user_id" value="<?php echo $lecturer['user_id']; ?>">

                <label for="lecturer_no">Lecturer No:</label><br>
                <input type="text" name="lecturer_no" id="lecturer_no" value="<?php echo $lecturer['lecturer_no']; ?>" required style="width: 400px; padding: 8px; margin-bottom: 15px;">
                <br><br>

                <label for="full_name">Full Name:</label><br>
                <input type="text" name="full_name" id="full_name" value="<?php echo $lecturer['full_name']; ?>" required style="width: 400px; padding: 8px; margin-bottom: 15px;">
                <br><br>

                <label for="email">Email:</label><br>
                <input type="email" name="email" id="email" value="<?php echo $lecturer['email']; ?>" required style="width: 400px; padding: 8px; margin-bottom: 15px;">
                <br><br>

                <label for="department">Department:</label><br>
                <input type="text" name="department" id="department" value="<?php echo $lecturer['department']; ?>" style="width: 400px; padding: 8px; margin-bottom: 15px;">
                <br><br>

                <button type="submit" name="update_lecturer">Update Lecturer</button>
                <a href="manage_lecturers.php" style="margin-left: 10px;">Cancel</a>
            </form>
        </main>
    </div>
</body>
</html>