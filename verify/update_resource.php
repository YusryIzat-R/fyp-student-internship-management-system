<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

$resource_page = "../dashboards/lecturer_resources.php";

if(!isset($_SESSION['role']) || $_SESSION['role'] != "lecturer") {
    header("Location: ../public/login.php");
    exit;
}

if($_SERVER["REQUEST_METHOD"] != "POST") {
    $_SESSION['error'] = "Invalid request method.";
    header("Location: $resource_page");
    exit;
}

if(!isset($_POST['update_resource'])) {
    $_SESSION['error'] = "Form was not submitted properly.";
    header("Location: $resource_page");
    exit;
}

$resource_id = trim($_POST['resource_id']);
$old_file_path = trim($_POST['old_file_path']);
$title = trim($_POST['title']);
$category = trim($_POST['category']);
$lecturer_id = $_SESSION['login_id'];

if($resource_id == "" || $title == "" || $category == "") {
    $_SESSION['error'] = "Please fill in all required fields.";
    header("Location: ../dashboards/edit_resource.php?id=$resource_id");
    exit;
}

/* Check resource belongs to lecturer */
$check_sql = "SELECT * FROM resources 
              WHERE resource_id = '$resource_id'
              AND lecturer_id = '$lecturer_id'";

$check_result = mysqli_query($conn, $check_sql);

if(!$check_result || mysqli_num_rows($check_result) == 0) {
    $_SESSION['error'] = "Resource not found or you are not allowed to update this resource.";
    header("Location: $resource_page");
    exit;
}

$file_path = $old_file_path;

/* If lecturer uploads a new replacement file */
if(isset($_FILES['resource_file']) && $_FILES['resource_file']['error'] == 0) {

    $file_name = $_FILES['resource_file']['name'];
    $file_tmp = $_FILES['resource_file']['tmp_name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    $allowed_ext = array("pdf", "doc", "docx", "ppt", "pptx");

    if(!in_array($file_ext, $allowed_ext)) {
        $_SESSION['error'] = "Only PDF, Word, and PowerPoint files are allowed.";
        header("Location: ../dashboards/edit_resource.php?id=$resource_id");
        exit;
    }

    $upload_folder = "../uploads/resources/";

    if(!is_dir($upload_folder)) {
        mkdir($upload_folder, 0777, true);
    }

    $new_file_name = time() . "_" . $file_name;
    $target_path = $upload_folder . $new_file_name;

    if(move_uploaded_file($file_tmp, $target_path)) {

        $file_path = "uploads/resources/" . $new_file_name;

        /* Delete old file if exists */
        $old_server_path = "../" . $old_file_path;

        if(file_exists($old_server_path)) {
            unlink($old_server_path);
        }

    } else {
        $_SESSION['error'] = "Failed to upload replacement file.";
        header("Location: ../dashboards/edit_resource.php?id=$resource_id");
        exit;
    }
}

/* Update resource record */
$sql = "UPDATE resources
        SET title = '$title',
            category = '$category',
            file_path = '$file_path'
        WHERE resource_id = '$resource_id'
        AND lecturer_id = '$lecturer_id'";

$result = mysqli_query($conn, $sql);

if($result) {
    $_SESSION['success'] = "Resource updated successfully.";
    header("Location: $resource_page");
    exit;
} else {
    $_SESSION['error'] = "Failed to update resource.";
    header("Location: ../dashboards/edit_resource.php?id=$resource_id");
    exit;
}
?>