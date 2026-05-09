<?php
include('../config/db.php');
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pet_id = $_POST['pet_id'];
    $vet_id = $_POST['vet_id'];
    $date = $_POST['appointment_date'];
    $time = $_POST['appointment_time'];

    $stmt = $conn->prepare("INSERT INTO appointments (pet_id, vet_id, appointment_date, appointment_time, status) VALUES (?, ?, ?, ?, 'Scheduled')");
    $stmt->bind_param("iiss", $pet_id, $vet_id, $date, $time);
    $stmt->execute();
}

// Fetch appointments
$result = $conn->query("SELECT a.*, p.pet_name, u.full_name AS vet_name 
                        FROM appointments a 
                        JOIN pets p ON a.pet_id=p.pet_id 
                        LEFT JOIN users u ON a.vet_id=u.user_id
                        ORDER BY a.appointment_date, a.appointment_time");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Appointments</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container">
    <h2>Appointment Scheduling</h2>
    <form method="POST">
        <input type="number" name="pet_id" placeholder="Pet ID" required>
        <input type="number" name="vet_id" placeholder="Vet ID" required>
        <input type="date" name="appointment_date" required>
        <input type="time" name="appointment_time" required>
        <input type="submit" value="Book Appointment">
    </form>

    <h3>Upcoming Appointments</h3>
    <table class="table">
        <tr>
            <th>ID</th><th>Pet</th><th>Veterinarian</th><th>Date</th><th>Time</th><th>Status</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['appointment_id'] ?></td>
            <td><?= $row['pet_name'] ?></td>
            <td><?= $row['vet_name'] ?></td>
            <td><?= $row['appointment_date'] ?></td>
            <td><?= $row['appointment_time'] ?></td>
            <td><?= $row['status'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
