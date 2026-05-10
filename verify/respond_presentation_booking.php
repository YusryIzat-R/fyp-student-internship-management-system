<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

$back_page = "../dashboards/lecturer_presentation_booking.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "lecturer") {
    header("Location: ../public/login.php");
    exit;
}

if($_SERVER["REQUEST_METHOD"] != "POST") {
    $_SESSION['error'] = "Invalid request method.";
    header("Location: $back_page");
    exit;
}

if(!isset($_POST['respond_booking'])) {
    $_SESSION['error'] = "Form was not submitted properly.";
    header("Location: $back_page");
    exit;
}

$booking_id = trim($_POST['booking_id']);
$response = trim($_POST['response']);
$lecturer_comment = "";

if(isset($_POST['lecturer_comment'])) {
    $lecturer_comment = trim($_POST['lecturer_comment']);
}

if($booking_id == "" || $response == "") {
    $_SESSION['error'] = "Invalid booking response.";
    header("Location: $back_page");
    exit;
}

if($response != "accepted" && $response != "rejected") {
    $_SESSION['error'] = "Invalid response type.";
    header("Location: $back_page");
    exit;
}

if($response == "rejected" && $lecturer_comment == "") {
    $_SESSION['error'] = "Please enter a reason for rejection.";
    header("Location: $back_page");
    exit;
}

/* Get lecturer id */
$login_id = $_SESSION['login_id'];

$lecturer_sql = "SELECT * FROM lecturers WHERE lecturer_no = '$login_id'";
$lecturer_result = mysqli_query($conn, $lecturer_sql);

if(!$lecturer_result || mysqli_num_rows($lecturer_result) == 0) {
    $_SESSION['error'] = "Lecturer record not found.";
    header("Location: $back_page");
    exit;
}

$lecturer = mysqli_fetch_assoc($lecturer_result);
$lecturer_id = $lecturer['id'];

/* Make sure booking belongs to this lecturer */
$booking_sql = "SELECT * FROM presentation_booking 
                WHERE booking_id = '$booking_id'
                AND lecturer_id = '$lecturer_id'";

$booking_result = mysqli_query($conn, $booking_sql);

if(!$booking_result || mysqli_num_rows($booking_result) == 0) {
    $_SESSION['error'] = "Booking not found or you are not allowed to update this booking.";
    header("Location: $back_page");
    exit;
}

$booking = mysqli_fetch_assoc($booking_result);

if($booking['status'] != "pending") {
    $_SESSION['error'] = "Only pending bookings can be updated.";
    header("Location: $back_page");
    exit;
}

if($response == "accepted") {
    $sql = "UPDATE presentation_booking
            SET status = 'accepted',
                lecturer_comment = NULL,
                updated_at = NOW()
            WHERE booking_id = '$booking_id'
            AND lecturer_id = '$lecturer_id'";
} else {
    $sql = "UPDATE presentation_booking
            SET status = 'rejected',
                lecturer_comment = '$lecturer_comment',
                updated_at = NOW()
            WHERE booking_id = '$booking_id'
            AND lecturer_id = '$lecturer_id'";
}

$result = mysqli_query($conn, $sql);

if($result) {
    $_SESSION['success'] = "Booking has been " . $response . " successfully.";
    header("Location: $back_page");
    exit;
} else {
    $_SESSION['error'] = "Failed to update booking response.";
    header("Location: $back_page");
    exit;
}
?>