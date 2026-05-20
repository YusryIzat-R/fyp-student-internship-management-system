<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin"){
    header("Location: ../public/login.php");
    exit;
}

$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : $_SESSION['login_id'];

$sql = "SELECT * FROM required_submissions
        ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

if(!$result){
    die("Query Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Required Submissions - CCI IMS</title>
        <link rel="stylesheet" href="../assets/css/dashboards.css">
    </head>
    <body>
        <div class="wrapper">
            <aside class="sidebar">
                <h3>Admin Menu</h3>

                <nav class="menu">
                    <a href="admin_dashboard.php" class="menu-item">Dashboard</a>
                    <a href="admin_announcement.php" class="menu-item">Announcements</a>
                    <a href="manage_lecturers.php" class="menu-item">Visiting Lecturer Management</a>
                    <a href="student_management.php" class="menu-item">Student Management</a>
                    <a href="admin_required_submissions.php" class="menu-item is-active">Required Submissions</a>
                    <a href="admin_results.php" class="menu-item">Results</a>
                    <a href="admin_help_requests.php" class="menu-item">Get Help</a>
                    <a href="../verify/logout.php" class="menu-item">Logout</a>
                </nav>
            </aside>

            <main class="content">
                <h1>Required Submissions</h1>
                <p><b><?php echo $full_name; ?></b></p>
                <p>
                    Manage required internship submissions 
                    needed for students to pass the internship.
                </p>

                <?php if(isset($_SESSION['success'])) { ?>
                    <div class="alert success">
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                <?php } ?>

                <?php if(isset($_SESSION['error'])) { ?>
                    <div class="alert error">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php } ?>

                <br>

                <div class="resource-card">
                    <h2>Add Required Submissions</h2>

                    <form action="../verify/add_required_submission.php" method="POST">
                        <label for="submission_type">
                            <b>Submission Type</b>
                        </label>

                        <br><br>

                        <input type="text" name="submission_type" id="submission_type" placeholder="Example: Final Report" required>

                        <br><br>

                        <label for="description">
                            <b>Description:</b>
                        </label>

                        <br><br>

                        <textarea name="description" id="description" rows="4" style="width: 100%;" placeholder="Enter submission description.."></textarea>

                        <br><br>

                        <button type="submit" name="add_required_submission">
                            Add Submission Requirement
                        </button>

                    </form>
                </div>

                <br>

                <h2>Submission Requirements List</h2>

                <br>

                <?php 
                if(mysqli_num_rows($result) > 0){
                    while($row = mysqli_fetch_assoc($result)){
                        echo "<div class='resource-card'>";
                        echo "<h2>" . $row['submission_type'] . "</h2>";

                        echo "<p><b>Status:</b>" . ucfirst($row['status']) . "</p>";
                        echo "<p><b>Description:</b></p>";

                        echo "<p>" . nl2br($row['description']) . "</p>";

                        echo "<br>";

                        if($row['status'] == "active"){
                            echo "<a href='../verify/update_required_submission_status?id=" . 
                            $row['requirement_id'] . "&status=inactive' class='resource-btn'>
                            Deactivate
                            </a>";

                        } else {
                            echo "<a href='../verify/update_required_submission_status?id=" .
                                 $row['requirement_id'] .
                                 "&status=active'
                                 class='resource-btn'>
                                 Activate
                                 </a>";
                        }

                        echo "</div>";

                        echo "<br>";
                    }
                } else {
                    echo "<div class='alert error'>";
                    echo "No required submissions found";
                    echo "</div>";
                }
                ?>
            </main>
        </div>
    </body>
</html>