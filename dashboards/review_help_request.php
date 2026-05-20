<?php
session_start();
require_once '../config/db.php';
/** @var mysqli $conn */

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin"){
    header("Location: ../public/login.php");
    exit;
}

if(!isset($_GET['id'])){
    $_SESSION['error'] = "Help request ID not found";
    header("Location: admin_help_request.php");
    exit;
}

$ticket_id = $_GET['id'];

$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : $_SESSION['login_id'];

$sql = "SELECT help_ticket.*,
               students.full_name AS student_name,
               students.email,
               students.program
        FROM help_ticket
        LEFT JOIN students
        ON help_ticket.student_id = students.student_no
        WHERE help_ticket.ticket_id = '$ticket_id'
        LIMIT 1";

$result = mysqli_query($conn, $sql);

if(!$result || mysqli_num_rows($result) == 0){
    $_SESSION['error'] = "Help request not found.";
    header("Location: admin_help_requests.php");
    exit;
}

$ticket = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Review Help Request - CCI IMS</title>
    <link rel="stylesheet" href="../assets/css/dashboards.css">
</head>
<body>
    <div class="wrapper">
        <aside class="sidebar">
            <h3>Admin Menu</h3>

            <nav class="menu">
                <a href="../dashboards/admin_dashboard.php" class="menu-item">Dashboard</a>
                <a href="../dashboards/admin_announcement.php" class="menu-item">Announcements</a>
                <a href="../dashboards/manage_lecturers.php" class="menu-item">Visitng Lecturer Management</a>
                <a href="../dashboards/student_management.php" class="menu-item">Student Management</a>
                <a href="../dashboards/admin_results.php" class="menu-item">Results</a>
                <a href="../dashboards/admin_help_requests.php" class="menu-item">Get Help</a>
                <a href="../verify/logout.php" class="menu-item">Logout</a>
            </nav>
        </aside>

        <main class="content">
            <h1>Review Help Request</h1>
            <p>Welcome, <b><?php echo $full_name; ?></b></p>
            
            <br>

            <a href="admin_help_requests.php" class="btn-main">
                ← Back to Help Requests
            </a>

            <br><br>

            <?php if(isset($_SESSION['error'])) { ?>
                <div class="alert error">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php } ?>

            <?php if(isset($_SESSION['success'])) { ?>
                <div class="alert success">
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php } ?>

            <br>

            <div class="resource-card">
                <h2><?php echo $ticket['category']; ?></h2>

                <p><b>Student:</b>
                    <?php echo $ticket['student_name']; ?>
                    (<?php echo $ticket['student_id']; ?>)
                </p>

                <p><b>Email:<?php echo $ticket['email']; ?></b></p>
                <p><b>Program:<?php echo $ticket['program']; ?></b></p>
                <p><b>Status:<?php echo ucfirst($ticket['status']); ?></b></p>
                <p><b>Submitted At:<?php echo $ticket['created_at'];?></b></p>

                <br>

                <p><b>Student Message:</b></p>
                <div style="background: #f5f5f5; padding: 15px; border-radius: 10px;">
                    <?php echo nl2br($ticket['message']); ?>
                </div>

            </div>

            <br>

            <div class="resource-card">
                <h2>Admin Response</h2>

                <form action="../verify/review_help_request.php" method="POST">
                    <input type="hidden" name="ticket_id" value="<?php echo $ticket['ticket_id']; ?>">
                    <label for="status"><b>Status:</b></label>
                    <br><br>

                    <select name="status" id="status" required>
                        <option value="submitted" <?php if($ticket['status'] == "submitted") echo "selected"; ?>>
                            Submitted
                        </option>

                        <option value="in progress" <?php if($ticket['status'] == "in progress") echo "selected"; ?>>
                            In Progress
                        </option>

                        <option value="resolved" <?php if($ticket['status'] == "resolved") echo "selected"; ?>>
                            Resolved
                        </option>

                        <option value="rejected" <?php if($ticket['status'] == "rejected") echo "selected"; ?>>
                            Rejected
                        </option>
                    </select>

                    <br><br>

                    <label for="admin_reply"><b>Admin Reply:</b></label>
                    <br><br>

                    <textarea name="admin_reply"
                          id="admin_reply"
                          rows="6"
                          style="width:100%;"
                          placeholder="Enter response to student..."><?php echo $ticket['admin_reply']; ?></textarea>

                    <br><br>

                    <button type="submit" name="review_help_request">
                        Save Response
                    </button>

                </form>
            </div>
        </main>
    </div>
</body>
</html>