<?php
include('../config/db.php');
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Mark notification as read
if (isset($_GET['mark_read'])) {
    $id = $_GET['mark_read'];
    $conn->query("UPDATE notifications SET status='Read' WHERE notification_id=$id");
    header("Location: notifications.php");
    exit;
}

// Fetch notifications
$result = $conn->query("SELECT * FROM notifications ORDER BY notification_date DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Notifications</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .unread {
            background-color: #f8d7da; /* light red for unread */
            color: #721c24;
            font-weight: bold;
        }
    </style>
</head>
<body>
<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container">
    <h2>System Notifications</h2>
    <table class="table">
        <tr>
            <th>ID</th><th>Message</th><th>Date</th><th>Status</th><th>Action</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr class="<?= ($row['status']=='Unread') ? 'unread' : '' ?>">
            <td><?= $row['notification_id'] ?></td>
            <td><?= $row['message'] ?></td>
            <td><?= $row['notification_date'] ?></td>
            <td><?= $row['status'] ?></td>
            <td>
                <?php if($row['status']=='Unread'): ?>
                    <a href="notifications.php?mark_read=<?= $row['notification_id'] ?>">Mark as Read</a>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
