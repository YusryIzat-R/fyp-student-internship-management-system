<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'student') {
    header("Location: ../public/login.php");
    exit;
}

$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : $_SESSION['login_id'];
$student_no = $_SESSION['student_no'];

$student_sql = "SELECT * FROM students WHERE student_no = '$student_no'";
$student_result = mysqli_query($conn, $student_sql);
$student = mysqli_fetch_assoc($student_result);

$student_id = $student['id'];
$lecturer_id = $student['assigned_lecturer_id'];

$booking_sql = "SELECT * FROM presentation_booking
                WHERE student_id = '$student_id'
                ORDER BY created_at DESC
                LIMIT 1";

$booking_result = mysqli_query($conn, $booking_sql);
$booking = NULL;

if($booking_result && mysqli_num_rows($booking_result) > 0) {
    $booking = mysqli_fetch_assoc($booking_result);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Presentation Timeslot Booking</title>
    <link rel="stylesheet" href="../assets/css/dashboards.css">
</head>
<body>
    <div class="wrapper">
        <aside class="sidebar">
            <h3>Student Menu</h3>

            <nav class="menu">
                <a href="../dashboards/student_dashboard.php" class="menu-item">Dashboard</a>
                <a href="../dashboards/student_announcement.php" class="menu-item">Announcements</a>
                <a href="#" class="menu-item">Resources</a>
                <a href="../dashboards/student_assigned_lecturer.php" class="menu-item">My Lecturer</a>
                <a href="../dashboards/student_presentation_booking.php" class="menu-item is-active">Presentation Booking</a>
                <a href="#" class="menu-item">My Result</a>
                <a href="#" class="menu-item">Get Help</a>
                <a href="../verify/logout.php" class="menu-item">Logout</a>
            </nav>
        </aside>

        <main class="content">
            <h1>Presentation Booking</h1>
            <p>Welcome, <b><?php echo $full_name; ?></b></p>
            <p>Book your presentation timeslot here.</p>

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

            <?php if($lecturer_id == NULL) { ?>
                <p style='color: red;'>You have not been assigned to a lecturer yet. Please contact your admin.</p>
            <?php } else if($booking == NULL) { ?>
                
                <h2>Book Presentation slot here</h2>

                <form action="../verify/add_presentation_booking.php" method="POST">
                    <label for="date">Presentation Date:</label><br>
                    <input type="date" name="date" id="date" required>
                    <br><br>

                    <label for="time_slot">Time Slot:</label><br>
                    <select name="time_slot" id="time_slot" required>
                        <option value="">-- Select Timeslot --</option>
                        <option value="09:00 AM - 10:00 AM">09:00 AM - 10:00 AM</option>
                        <option value="10:00 AM - 11:00 AM">10:00 AM - 11:00 AM</option>
                        <option value="11:00 AM - 12:00 AM">11:00 AM - 12:00 AM</option>
                        <option value="02:00 PM - 03:00 PM">02:00 PM - 03:00 PM</option>
                        <option value="03:00 PM - 04:00 PM">03:00 PM - 04:00 PM</option>
                    </select>
                    <br><br>

                    <label for="venue">Venue:</label><br>
                    <input type="text" name="venue" id="venue" placeholder="Example: Google meet / Zoom / Microsoft Teams Meetings" required>
                    <br><br>

                    <button type="submit" name="add_booking">Book Slot</button>
                </form>
            <?php } else { ?>
                <h2>Your Booking</h2>

                <p><b>Date:</b><?php echo $booking['date']; ?></p>
                <p><b>Time Slot:</b><?php echo $booking['time_slot']; ?></p>
                <p><b>Venue:</b><?php echo $booking['venue']; ?></p>
                <p><b>Status:</b><?php echo ucfirst($booking['date']); ?></p>

                <?php if($booking['lecturer_comment'] != "") { ?>
                    <p><b>Lecturer Comment:</b><?php echo $booking['lecturer_comment']; ?></p>
                <?php } ?>

                <?php if($booking['status'] == "pending" || $booking['status'] == "rejected") { ?>
                    <br>
                    <a href="edit_presentation_booking.php?id=<?php echo $booking['booking_id']; ?>">Edit Booking</a>
                <?php } else { ?>
                    <p style="color:green;">Your booking has been accepted and can no longer be edited.</p>
                <?php } ?>
                    
                <?php } ?>
        </main>
    </div>
</body>
</html>