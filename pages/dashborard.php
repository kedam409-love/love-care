<?php
include('../config/db.php');
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
$user = $_SESSION['user'];

// Fetch unread notifications
$alerts = $conn->query("SELECT * FROM notifications WHERE status='Unread' ORDER BY notification_date DESC");

// Quick stats (example: appointments count)
$appointments = $conn->query("SELECT COUNT(*) AS total FROM appointments")->fetch_assoc();
$pets = $conn->query("SELECT COUNT(*) AS total FROM pets")->fetch_assoc();
$invoices = $conn->query("SELECT COUNT(*) AS total FROM invoices")->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Veterinary Management System</title>
    <link rel="stylesheet" href="../assets/css/theme.css">
    <link rel="stylesheet" href="../assets/css/alerts.css">
    <link rel="stylesheet" href="../assets/css/badges.css">
    <link rel="stylesheet" href="../assets/css/res.css">
</head>
<body>
<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container">
    <h3>Welcome, <?= $user['full_name']; ?> (<?= $user['role']; ?>)</h3>
    <p>Use the navigation bar to manage clinic operations.</p>

    <!-- Role-aware cards -->
    <div class="dashboard-grid">
        <?php if ($user['role'] == 'Administrator'): ?>
            <div class="card">
                <h3>Appointments</h3>
                <p>Total: <?= $appointments['total']; ?></p>
                <a href="appointments.php" class="btn-blue">Manage Appointments</a>
            </div>
            <div class="card">
                <h3>Pets</h3>
                <p>Total: <?= $pets['total']; ?></p>
                <a href="pets.php" class="btn-green">Manage Pets</a>
            </div>
            <div class="card">
                <h3>Invoices</h3>
                <p>Total: <?= $invoices['total']; ?></p>
                <a href="billing.php" class="btn-orange">Manage Billing</a>
            </div>
        <?php elseif ($user['role'] == 'Receptionist'): ?>
            <div class="card">
                <h3>Appointments</h3>
                <a href="appointments.php" class="btn-blue">Go to Appointments</a>
            </div>
            <div class="card">
                <h3>Billing</h3>
                <a href="billing.php" class="btn-orange">Go to Billing</a>
            </div>
            <div class="card">
                <h3>Pets</h3>
                <a href="pets.php" class="btn-green">Go to Pets</a>
            </div>
        <?php elseif ($user['role'] == 'Veterinarian'): ?>
            <div class="card">
                <h3>Consultations</h3>
                <a href="consultations.php" class="btn-blue">Go to Consultations</a>
            </div>
            <div class="card">
                <h3>Patient Records</h3>
                <a href="pets.php" class="btn-green">Go to Patient Records</a>
            </div>
        <?php elseif ($user['role'] == 'PetOwner'): ?>
            <div class="card">
                <h3>My Pets</h3>
                <a href="pets.php" class="btn-green">View Pets</a>
            </div>
            <div class="card">
                <h3>Invoices</h3>
                <a href="invoices.php" class="btn-orange">View Invoices</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Notifications -->
    <h3>Notifications</h3>
    <?php if ($alerts->num_rows > 0): ?>
        <?php while($row = $alerts->fetch_assoc()): ?>
            <div class="alert alert-warning">
                <?= $row['message'] ?> (<?= $row['notification_date'] ?>)
                <a href="notifications.php?mark_read=<?= $row['notification_id'] ?>" class="btn-blue" style="float:right;">Mark as Read</a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No new alerts.</p>
    <?php endif; ?>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
