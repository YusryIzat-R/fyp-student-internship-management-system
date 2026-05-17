<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

if(!isset($_SESSION['role']) || $_SESSION['role'] != "lecturer") {
    header("Location: ../public/login.php");
    exit;
}

if(!isset($_GET['id'])) {
    $_SESSION['error'] = "Resources ID not found!";
    header("Location: lecturer_resources.php");
    exit;
}

$resource_id = $_GET['id'];
$lecturer_id = $_SESSION['login_id'];
$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : $_SESSION['login_id'];

$sql = "SELECT * FROM resources 
        WHERE resource_id = '$resource_id'
        AND lecturer_id = '$lecturer_id'";

$result = mysqli_query($conn, $sql);

if(!$result || mysqli_num_rows($result) == 0) {
    $_SESSION['error'] = "Resource not found or you are not allowed to edit this resource.";
    header("Location: lecturer_resources.php");
    exit;
}

$resource = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Resource - CCI IMS</title>
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
            <h1>Edit Resource</h1>
            <p><b><?php echo $full_name; ?></b></p>
            <p>Update resource information or replace the upload file.</p>

            <?php if(isset($_SESSION['error'])) { ?>
                <div class="alert error">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php } ?>

            <br>

            <form action="../verify/update_resource.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="resource_id" value="<?php echo $resource['resource_id']; ?>">
                <input type="hidden" name="old_file_path" value="<?php echo $resource['file_path']; ?>">

                <label for="title">Resource Title:</label><br>
                <input type="text" name="title" id="title" value="<?php echo $resource['title'];?>">
                <br><br>

                <label for="category">Category:</label><br>
                <select name="category" id="category" required style="width: 400px; padding: 8px;">
                    <option value="Final Report Template" <?php if($resource['category'] == "Final Report Template") echo "selected"; ?>>Final Report Template</option>
                    <option value="Presentation Slide Template" <?php if($resource['category'] == "Presentation Slide Template") echo "selected"; ?>>Presentation Slide Template</option>
                    <option value="Logbook Template" <?php if($resource['category'] == "Logbook Template") echo "selected"; ?>>Logbook Template</option>
                    <option value="Official Form" <?php if($resource['category'] == "Official Form") echo "selected"; ?>>Official Form</option>
                    <option value="Guideline" <?php if($resource['category'] == "Guideline") echo "selected"; ?>>Guideline</option>
                    <option value="Other" <?php if($resource['category'] == "Other") echo "selected"; ?>>Other</option>
                </select>
                <br><br>

                <p><b>Current File:</b>
                    <a href="../<?php echo $resource['file_path']; ?>" target="_blank">
                        View Current File
                    </a>
                </p>

                <br>

                <label for="resource_file">Replace File (optional):</label><br>
                <input type="file" name="resource_file" id="resource_file" accept=".pdf,.doc,.docx,.ppt,.pptx">
                <br><br>

                <button type="submit" name="update_resource">Update Resource</button>
                <a href="lecturer_resources.php" style="margin-left: 10px;">Cancel</a>
            </form>
        </main>
    </div>
</body>
</html>
