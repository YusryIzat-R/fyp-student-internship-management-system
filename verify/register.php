<?php

declare(strict_types=1);
session_start();

require_once __DIR__ . "/../config/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../public/register.php?err=Invalid request");
    exit;
}

$role  = $_POST["role"] ?? "";
$id    = trim($_POST["id"] ?? "");
$name  = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$pw    = $_POST["password"] ?? "";
$cpw   = $_POST["confirm_password"] ?? "";

// role validation
if (!in_array($role, ["student", "lecturer"], true)) {
    header("Location: ../public/register.php?err=Invalid role selected");
    exit;
}

if ($id === "" || $name === "" || $email === "" || $pw === "" || $cpw === "") {
    header("Location: ../public/register.php?err=Please fill in all fields");
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../public/register.php?err=Invalid email format");
    exit;
}
if (strlen($pw) < 6) {
    header("Location: ../public/register.php?err=Password must be at least 6 characters");
    exit;
}
if ($pw !== $cpw) {
    header("Location: ../public/register.php?err=Passwords do not match");
    exit;
}
$hash = password_hash($pw, PASSWORD_BCRYPT);

try {
    if ($role === "student") {
        $stmt = $pdo->prepare("INSERT INTO students (student_id, name, email, password, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$id, $name, $email, $hash]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO lecturers (lecturer_id, name, email, password, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$id, $name, $email, $hash]);
    }
    header("Location: ../public/register.php?ok=1");
    exit;
} catch (PDOException $e) {
    if ($e->getCode() === "23000") {
        header("Location: ../public/register.php?err=Registration failed. Please Try Again Another Time");
        exit;
    }
}
