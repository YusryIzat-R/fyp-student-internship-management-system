<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

$resource_page = "../dashboards/lecturer_resources.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "lecturer") {
    header("Location: ../public/login.php");
    exit;
}

if(!isset($_GET['id'])) {
    $_SESSION['error'] = "Resource ID not found.";
    header("Location: $resource_page");
    exit;
}

$resource_id = $_GET['id'];
$lecturer_id = $_SESSION['login_id'];

/* Check resource exists and belongs to lecturer */
$check_sql = "SELECT * FROM resources
              WHERE resource_id = '$resource_id'
              AND lecturer_id = '$lecturer_id'";

$check_result = mysqli_query($conn, $check_sql);

if(!$check_result || mysqli_num_rows($check_result) == 0) {
    $_SESSION['error'] = "Resource not found or you are not allowed to delete this resource.";
    header("Location: $resource_page");
    exit;
}

$resource = mysqli_fetch_assoc($check_result);

$file_path = "../" . $resource['file_path'];

/* Delete database record first */
$delete_sql = "DELETE FROM resources
               WHERE resource_id = '$resource_id'
               AND lecturer_id = '$lecturer_id'";

$delete_result = mysqli_query($conn, $delete_sql);

if($delete_result) {

    /* Delete physical file if exists */
    if(file_exists($file_path)) {
        unlink($file_path);
    }

    $_SESSION['success'] = "Resource deleted successfully.";
    header("Location: $resource_page");
    exit;

} else {
    $_SESSION['error'] = "Failed to delete resource.";
    header("Location: $resource_page");
    exit;
}
?>