<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'Veterinarian') {
    header("Location: login.php");
    exit;
}
include('../config/db.php');

// Fetch quick stats
$appointments = $conn->query("SELECT COUNT(*) AS total FROM appointments WHERE vet_id=".$_SESSION['user']['user_id']." AND appointment_date >= CURDATE()")->fetch_assoc();
$consultations = $conn->query("SELECT COUNT(*) AS total FROM consultations WHERE vet_id=".$_SESSION['user']['user_id'])->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Veterinarian Dashboard - VMS</title>
    <link rel="stylesheet" href="../assets/css/theme.css">
    <link rel="stylesheet" href="../assets/css/cards.css">
    <link rel="stylesheet" href="../assets/css/res.css">
    <link rel="stylesheet" href="../assets/css/nav.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <?php include('../includes/navbar.php'); ?>

    <div class="container">
        <h1>Veterinarian Dashboard</h1>
        <p>Welcome, <?php echo $_SESSION['user']['full_name']; ?>!</p>

        <div class="dashboard-grid">
            <div class="card">
                <h3><i class="fa-solid fa-calendar-check"></i> Upcoming Appointments</h3>
                <p>You have <strong><?php echo $appointments['total']; ?></strong> upcoming appointments.</p>
                <a href="appointments.php" class="btn-green">Go to Appointments</a>
            </div>

            <div class="card">
                <h3><i class="fa-solid fa-stethoscope"></i> Consultations</h3>
                <p>You have recorded <strong><?php echo $consultations['total']; ?></strong> consultations.</p>
                <a href="consultations.php" class="btn-blue">Go to Consultations</a>
            </div>

            <div class="card">
                <h3><i class="fa-solid fa-dog"></i> Patient Records</h3>
                <p>Access and update pet medical records.</p>
                <a href="pets.php" class="btn-orange">Go to Patient Records</a>
            </div>
        </div>
    </div>

    <?php include('../includes/footer.php'); ?>
</body>
</html>
