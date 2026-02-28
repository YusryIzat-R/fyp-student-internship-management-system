<?php
declare(strict_types=1);
require_once __DIR__ . "/../config/db.php";

$message = "";
$step = "verify";
$role = "";
$id = "";
$email = "";

$table = "";
$idColumn = "";

$action = $_POST["action"] ?? ""; // FIX: define outside

function mapRole(string $role): array {
    if ($role === "student") return ["students", "student_id"];
    if ($role === "lecturer") return ["lecturers", "lecturer_id"];
    return ["admin", "admin_id"];
}

if ($_SERVER["REQUEST_METHOD"] === "POST") { // FIX: REQUEST_METHOD
    if ($action === "verify") {
        $role = $_POST["role"] ?? "";
        $id = trim($_POST["id"] ?? "");
        $email = trim($_POST["email"] ?? "");

        if (!in_array($role, ["student", "lecturer", "admin"], true)){
            $message = "Invalid role selected.";
        } else if ($id === "" || $email === "") {
            $message = "Please fill in the required fields.";
        } else {
            [$table, $idColumn] = mapRole($role);

            $stmt = $pdo->prepare("SELECT $idColumn FROM $table WHERE $idColumn = ? AND email = ?");
            $stmt->execute([$id, $email]);
            $user = $stmt->fetch();

            if ($user){
                $step = "reset";
            } else {
                $message = "Invalid email or ID.";
            }
        }
    }

    if ($action === "reset") {
        $role = $_POST["role"] ?? "";
        $id = trim($_POST["id"] ?? "");
        $email = trim($_POST["email"] ?? "");
        $pw = $_POST["password"] ?? "";
        $cpw = $_POST["confirm_password"] ?? "";

        if (!in_array($role, ["student", "lecturer", "admin"], true)) {
            $message = "Invalid role selected.";
            $step = "verify";
        } else {
            [$table, $idColumn] = mapRole($role);

            $stmt = $pdo->prepare("SELECT $idColumn FROM $table WHERE $idColumn = ? AND email = ?");
            $stmt->execute([$id, $email]);
            $user = $stmt->fetch();

            if(!$user){
                $message = "Invalid reset password request.";
                $step = "verify";
            } elseif ($pw === "" || $cpw === "") {
                $message = "Please fill in all required fields.";
                $step = "reset";
            } elseif ($pw !== $cpw) {
                $message = "Passwords do not match.";
                $step = "reset";
            } else {
                $hashed = password_hash($pw, PASSWORD_DEFAULT);
                $upd = $pdo->prepare("UPDATE $table SET password = ? WHERE $idColumn = ? AND email = ?");
                $upd->execute([$hashed, $id, $email]);

                header("Location: login.php?msg=Password updated successfully!!");
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset ="UTF-8">
        <title>Forgot Password -  CCI IMS</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <h1>Forgot Passsword</h1>

        <?php if($message !== ""): ?>
            <p style="color:red;"><?=  htmlspecialchars($message) ?></p>
            <?php endif;?>
        
        <?php if($step === "verify"): ?>
            <form method="POST">
                <input type="hidden" name="action" value="verify">
                <label>Role</label><br>
                <select name="role" required>
                    <option value="">Select Role</option>
                    <option value="student">Student</option>
                    <option value="lecturer">Lecturer</option>
                    <option value="admin">Admin</option>
                </select><br><br>

                <input type="text" name="id" placeholder="ID" required><br><br>
                <input type="email" name="email" placeholder="Email" required><br><br>

                <button type="submit">Verify</button>
            </form>

            <br>
            <a href="login.php">
                <button type="button">Cancel</button>
            </a>

            <?php else: ?>
                <form method="POST">
                    <input type="hidden" name="action" value="reset">
                    <input type="hidden" name="role" value="<?= htmlspecialchars($role) ?>">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
                    <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">

                    <input type="password" name="password" placeholder="New Password" required>
                    <input type="password" name="confirm_password" placeholder="Confirm Password" required><br><br>

                    <button type="submit">Reset Password</button>
            </form>
        <?php endif; ?>
    </body>
</html>