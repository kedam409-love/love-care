<?php
include('../config/db.php');
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Filter consultations by pet if pet_id is passed
$where = "";
if (isset($_GET['pet_id'])) {
    $pet_id = intval($_GET['pet_id']);
    $where = "WHERE c.pet_id = $pet_id";
}
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $appointment_id = $_POST['appointment_id'];
    $diagnosis = $_POST['diagnosis'];
    $treatment = $_POST['treatment'];
    $notes = $_POST['notes'];

    $stmt = $conn->prepare("INSERT INTO consultations (appointment_id, diagnosis, treatment, notes) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $appointment_id, $diagnosis, $treatment, $notes);
    $stmt->execute();
}

// Fetch consultations
$sql = "SELECT c.*, p.pet_name, u.full_name AS vet_name 
        FROM consultations c
        JOIN pets p ON c.pet_id=p.pet_id
        LEFT JOIN users u ON c.vet_id=u.user_id
        $where
        ORDER BY c.consultation_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Consultations</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/res.css">
</head>
<body>
<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container">
    <h2>Consultation Records</h2>
    <form method="POST">
        <input type="number" name="appointment_id" placeholder="Appointment ID" required>
        <input type="text" name="diagnosis" placeholder="Diagnosis" required>
        <input type="text" name="treatment" placeholder="Treatment" required>
        <textarea name="notes" placeholder="Additional Notes"></textarea>
        <input type="submit" value="Add Consultation">
    </form>

    <h3>Consultation History</h3>
    <table class="table">
        <tr>
            <th>ID</th><th>Pet</th><th>Veterinarian</th><th>Date</th><th>Diagnosis</th><th>Treatment</th><th>Notes</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['consultation_id'] ?></td>
            <td><?= $row['pet_name'] ?></td>
            <td><?= $row['vet_name'] ?></td>
            <td><?= $row['appointment_date'] ?></td>
            <td><?= $row['diagnosis'] ?></td>
            <td><?= $row['treatment'] ?></td>
            <td><?= $row['notes'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
