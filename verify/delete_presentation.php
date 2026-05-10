<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

$booking_page = "../dashboards/student_presentation_booking.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "student") {
    header("Location: ../public/login.php");
    exit;
}

if(!isset($_GET['id'])) {
    $_SESSION['error'] = "No Booking ID found.";
    exit;
}

$booking_id = $_GET['id'];
$student_no = $_SESSION['student_no'];

/** Get student's data */
$student_sql = "SELECT * FROM students WHERE student_no = '$student_no'";
$student_result = mysqli_query($conn, $student_sql);

if(!$student_result || mysqli_num_rows($student_result) == 0) {
    $_SESSION['error'] = "Student's record not found.";
    exit;
}

$student = mysqli_fetch_assoc($student_result);
$student_id = $student['id'];

/** Check timeslot booked belongs to which student */
$booking_sql = "SELECT * FROM presentation_booking
                WHERE booking_id = '$booking_id'
                AND student_id = '$student_id'";

$booking_result = mysqli_query($conn, $booking_sql);

if(!$booking_result || mysqli_num_rows($booking_result) == 0) {
    $_SESSION['error'] = "Booking record not found.";
    header("Location: $booking_page");
    exit;
}

$booking = mysqli_fetch_assoc($booking_result);

/** Do not allow delete if the timeslot booked accepted */
if($booking['status'] == "accepted") {
    $_SESSION['error'] = "This booking can no be deleted because it has been accepted by the lecturer.";
    header("Location: $booking_page");
    exit;
}

/** Delete the booked timeslot */
$delete_sql = "DELETE FROM presentation_booking 
               WHERE booking_id = '$booking_id' 
               AND student_id = '$student_id'";
               
$delete_result = mysqli_query($conn, $delete_sql);

if($delete_result) {
    $_SESSION['success'] = "Presentation timeslot booking deleted successfully";
    header("Location: $booking_page");
    exit;
} else {
    $_SESSION['error'] = "Failed to delete booking. Please try again.";
    header("Location: $booking_page");
    exit;
}
?>