<?php
session_start();
require_once '../config/db.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../public/login.php");
    exit;
}

if(!isset($_GET['id'])) {
    echo "<p style='color:red;'>Announcement ID not found.</p>";
    echo "<a href='admin_announcement.php'>Back to Announcements</a>";
    exit;
}

$id = $_GET['id'];

$sql = "SELECT * FROM announcements WHERE announcement_id = '$id'";
$result = mysqli_query($conn, $sql);

if(!$result || mysqli_num_rows($result) == 0) {
    echo "<p style='color:red;'>Announcement not found.</p>";
    echo "<a href='admin_announcement.php'>Back to Announcements</a>";
    exit;
}

$row = mysqli_fetch_assoc($result);

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
        <title>Edit Announcement - CCI IMS</title>
        <link rel="stylesheet" href="../assets/css/dashboards.css">
    </head>
    <body>
        <div class="wrapper">
            <aside class="sidebar">
                <h3>Admin Menu</h3>

                <nav class="menu">
                <a href="admin_dashboard.php" class="menu-item">Dashboard</a>
                <a href="admin_announcement.php" class="menu-item is-active">Announcements</a>
                <a href="assign_lecturer.php" class="menu-item">Manage Students</a>
                <a href="manage_lecturers.php" class="menu-item">Manage Lecturers</a>
                <a href="#" class="menu-item">Results</a>
                <a href="#" class="menu-item">Get Help</a>
                <a href="logout.php" class="menu-item">Logout</a>
            </nav>
            </aside>

            <main class="content">
            <h1>Edit Announcement</h1>
            <p>Welcome, <b><?php echo $full_name; ?></b></p>

            <br>

            <form action="../verify/update_announcement.php" method="POST">
                <input type="hidden" name="announcement_id" value="<?php echo $row['announcement_id']; ?>">

                <label for="title">Title:</label><br>
                <input type="text" name="title" id="title" value="<?php echo $row['title']; ?>" required style="width: 400px; padding: 8px;">
                <br><br>

                <label for="content">Content:</label><br>
                <textarea name="content" id="content" rows="5" cols="60" required style="padding: 8px;"><?php echo $row['content']; ?></textarea>
                <br><br>

                <button type="submit" name="update_announcement">Update Announcement</button>
                <a href="admin_announcement.php" style="margin-left: 10px;">Cancel</a>
            </form>
        </main>
        </div>
    </body>
</html>