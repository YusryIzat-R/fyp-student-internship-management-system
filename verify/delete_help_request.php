<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

$help_page = "../dashboards/student_get_help.php";

/** Check if the session or user logged in as a student */
if(!isset($_SESSION['role']) || $_SESSION['role'] != "student") {
    header("Location: ../public/login.php");
    exit;
}

if(!isset($_GET['id'])) {
    $_SESSION['error'] = "Help request ID not found.";
    header("Location: $help_page");
    exit;
}

$ticket_id = trim($_GET['id']);
$student_no = $_SESSION['student_no'];

/* Check ticket belongs to student */
$check_sql = "SELECT * FROM help_ticket
              WHERE ticket_id = '$ticket_id'
              AND student_id = '$student_no'
              LIMIT 1";

$check_result = mysqli_query($conn, $check_sql);

/** Check if the ticket exists and display error message if doesn't exist */
if(!$check_result || mysqli_num_rows($check_result) == 0) {
    $_SESSION['error'] = "Help request not found.";
    header("Location: $help_page");
    exit;
}

$ticket = mysqli_fetch_assoc($check_result);

/* Only allow delete if still submitted and no admin reply yet */
if($ticket['status'] != "submitted" || $ticket['admin_reply'] != "") {
    $_SESSION['error'] = "This help request can no longer be deleted.";
    header("Location: $help_page");
    exit;
}

$delete_sql = "DELETE FROM help_ticket
               WHERE ticket_id = '$ticket_id'
               AND student_id = '$student_no'";

$delete_result = mysqli_query($conn, $delete_sql);

/** Success message and error message for delete operation */
if($delete_result) {
    $_SESSION['success'] = "Help request deleted successfully.";
    header("Location: $help_page");
    exit;
} else {
    $_SESSION['error'] = "Failed to delete help request.";
    header("Location: $help_page");
    exit;
}
?>