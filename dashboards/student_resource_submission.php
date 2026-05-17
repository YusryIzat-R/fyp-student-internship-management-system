<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

if(!isset($_SESSION['role']) || $_SESSION['role'] != "student") {
    header("Location: ../public/login.php");
    exit;
}

if(!isset($_GET['resource_id'])) {
    $_SESSION['error'] = "Resource ID not found.";
    header("Location: student_resource.php");
    exit;
}

$resource_id = $_GET['resource_id'];
$student_no = $_SESSION['student_no'];
$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : $_SESSION['login_id'];

/** Get student's data */
$student_sql = "SELECT * FROM students WHERE student_no = '$student_no'";
$student_result = mysqli_query($conn, $student_sql);

if(!$student_result || mysqli_num_rows($student_result) == 0) {
    $_SESSION['error'] = "No student's record was found.";
    header("Location: student_resource.php");
    exit;
}

$student = mysqli_fetch_assoc($student_result);
$assigned_lecturer_id = $student['assigned_lecturer_id'];

/** Get assigned lecturer's data */
$lecturer_sql = "SELECT * FROM lecturers WHERE id = '$assigned_lecturer_id'";
$lecturer_result = mysqli_query($conn, $lecturer_sql);

if(!$lecturer_result || mysqli_num_rows($lecturer_result) == 0) {
    $_SESSION['error'] = "No assigned lecturer's record was found.";
    header("Location: student_resource.php");
    exit;
}

$lecturer = mysqli_fetch_assoc($lecturer_result);
$lecturer_no = $lecturer['lecturer_no'];

/** Get selected resources */
$resource_sql = "SELECT * FROM resources
                 WHERE resource_id = '$resource_id' 
                 AND lecturer_id = '$lecturer_no'";

$resource_result = mysqli_query($conn, $resource_sql);

if(!$resource_result || mysqli_num_rows(($resource_result)) == 0) {
    $_SESSION['error'] = "Resources not found or not available yet";
    header("Location: student_resource.php");
    exit;
}

$resource = mysqli_fetch_assoc($resource_result);

/** Check existing submission for the resource */
$submission_sql = "SELECT * FROM submissions
                   WHERE student_id = '$student_no' 
                   AND resource_id = '$resource_id'
                   ORDER BY submitted_at DESC
                   LIMIT 1";

$submission_result = mysqli_query($conn, $submission_sql);
$submission = NULL;

if($submission_result && mysqli_num_rows($submission_result) > 0) {
    $submission = mysqli_fetch_assoc($submission_result);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit Resource - CCI IMS</title>
    <link rel="stylesheet" href="../assets/css/dashboards.css">
</head>
<body>
    <div class="wrapper">
        <aside class="sidebar">
            <h3>Student Menu</h3>

            <nav class="menu">
                <a href="../dashboards/student_dashboard.php" class="menu-item">Dashboard</a>
                <a href="../dashboards/student_announcement.php" class="menu-item">Announcements</a>
                <a href="../dashboards/student_resource.php" class="menu-item is-active">Resources</a>
                <a href="../dashboards/student_assigned_lecturer.php" class="menu-item">My Lecturer</a>
                <a href="../dashboards/student_presentation_booking.php" class="menu-item">Presentation Booking</a>
                <a href="#" class="menu-item">My Result</a>
                <a href="#" class="menu-item">Get Help</a>
                <a href="../verify/logout.php" class="menu-item">Logout</a>
            </nav>
        </aside>

        <main class="content">
            <h1>Submit Resource</h1>
            <p>Welcome, <b><?php echo $full_name; ?></b></p>

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

            <h2><?php echo $resource['title']; ?></h2>
            <p><b>Category:</b><?php echo $resource['category']; ?></p>
            <p>
                <b>Resource File:</b>
                <a href="../<?php echo $resource['file_path']; ?>" target="_blank">Download / View</a>
            </p>

            <br><hr><br>

            <?php if($submission != null) { ?>
                <h2>Your Submission</h2>
                <p><b>Status:</b><?php echo ucfirst($submission['status']); ?></p>
                <p><b>Submitted At:</b><?php echo $submission['submitted_at']; ?></p>

                <?php if($submission['lecturer_comment'] != "") { ?>
                    <p><b>Lecturer's Comment:</b><?php echo $submission['lecturer_comment']; ?></p>
                <?php } ?>

                <p>
                    <b>Your File:</b>
                    <a href="../<?php echo $submission['file_path']; ?>" target="_blank">View / Download</a>
                </p>

                <?php if($submission['status'] != "approved") { ?>
                    <br>
                    <h3>Reupload Submissions</h3>
                
                    <form action="../verify/upload_submission.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="resource_id" value="<?php echo $resource['resource_id']; ?>">
                        <input type="hidden" name="submission_id" value="<?php echo $submission['submission_id']; ?>">

                        <label for="submission_id">Choose New File:</label><br>
                        <input type="file" name="submission_file" id="submission_file" accept=".pdf,.doc,.docx,.ppt,.pptx" required>
                        <br><br>

                        <button type="submit" name="upload_submission">Reupload Submission</button>
                    </form>
                    <?php } else { ?>
                        <p style="color: green;">This submission has been approved and can no longer be edited.</p>
                    <?php } ?>

                <?php } else { ?>
                
                    <h2>Upload Submission</h2>
                    
                    <form action="../verify/upload_submission.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="resource_id" value="<?php echo $resource['resource_id'];?>">

                        <label for="submission_file" class="drop-area">
                            <p>Drag & Drop Your File Here</p>
                            <p>or Click to Select File</p>
                            <small>Allowed: PDF, DOC, DOCX, PPT, PPTX</small>
                        </label>

                        <input type="file"
                            name="submission_file"
                            id="submission_file"
                            accept=".pdf,.doc,.docx,.ppt,.pptx"
                            required>

                        <p id="file-name">No file selected</p>

                        <br>

                        <button type="submit" name="upload_submission">Submit File</button>
                        <a href="student_resource.php" style="margin-left: 10px;">Back</a>
                    </form>

                <?php } ?>
        </main>
    </div>

    <script>
    const dropArea = document.querySelector(".drop-area");
    const fileInput = document.getElementById("submission_file");
    const fileName = document.getElementById("file-name");

    /* Click upload */
    dropArea.addEventListener("click", () => {
        fileInput.click();
    });

    /* Show selected filename */
    fileInput.addEventListener("change", function() {

        if(fileInput.files.length > 0) {
            fileName.textContent =
                "Selected file: " + fileInput.files[0].name;
        }

    });

    /* Prevent browser default drag behavior */
    ["dragenter", "dragover", "dragleave", "drop"].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    /* Highlight drop area */
    ["dragenter", "dragover"].forEach(eventName => {
        dropArea.addEventListener(eventName, () => {
            dropArea.style.borderColor = "#007bff";
            dropArea.style.backgroundColor = "#f0f7ff";
        });
    });

    /* Remove highlight */
    ["dragleave", "drop"].forEach(eventName => {
        dropArea.addEventListener(eventName, () => {
            dropArea.style.borderColor = "#999";
            dropArea.style.backgroundColor = "#fff";
        });
    });

    /* Handle dropped file */
    dropArea.addEventListener("drop", function(e) {

        const files = e.dataTransfer.files;

        if(files.length > 0) {

            fileInput.files = files;

            fileName.textContent =
                "Selected file: " + files[0].name;
        }

    });
    </script>
</body>
</html>