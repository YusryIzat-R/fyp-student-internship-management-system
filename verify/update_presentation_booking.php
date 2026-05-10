<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

$booking_page = "../dashboards/student_presentation_booking.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "student") {
    header("Location: ../public/login.php");
    exit;
}

if($_SERVER["REQUEST_METHOD"] != "POST") {
    $_SESSION['error'] = "Invalid request method.";
    header("Location: $booking_page");
    exit;
}

if(!isset($_POST['update_booking'])) {
    $_SESSION['error'] = "Form was not submitted properly.";
    header("Location: $booking_page");
    exit;
}

/* Check or retrieve data from the database */
$booking_id = trim($_POST['booking_id']);
$date = trim($_POST['date']);
$time_slot = trim($_POST['time_slot']);
$venue = trim($_POST['venue']);
$student_no = $_SESSION['student_no'];

/** Check for blank fields and show error message */
if($booking_id == "" || $date == "" || $time_slot == "" || $venue == "") {
    $_SESSION['error'] = "Please fill in all fields.";
    header("Location: $booking_page");
    exit;
}

/* Get student data */
$student_sql = "SELECT * FROM students WHERE student_no = '$student_no'";
$student_result = mysqli_query($conn, $student_sql);

/** Error message if no student records found */
if(!$student_result || mysqli_num_rows($student_result) == 0) {
    $_SESSION['error'] = "Student record not found.";
    header("Location: $booking_page");
    exit;
}

$student = mysqli_fetch_assoc($student_result);
$student_id = $student['id'];
$lecturer_id = $student['assigned_lecturer_id'];

if($lecturer_id == NULL) {
    $_SESSION['error'] = "You have not been assigned to a lecturer yet.";
    header("Location: $booking_page");
    exit;
}

/* Check booking belongs to student */
$booking_sql = "SELECT * FROM presentation_booking 
                WHERE booking_id = '$booking_id'
                AND student_id = '$student_id'";

$booking_result = mysqli_query($conn, $booking_sql);

if(!$booking_result || mysqli_num_rows($booking_result) == 0) {
    $_SESSION['error'] = "Booking not found.";
    header("Location: $booking_page");
    exit;
}

$booking = mysqli_fetch_assoc($booking_result);

if($booking['status'] == "accepted") {
    $_SESSION['error'] = "Accepted bookings cannot be edited.";
    header("Location: $booking_page");
    exit;
}

/* Check if selected slot is already taken by another booking */
$slot_check_sql = "SELECT * FROM presentation_booking
                   WHERE lecturer_id = '$lecturer_id'
                   AND date = '$date'
                   AND time_slot = '$time_slot'
                   AND status IN ('pending', 'accepted')
                   AND booking_id != '$booking_id'";

$slot_check_result = mysqli_query($conn, $slot_check_sql);

if($slot_check_result && mysqli_num_rows($slot_check_result) > 0) {
    $_SESSION['error'] = "This time slot is already booked. Please choose another slot.";
    header("Location: $booking_page");
    exit;
}

/* Update booking. If previously rejected, set back to pending */
$sql = "UPDATE presentation_booking
        SET date = '$date',
            time_slot = '$time_slot',
            venue = '$venue',
            status = 'pending',
            lecturer_comment = NULL,
            updated_at = NOW()
        WHERE booking_id = '$booking_id'
        AND student_id = '$student_id'";

$result = mysqli_query($conn, $sql);

if($result) {
    $_SESSION['success'] = "Presentation booking updated successfully. Waiting for lecturer approval.";
    header("Location: $booking_page");
    exit;
} else {
    $_SESSION['error'] = "Failed to update booking.";
    header("Location: $booking_page");
    exit;
}
?>