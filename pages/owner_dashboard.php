<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'PetOwner') {
    header("Location: login.php");
    exit;
}
include('../config/db.php');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pet Owner Dashboard - VMS</title>
    <!-- Consistent styles -->
    <link rel="stylesheet" href="../assets/css/theme.css">
    <link rel="stylesheet" href="../assets/css/alerts.css">
    <link rel="stylesheet" href="../assets/css/res.css">
    <link rel="stylesheet" href="../assets/css/nav.css">
    <link rel="stylesheet" href="../assets/css/badges.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <!-- Shared header with logo -->
    <?php include '../includes/header.php'; ?>
    <!-- Shared navbar -->
    <?php include '../includes/navbar.php'; ?>

    <div class="container">
        <div class="card">
            <h1><i class="fa-solid fa-house-user"></i> Pet Owner Dashboard</h1>
            <p>Welcome, <?php echo $_SESSION['user']['full_name'] ?? $_SESSION['user']['username']; ?>!</p>
        </div>
    </div>

    <div class="container">
        <!-- Pets -->
        <div class="card">
            <h3><i class="fa-solid fa-dog"></i> My Pets</h3>
            <p>View and manage your pet records.</p>
            <a class="btn-green" href="pets.php">Go to Pets</a>
        </div>

        <!-- Appointments -->
        <div class="card">
            <h3><i class="fa-solid fa-calendar-check"></i> Appointments</h3>
            <p>Check upcoming and past appointments.</p>
            <a class="btn-blue" href="appointments.php">Go to Appointments</a>
        </div>

        <!-- Invoices -->
        <div class="card">
            <h3><i class="fa-solid fa-file-invoice-dollar"></i> Invoices</h3>
            <p>View bills and payment status.</p>
            <a class="btn-green" href="invoices.php">Go to Invoices</a>
        </div>

        <!-- Notifications -->
        <div class="card">
            <h3><i class="fa-solid fa-bell"></i> Notifications</h3>
            <p>Check your latest updates.</p>
            <a class="btn-blue" href="notifications.php">View Notifications</a>
        </div>
    </div>

    <!-- Shared footer -->
    <?php include '../includes/footer.php'; ?>
</body>
</html>
