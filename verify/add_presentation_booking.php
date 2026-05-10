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

if(!isset($_POST['add_booking'])) {
    $_SESSION['error'] = "Form was not submitted properly";
    header("Location: $booking_page");
    exit;
}

$date = trim($_POST['date']);
$time_slot = trim($_POST['time_slot']);
$venue = trim($_POST['venue']);

$student_no = $_SESSION['student_no'];

if($date == "" || $time_slot == "" || $venue == "") {
    $_SESSION['error'] = "Please fill in all fields.";
    header("Location: $booking_page");
    exit;
}

/** Get Student data */
$student_sql = "SELECT * FROM students WHERE student_no = '$student_no'";
$student_result = mysqli_query($conn, $student_sql);

if(!$student_result || mysqli_num_rows($student_result) == 0) {
    $_SESSION['error'] = "Student record not found.";
    header("Location: $booking_page");
    exit;
}

$student = mysqli_fetch_assoc($student_result);

$student_id = $student['id'];
$lecturer_id = $student['assigned_lecturer_id'];

if($lecturer_id == NULL) {
    $_SESSION['error'] == "You have not been assigned to a lecturer yet.";
    header("Location: $booking_page");
    exit;
}

/** Check if student already has pending or accepted booking */
$booking_check_sql = "SELECT * FROM presentation_booking
                      WHERE student_id = '$student_id'
                      AND status IN ('pending', 'accepted')";

$booking_check_result = mysqli_query($conn, $booking_check_sql);

if($booking_check_result && mysqli_num_rows($booking_check_result) > 0) {
    $_SESSION['error'] = "You already have an active booking!";
    exit;
}

/** Check if same lecturer already has same date and time slot accepted/pending */
$slot_check_sql = "SELECT * FROM presentation_booking
                   WHERE lecturer_id = '$lecturer_id'
                   AND date = '$date'
                   AND time_slot = '$time_slot'
                   AND status IN ('pending', 'accepted')";
                
$slot_check_result = mysqli_query($conn, $slot_check_sql);

if($slot_check_result && mysqli_num_rows($slot_check_result) > 0) {
    $_SESSION['error'] = "This time slot is already booked. Please choose another slot.";
    header("Location: $booking_page");
    exit;
}

/*  Insert booking */
$sql = "INSERT INTO presentation_booking
        (student_id, lecturer_id, date, time_slot, venue, status)
        VALUES
        ('$student_id', '$lecturer_id', '$date', '$time_slot', '$venue', 'pending')";

$result = mysqli_query($conn, $sql);

if($result) {
    $_SESSION['success'] = "Presentation booking submitted successfully. Waiting for lecturer approval.";
    header("Location: $booking_page");
    exit;
} else {
    $_SESSION['error'] = "Failed to submit booking.";
    header("Location: $booking_page");
    exit;
}
?>