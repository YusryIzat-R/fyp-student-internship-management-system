<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

$help_page = "../dashboards/student_get_help.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "student") {
    header("Location: ../public/login.php");
    exit;
}

if($_SERVER["REQUEST_METHOD"] != "POST") {
    $_SESSION['error'] = "Invalid request method.";
    header("Location: $help_page");
    exit;
}

/** Validate form submitted */
if(!isset($_POST['update_help_request'])) {
    $_SESSION['error'] = "Form was not submitted properly.";
    header("Location: $help_page");
    exit;
}

$ticket_id = trim($_POST['ticket_id']);
$category = trim($_POST['category']);
$message = trim($_POST['message']);
$student_no = $_SESSION['student_no'];

/** Validate input (error message if no field is filled) */
if($ticket_id == "" || $category == "" || $message == "") {
    $_SESSION['error'] = "Please fill in all fields.";
    header("Location: ../dashboards/edit_help_request.php?id=$ticket_id");
    exit;
}

/* Check get help ticket belongs to student */
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

/* Only allow update if submitted and no admin reply yet */
if($ticket['status'] != "submitted" || $ticket['admin_reply'] != "") {
    $_SESSION['error'] = "This help request can no longer be edited.";
    header("Location: $help_page");
    exit;
}

$update_sql = "UPDATE help_ticket
               SET category = '$category',
                   message = '$message',
                   updated_at = NOW()
               WHERE ticket_id = '$ticket_id'
               AND student_id = '$student_no'";

$update_result = mysqli_query($conn, $update_sql);

/** Success message and error message for update operation */
if($update_result) {
    $_SESSION['success'] = "Help request updated successfully.";
    header("Location: $help_page");
    exit;
} else {
    $_SESSION['error'] = "Failed to update help request.";
    header("Location: ../dashboards/edit_help_request.php?id=$ticket_id");
    exit;
}
?>