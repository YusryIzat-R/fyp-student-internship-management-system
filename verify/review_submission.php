<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

$back_page = "../dashboards/lecturer_submissions.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "lecturer") {
    header("Location: ../public/login.php");
    exit;
}

if($_SERVER["REQUEST_METHOD"] != "POST") {
    $_SESSION['error'] = "Invalid request method.";
    header("Location: $back_page");
    exit;
}

if(!isset($_POST['review_submission'])) {
    $_SESSION['error'] = "Form was not submitted properly! Please try again.";
    header("Location: $back_page");
    exit;
}

$submission_id = trim($_POST['submission_id']);
$review_action = trim($_POST['review_action']);
$lecturer_comment = "";

if(isset($_POST['lecturer_comment'])) {
    $lecturer_comment = trim($_POST['lecturer_comment']);
}

if($submission_id == "" || $review_action == "") {
    $_SESSION['error'] = "Invalid review action. Please try again.";
    header("Location: $back_page");
    exit;
}

if($review_action != "approved" && $review_action != "rejected") {
    $_SESSION['error'] = "Invalid review action. Please try again.";
    header("Location: $back_page");
    exit;
}

if($review_action == "rejected" && $lecturer_comment == "") {
    $_SESSION['error'] = "Please enter a reason for rejection of the submission.";
    header("Location: $back_page");
    exit;
}

$lecturer_no = $_SESSION['login_id'];

/** Make sure this submission belongs to this lecturer's resources */
$check_sql = "SELECT submissions.*, resources.lecturer_id
              FROM submissions
              INNER JOIN resources ON submissions.resource_id = resources.resource_id
              WHERE submissions.submission_id = '$submission_id'
              AND resources.lecturer_id = '$lecturer_no'";

$check_result = mysqli_query($conn, $check_sql);

if(!$check_result || mysqli_num_rows($check_result) == 0) {
    $_SESSION['error'] = "Submission not found or your are not allowed to review it.";
    header("Location: $back_page");
    exit;
}

$submission= mysqli_fetch_assoc($check_result);

if($submission['status'] != "pending") {
    $_SESSION['error'] = "Only pending submissions can be reviewed by the lecturer.";
    header("Location: $back_page");
    exit;
}

if($review_action == "approved") {
    $sql = "UPDATE submissions
            SET status = 'approved',
                lecturer_comment = NULL,
                updated_at = NOW()
            WHERE submission_id = '$submission_id'";
} else {
    $sql = "UPDATE submissions
            SET status = 'rejected',
                lecturer_comment = '$lecturer_comment',
                updated_at = NOW()
            WHERE submission_id = '$submission_id'";
}

$result = mysqli_query($conn, $sql);

if($result) {
    $_SESSION['success'] = "Submission has been " . $review_action . " successfully.";
    header("Location: $back_page");
    exit;
} else {
    $_SESSION['error'] = "Failed to update submission status. Please try again.";
    header("Location: $back_page");
    exit;
}
?>