<?php
include('../config/db.php');
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
$user = $_SESSION['user'];

// Fetch unread notifications
$alerts = $conn->query("SELECT * FROM notifications WHERE status='Unread' ORDER BY notification_date DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Veterinary Management System</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .alert {
            background-color: #f8d7da; /* light red */
            color: #721c24;
            padding: 10px;
            margin: 5px 0;
            border-radius: 4px;
        }
    </style>
</head>
<body>
<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container">
    <h3>Welcome, <?= $user['full_name']; ?> (<?= $user['role']; ?>)</h3>
    <p>Use the navigation bar to manage clinic operations.</p>

    <h3>Notifications</h3>
    <?php if ($alerts->num_rows > 0): ?>
        <?php while($row = $alerts->fetch_assoc()): ?>
            <div class="alert">
                <?= $row['message'] ?> (<?= $row['notification_date'] ?>)
                <a href="notifications.php?mark_read=<?= $row['notification_id'] ?>" style="float:right; color:#007bff;">Mark as Read</a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No new alerts.</p>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
