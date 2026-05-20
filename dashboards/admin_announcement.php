<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

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

$sql = "SELECT * FROM announcements ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Announcements - CCI IMS</title>
        <link rel="stylesheet" href="../assets/css/dashboards.css">
    </head>
    <body>
        <div class="wrapper">
            <aside class="sidebar">
                <h3>Admin Menu</h3>

                <nav class="menu">
                    <a href="admin_dashboard.php" class="menu-item">Dashboard</a>
                    <a href="admin_announcement.php" class="menu-item is-active">Announcements</a>
                    <a href="manage_lecturers.php" class="menu-item">Visiting Lecturer Management</a>
                    <a href="student_management.php" class="menu-item">Student Management</a>
                    <a href="admin_required_submissions.php" class="menu-item">Required Submissions</a>
                    <a href="admin_results.php" class="menu-item">Results</a>
                    <a href="admin_help_requests.php" class="menu-item">Get Help</a>
                    <a href="../verify/logout.php" class="menu-item">Logout</a>
                </nav>
            </aside>

            <main class="content">
                <h1>Announcements</h1>
                <p>Welcome, <b><?php echo $full_name; ?></b></p>
                <p>Create and manage announcements for students and lecturers here.</p>

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

                <br>

                <h2>Add New Announcements</h2>
                <form action="../verify/add_announcement.php" method="POST">
                    <label for="title">Title:</label><br>
                    <input type="text" name="title" id="title" required style="width: 400px; padding: 8px;">
                    <br><br>

                    <label for="content">Content:</label><br>
                    <textarea name="content" id="content" rows="5" cols="60" required style="padding: 8px;"></textarea>
                    <br><br>

                    <button type="submit" name="add_announcement">Save Announcement</button>
                </form>

                <br><hr><br>

                <h2>Announcement List.</h2>

                <?php
                if($result && mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        echo "<div style='background: white; padding: 15px; margin-bottom: 15px; border: 1px solid #ccc'>";
                        echo "<h3>" . $row['title'] . "</h3>";
                        echo "<p>" . $row['content'] . "</p>";
                        echo "<p><b>Posted By:</b>" . $row['posted_by'] . "</p>";
                        echo "<p><b>Role:</b>" . $row['role'] . "</p>";
                        echo "<p><b>Created At:</b>" . $row['created_at'] . "</p>";

                        echo "<br>";
                        echo "<a href='../dashboards/edit_announcement.php?id=" . $row['announcement_id'] . "'style='margin-right: 10px;'>Edit</a>";
                        echo "<a href='../verify/delete_announcement.php?id=" . $row['announcement_id'] . "' style='color: red;' onclick='return confirm(\"Are you sure you want to delete this announcement?\");'>Delete</a>";
                        echo "</div>";
                    } 
                } else {
                        echo "<p>No announcements available.</p>";
                    }
                    ?>
            </main>
        </div>
    </body>
</html>