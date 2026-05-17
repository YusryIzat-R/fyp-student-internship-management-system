<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

if(!isset($_SESSION['role']) || $_SESSION['role'] != "lecturer") {
    header("Location: ../public/login.php");
    exit;
}

$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : $_SESSION['login_id'];
$lecturer_id = $_SESSION['login_id'];

$sql = "SELECT * FROM resources
        WHERE lecturer_id = '$lecturer_id'
        ORDER BY created_at DESC";

$result = mysqli_query($conn, $sql);

if(!$result) {
    die("Query Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lecturer Resources - CCI IMS</title>
    <link rel="stylesheet" href="../assets/css/dashboards.css">
</head>
<body>
    <div class="wrapper">
        <aside class="sidebar">
            <h3>Lecturer Menu</h3>

            <nav class="menu">
                <a href="lecturer_dashboard.php" class="menu-item">Dashboard</a>
                <a href="lecturer_announcement.php" class="menu-item">Announcements</a>
                <a href="lecturer_resources.php" class="menu-item is-active">Internship Resources Management</a>
                <a href="lecturer_assigned_students.php" class="menu-item">My Students</a>
                <a href="lecturer_presentation_booking.php" class="menu-item">Presentation Timeslot Management</a>
                <a href="lecturer_grading.php" class="menu-item">Grading</a>
                <a href="../verify/logout.php" class="menu-item">Logout</a>
        </nav>
    </aside>

    <main class="content">
        <h1>Internship Resources Management</h1>
        <p>Welcome, <b><?php echo $full_name; ?></b></p>
        <p>Upload internship templates and documents for students to download.</p>
    
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

        <h2>Upload Resources</h2>

        <form action="../verify/upload_resource.php" method="POST" enctype="multipart/form-data">
            <label for="title">Resources Title:</label><br>
            <input type="text" name="title" id="title" required style="width: 400px; padding: 8px;">
            <br><br>

            <label for="category">Category:</label><br>
            <select name="category" id="category" required style="width: 400px; padding: 8px;">
                <option value="">-- Select Category --</option>
                <option value="Final Report Template">Final Report Template</option>
                <option value="Presentation Slide Template">Presentation Slide Template</option>
                <option value="Logbook Template">Logbook Template</option>
                <option value="Official Form">Official Form</option>
                <option value="Guideline">Guideline</option>
                <option value="Other">Other</option>
            </select>
            <br><br>

            <label for="resources_file">Upload File:</label><br>
            <input type="file" name="resource_file" id="resource_file" accept=".pdf,.doc,.docx,.ppt,.pptx" required>
            <br><br>

            <button type="submit" name="upload_resource">Upload Resource</button>
        </form>

        <br><hr><br>

        <h2>Uploaded Resources</h2>

        <div class="resource-list">
            <?php
            if(mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
            ?>

                <div class="resource-card">
                    <h3><?php echo $row['title']; ?></h3>
                    <p><b>Category:</b> <?php echo $row['category']; ?></p>
                    <p><b>Uploaded At:</b> <?php echo $row['created_at']; ?></p>

                    <div class="resource-actions">
                        <a href="../<?php echo $row['file_path']; ?>" target="_blank" class="resource-btn download-btn">
                            Download / View
                        </a>

                        <a href="edit_resource.php?id=<?php echo $row['resource_id'];?>" 
                        class="resource-btn edit-btn">
                            Edit
                        </a>

                        <a href="lecturer_submissions.php?resource_id=<?php echo $row['resource_id']; ?>"
                        class="resource-btn submission-btn">
                            View Submissions
                        </a>

                        <a href="../verify/delete_resource.php?id=<?php echo $row['resource_id']; ?>"
                            class="resource-btn delete-btn"
                            onclick="return confirm('Delete this resource?');">
                            Delete
                        </a>
                    </div>
                </div>

            <?php
                }
            } else {
                echo "<p>No resources uploaded yet.</p>";
            }
            ?>
        </div>
    </main>
    </div>
</body>
</html>