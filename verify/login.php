<?php 
declare(strict_types=1);

session_start();
require_once __DIR__ . "/../config/db.php";

if($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../public/login.php?err=Invalid request");
    exit;
}

$role  = $_POST["role"] ?? "";
$id    = trim($_POST["id"] ?? "");
$pw    = $_POST["password"] ?? "";

if(!in_array($role, ["student", "lecturer", "admin"], true)) {
    header("Location: ../public/login.php?err= Invalid role selected");
    exit;
}

if($id === "" || $pw === "") {
    header("Location: ../public/login.php?err= Please fill in all fields");
    exit;
}

$table = "";
$idColumn = "";

if ($role === "student") {
    $table = "students";
    $idColumn = "student_id";
} elseif ($role === "lecturer") {
    $table = "lecturers";
    $idColumn = "lecturer_id";
} else {
    $table = "admin";
    $idColumn = "admin_id";
}

try {
    $stmt = $pdo->prepare("SELECT $idColumn AS uid, password FROM $table WHERE $idColumn = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($pw, $user["password"])) {
        header("Location: ../public/login.php?err=Invalid ID or Password");
        exit;
    }

    $_SESSION["role"] = $role;
    $_SESSION["uid"]  = $user["uid"];

    // Redirect based on role
    if ($role === "student") {
        header("Location: ../dashboards/student_dashboard.php");
        exit;
    } elseif ($role === "lecturer") {
        header("Location: ../dashboards/lecturer_dashboard.php");
        exit;
    } else {
        header("Location: ../dashboards/admin_dashboard.php");
        exit;
    }

} catch (PDOException $e) {
    // Hide DB error for security
    header("Location: ../public/login.php?err=Login failed. Please try again.");
    exit;
}
