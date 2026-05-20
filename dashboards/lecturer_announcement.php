<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

if(!isset($_SESSION['role']) || $_SESSION['role'] != "lecturer") {
    header("Location: ../public/login.php");
    exit;
}

$full_name = "";
if(isset($_SESSION['full_name'])) {
    $full_name = $_SESSION["full_name"];
} else {
    $full_name = $_SESSION["login_id"];
}

$login_id = $_SESSION["login_id"];

$sql = "SELECT * FROM announcements WHERE posted_by = '$login_id' AND role = 'lecturer' ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Lecturer Announcements - CCI IMS</title>
        <link rel="stylesheet" href="../assets/css/dashboards.css">
    </head>
    <body>
        <div class="wrapper">
            <aside class="sidebar">
                <h3>Lecturer Menu</h3>

                <nav class="menu">
                    <a href="../dashboards/lecturer_dashboard.php" class="menu-item">Dashboard</a>
                    <a href="../dashboards/lecturer_announcement.php" class="menu-item is-active">Announcements</a>
                    <a href="../dashboards/lecturer_resources.php" class="menu-item">Internship Resources Management</a>
                    <a href="../dashboards/lecturer_assigned_students.php" class="menu-item">My Students</a>
                    <a href="../dashboards/lecturer_presentation_booking.php" class="menu-item">Presentation Timeslot Management</a>
                    <a href="../dashboards/lecturer_grading.php" class="menu-item">Grading</a>
                    <a href="../verify/logout.php" class="menu-item">Logout</a>
                </nav>
            </aside>

            <main class="content">
                <h1>My Announcements</h1>
                <p>Welcome, <b><?php echo $full_name; ?></b></p>
                <p>Create and manage your own announcements here.</p>

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

                <h2>Add New Announcement</h2>
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

                <h2>My Announcement List</h2>

                <?php
                if($result && mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        echo "<div style='background: white; padding: 15px; margin-bottom: 15px; border: 1px solid #ccc;'>";
                        echo "<h3>" . $row['title'] . "</h3>";
                        echo "<p>" . $row['content'] . "</p>";
                        echo "<p><b>Posted by:</b> " . $row['posted_by'] . "</p>";
                        echo "<p><b>Role:</b> " . $row['role'] . "</p>";
                        echo "<p><b>Created At:</b> " . $row['created_at'] . "</p>";

                        echo "<br>";
                        echo "<a href='edit_lecturer_announcement.php?id=" . $row['announcement_id'] . "' style='margin-right: 10px;'>Edit</a>";
                        echo "<a href='../verify/delete_announcement.php?id=" . $row['announcement_id'] . "' style='color: red;' onclick='return confirm(\"Are you sure you want to delete this announcement?\");'>Delete</a>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No announcements found. Start by adding a new announcement above.</p>";
                }
                ?>
            </main>
        </div>
    </body>
</html>