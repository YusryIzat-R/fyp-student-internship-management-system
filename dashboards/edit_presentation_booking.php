<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

if(!isset($_SESSION['role']) || $_SESSION['role'] != "student") {
    header("Location: ../public/login.php");
    exit;
}

if(!isset($_GET['id'])) {
    $_SESSION['error'] = "Booking ID not found!";
    header("Location: student_presentation_booking.php");
    exit;
}

$booking_id = $_GET['id'];
$student_no = $_SESSION['student_no'];

$student_sql = "SELECT * FROM students WHERE student_no = '$student_no'";
$student_result = mysqli_query($conn, $student_sql);

$student = mysqli_fetch_assoc($student_result);
$student_id = $student['id'];

$sql = "SELECT * FROM presentation_booking
        WHERE booking_id = '$booking_id'
        AND student_id = '$student_id'";

$result = mysqli_query($conn, $sql);

if(!$result || mysqli_num_rows($result) == 0) {
    $_SESSION['error'] = "No Booked Timeslot found!";
    header("Locatin: student_presentation_booking.php");
    exit;
}

$booking = mysqli_fetch_assoc($result);

/* Prevent editing accepted booking */
if($booking['status'] == "accepted") {
    $_SESSION['error'] = "Accepted bookings cannot be edited.";
    header("Location: student_presentation_booking.php");
    exit;
}

$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : $_SESSION['lgin_id'];
?>

<!DOCTYPE >
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Presentation Booking - CCI IMS</title>
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
            <h1>Edit Presentation Booking</h1>
            <p>Welcome, <b><?php echo $full_name; ?></b></p>
            <p>Update your presentation booking details below.</p>

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

            <form action="../verify/update_presentation_booking.php" method="POST">
                <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                
                <label for="date">Presentation Date:</label><br>
                <input type="date" name="date" id="date" value="<?php echo $booking['date']; ?>" min="<?php echo date('Y-m-d'); ?>" required>
                <br><br>

                <label for="time_slot">Time Slot:</label><br>
                <select name="time_slot" id="time_slot" required>
                    <option value="09:00 AM - 10:00 AM"
                        <?php if($booking['time_slot'] == "09:00 AM - 10:00 AM") echo "selected"; ?>>
                        09:00 AM - 10:00 AM
                    </option>

                    <option value="10:00 AM - 11:00 AM"
                        <?php if($booking['time_slot'] == "10:00 AM - 11:00 AM") echo "selected"; ?>>
                        10:00 AM - 11:00 AM
                    </option>

                    <option value="11:00 AM - 12:00 PM"
                        <?php if($booking['time_slot'] == "11:00 AM - 12:00 PM") echo "selected"; ?>>
                        11:00 AM - 12:00 PM
                    </option>

                    <option value="02:00 PM - 03:00 PM"
                        <?php if($booking['time_slot'] == "02:00 PM - 03:00 PM") echo "selected"; ?>>
                        02:00 PM - 03:00 PM
                    </option>

                    <option value="03:00 PM - 04:00 PM"
                        <?php if($booking['time_slot'] == "03:00 PM - 04:00 PM") echo "selected"; ?>>
                        03:00 PM - 04:00 PM
                    </option>
                </select>

                <br><br>

                <label for="venue">Venue:</label><br>
                <input type="text"
                   name="venue"
                   id="venue"
                   value="<?php echo $booking['venue']; ?>"
                   required>

                <br><br>

                <button type="submit" name="update_booking">
                    Update Booking
                </button>

                <a href="student_presentation_booking.php"
                style="margin-left: 10px;">
                    Cancel
                </a>

            </form>
        </main>
    </div>
</body>
</html>