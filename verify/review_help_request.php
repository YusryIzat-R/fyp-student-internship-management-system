<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

$admin_help_page = "../dashboards/admin_help_requests.php";

/** Check for sessions belongs to or which user logged in */
if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("Location: ../public/login.php");
    exit;
}

/** REquest method validation */
if($_SERVER["REQUEST_METHOD"] != "POST") {
    $_SESSION['error'] = "Invalid request method.";
    header("Location: $admin_help_page");
    exit;
}

/** Check if form was submitted properly */
if(!isset($_POST['review_help_request'])) {
    $_SESSION['error'] = "Form was not submitted properly.";
    header("Location: $admin_help_page");
    exit;
}

/** Get data/information from the database */
$ticket_id = trim($_POST['ticket_id']);
$status = trim($_POST['status']);
$admin_reply = trim($_POST['admin_reply']);
$handled_by = $_SESSION['login_id'];

$back_page = "../dashboards/review_help_request.php?id=$ticket_id";

/** Validation/error message for invalid help request */
if($ticket_id == "" || $status == "") {
    $_SESSION['error'] = "Invalid help request.";
    header("Location: $admin_help_page");
    exit;
}

/** Error for invalid status selected */
if($status != "submitted" && $status != "in progress" && $status != "resolved" && $status != "rejected") {
    $_SESSION['error'] = "Invalid status selected.";
    header("Location: $back_page");
    exit;
}

/* Check ticket exists */
$check_sql = "SELECT * FROM help_ticket 
              WHERE ticket_id = '$ticket_id'
              LIMIT 1";

$check_result = mysqli_query($conn, $check_sql);

if(!$check_result || mysqli_num_rows($check_result) == 0) {
    $_SESSION['error'] = "Help request not found.";
    header("Location: $admin_help_page");
    exit;
}

/** Update response from admin */
$update_sql = "UPDATE help_ticket
               SET status = '$status',
                   admin_reply = '$admin_reply',
                   handled_by = '$handled_by',
                   updated_at = NOW()
               WHERE ticket_id = '$ticket_id'";

$update_result = mysqli_query($conn, $update_sql);

/** Success or error message for response operation */
if($update_result) {
    $_SESSION['success'] = "Help request updated successfully.";
    header("Location: $back_page");
    exit;
} else {
    $_SESSION['error'] = "Failed to update help request.";
    header("Location: $back_page");
    exit;
}
?>