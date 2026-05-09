<?php
include('../config/db.php');
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Handle invoice creation
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $appointment_id = $_POST['appointment_id'];
    $amount = $_POST['amount'];
    $payment_status = $_POST['payment_status'];
    $items = $_POST['items']; // comma-separated inventory item IDs

    // Insert invoice
    $stmt = $conn->prepare("INSERT INTO invoices (appointment_id, amount, payment_status, payment_date) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iis", $appointment_id, $amount, $payment_status);
    $stmt->execute();

    // Deduct inventory items
    $item_ids = explode(",", $items);
    foreach ($item_ids as $id) {
        $id = trim($id);
        if ($id != "") {
            $conn->query("UPDATE inventory SET quantity = quantity - 1 WHERE item_id=$id");
            // Trigger notification if below threshold
            $check = $conn->query("SELECT * FROM inventory WHERE item_id=$id")->fetch_assoc();
            if ($check['quantity'] < $check['threshold']) {
                $msg = "Low stock alert: " . $check['item_name'] . " has only " . $check['quantity'] . " left.";
                $conn->query("INSERT INTO notifications (user_id, message, status, notification_date) VALUES (1, '$msg', 'Unread', NOW())");
            }
        }
    }
}

// Fetch invoices
$result = $conn->query("SELECT i.*, p.pet_name, u.full_name AS vet_name 
                        FROM invoices i
                        JOIN appointments a ON i.appointment_id=a.appointment_id
                        JOIN pets p ON a.pet_id=p.pet_id
                        LEFT JOIN users u ON a.vet_id=u.user_id
                        ORDER BY i.payment_date DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Billing & Invoices</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include('../includes/header.php'); ?>
<?php include('../includes/navbar.php'); ?>

<div class="container">
    <h2>Billing & Invoices</h2>
    <form method="POST">
        <input type="number" name="appointment_id" placeholder="Appointment ID" required>
        <input type="number" name="amount" placeholder="Amount (FCFA)" required>
        <select name="payment_status" required>
            <option value="Paid">Paid</option>
            <option value="Pending">Pending</option>
        </select>
        <input type="text" name="items" placeholder="Inventory Item IDs (comma-separated)">
        <input type="submit" value="Generate Invoice">
    </form>

    <h3>Invoice Records</h3>
    <table class="table">
        <tr>
            <th>ID</th><th>Pet</th><th>Veterinarian</th><th>Amount</th><th>Status</th><th>Date</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['invoice_id'] ?></td>
            <td><?= $row['pet_name'] ?></td>
            <td><?= $row['vet_name'] ?></td>
            <td><?= $row['amount'] ?> FCFA</td>
            <td><?= $row['payment_status'] ?></td>
            <td><?= $row['payment_date'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
