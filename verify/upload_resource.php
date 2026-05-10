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
    $_SESSION['error'] = "Invalid request method!";
    header("Location: $resource_page");
    exit;
}

if(!isset($_POST["upload_resource"])) {
    $_SESSION['error'] = "Form was not submitted properly!";
    header("Location: $resource_page");
    exit;
}

$title = trim($_POST['title']);
$category = trim($_POST['category']);
$lecturer_id = $_SESSION['login_id'];

/** Error message for empty fields */
if($title == "" || $category == "") {
    $_SESSION['error'] = "Please fill in all the fields and choose the options first!";
    header("Location: $resource_page");
    exit;
}

/** Error message for wrong uploaded file format */
if(!isset($_FILES['resource_file']) || $_FILES['resource_file']['error'] != 0) {
    $_SESSION['error'] = "Please upload a valid file format!";
    header("Location: $resource_page");
    exit;
}

$file_name = $_FILES['resource_file']['name'];
$file_tmp = $_FILES['resource_file']['tmp_name'];
$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

$allowed_ext = array("pdf", "doc", "docx", "ppt", "pptx");

/** Other file format validation error message */
if(!in_array($file_ext, $allowed_ext)) {
    $_SESSION['error'] = "Only PDF, word and PowerPoint files are allowed!";
    header("Location: $resource_page");
    exit;
}

/** upload folder filepath */
$upload_folder = "../uploads/resources/";

if(!is_dir($upload_folder)) {
    mkdir($upload_folder, 0777, true);
}

$new_file_name = time() . "_" . $file_name;
$target_path = $upload_folder . $new_file_name;

if(move_uploaded_file($file_tmp, $target_path)) {
    $file_path = "uploads/resources/" . $new_file_name;
    
    $sql = "INSERT INTO resources (lecturer_id, title, category, file_path)
            VALUES ('$lecturer_id', '$title', '$category', '$file_path')";

    if(mysqli_query($conn, $sql)) {
        $_SESSION['success'] = "Resources uploaded succcessfully";
        header("Location: $resource_page");
        exit;
    } else {
        $_SESSION['error'] = "Failed to save resources information. Please try again.";
        header("Location: $resource_page");
        exit;
    }
} else {
    $_SESSION['error'] = "Failed to upload file. Please try again.";
    header("Location: $resource_page");
    exit;
}
?>