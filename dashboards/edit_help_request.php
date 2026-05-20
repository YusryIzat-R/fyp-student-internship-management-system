<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

if(!isset($_SESSION['role']) || $_SESSION['role'] != "student") {
    header("Location: ../public/login.php");
    exit;
}

if(!isset($_GET['id'])){
    $_SESSION['error'] = "Help request ID not found.";
    header("Location: student_get_help.php");
    exit;
}

$ticket_id = $_GET['id'];
$student_no = $_SESSION['student_no'];

$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : $_SESSION['login_id'];

$sql = "SELECT * FROM help_ticket 
        WHERE ticket_id = '$ticket_id'
        AND student_id = '$student_no'
        LIMIT 1";

$result = mysqli_query($conn, $sql);

if(!$result || mysqli_num_rows($result) == 0) {
    $_SESSION['error'] = "Help request record not found.";
    header("Location: student_get_help.php");
    exit;
}

$ticket = mysqli_fetch_assoc($result);

/** Only allow edit the request if get help request is submitted and no reply by admin yet. */
if($ticket['status'] != "submitted" || $ticket['admin_reply'] != "") {
    $_SESSION['error'] = "This help request can no longer be editted.";
    header("Location: student_get_help.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Help Request - CCI IMS</title>
    <link rel="stylesheet" href="../assets/css/dashboards.css">
</head>
<body>
    <div class="wrapper">
        <aside class="sidebar">
            <h3>Student Menu</h3>

            <nav class="menu">
                <a href="../dashboards/student_dashboard.php" class="menu-item">Dashboard</a>
                <a href="../dashboards/student_announcement.php" class="menu-item">Announcements</a>
                <a href="../dashboards/student_resource.php" class="menu-item">Resources</a>
                <a href="../dashboards/student_assigned_lecturer.php" class="menu-item">My Lecturer</a>
                <a href="../dashboards/student_presentation_booking.php" class="menu-item">Presentation Booking</a>
                <a href="../dashboards/student_result.php" class="menu-item">My Result</a>
                <a href="../dashboards/student_get_help.php" class="menu-item is-active">Get Help</a>
                <a href="../verify/logout.php" class="menu-item">Logout</a>
            </nav>
        </aside>

        <main class="content">
            <h1>Edit Help Request</h1>
            <p><b><?php echo $full_name; ?></b></p>
            <p>Update your help request details below.</p>

            <?php if(isset($_SESSION['error'])) { ?>
                <div class="alert error">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php } ?>

            <br>

            <div class="resource-card">
                <form action="../verify/update_help_request.php" method="POST">
                    <input type="hidden" name="ticket_id" value="<?php echo $ticket['ticket_id']; ?>">

                    <label for="category"><b>Category:</b></label>
                    <br><br>

                    <select name="category" id="category" required>
                        <option value="Technical Issue" <?php if($ticket['category'] == "Technical Issue") echo "selected"; ?>>
                            Technical Issue
                        </option>
                        <option value="Internship Problem" <?php if($ticket['category'] == "Internship Problem") echo "selected"; ?>>
                            Internship Problem 
                        </option>
                        <option value="Presentation Issue" <?php if($ticket['category'] == "Presentation Issue") echo "selected";?>>
                            Presentation Issue
                        </option>
                        <option value="Other" <?php if($ticket['category'] == "Other") echo "selected"; ?>>
                            Other
                        </option>
                    </select>

                    <br><br>

                    <label for="message"><b>Message:</b></label>
                    <br><br>

                    <textarea name="message" id="message" rows="6" style="width: 100%;" required><?php echo $ticket['message']; ?></textarea>

                    <button type="submit" name="update_help_request">Update Request</button>

                    <a href="student_get_help.php" style="margin-left: 10px;">
                        Cancel
                    </a>

                </form>
            </div>
        </main>
    </div>
</body>
</html>