<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

if(!isset($_SESSION['role']) || $_SESSION['role'] != "student"){
    header("Location: ../public/login.php");
    exit;
}

$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : $_SESSION['login_id'];
$student_no = $_SESSION['student_no'];

/** Get student's help requests */
$sql = "SELECT * FROM help_ticket
        WHERE student_id = '$student_no'
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
        <title>Get Help - CCI IMS</title>
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
                <h1>Get Help</h1>
                <p>Welcome, <b><?php echo $full_name; ?></b></p>
                <p>
                    Submit internship-related issues, complaints, technical problems
                </p>

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

                <div class="resource-card">
                    <h2>Submit Help Request</h2>

                    <form action="../verify/submit_help_request.php" method="POST">
                        <label for="category"><b>Category:</b></label>
                        <br><br>

                        <select name="category" id="category" required>
                            <option value="">-- Select Category --</option>
                            <option value="Technical Issue">Technical Issue</option>
                            <option value="Internship Problem">Internship Problem</option>
                            <option value="Presentation Issue">Presentation Issue</option>
                            <option value="Other">Others</option>
                        </select>

                        <br><br>

                        <label for="message"><b>Message:</b></label>
                        <br><br>

                        <textarea name="message" id="message" rows="6" style="width: 100%;" placeholder="Describe your issue here" required></textarea>

                        <br><br>

                        <button type="submit" name="submit_help_request">
                            Submit Request
                        </button>

                    </form>

                </div>

                <br>

                <h2>My Help Requests</h2>

                <br>

                <?php 
                if(mysqli_num_rows($result) > 0){
                    while($row = mysqli_fetch_assoc($result)) {
                        echo "<div class='resource-card'>";
                        echo "<h3>" . $row['category'] . "</h3>";
                        echo "<p><b>Status:</b> " . ucfirst($row['status']) . "</p>";
                        echo "<p><b>Submitted At:</b>" . $row['created_at'] . "</p>";
                        echo "<br>";

                        echo "<p><b>Message:</b></p>";
                        echo "<p>" . nl2br($row['message']) . "</p>";

                        if(isset($row['admin_reply']) && $row['admin_reply'] != "") {
                            echo "<br>";
                            echo "<p><b>Admin Reply:</b></p>";
                            echo "<div style='background:#f5f5f5;
                                              padding:15px;
                                              border-radius: 10px;'>";

                            echo nl2br($row['admin_reply']);

                            echo "</div>";
                        }

                        echo "<br>";

                        /** Allow edit/delete only if admin still not accept and no reply yet */
                        if($row['status'] == "submitted" && (!isset($row['admin_reply']) || $row['admin_reply'] == "" || $row['admin_reply'] == null)) {
                            echo "<a href='edit_help_request.php?id=" . $row['ticket_id'] . "'>Edit</a>";

                            echo " | ";

                            echo "<a href='../verify/delete_help_request.php?id=" . $row['ticket_id'] . "' onclick='return confirm(\"Delete this request?\");'>Delete</a>";
                        }
                        echo "</div>";
                        echo "<br>";
                    }

                } else {
                    echo "<div class='alert error'>";
                    echo "No help requests submitted yet.";
                    echo "</div>";
                }
                ?>

            </main>
        </div>
    </body>
</html>
