<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin"){
    header("Location: ../public/login.php");
    exit;
}

$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : $_SESSION['login_id'];

$sql = "SELECT help_ticket.*,
               students.full_name AS student_name,
               students.program
        FROM help_ticket
        LEFT JOIN students
        ON help_ticket.student_id = students.student_no
        ORDER BY help_ticket.created_at DESC";

$result = mysqli_query($conn, $sql);

if(!$result) {
    die("Query Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Help Requests - CCI IMS</title>
    <link rel="stylesheet" href="../assets/css/dashboards.css">
</head>
<body>
    <div class="wrapper">
        <aside class="sidebar">
            <h3>Admin Menu</h3>

            <nav class="menu">
                <a href="../dashboards/admin_dashboard.php" class="menu-item">Dashboard</a>
                <a href="../dashboards/admin_announcement.php" class="menu-item">Announcements</a>
                <a href="../dashboards/manage_lecturers.php" class="menu-item">Visiting Lecturer Management</a>
                <a href="../dashboards/student_management.php" class="menu-item">Student Management</a>
                <a href="../dashboards/admin_required_submissions.php" class="menu-item">Required Submissions</a>
                <a href="../dashboards/admin_results.php" class="menu-item">Results</a>
                <a href="../dashboards/admin_help_requests.php" class="menu-item is-active">Get Help</a>
                <a href="../verify/logout.php" class="menu-item">Logout</a>
            </nav>
        </aside>

        <main class="content">
            <h1>Help Requests</h1>
            <p>Welcome, <b><?php echo $full_name; ?></b></p>
            <p>
                View and manage students' 
                internship help requests and complaints below:
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

            <?php
            if(mysqli_num_rows($result) > 0) {

                while($row = mysqli_fetch_assoc($result)) {

                    echo "<div class='resource-card'>";

                    echo "<h2>" . $row['category'] . "</h2>";

                    echo "<p><b>Student:</b> " .
                        $row['student_name'] .
                         " (" . $row['student_id'] . ")</p>";

                    echo "<p><b>Program:</b> " .
                         $row['program'] . "</p>";

                    echo "<p><b>Status:</b> " .
                         ucfirst($row['status']) . "</p>";

                    echo "<p><b>Submitted At:</b> " .
                         $row['created_at'] . "</p>";

                    if($row['handled_by'] != "") {
                        echo "<p><b>Handled By:</b> " .
                             $row['handled_by'] . "</p>";
                    }

                    echo "<br>";

                    echo "<p><b>Message:</b></p>";
                    echo "<p>" . nl2br($row['message']) . "</p>";

                    if($row['admin_reply'] != "") {

                        echo "<br>";

                        echo "<p><b>Admin Reply:</b></p>";

                        echo "<div style='background:#f5f5f5;
                                        padding:15px;
                                        border-radius:10px;'>";

                        echo nl2br($row['admin_reply']);

                        echo "</div>";
                    }

                    echo "<br>";

                    echo "<a href='review_help_request.php?id=" .
                         $row['ticket_id'] .
                         "' class='resource-btn'>
                         Review Request
                         </a>";

                    echo "</div>";

                    echo "<br>";
                }

            } else {

                echo "<div class='alert error'>";
                echo "No help requests found.";
                echo "</div>";
            }
            ?>

        </main>
    </div>
</body>
</html>
        