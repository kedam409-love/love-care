<?php
include('../config/db.php');
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$role = $_SESSION['user']['role'];
$user_id = $_SESSION['user']['id'];

// Mark notification as read (secure prepared statement)
if (isset($_GET['mark_read'])) {
    $id = intval($_GET['mark_read']);

    if ($role === 'PetOwner') {
        // Pet Owner can only mark their own notifications
        $stmt = $conn->prepare("UPDATE notifications SET status='Read' WHERE notification_id=? AND owner_id=?");
        $stmt->bind_param("ii", $id, $user_id);
    } else {
        // Other roles can mark any notification
        $stmt = $conn->prepare("UPDATE notifications SET status='Read' WHERE notification_id=?");
        $stmt->bind_param("i", $id);
    }

    $stmt->execute();
    header("Location: notifications.php?marked=1");
    exit;
}

// Filter notifications by role
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

if ($role === 'PetOwner') {
    // Pet Owners → only their own notifications
    $sql = ($filter == 'unread')
        ? "SELECT * FROM notifications WHERE owner_id=? AND status='Unread' ORDER BY notification_date DESC"
        : "SELECT * FROM notifications WHERE owner_id=? ORDER BY notification_date DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

} elseif ($role === 'Receptionist') {
    // Receptionists → see all notifications
    $sql = ($filter == 'unread')
        ? "SELECT * FROM notifications WHERE status='Unread' ORDER BY notification_date DESC"
        : "SELECT * FROM notifications ORDER BY notification_date DESC";
    $result = $conn->query($sql);

} elseif ($role === 'Veterinarian') {
    // Veterinarians → notifications linked to their consultations
    $sql = ($filter == 'unread')
        ? "SELECT n.* FROM notifications n 
           JOIN consultations c ON n.pet_id=c.pet_id 
           WHERE c.vet_id=? AND n.status='Unread' ORDER BY n.notification_date DESC"
        : "SELECT n.* FROM notifications n 
           JOIN consultations c ON n.pet_id=c.pet_id 
           WHERE c.vet_id=? ORDER BY n.notification_date DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

} elseif ($role === 'Administrator') {
    // Admins → see everything
    $sql = ($filter == 'unread')
        ? "SELECT * FROM notifications WHERE status='Unread' ORDER BY notification_date DESC"
        : "SELECT * FROM notifications ORDER BY notification_date DESC";
    $result = $conn->query($sql);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Notifications</title>
    <link rel="stylesheet" href="../assets/css/theme.css">
    <link rel="stylesheet" href="../assets/css/alerts.css">
    <link rel="stylesheet" href="../assets/css/badges.css">
    <link rel="stylesheet" href="../assets/css/res.css">
</head>
<body>
<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container">
    <h2><i class="fa-solid fa-bell"></i> System Notifications</h2>

    <!-- Success banner -->
    <?php if (isset($_GET['marked'])): ?>
        <div class="alert alert-success">Notification marked as read.</div>
    <?php endif; ?>

    <!-- Filter bar -->
    <div style="margin-bottom:15px;">
        <a href="notifications.php?filter=all" class="btn-blue">All</a>
        <a href="notifications.php?filter=unread" class="btn-green">Unread Only</a>
    </div>

    <table class="table">
        <tr>
            <th>ID</th><th>Message</th><th>Date</th><th>Status</th><th>Action</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['notification_id'] ?></td>
            <td><?= htmlspecialchars($row['message']) ?></td>
            <td><?= $row['notification_date'] ?></td>
            <td>
                <span class="badge badge-<?= strtolower($row['status']); ?>">
                    <?= $row['status'] ?>
                </span>
            </td>
            <td>
                <?php if($row['status']=='Unread'): ?>
                    <a href="notifications.php?mark_read=<?= $row['notification_id'] ?>" class="btn-green">Mark as Read</a>
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
