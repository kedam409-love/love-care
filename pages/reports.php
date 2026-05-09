<?php
include('../config/db.php');
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Fetch totals
$totalPets = $conn->query("SELECT COUNT(*) AS c FROM pets")->fetch_assoc()['c'];
$totalAppointments = $conn->query("SELECT COUNT(*) AS c FROM appointments")->fetch_assoc()['c'];
$totalRevenue = $conn->query("SELECT SUM(amount) AS s FROM invoices WHERE payment_status='Paid'")->fetch_assoc()['s'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Clinic Reports</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container">
    <h2>Clinic Reports</h2>
    <p><strong>Total Pets Registered:</strong> <?= $totalPets ?></p>
    <p><strong>Total Appointments:</strong> <?= $totalAppointments ?></p>
    <p><strong>Total Revenue:</strong> <?= $totalRevenue ?> FCFA</p>

    <h3>Export Data</h3>
    <form method="POST" action="export_csv.php">
        <input type="submit" value="Export to CSV">
    </form>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
