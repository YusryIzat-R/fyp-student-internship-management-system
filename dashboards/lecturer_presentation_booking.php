<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

if(!isset($_SESSION['role']) || $_SESSION['role'] != "lecturer") {
    header("Location: ../public/login.php");
    exit;
}

$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : $_SESSION['login_id'];
$login_id = $_SESSION['login_id'];

$lecturer_sql = "SELECT * FROM lecturers WHERE lecturer_no = '$login_id'";
$lecturer_result = mysqli_query($conn, $lecturer_sql);

if(!$lecturer_result || mysqli_num_rows($lecturer_result) == 0) {
    die("Lecturer record not found.");
}

$lecturer = mysqli_fetch_assoc($lecturer_result);
$lecturer_id = $lecturer['id'];

$sql = "SELECT presentation_booking.*, 
               students.student_no, 
               students.full_name AS student_name,
               students.email,
               students.program
        FROM presentation_booking
        INNER JOIN students ON presentation_booking.student_id = students.id
        WHERE presentation_booking.lecturer_id = '$lecturer_id'
        ORDER BY presentation_booking.date ASC, presentation_booking.time_slot ASC";

$result = mysqli_query($conn, $sql);

if(!$result) {
    die("Query Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Presentation Booking Requests - CCI IMS</title>
    <link rel="stylesheet" href="../assets/css/dashboards.css">
</head>
<body>
<div class="wrapper">
    <aside class="sidebar">
        <h3>Lecturer Menu</h3>

        <nav class="menu">
            <a href="lecturer_dashboard.php" class="menu-item">Dashboard</a>
            <a href="lecturer_announcement.php" class="menu-item">Announcements</a>
            <a href="lecturer_resources.php" class="menu-item">Internship Resources Management</a>
            <a href="lecturer_assigned_students.php" class="menu-item">My Students</a>
            <a href="lecturer_presentation_booking.php" class="menu-item is-active">Presentation Timeslot Management</a>
            <a href="#" class="menu-item">Grading</a>
            <a href="../verify/logout.php" class="menu-item">Logout</a>
        </nav>
    </aside>

    <main class="content">
        <h1>Presentation Booking Requests</h1>
        <p>Welcome, <b><?php echo $full_name; ?></b></p>
        <p>Review and manage presentation timeslot requests from your assigned students.</p>

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

        <table border="1" cellpadding="10" cellspacing="0" style="width:100%; background:white;">
            <tr>
                <th>No</th>
                <th>Student No</th>
                <th>Student Name</th>
                <th>Date</th>
                <th>Time Slot</th>
                <th>Venue</th>
                <th>Status</th>
                <th>Comment</th>
                <th>Action</th>
            </tr>

            <?php
            if(mysqli_num_rows($result) > 0) {
                $no = 1;

                while($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $no . "</td>";
                    echo "<td>" . $row['student_no'] . "</td>";
                    echo "<td>" . $row['student_name'] . "</td>";
                    echo "<td>" . $row['date'] . "</td>";
                    echo "<td>" . $row['time_slot'] . "</td>";
                    echo "<td>" . $row['venue'] . "</td>";
                    echo "<td>" . ucfirst($row['status']) . "</td>";
                    echo "<td>" . $row['lecturer_comment'] . "</td>";

                    echo "<td>";

                    if($row['status'] == "pending") {
                        echo "<form action='../verify/respond_presentation_booking.php' method='POST' style='margin-bottom:10px;'>";
                        echo "<input type='hidden' name='booking_id' value='" . $row['booking_id'] . "'>";
                        echo "<input type='hidden' name='response' value='accepted'>";
                        echo "<button type='submit' name='respond_booking' onclick='return confirm(\"Accept this booking?\");'>Accept</button>";
                        echo "</form>";

                        echo "<form action='../verify/respond_presentation_booking.php' method='POST'>";
                        echo "<input type='hidden' name='booking_id' value='" . $row['booking_id'] . "'>";
                        echo "<input type='hidden' name='response' value='rejected'>";
                        echo "<input type='text' name='lecturer_comment' placeholder='Reason for rejection' required>";
                        echo "<br><br>";
                        echo "<button type='submit' name='respond_booking' onclick='return confirm(\"Reject this booking?\");'>Reject</button>";
                        echo "</form>";
                    } else {
                        echo "-";
                    }

                    echo "</td>";
                    echo "</tr>";

                    $no++;
                }
            } else {
                echo "<tr><td colspan='9'>No presentation booking requests found.</td></tr>";
            }
            ?>
        </table>
    </main>
</div>
</body>
</html>